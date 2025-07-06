<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class Karyawan extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = "karyawans";
    protected $primaryKey = "_id";
    public $incrementing = false;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'email',
        'password',
        'foto',
        'face_embedding', // HANYA INI, face_embedding_version DIHAPUS
        'office_location',
        'office_radius',
        'is_admin',
        'is_komandan',
        'is_ketua_departemen',
        'unit',
        'departemen',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_komandan' => 'boolean',
        'is_ketua_departemen' => 'boolean',
        'office_radius' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFaceEmbeddingAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }
        return $value;
    }

    public function setFaceEmbeddingAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['face_embedding'] = $value;
        } else {
            $this->attributes['face_embedding'] = null;
        }
    }
    
    public function getOfficeLocationAttribute($value)
    {
        if (is_array($value) && isset($value['type']) && $value['type'] === 'Point' && isset($value['coordinates'])) {
            return $value;
        }
        return null;
    }
    
    /**
     * PERBAIKAN: Mutator dibuat lebih robust untuk menangani data dari controller.
     * Ia akan menerima string "lat,lng" dan mengubahnya menjadi format GeoJSON.
     */
    public function setOfficeLocationAttribute($value)
    {
        if (is_string($value) && strpos($value, ',') !== false) {
            $coords = explode(',', $value);
            if(count($coords) === 2) {
                $lat = floatval(trim($coords[0]));
                $lng = floatval(trim($coords[1]));
                // Format GeoJSON adalah [longitude, latitude]
                $this->attributes['office_location'] = [
                    'type' => 'Point',
                    'coordinates' => [$lng, $lat] 
                ];
            } else {
                $this->attributes['office_location'] = null;
            }
        } elseif (is_array($value) && isset($value['type']) && $value['type'] === 'Point') {
             // Jika data sudah dalam format GeoJSON, langsung gunakan
            $this->attributes['office_location'] = $value;
        } else {
            $this->attributes['office_location'] = null;
        }
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'unit', 'nama');
    }
}