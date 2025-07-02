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
        $validator = Validator::make($request->all(), [
            'tgl_laporan' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'jenis_laporan' => 'required|in:harian,kegiatan,masalah',
            'keterangan' => 'required|string|max:2000',
            'lokasi' => 'required|string|max:255',
            'foto' => 'required|string', 
            'face_image' => 'required|string',
            'nik' => 'required|string|exists:karyawans,nik'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan NIK yang dikirim sama dengan NIK user yang login
        if (Auth::guard('karyawan')->user()->nik !== $request->nik) {
             return response()->json([
                'success' => false,
                'message' => 'Aksi tidak diizinkan. NIK tidak sesuai.'
            ], 403);
        }
        
        // Ambil user yang sedang login (sudah diautentikasi)
        $user = Auth::guard('karyawan')->user();


        try {
            $faceVerification = $this->faceRecognitionService->verifyFace(
                $request->face_image,
                $request->nik
            );

            if (!$faceVerification['success'] || !$faceVerification['match']) {
                return response()->json([
                    'success' => false,
                    'message' => "Verifikasi wajah gagal. Kemiripan: " .
                                 round(($faceVerification['similarity'] ?? 0) * 100, 2) . "% (Minimum " .
                                 round(($faceVerification['threshold'] ?? config('face_recognition.threshold')) * 100, 2) . "%)",
                    'similarity' => $faceVerification['similarity'] ?? 0
                ], 401);
            }

            $currentDate = now();
            $yearMonth = $currentDate->format('Y/m');
            
            $fotoPath = $this->processBase64Image(
                $request->foto,
                "laporans/{$yearMonth}/evidence",
                'evidence_' . $request->nik . '_' . time()
            );
            
            $faceImagePath = $this->processBase64Image(
                $request->face_image, // Ini adalah face_image dari verifikasi, bukan foto bukti
                "laporans/{$yearMonth}/verification",
                'faceverify_' . $request->nik . '_' . time()
            );

            $laporan = new Laporan([
                'nik' => $request->nik,
                'tanggal' => new UTCDateTime(Carbon::parse($request->tgl_laporan)->timestamp * 1000),
                'jam' => $request->jam,
                'jenis_laporan' => $request->jenis_laporan,
                'keterangan' => $request->keterangan,
                'lokasi' => $request->lokasi,
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
