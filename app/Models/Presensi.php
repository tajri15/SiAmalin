<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Carbon;

class Presensi extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'presensi';

    protected $fillable = [
        'nik',
        'tgl_presensi',
        'jam_in',
        'jam_out',
        'foto_in',
        'foto_out',
        'lokasi_in',
        'lokasi_out'
    ];

    public $timestamps = false; 

    /**
     * The attributes that should be cast.
     * Ini akan secara otomatis menangani konversi antara objek Carbon di Laravel 
     * dan objek UTCDateTime di MongoDB dengan benar, termasuk zona waktu.
     *
     * @var array
     */
    protected $casts = [
        'tgl_presensi' => 'datetime',
    ];

    /**
     * Relasi ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
