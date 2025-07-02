<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fakultas;
use Illuminate\Validation\Rule;

class AdminFakultasController extends Controller
{
    protected $daftarNamaFakultas = [
        'Fakultas Hukum (FH)',
        'Fakultas Ekonomika dan Bisnis (FEB)',
        'Fakultas Kedokteran (FK)',
        'Fakultas Teknik (FT)',
        'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)',
        'Fakultas Ilmu Budaya (FIB)',
        'Fakultas Peternakan dan Pertanian (FPP)',
        'Fakultas Sains dan Matematika (FSM)',
        'Fakultas Perikanan dan Ilmu Kelautan (FPIK)',
        'Fakultas Kesehatan Masyarakat (FKM)',
        'Fakultas Psikologi',
        'Sekolah Vokasi (SV)',
    ];

    // Data Program Studi Fakultas Teknik
    protected $daftarProdiTeknik = [
        ['nama_prodi' => 'Arsitektur', 'koordinat' => '-7.051548980978043, 110.43832160759905', 'radius' => '50'],
        ['nama_prodi' => 'Perencanaan Wilayah dan Kota', 'koordinat' => '-7.051357322625458, 110.4391262702633', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Elektro', 'koordinat' => '-7.049738031625554, 110.43994267975074', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Geodesi', 'koordinat' => '-7.051577851662326, 110.44042972256258', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Geologi', 'koordinat' => '-7.052262994626362, 110.4394890049726', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Industri', 'koordinat' => '-7.05107340508552, 110.44151837569481', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Kimia', 'koordinat' => '-7.052045739394591, 110.44117709441701', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Komputer', 'koordinat' => '-7.051106691509728, 110.44021151482586', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Lingkungan', 'koordinat' => '-7.050469281826524, 110.44026483963367', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Mesin', 'koordinat' => '-7.050159045754759, 110.44175646710545', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Perkapalan', 'koordinat' => '-7.050751018608915, 110.43993949004893', 'radius' => '50'],
        ['nama_prodi' => 'Teknik Sipil', 'koordinat' => '-7.052050709419995, 110.43906595798757', 'radius' => '50'],
    ];


    public function index()
    {
        $fakultasData = Fakultas::orderBy('nama', 'asc')->get();
        return view('admin.fakultas.index', compact('fakultasData'));
    }

    public function create()
    {
        $namaFakultasOptions = $this->daftarNamaFakultas;
        $prodiTeknikOptions = collect($this->daftarProdiTeknik)->sortBy('nama_prodi')->values()->all();
        return view('admin.fakultas.create', compact('namaFakultasOptions', 'prodiTeknikOptions'));
    }

    public function store(Request $request)
    {
        $namaProdiTeknikValues = collect($this->daftarProdiTeknik)->pluck('nama_prodi')->implode(',');

        $validatedData = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('fakultas', 'nama'), Rule::in($this->daftarNamaFakultas)],
            'tipe_fakultas' => 'required|in:Non-Teknik,Teknik',
            'koordinat_fakultas' => 'nullable|string|max:255',
            'radius_fakultas' => 'nullable|numeric|min:1',
            'program_studi' => 'nullable|array',
            'program_studi.*.nama_prodi' => 'required_with:program_studi|string|max:255',
            'detail_prodi' => 'nullable|array',
            'detail_prodi.*.nama_prodi' => ['required_if:tipe_fakultas,Teknik','nullable','string', Rule::in(explode(',', $namaProdiTeknikValues))],
            'detail_prodi.*.koordinat' => 'required_if:tipe_fakultas,Teknik|nullable|string|max:255',
            'detail_prodi.*.radius' => 'required_if:tipe_fakultas,Teknik|nullable|numeric|min:1',
        ]);

        $dataToCreate = [
            'nama' => $validatedData['nama'],
            'tipe_fakultas' => $validatedData['tipe_fakultas'],
        ];

        $defaultJenjang = ($validatedData['nama'] === 'Sekolah Vokasi (SV)') ? 'D4' : 'S1';

        if ($validatedData['tipe_fakultas'] === 'Non-Teknik') {
            $dataToCreate['koordinat_fakultas'] = $validatedData['koordinat_fakultas'] ?? null;
            $dataToCreate['radius_fakultas'] = isset($validatedData['radius_fakultas']) && is_numeric($validatedData['radius_fakultas']) ? $validatedData['radius_fakultas'] . ' meter' : null;
            
            $programStudiProcessed = [];
            if (isset($validatedData['program_studi']) && is_array($validatedData['program_studi'])) {
                foreach ($validatedData['program_studi'] as $prodi) {
                    if (is_array($prodi) && !empty(trim($prodi['nama_prodi'] ?? ''))) {
                        $programStudiProcessed[] = [
                            'jenjang' => $defaultJenjang,
                            'nama_prodi' => trim($prodi['nama_prodi'])
                        ];
                    }
                }
            }
            $dataToCreate['program_studi_json'] = !empty($programStudiProcessed) ? json_encode(array_values($programStudiProcessed)) : null;
            $dataToCreate['detail_prodi_json'] = null;
        } elseif ($validatedData['tipe_fakultas'] === 'Teknik') {
            $detailProdiProcessed = [];
            if (isset($validatedData['detail_prodi']) && is_array($validatedData['detail_prodi'])) {
                foreach ($validatedData['detail_prodi'] as $prodi) {
                    if (is_array($prodi) && !empty(trim($prodi['nama_prodi'] ?? ''))) {
                        // Saat CREATE, ambil koordinat & radius dari $daftarProdiTeknik berdasarkan nama_prodi yang dipilih
                        $selectedProdiData = collect($this->daftarProdiTeknik)->firstWhere('nama_prodi', $prodi['nama_prodi']);
                        $koordinatOtomatis = $selectedProdiData['koordinat'] ?? ($prodi['koordinat'] ?? null); // Fallback ke input jika tidak ada di master
                        $radiusOtomatis = $selectedProdiData['radius'] ?? ($prodi['radius'] ?? null); // Fallback ke input jika tidak ada di master

                        $detailProdiProcessed[] = [
                            'jenjang' => $defaultJenjang,
                            'nama_prodi' => trim($prodi['nama_prodi']),
                            'koordinat' => $koordinatOtomatis,
                            'radius' => isset($radiusOtomatis) && is_numeric($radiusOtomatis) ? $radiusOtomatis . ' meter' : null,
                        ];
                    }
                }
            }
            $dataToCreate['detail_prodi_json'] = !empty($detailProdiProcessed) ? json_encode(array_values($detailProdiProcessed)) : null;
            $dataToCreate['program_studi_json'] = null;
            $dataToCreate['koordinat_fakultas'] = null;
            $dataToCreate['radius_fakultas'] = null;
        }

        Fakultas::create($dataToCreate);

        return redirect()->route('admin.fakultas.index')
                         ->with('success', 'Data fakultas berhasil ditambahkan.');
    }

    public function edit(Fakultas $fakultas)
    {
        $namaFakultasOptions = $this->daftarNamaFakultas;
        $prodiTeknikOptions = collect($this->daftarProdiTeknik)->sortBy('nama_prodi')->values()->all();
        return view('admin.fakultas.edit', compact('fakultas', 'namaFakultasOptions', 'prodiTeknikOptions'));
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $namaProdiTeknikValues = collect($this->daftarProdiTeknik)->pluck('nama_prodi')->implode(',');

        $validatedData = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('fakultas', 'nama')->ignore($fakultas->_id, '_id'), Rule::in($this->daftarNamaFakultas)],
            'tipe_fakultas' => 'required|in:Non-Teknik,Teknik',
            'koordinat_fakultas' => 'nullable|string|max:255',
            'radius_fakultas' => 'nullable|numeric|min:1',
            'program_studi' => 'nullable|array',
            'program_studi.*.nama_prodi' => 'required_with:program_studi|string|max:255',
            'detail_prodi' => 'nullable|array',
            'detail_prodi.*.nama_prodi' => ['required_if:tipe_fakultas,Teknik','nullable','string', Rule::in(explode(',', $namaProdiTeknikValues))],
            'detail_prodi.*.koordinat' => 'required_if:tipe_fakultas,Teknik|nullable|string|max:255',
            'detail_prodi.*.radius' => 'required_if:tipe_fakultas,Teknik|nullable|numeric|min:1',
        ]);

        $dataToUpdate = [
            'nama' => $validatedData['nama'],
            'tipe_fakultas' => $validatedData['tipe_fakultas'],
        ];

        $defaultJenjang = ($validatedData['nama'] === 'Sekolah Vokasi (SV)') ? 'D4' : 'S1';

        if ($validatedData['tipe_fakultas'] === 'Non-Teknik') {
            $dataToUpdate['koordinat_fakultas'] = $validatedData['koordinat_fakultas'] ?? null;
            $dataToUpdate['radius_fakultas'] = isset($validatedData['radius_fakultas']) && is_numeric($validatedData['radius_fakultas']) ? $validatedData['radius_fakultas'] . ' meter' : null;
            $programStudiProcessed = [];
            if (isset($validatedData['program_studi']) && is_array($validatedData['program_studi'])) {
                foreach ($validatedData['program_studi'] as $prodi) {
                    if (is_array($prodi) && !empty(trim($prodi['nama_prodi'] ?? ''))) {
                        $programStudiProcessed[] = [
                            'jenjang' => $defaultJenjang,
                            'nama_prodi' => trim($prodi['nama_prodi'])
                        ];
                    }
                }
            }
            $dataToUpdate['program_studi_json'] = !empty($programStudiProcessed) ? json_encode(array_values($programStudiProcessed)) : null;
            $dataToUpdate['detail_prodi_json'] = null;
        } elseif ($validatedData['tipe_fakultas'] === 'Teknik') {
            $detailProdiProcessed = [];
            if (isset($validatedData['detail_prodi']) && is_array($validatedData['detail_prodi'])) {
                foreach ($validatedData['detail_prodi'] as $prodi) {
                    if (is_array($prodi) && !empty(trim($prodi['nama_prodi'] ?? ''))) {
                        // PERBAIKAN: Saat update, gunakan koordinat dan radius dari request (form)
                        // bukan dari $this->daftarProdiTeknik lagi, karena user mungkin mengubahnya di form
                        // (meskipun readonly, JS yang mengisinya dari data-attribute yang mungkin berbeda jika master list diubah)
                        // Namun, karena form FT sekarang dropdown untuk nama prodi dan koordinat/radius readonly diisi JS,
                        // maka nilai yang dikirim dari form ($prodi['koordinat'] dan $prodi['radius']) adalah yang terbaru dari JS.
                        $koordinatDariForm = $prodi['koordinat'] ?? null;
                        $radiusDariForm = $prodi['radius'] ?? null;
                        
                        $detailProdiProcessed[] = [
                            'jenjang' => $defaultJenjang,
                            'nama_prodi' => trim($prodi['nama_prodi']),
                            'koordinat' => $koordinatDariForm, // Gunakan data dari form
                            'radius' => isset($radiusDariForm) && is_numeric($radiusDariForm) ? $radiusDariForm . ' meter' : null, // Gunakan data dari form
                        ];
                    }
                }
            }
            $dataToUpdate['detail_prodi_json'] = !empty($detailProdiProcessed) ? json_encode(array_values($detailProdiProcessed)) : null;
            $dataToUpdate['program_studi_json'] = null;
            $dataToUpdate['koordinat_fakultas'] = null;
            $dataToUpdate['radius_fakultas'] = null;
        }

        $fakultas->update($dataToUpdate);

        return redirect()->route('admin.fakultas.index')
                         ->with('success', 'Data fakultas berhasil diperbarui.');
    }

    public function destroy(Fakultas $fakultas)
    {
        $fakultas->delete();
        return redirect()->route('admin.fakultas.index')
                         ->with('success', 'Data fakultas berhasil dihapus.');
    }

    public function getFakultasDetails(Request $request, $nama_fakultas)
    {
        $fakultas = Fakultas::where('nama', $nama_fakultas)->first();

        if (!$fakultas) {
            if ($nama_fakultas === 'Fakultas Teknik (FT)') {
                return response()->json([
                    'tipe_fakultas' => 'Teknik',
                    'program_studi' => collect($this->daftarProdiTeknik)->sortBy('nama_prodi')->values()->all(),
                ]);
            } else {
                $fakultasFromStatic = collect($this->getInitialFakultasData())->firstWhere('nama', $nama_fakultas);
                 if ($fakultasFromStatic) {
                     $prodiData = [];
                     if(isset($fakultasFromStatic['program_studi'])) {
                        foreach($fakultasFromStatic['program_studi'] as $ps) {
                            $prodiData[] = ['nama_prodi' => $ps['nama_prodi'], 'jenjang' => $ps['jenjang']];
                        }
                     }
                    return response()->json([
                        'tipe_fakultas' => $fakultasFromStatic['tipe_fakultas'] ?? 'Non-Teknik',
                        'program_studi' => $prodiData,
                        'koordinat_fakultas' => $fakultasFromStatic['koordinat'] ?? null,
                        'radius_fakultas' => isset($fakultasFromStatic['radius']) ? preg_replace('/[^0-9]/', '', $fakultasFromStatic['radius']) : null,
                    ]);
                }
                return response()->json(['error' => 'Data Fakultas tidak ditemukan dan tidak ada data statis.'], 404);
            }
        }

        $data = [
            'tipe_fakultas' => $fakultas->tipe_fakultas,
            'program_studi' => [],
            'koordinat_fakultas' => null,
            'radius_fakultas' => null,
            'detail_prodi_options' => []
        ];

        if ($fakultas->tipe_fakultas === 'Teknik') {
            $data['program_studi'] = $fakultas->detail_prodi; 
            $data['detail_prodi_options'] = collect($this->daftarProdiTeknik)->sortBy('nama_prodi')->values()->all();
        } else { 
            $data['program_studi'] = $fakultas->program_studi; 
            $data['koordinat_fakultas'] = $fakultas->koordinat_fakultas;
            $data['radius_fakultas'] = preg_replace('/[^0-9]/', '', $fakultas->radius_fakultas ?? '');
        }

        return response()->json($data);
    }
    
    private function getInitialFakultasData(){
        return [
             [
                'nama' => 'Fakultas Hukum (FH)',
                'tipe_fakultas' => 'Non-Teknik',
                'program_studi' => [['jenjang' => 'S1', 'nama_prodi' => 'Ilmu Hukum']],
                'koordinat' => '-7.051191, 110.436203',
                'radius' => '150 meter',
             ]
        ];
    }
    public function getProdiTeknikDataForJs() {
        return collect($this->daftarProdiTeknik)->sortBy('nama_prodi')->values()->all();
    }
}
