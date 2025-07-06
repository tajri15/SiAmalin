<?php
// File: app/Services/FaceRecognitionService.php

namespace App\Services;

use App\Models\Karyawan;
use Exception;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService
{
    /**
     * Ambang batas (threshold) untuk Jarak Euclidean.
     * Nilai umum untuk face-api.js adalah 0.6. Semakin kecil, semakin ketat.
     * @var float
     */
    protected $threshold;

    public function __construct()
    {
        // Anda bisa mengatur nilai default ini di config/face_recognition.php
        $this->threshold = config('face_recognition.threshold', 0.6);
    }

    /**
     * Membandingkan descriptor wajah yang baru dengan yang tersimpan di database.
     *
     * @param array $newDescriptor Deskriptor dari frontend (berisi 128 float).
     * @param string $nik NIK karyawan untuk dicocokkan.
     * @return array Hasil verifikasi.
     */
    public function verifyFaceDescriptor(array $newDescriptor, string $nik): array
    {
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();

            if (!$karyawan) {
                throw new Exception("Karyawan dengan NIK {$nik} tidak ditemukan.");
            }

            if (empty($karyawan->face_embedding) || !isset($karyawan->face_embedding['embedding'])) {
                return $this->buildResult(false, 'Wajah belum terdaftar untuk karyawan ini.', 0);
            }

            $storedDescriptor = $karyawan->face_embedding['embedding'];

            if (count($newDescriptor) !== 128 || count($storedDescriptor) !== 128) {
                throw new Exception('Format deskriptor wajah tidak valid.');
            }

            // Hitung Euclidean Distance, metode standar untuk face-api.js
            $distance = $this->calculateEuclideanDistance($newDescriptor, $storedDescriptor);

            // Jika jarak LEBIH KECIL dari threshold, maka wajah cocok.
            $isMatch = $distance < $this->threshold;
            $message = $isMatch ? 'Wajah terverifikasi' : 'Wajah tidak cocok';

            return $this->buildResult($isMatch, $message, $distance);

        } catch (Exception $e) {
            Log::error("Face descriptor verification failed for NIK {$nik}: " . $e->getMessage());
            return $this->buildResult(false, $e->getMessage(), 0);
        }
    }

    /**
     * Menghitung Jarak Euclidean antara dua vektor (descriptor).
     *
     * @param array $a Vektor pertama.
     * @param array $b Vektor kedua.
     * @return float Jarak Euclidean.
     */
    private function calculateEuclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        $count = count($a); // Seharusnya 128
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Membangun array hasil yang konsisten.
     *
     * @param bool $match Apakah wajah cocok.
     * @param string $message Pesan hasil.
     * @param float $distance Jarak yang dihitung.
     * @return array
     */
    private function buildResult(bool $match, string $message, float $distance): array
    {
        return [
            'success' => true, // Menandakan API call berhasil, bukan hasil verifikasi
            'match' => $match,
            'message' => $message,
            'distance' => $distance,
            'threshold' => $this->threshold,
        ];
    }
}