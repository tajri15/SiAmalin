<?php
//app/Models/Patrol.php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;

class Patrol extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'patrols';

    protected $fillable = [
        'karyawan_nik',
        'start_time',
        'end_time',
        'duration_seconds',
        'total_distance_meters',
        'status', // aktif, jeda, selesai
        'path', // Array GeoJSON LineString coordinates
        'face_verified', // NEW: Boolean for face verification status
        'face_verification_image', // NEW: Path to face verification image
        'face_verification_time', // NEW: Timestamp of face verification
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'face_verification_time' => 'datetime',
        'duration_seconds' => 'integer',
        'total_distance_meters' => 'float',
        'path' => 'array',
        'face_verified' => 'boolean',
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
}