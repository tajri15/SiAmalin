<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Karyawan; // PASTIKAN BARIS INI ADA
use App\Services\FaceRecognitionService;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    protected $faceRecognitionService;

    public function __construct(FaceRecognitionService $faceRecognitionService)
    {
        $this->faceRecognitionService = $faceRecognitionService;
    }

    public function index()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $laporan = Laporan::where('nik', $nik)
                          ->orderBy('created_at', 'desc')
                          ->get()
                          ->map(function($item) {
                              if ($item->tanggal instanceof UTCDateTime) {
                                  $item->tanggal_formatted = Carbon::parse($item->tanggal->toDateTime())->isoFormat('D MMM YY'); // Disesuaikan formatnya
                              } elseif ($item->tanggal instanceof \DateTimeInterface) {
                                  $item->tanggal_formatted = Carbon::parse($item->tanggal)->isoFormat('D MMM YY');
                              } else {
                                  $item->tanggal_formatted = 'Tanggal tidak valid';
                              }
                              return $item;
                          });

        return view('presensi.lapor', compact('laporan'));
    }

    public function create()
    {
        $karyawan = Auth::guard('karyawan')->user();
        if (!$karyawan) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk membuat laporan.');
        }
        return view('presensi.buatlaporan', compact('karyawan'));
    }

    public function store(Request $request)
    {
        // Ambil semua data dari request
        $data = $request->all();

        // --- PERBAIKAN: Ubah string JSON face_descriptor menjadi array PHP sebelum validasi ---
        if (isset($data['face_descriptor']) && is_string($data['face_descriptor'])) {
            $decodedDescriptor = json_decode($data['face_descriptor'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['face_descriptor'] = $decodedDescriptor;
            } else {
                // Jika decode gagal, set ke null agar validasi gagal dengan benar
                $data['face_descriptor'] = null; 
            }
        }

        // Validasi data yang sudah dimodifikasi
        $validator = Validator::make($data, [
            'tgl_laporan' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'jenis_laporan' => 'required|in:harian,kegiatan,masalah',
            'keterangan' => 'required|string|max:2000',
            'lokasi' => 'required|string|max:255',
            'foto' => 'required|string',
            'face_image' => 'required|string',
            'nik' => 'required|string|exists:karyawans,nik',
            'face_descriptor' => 'required|array' // Aturan ini sekarang akan berhasil
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, periksa kembali data yang Anda masukkan.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        if (Auth::guard('karyawan')->user()->nik !== $validatedData['nik']) {
             return response()->json([
                'success' => false,
                'message' => 'Aksi tidak diizinkan. NIK tidak sesuai.'
            ], 403);
        }
        
        $user = Auth::guard('karyawan')->user();

        try {
            $faceVerification = $this->faceRecognitionService->verifyFaceDescriptor(
                $validatedData['face_descriptor'],
                $validatedData['nik']
            );

            if (!$faceVerification['success'] || !$faceVerification['match']) {
                $distanceMsg = isset($faceVerification['distance']) ? "Jarak: " . round($faceVerification['distance'], 4) : "";
                $thresholdMsg = isset($faceVerification['threshold']) ? "(Batas: " . $faceVerification['threshold'] . ")" : "";
                
                return response()->json([
                    'success' => false,
                    'message' => "Verifikasi wajah gagal. {$distanceMsg} {$thresholdMsg}",
                    'distance' => $faceVerification['distance'] ?? 99
                ], 401);
            }

            $currentDate = now();
            $yearMonth = $currentDate->format('Y/m');
            
            $fotoPath = $this->processBase64Image(
                $validatedData['foto'],
                "laporans/{$yearMonth}/evidence",
                'evidence_' . $validatedData['nik'] . '_' . time()
            );
            
            $faceImagePath = $this->processBase64Image(
                $validatedData['face_image'],
                "laporans/{$yearMonth}/verification",
                'faceverify_' . $validatedData['nik'] . '_' . time()
            );

            $laporan = new Laporan([
                'nik' => $validatedData['nik'],
                'tanggal' => new UTCDateTime(Carbon::parse($validatedData['tgl_laporan'])->timestamp * 1000),
                'jam' => $validatedData['jam'],
                'jenis_laporan' => $validatedData['jenis_laporan'],
                'keterangan' => $validatedData['keterangan'],
                'lokasi' => $validatedData['lokasi'],
                'foto' => $fotoPath['storage_path'], 
                'face_verification_image' => $faceImagePath['storage_path'], 
                'created_at' => new UTCDateTime($currentDate->timestamp * 1000),
                'updated_at' => new UTCDateTime($currentDate->timestamp * 1000),
                'status_admin' => null, 
                'catatan_admin' => null,
            ]);
            
            $laporan->save();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil disimpan',
                'data' => $laporan,
                'redirect_url' => route('laporan.index') 
            ]);

        } catch (\Exception $e) {
            Log::error('Laporan Store Error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine(), ['trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan internal.',
            ], 500);
        }
    }

    private function processBase64Image($base64Image, $folderPath, $fileNamePrefix = 'img_')
    {
        try {
            if (!preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64Image, $typeMatch)) {
                throw new \Exception('Format gambar tidak valid. Hanya menerima JPEG/JPG/PNG');
            }

            $imageType = $typeMatch[1]; 
            $data = explode(',', $base64Image)[1];
            $decodedImage = base64_decode($data);

            if (!$decodedImage) {
                throw new \Exception('Gagal mendekode gambar base64');
            }

            $storageRelativePath = "{$folderPath}/{$fileNamePrefix}" . uniqid() . '.' . $imageType;
            
            Storage::disk('public')->put($storageRelativePath, $decodedImage);

            return [
                'storage_path' => $storageRelativePath, 
                'full_path' => storage_path("app/public/{$storageRelativePath}"), 
                'mime_type' => 'image/'.$imageType
            ];

        } catch (\Exception $e) {
            Log::error('Error proses gambar base64: '.$e->getMessage());
            throw $e; 
        }
    }
}
