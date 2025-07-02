<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\UTCDateTime; // Pastikan ini di-import

class PatrolPoint extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'patrol_points'; // Nama koleksi untuk titik-titik patroli

    protected $fillable = [
        'patrol_id', // FK ke Patrols
        'karyawan_nik',
        'latitude',
        'longitude',
        'accuracy', // Akurasi lokasi dalam meter
        'speed', // Kecepatan dalam meter/detik (jika tersedia)
        'timestamp', // Waktu titik ini direkam
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'speed' => 'float',
        'timestamp' => 'datetime',
    ];

    /**
     * Relasi ke model Patrol.
     * Setiap titik patroli dimiliki oleh satu patroli.
     */
    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'patrol_id', '_id');
    }
}
