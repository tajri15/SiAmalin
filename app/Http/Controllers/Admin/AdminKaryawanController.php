<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\FaceRecognitionService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AdminKaryawanController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    public function index(Request $request)
    {
        $query = Karyawan::query();

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%')
                  ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        // Sorting logic
        $sortBy = $request->get('sort_by', 'nama_lengkap');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['nik', 'nama_lengkap', 'jabatan'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('nama_lengkap', 'asc'); // Default sort
        }

        $karyawans = $query->paginate(10)->appends($request->query());

        return view('admin.karyawan.index', compact('karyawans', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        $fakultasOptions = Fakultas::orderBy('nama')->pluck('nama', 'nama')->all();
        $jabatanOptions = [
            'Admin' => 'Admin',
            'Komandan' => 'Komandan',
            'Ketua Departemen' => 'Ketua Departemen',
            'Petugas Keamanan' => 'Petugas Keamanan',
        ];
        return view('admin.karyawan.create_step1', compact('fakultasOptions', 'jabatanOptions'));
    }

    public function storeStep1(Request $request)
    {
        $jabatan = $request->input('jabatan');

        $rules = [
            'nik' => 'required|string|unique:karyawans,nik',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|in:Admin,Komandan,Ketua Departemen,Petugas Keamanan',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($jabatan === 'Komandan') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
        } elseif ($jabatan === 'Ketua Departemen') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
            $rules['program_studi_nama'] = 'required|string|max:255';
        } elseif ($jabatan === 'Petugas Keamanan') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
            $rules['program_studi_nama'] = 'required|string|max:255';
            $rules['office_radius'] = 'required|numeric|min:1';
        }
        
        $request->validate($rules);

        $karyawanData = $request->except(['password_confirmation', '_token', 'foto']);
        $karyawanData['password'] = Hash::make($request->password);

        // Initialize fields
        $karyawanData['is_admin'] = false;
        $karyawanData['is_komandan'] = false;
        $karyawanData['is_ketua_departemen'] = false;
        $karyawanData['unit'] = null;
        $karyawanData['departemen'] = null;
        $karyawanData['office_location'] = null;
        $karyawanData['office_radius'] = null;
        $karyawanData['face_embedding'] = null;

        if ($jabatan === 'Admin') {
            $karyawanData['is_admin'] = true;
        } elseif ($jabatan === 'Komandan') {
            $karyawanData['is_komandan'] = true;
            $karyawanData['unit'] = $request->fakultas_nama;
        } elseif ($jabatan === 'Ketua Departemen') {
            $karyawanData['is_ketua_departemen'] = true;
            $karyawanData['unit'] = $request->fakultas_nama;
            $karyawanData['departemen'] = $request->program_studi_nama;
        } elseif ($jabatan === 'Petugas Keamanan') {
            $karyawanData['unit'] = $request->fakultas_nama;
            $karyawanData['departemen'] = $request->program_studi_nama;

            $fakultas = Fakultas::where('nama', $request->fakultas_nama)->first();
            $office_location_coords = null;
            $office_radius_val = $request->office_radius;

            if ($fakultas) {
                if ($fakultas->tipe_fakultas === 'Teknik') {
                    $detailProdiArray = $fakultas->detail_prodi;
                    if (is_array($detailProdiArray)) {
                        foreach ($detailProdiArray as $prodi) {
                            if (isset($prodi['nama_prodi']) && $prodi['nama_prodi'] === $request->program_studi_nama) {
                                if (isset($prodi['koordinat'])) {
                                    $coords = explode(',', $prodi['koordinat']);
                                    if (count($coords) == 2) {
                                        $office_location_coords = [floatval(trim($coords[1])), floatval(trim($coords[0]))]; // lng, lat
                                    }
                                }
                                break;
                            }
                        }
                    }
                } else { // Non-Teknik
                    if ($fakultas->koordinat_fakultas) {
                        $coords = explode(',', $fakultas->koordinat_fakultas);
                        if (count($coords) == 2) {
                            $office_location_coords = [floatval(trim($coords[1])), floatval(trim($coords[0]))]; // lng, lat
                        }
                    }
                }
            }

            if ($office_location_coords) {
                $karyawanData['office_location'] = ['type' => 'Point', 'coordinates' => $office_location_coords];
            }
            $karyawanData['office_radius'] = (int)$office_radius_val;
        }

        // Handle file upload
        $finalPath = null;
        if ($request->hasFile('foto')) {
            $filePath = $request->file('foto')->store('temp_uploads/karyawan_profile', 'public');
            if ($jabatan !== 'Petugas Keamanan') {
                $originalFile = new \Illuminate\Http\File(storage_path('app/public/' . $filePath));
                $newFileName = \Illuminate\Support\Str::random(40) . '.' . $originalFile->guessExtension();
                $finalPath = 'uploads/karyawan/' . $newFileName;
                Storage::disk('public')->move($filePath, $finalPath);
                $karyawanData['foto'] = $finalPath;
            } else { 
                $request->session()->put('karyawan_temp_foto_path', $filePath);
            }
        }

        if ($jabatan !== 'Petugas Keamanan') {
            try {
                Karyawan::create($karyawanData);
                return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan (' . $jabatan . ') berhasil ditambahkan.');
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                if (isset($finalPath) && Storage::disk('public')->exists($finalPath)) {
                    Storage::disk('public')->delete($finalPath);
                }
                return redirect()->back()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage())->withInput();
            }
        } else {
            $request->session()->put('karyawan_temp_data', $karyawanData);
            return redirect()->route('admin.karyawan.face_registration');
        }
    }

    public function showFaceRegistration()
    {
        if (!session()->has('karyawan_temp_data')) {
            return redirect()->route('admin.karyawan.create')
                             ->with('error', 'Silakan isi data karyawan terlebih dahulu');
        }
        $tempData = session('karyawan_temp_data');
        if ($tempData['jabatan'] !== 'Petugas Keamanan') {
             return redirect()->route('admin.karyawan.index')->with('info', 'Hanya Petugas Keamanan yang memerlukan registrasi wajah.');
        }
        return view('admin.karyawan.create_step2');
    }

    public function completeRegistration(Request $request)
    {
        $request->validate(['face_image' => 'required|string']);

        $karyawanData = session('karyawan_temp_data');
        $tempFotoPath = session('karyawan_temp_foto_path');

        if (!$karyawanData) {
            return redirect()->route('admin.karyawan.create')
                             ->with('error', 'Sesi pendaftaran telah kadaluarsa atau data tidak lengkap.');
        }
        
        if ($karyawanData['jabatan'] !== 'Petugas Keamanan') {
            session()->forget(['karyawan_temp_data', 'karyawan_temp_foto_path']);
            Log::warning('Attempted face registration for non-security role in completeRegistration.');
            return redirect()->route('admin.karyawan.index')->with('info', 'User sudah terdaftar tanpa wajah.');
        }

        try {
            $faceEmbedding = $this->faceRecognitionService->generateFaceEmbedding(base64_decode(explode(',', $request->face_image)[1]));
            $karyawan = new Karyawan();
            $karyawan->fill($karyawanData);
            $karyawan->face_embedding = $faceEmbedding;

            if ($tempFotoPath && Storage::disk('public')->exists($tempFotoPath)) {
                $originalFile = new \Illuminate\Http\File(storage_path('app/public/' . $tempFotoPath));
                $newFileName = \Illuminate\Support\Str::random(40) . '.' . $originalFile->guessExtension();
                $finalPath = 'uploads/karyawan/' . $newFileName;
                Storage::disk('public')->move($tempFotoPath, $finalPath);
                $karyawan->foto = $finalPath;
            }
            $karyawan->save();
            session()->forget(['karyawan_temp_data', 'karyawan_temp_foto_path']);
            return redirect()->route('admin.karyawan.index')
                             ->with('success', 'Karyawan berhasil didaftarkan dengan data wajah dan lokasi kantor.');
        } catch (\Exception $e) {
            Log::error('Error completing registration: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            session()->forget(['karyawan_temp_data', 'karyawan_temp_foto_path']);
            if ($tempFotoPath && Storage::disk('public')->exists($tempFotoPath)) {
                Storage::disk('public')->delete($tempFotoPath);
            }
            return redirect()->back()
                             ->with('error', 'Gagal memproses data wajah atau menyimpan karyawan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('admin.karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $fakultasOptions = Fakultas::orderBy('nama')->pluck('nama', 'nama')->all();
        $jabatanOptions = [
            'Admin' => 'Admin',
            'Komandan' => 'Komandan',
            'Ketua Departemen' => 'Ketua Departemen',
            'Petugas Keamanan' => 'Petugas Keamanan',
        ];

        $programStudiOptions = [];
        $selectedFakultas = null;
        if ($karyawan->unit) {
            $selectedFakultas = Fakultas::where('nama', $karyawan->unit)->first();
            if ($selectedFakultas) {
                 if ($selectedFakultas->tipe_fakultas === 'Teknik') {
                    $prodiTeknikData = app(\App\Http\Controllers\Admin\AdminFakultasController::class)->getProdiTeknikDataForJs();
                    foreach($prodiTeknikData as $prodi) {
                        $programStudiOptions[] = ['nama_prodi' => $prodi['nama_prodi'], 'radius' => $prodi['radius'] ?? null, 'koordinat' => $prodi['koordinat'] ?? null];
                    }
                } else {
                    $prodiNonTeknik = $selectedFakultas->program_studi;
                    if (is_array($prodiNonTeknik)) {
                        foreach($prodiNonTeknik as $prodi) {
                            $programStudiOptions[] = ['nama_prodi' => $prodi['nama_prodi']];
                        }
                    }
                }
            }
        }

        return view('admin.karyawan.edit', compact('karyawan', 'fakultasOptions', 'jabatanOptions', 'programStudiOptions', 'selectedFakultas'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $jabatan = $request->input('jabatan');

        $rules = [
            'nik' => 'required|string|unique:karyawans,nik,' . $karyawan->_id . ',_id',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan' => 'required|string|in:Admin,Komandan,Ketua Departemen,Petugas Keamanan',
            'no_hp' => 'required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($jabatan === 'Komandan') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
        } elseif ($jabatan === 'Ketua Departemen') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
            $rules['program_studi_nama'] = 'required|string|max:255';
        } elseif ($jabatan === 'Petugas Keamanan') {
            $rules['fakultas_nama'] = 'required|string|exists:fakultas,nama';
            $rules['program_studi_nama'] = 'required|string|max:255';
            $rules['office_radius'] = 'required|numeric|min:1';
        }

        $request->validate($rules);
        $data = $request->only(['nik', 'nama_lengkap', 'jabatan', 'no_hp']);

        $data['is_admin'] = false;
        $data['is_komandan'] = false;
        $data['is_ketua_departemen'] = false;
        $data['unit'] = null;
        $data['departemen'] = null;
        $data['office_location'] = null;
        $data['office_radius'] = null;

        if ($jabatan === 'Admin') {
            $data['is_admin'] = true;
        } elseif ($jabatan === 'Komandan') {
            $data['is_komandan'] = true;
            $data['unit'] = $request->fakultas_nama;
        } elseif ($jabatan === 'Ketua Departemen') {
            $data['is_ketua_departemen'] = true;
            $data['unit'] = $request->fakultas_nama;
            $data['departemen'] = $request->program_studi_nama;
        } elseif ($jabatan === 'Petugas Keamanan') {
            $data['unit'] = $request->fakultas_nama;
            $data['departemen'] = $request->program_studi_nama;

            $fakultas = Fakultas::where('nama', $request->fakultas_nama)->firstOrFail();
            $office_location_coords = null;
            $office_radius_val = $request->office_radius;

            if ($fakultas->tipe_fakultas === 'Teknik') {
                $detailProdiArray = $fakultas->detail_prodi;
                if (is_array($detailProdiArray)) {
                    foreach ($detailProdiArray as $prodi) {
                        if (isset($prodi['nama_prodi']) && $prodi['nama_prodi'] === $request->program_studi_nama) {
                            if (isset($prodi['koordinat'])) {
                                $coords = explode(',', $prodi['koordinat']);
                                if (count($coords) == 2) {
                                    $office_location_coords = [floatval(trim($coords[1])), floatval(trim($coords[0]))];
                                }
                            }
                            break;
                        }
                    }
                }
            } else {
                if ($fakultas->koordinat_fakultas) {
                    $coords = explode(',', $fakultas->koordinat_fakultas);
                    if (count($coords) == 2) {
                        $office_location_coords = [floatval(trim($coords[1])), floatval(trim($coords[0]))];
                    }
                }
            }

            if ($office_location_coords) {
                $data['office_location'] = ['type' => 'Point', 'coordinates' => $office_location_coords];
            }
            $data['office_radius'] = (int)$office_radius_val;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $filePath = $request->file('foto')->store('uploads/karyawan', 'public');
            $data['foto'] = $filePath;
        }

        if ($karyawan->jabatan === 'Petugas Keamanan' && $jabatan !== 'Petugas Keamanan') {
            $data['face_embedding'] = null;
        }

        $karyawan->update($data);
        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $karyawan->delete();
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function viewFaceData($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('admin.karyawan.face_data', compact('karyawan'));
    }

    public function resetFaceData(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->face_embedding = null;
        $karyawan->save();
        return redirect()->back()->with('success', 'Data wajah karyawan berhasil direset. Karyawan perlu registrasi wajah ulang jika diperlukan.');
    }

     public function resetOfficeLocation(Request $request, $id)
     {
       $karyawan = Karyawan::findOrFail($id);
       $karyawan->office_location = null;
       $karyawan->office_radius = null;
       $karyawan->save();
       return redirect()->back()->with('success', 'Lokasi kantor karyawan berhasil direset.');
     }
}