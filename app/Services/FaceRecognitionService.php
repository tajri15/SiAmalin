<?php

namespace App\Services;

use App\Models\Karyawan;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class FaceRecognitionService
{
    protected $pythonPath;
    protected $scriptPath;

    public function __construct()
    {
        $this->pythonPath = base_path('venv/Scripts/python.exe');
        $this->scriptPath = base_path('face_recognition_service.py');
    }

    private function runPythonScript(array $commandArgs): array
    {
        $processCommand = array_merge([$this->pythonPath, $this->scriptPath], $commandArgs);
        Log::info('--- MEMULAI PROSES PYTHON (METODE FILE) ---');
        Log::info('Executing command: ' . implode(' ', $processCommand));

        $process = new Process($processCommand);
        $process->setTimeout(120);
        $process->run();
        
        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            Log::error('ERROR dari Skrip Python: ' . $errorOutput);
            throw new Exception('Proses Python gagal. Cek log. Error: ' . $errorOutput);
        }

        $rawOutput = $process->getOutput();
        Log::info('Output Mentah dari Python: ' . $rawOutput);

        // ======================= PERBAIKAN UTAMA DI SINI =======================
        // Mencari string JSON di dalam output mentah menggunakan regular expression.
        // Ini akan menemukan blok yang diawali dengan { dan diakhiri dengan }
        preg_match('/\{.*\}/s', $rawOutput, $matches);

        if (empty($matches)) {
            Log::error('Tidak ditemukan string JSON yang valid pada output Python.');
            throw new Exception('Tidak ada output JSON dari skrip Python.');
        }

        // Hanya mengambil bagian JSON yang cocok
        $jsonOutput = $matches[0];
        $result = json_decode($jsonOutput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gagal memecahkan JSON dari Python setelah filtering.', ['filtered_json' => $jsonOutput]);
            throw new Exception('Gagal memproses hasil JSON dari skrip pengenalan wajah.');
        }
        // =======================================================================

        return $result;
    }
    
    // Fungsi generateDescriptorFromImage tidak perlu diubah
    public function generateDescriptorFromImage(string $base64Image): ?array
    {
        $tempImageFile = 'temp/face_generate_' . uniqid() . '.txt';
        Storage::put($tempImageFile, $base64Image);
        $imagePath = Storage::path($tempImageFile);

        try {
            $result = $this->runPythonScript(['generate', $imagePath]);
            if ($result['success'] && isset($result['descriptor'])) {
                return $result['descriptor'];
            }
            Log::warning('Generate Descriptor Gagal (dari Python):', ['message' => $result['message'] ?? 'Unknown error.']);
        } catch (Exception $e) {
            Log::error('FaceRecognitionService Exception:', ['error' => $e->getMessage()]);
        } finally {
            Storage::delete($tempImageFile);
        }
        return null;
    }

    // Fungsi verifyImageAgainstStored tidak perlu diubah
    public function verifyImageAgainstStored(string $base64Image, string $nik): array
    {
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan || empty($karyawan->foto)) {
            return ['success' => false, 'match' => false, 'message' => 'Foto profil karyawan tidak ditemukan untuk perbandingan.'];
        }

        $tempLiveImageFile = 'temp/face_verify_' . uniqid() . '.txt';
        Storage::put($tempLiveImageFile, $base64Image);
        
        $liveImagePath = Storage::path($tempLiveImageFile);
        $registeredPhotoPath = Storage::disk('public')->path($karyawan->foto);
        
        try {
            $result = $this->runPythonScript(['verify', $liveImagePath, $registeredPhotoPath]);
            if (!$result['success']) {
                 throw new Exception($result['message'] ?? 'Verifikasi gagal di skrip Python.');
            }
            $finalResult = ['success' => true, 'match' => $result['match'], 'message' => $result['match'] ? 'Wajah terverifikasi.' : 'Wajah tidak cocok.'];
        } catch (Exception $e) {
            Log::error("Face verification failed for NIK {$nik}: " . $e->getMessage());
            $finalResult = ['success' => false, 'match' => false, 'message' => 'Kesalahan internal saat verifikasi.'];
        } finally {
            Storage::delete($tempLiveImageFile);
        }
        
        return $finalResult;
    }
}