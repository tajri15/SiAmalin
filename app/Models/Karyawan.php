<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class Karyawan extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = "karyawans"; // Sesuai dengan nama koleksi Anda
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
        'face_embedding',
        'face_embedding_version',
        'office_location',
        'office_radius',
        'is_admin',
        'is_komandan',
        'is_ketua_departemen', // Ditambahkan
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
        'is_ketua_departemen' => 'boolean', // Ditambahkan
        'office_radius' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor for face_embedding.
     */
    public function getFaceEmbeddingAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }
        return $value;
    }

    /**
     * Mutator for face_embedding.
     */
    public function setFaceEmbeddingAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['face_embedding'] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
        } elseif (is_array($value)) {
            $this->attributes['face_embedding'] = $value;
        } else {
            $this->attributes['face_embedding'] = null;
        }
    }
    
    /**
     * Accessor for office_location.
     */
    public function getOfficeLocationAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['type']) && $decoded['type'] === 'Point' && isset($decoded['coordinates']) && is_array($decoded['coordinates'])) {
                return $decoded;
            }
            Log::warning("Accessor: office_location is a string but not valid GeoJSON Point: " . $value);
            return null; 
        }
        if (is_array($value) && isset($value['type']) && $value['type'] === 'Point' && isset($value['coordinates']) && is_array($value['coordinates'])) {
            return $value;
        }
        if (!is_null($value)) { 
             Log::warning("Accessor: office_location is not null, not a valid string JSON, and not a valid GeoJSON array structure: ", (array)$value);
        }
        return null;
    }

    /**
     * Mutator for office_location.
     */
    public function setOfficeLocationAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['type']) && $decoded['type'] === 'Point' && isset($decoded['coordinates']) && is_array($decoded['coordinates']) && count($decoded['coordinates']) === 2) {
                $coordinates = array_map('floatval', $decoded['coordinates']);
                $this->attributes['office_location'] = ['type' => 'Point', 'coordinates' => $coordinates];
            } else {
                Log::warning("Mutator: Invalid JSON string for office_location: " . $value);
                $this->attributes['office_location'] = null;
            }
        } elseif (is_array($value) && isset($value['type']) && $value['type'] === 'Point' && isset($value['coordinates']) && is_array($value['coordinates']) && count($value['coordinates']) === 2) {
            $coordinates = array_map('floatval', $value['coordinates']);
            $this->attributes['office_location'] = ['type' => 'Point', 'coordinates' => $coordinates];
        } else {
            Log::warning("Mutator: Invalid data type or structure for office_location. Expected GeoJSON Point array or valid JSON string. Value: ", (array)$value);
            $this->attributes['office_location'] = null;
        }
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'unit', 'nama');
    }
}
