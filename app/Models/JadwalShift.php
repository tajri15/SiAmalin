<?php
// File: app/Models/JadwalShift.php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class JadwalShift extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'jadwal_shifts';

    protected $fillable = [
        'karyawan_nik',
        'nama_karyawan',
        'fakultas',
        'tanggal',      // Akan disimpan sebagai UTCDateTime
        'shift_nama',
        'jam_mulai',    // HH:MM
        'jam_selesai',  // HH:MM
        'dibuat_oleh_nik',
        'dibuat_oleh_nama',
        'keterangan',
    ];

    protected $casts = [
        // 'tanggal' => 'datetime:Y-m-d', // Dihapus, ditangani oleh accessor/mutator
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke model Karyawan (Petugas).
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nik', 'nik');
    }

    /**
     * Mutator untuk field 'tanggal'.
     * Memastikan tanggal disimpan sebagai UTCDateTime di MongoDB, distandarisasi ke awal hari (00:00:00 UTC).
     */
    public function setTanggalAttribute($value)
    {
        if ($value instanceof Carbon) {
            // Konversi Carbon ke UTC, lalu ambil timestamp untuk UTCDateTime
            $this->attributes['tanggal'] = new UTCDateTime($value->copy()->startOfDay()->utc()->getTimestamp() * 1000);
        } elseif (is_string($value)) {
            try {
                // Parse string sebagai tanggal, standarisasi ke awal hari, konversi ke UTC, lalu ambil timestamp
                $date = Carbon::parse($value)->startOfDay()->utc();
                $this->attributes['tanggal'] = new UTCDateTime($date->getTimestamp() * 1000);
            } catch (\Exception $e) {
                $this->attributes['tanggal'] = null;
                Log::error("Invalid date string for JadwalShift setTanggalAttribute: {$value} - Error: {$e->getMessage()}");
            }
        } elseif ($value instanceof UTCDateTime) {
            // Jika sudah UTCDateTime, pastikan itu merepresentasikan awal hari UTC
            $carbonDate = Carbon::instance($value->toDateTime())->startOfDay()->utc();
            $this->attributes['tanggal'] = new UTCDateTime($carbonDate->getTimestamp() * 1000);
        } else {
            $this->attributes['tanggal'] = null;
        }
    }

    /**
     * Accessor untuk field 'tanggal'.
     * Memastikan tanggal diambil sebagai instance Carbon dari UTCDateTime,
     * dan dikonversi ke timezone aplikasi.
     */
    public function getTanggalAttribute($value)
    {
        if ($value instanceof UTCDateTime) {
            // UTCDateTime dari DB adalah UTC. Konversi ke Carbon, lalu set ke timezone aplikasi.
            return Carbon::instance($value->toDateTime())->setTimezone(config('app.timezone'));
        }
        if ($value instanceof Carbon) {
            return $value->setTimezone(config('app.timezone'));
        }
        if (is_string($value)) {
            try {
                return Carbon::parse($value)->setTimezone(config('app.timezone'));
            } catch (\Exception $e) {
                Log::error("Accessor getTanggalAttribute (JadwalShift): Failed to parse string '{$value}' - Error: {$e->getMessage()}");
                return null;
            }
        }
        return null;
    }
}
