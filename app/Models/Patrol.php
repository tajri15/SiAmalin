<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\UTCDateTime; // Pastikan ini di-import

class Patrol extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'patrols'; // Nama koleksi untuk data patroli

    protected $fillable = [
        'karyawan_nik',
        'start_time',
        'end_time',
        'duration_seconds',
        'total_distance_meters',
        'status', // aktif, jeda, selesai
        'path', // Array GeoJSON LineString coordinates
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_seconds' => 'integer',
        'total_distance_meters' => 'float',
        'path' => 'array', // Untuk menyimpan array koordinat
    ];

    /**
     * Relasi ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nik', 'nik');
    }

    /**
     * Relasi ke model PatrolPoint.
     * Satu patroli memiliki banyak titik patroli.
     */
    public function points()
    {
        return $this->hasMany(PatrolPoint::class, 'patrol_id', '_id');
    }

    // Jika Anda ingin menyimpan path sebagai GeoJSON LineString secara langsung
    // Anda mungkin memerlukan custom cast atau accessor/mutator
    // Untuk saat ini, kita simpan sebagai array of arrays [lng, lat]
}
