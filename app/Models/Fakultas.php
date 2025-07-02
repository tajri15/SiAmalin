<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Fakultas extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'fakultas'; 

    protected $fillable = [
        'nama',                 
        'tipe_fakultas', // 'Teknik' atau 'Non-Teknik'      
        'program_studi_json',   // Untuk Non-Teknik: [{"jenjang": "S1", "nama_prodi": "Nama Prodi"}]
        'koordinat_fakultas',   // Untuk Non-Teknik
        'radius_fakultas',      // Untuk Non-Teknik
        'detail_prodi_json',    // Untuk Teknik: [{"jenjang": "S1", "nama_prodi": "Nama Prodi", "koordinat": "...", "radius": "..."}]
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor untuk program_studi fakultas Non-Teknik.
     * Mengembalikan array program studi.
     */
    public function getProgramStudiAttribute()
    {
        if (isset($this->attributes['program_studi_json']) && !is_null($this->attributes['program_studi_json'])) {
            $decoded = json_decode($this->attributes['program_studi_json'], true);
            if (is_null($decoded) && $this->attributes['program_studi_json'] !== 'null' && json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error for program_studi_json on Fakultas ID ' . ($this->attributes['_id'] ?? 'N/A') . ': ' . json_last_error_msg() . ' | Data: ' . $this->attributes['program_studi_json']);
                return [];
            }
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Accessor untuk detail_prodi Fakultas Teknik.
     * Mengembalikan array detail program studi.
     */
    public function getDetailProdiAttribute()
    {
        if (isset($this->attributes['detail_prodi_json']) && !is_null($this->attributes['detail_prodi_json'])) {
            $decoded = json_decode($this->attributes['detail_prodi_json'], true);
            if (is_null($decoded) && $this->attributes['detail_prodi_json'] !== 'null' && json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error for detail_prodi_json on Fakultas ID ' . ($this->attributes['_id'] ?? 'N/A') . ': ' . json_last_error_msg() . ' | Data: ' . $this->attributes['detail_prodi_json']);
                return [];
            }
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
