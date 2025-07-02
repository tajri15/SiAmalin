<?php
// File: C:\Users\dafii\OneDrive\Desktop\SiAmalin-BARU\SiAmalin\app\Http/Controllers/Admin/AdminPresensiController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Validator;

class AdminPresensiController extends Controller
{
    /**
     * Menampilkan halaman rekapitulasi presensi utama.
     */
    public function rekapitulasi(Request $request)
    {
        $bulanIni = $request->input('bulan', date('m'));
        $tahunIni = $request->input('tahun', date('Y'));
        $searchNik = $request->input('nik');
        $searchNama = $request->input('nama');

        $query = Presensi::query();

        // Filter berdasarkan bulan dan tahun
        $startDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahunIni, $bulanIni, 1)->endOfMonth();

        $query->where('tgl_presensi', '>=', new UTCDateTime($startDate->timestamp * 1000))
              ->where('tgl_presensi', '<=', new UTCDateTime($endDate->timestamp * 1000));

        // Join dengan Karyawan untuk filter NIK atau Nama
        if ($searchNik || $searchNama) {
            $query->whereHas('karyawan', function ($q) use ($searchNik, $searchNama) {
                if ($searchNik) {
                    $q->where('nik', 'like', '%' . $searchNik . '%');
                }
                if ($searchNama) {
                    $q->where('nama_lengkap', 'like', '%' . $searchNama . '%');
                }
            });
        }
        
        $presensiData = $query->with('karyawan')->orderBy('tgl_presensi', 'desc')->paginate(15);

        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('admin.presensi.rekapitulasi', compact('presensiData', 'bulanIni', 'tahunIni', 'namaBulan', 'searchNik', 'searchNama'));
    }

    /**
     * Menampilkan laporan harian presensi.
     */
    public function laporanHarian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        $presensiHarian = Presensi::where('tgl_presensi', '>=', new UTCDateTime(strtotime($tanggal . " 00:00:00") * 1000))
                                    ->where('tgl_presensi', '<=', new UTCDateTime(strtotime($tanggal . " 23:59:59") * 1000))
                                    ->with('karyawan')
                                    ->orderBy('jam_in', 'asc')
                                    ->get();

        return view('admin.presensi.harian', compact('presensiHarian', 'tanggal'));
    }

    /**
     * Menampilkan laporan bulanan presensi.
     */
    public function laporanBulanan(Request $request)
    {
        return $this->rekapitulasi($request);
    }

    /**
     * Menampilkan detail presensi untuk karyawan tertentu.
     */
    public function detailKaryawan(Request $request, $nik)
    {
        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
        
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $historiPresensi = Presensi::where('nik', $nik)
                                    ->where('tgl_presensi', '>=', new UTCDateTime($startDate->timestamp * 1000))
                                    ->where('tgl_presensi', '<=', new UTCDateTime($endDate->timestamp * 1000))
                                    ->orderBy('tgl_presensi', 'asc')
                                    ->get();
        
        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('admin.presensi.detail_karyawan', compact('karyawan', 'historiPresensi', 'bulan', 'tahun', 'namaBulan'));
    }

    /**
     * Menampilkan form untuk edit data presensi.
     */
    public function editPresensi($id)
    {
        $presensi = Presensi::with('karyawan')->findOrFail($id);
        return view('admin.presensi.edit', compact('presensi'));
    }

    /**
     * Memperbarui data presensi.
     */
    public function updatePresensi(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tgl_presensi_edit' => 'required|date',
            'jam_in_edit' => 'nullable|date_format:H:i:s',
            'jam_out_edit' => 'nullable|date_format:H:i:s|after_or_equal:jam_in_edit',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'tgl_presensi' => new UTCDateTime(strtotime($request->tgl_presensi_edit) * 1000),
                'jam_in' => $request->jam_in_edit ?: null,
                'jam_out' => $request->jam_out_edit ?: null,
            ];

            $presensi->update($updateData);

            return redirect()->route('admin.presensi.rekapitulasi')->with('success', 'Data presensi berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data presensi: ' . $e->getMessage())->withInput();
        }
    }
}