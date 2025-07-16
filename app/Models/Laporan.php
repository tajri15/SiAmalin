<?php
//app\Models\Laporan.php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;

class Laporan extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'laporan';
    protected $primaryKey = '_id';

    protected $fillable = [
        'nik',
        'tanggal',
        'jam',
        'jenis_laporan',
        'keterangan',
        'lokasi',
        'foto',
        'face_verification_image'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'created_at' => 'datetime'
    ];

    // Accessor untuk URL foto
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/'.str_replace('public/', '', $this->foto)) : null;
    }

    // Accessor untuk URL face verification
    public function getFaceVerificationUrlAttribute()
    {
        return $this->face_verification_image ? asset('storage/'.str_replace('public/', '', $this->face_verification_image)) : null;
    }

    // Relationship dengan karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}