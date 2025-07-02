<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Karyawan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class AdminLaporanController extends Controller
{
    /**
     * Menampilkan daftar semua laporan.
     */
    public function index(Request $request)
    {
        $query = Laporan::query()->with('karyawan');

        // Filter berdasarkan rentang tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [
                new UTCDateTime($tanggalMulai->timestamp * 1000),
                new UTCDateTime($tanggalAkhir->timestamp * 1000)
            ]);
        } elseif ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->where('tanggal', '>=', new UTCDateTime($tanggalMulai->timestamp * 1000));
        } elseif ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->where('tanggal', '<=', new UTCDateTime($tanggalAkhir->timestamp * 1000));
        }

        // Filter berdasarkan NIK Karyawan
        if ($request->filled('nik_karyawan')) {
            $query->where('nik', $request->nik_karyawan);
        }
        
        // Filter berdasarkan Nama Karyawan
        if ($request->filled('nama_karyawan')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
            });
        }

        // Filter berdasarkan Jenis Laporan
        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        // Filter berdasarkan Status Admin (jika ada field status_admin)
        if ($request->filled('status_admin')) {
            if ($request->status_admin == 'belum_ditinjau') {
                $query->whereNull('status_admin');
            } else {
                $query->where('status_admin', $request->status_admin);
            }
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(15);
        $karyawans = Karyawan::orderBy('nama_lengkap')->get(['nik', 'nama_lengkap']); // Untuk dropdown filter

        return view('admin.laporan.index', compact('laporans', 'karyawans'));
    }

    /**
     * Menampilkan detail laporan tertentu.
     */
    public function show($id)
    {
        $laporan = Laporan::with('karyawan')->findOrFail($id);
        return view('admin.laporan.show', compact('laporan'));
    }

    /**
     * Memperbarui status laporan oleh admin.
     * Anda perlu menambahkan field 'status_admin' dan 'catatan_admin' (opsional) pada model Laporan.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_admin' => 'required|string|in:Diterima,Ditolak', // Sesuaikan status
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $laporan = Laporan::findOrFail($id);
        $laporan->status_admin = $request->status_admin;
        $laporan->catatan_admin = $request->catatan_admin; // Pastikan field ini ada di model Laporan
        $laporan->admin_peninjau_id = auth()->guard('karyawan')->id(); // Simpan ID admin yang meninjau
        $laporan->tanggal_peninjauan_admin = new UTCDateTime(now()->timestamp * 1000);
        $laporan->save();

        // Kirim notifikasi ke karyawan jika perlu

        return redirect()->route('admin.laporan.show', $id)->with('success', 'Status laporan berhasil diperbarui.');
    }
}
