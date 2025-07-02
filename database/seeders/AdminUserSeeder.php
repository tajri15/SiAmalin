<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Karyawan::updateOrCreate(
            ['nik' => 'admin'], // Kriteria untuk mencari, jika ada akan diupdate, jika tidak akan dibuat
            [
                'nama_lengkap' => 'Super Admin',
                'jabatan' => 'Administrator',
                'no_hp' => '0000000000',
                'password' => Hash::make('admin123'), // Ganti dengan password yang kuat
                'is_admin' => true,
                // tambahkan field lain yang mandatory jika ada
            ]
        );
    }
}