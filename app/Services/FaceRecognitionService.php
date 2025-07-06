<?php

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
    protected $embeddingVersion;

    public function __construct()
    {
        $this->threshold = config('face_recognition.threshold', 0.6);
        $this->embeddingVersion = config('face_recognition.embedding_version', 'v2');
    }

    /**
     * Method BARU untuk pendaftaran: Membuat embedding dari gambar.
     * Method ini dipanggil saat Admin mendaftarkan wajah karyawan baru.
     *
     * @param string $imageData Data gambar mentah (hasil base64_decode).
     * @return array|null Array embedding atau null jika gagal.
     */
    public function generateFaceEmbedding($imageData)
    {
        // PENTING: Ini adalah implementasi tiruan (dummy).
        // Di lingkungan produksi, Anda akan memanggil model Machine Learning di sini.
        // Untuk tujuan pengembangan, kita buat data embedding acak.
        try {
            $embedding = [];
            for ($i = 0; $i < 128; $i++) {
                $embedding[] = mt_rand() / mt_getrandmax(); // Nilai float acak antara 0 dan 1
            }

            // Normalisasi vektor (praktik umum untuk perbandingan kosinus/euclidean)
            $norm = sqrt(array_sum(array_map(fn($x) => $x * $x, $embedding)));
            if ($norm == 0) {
                // Hindari pembagian dengan nol jika vektornya nol (sangat tidak mungkin)
                $normalizedEmbedding = $embedding;
            } else {
                $normalizedEmbedding = array_map(fn($x) => $x / $norm, $embedding);
            }

            return [
                'embedding' => $normalizedEmbedding,
                'version' => $this->embeddingVersion,
                'created_at' => now()->toDateTimeString()
            ];
        } catch (\Exception $e) {
            Log::error('Error generating face embedding: ' . $e->getMessage());
            throw $e; // Lemparkan kembali error agar bisa ditangkap oleh controller
        }
    }

    /**
     * Membandingkan descriptor wajah yang baru dengan yang tersimpan di database.
     * Method ini dipanggil saat karyawan melakukan presensi.
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
                return $this->buildResult(false, 'Wajah belum terdaftar untuk karyawan ini.', 99); // Jarak 99 untuk menandakan tidak ada data
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
            return $this->buildResult(false, $e->getMessage(), 99);
        }
    }

    /**
     * Menghitung Jarak Euclidean antara dua vektor (descriptor).
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
