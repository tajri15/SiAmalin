<?php
// File: app/Http/Controllers/Admin/AdminPresensiController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

        if ($searchNama) {
            $niksFromName = Karyawan::where('nama_lengkap', 'regexp', "/.*$searchNama.*/i")
                                    ->pluck('nik')
                                    ->toArray();
            
            if (empty($niksFromName)) {
                $query->where('_id', 'no-results-found');
            } else {
                $query->whereIn('nik', $niksFromName);
            }
        }

        if ($searchNik) {
            $query->where('nik', 'regexp', "/.*$searchNik.*/i");
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

        // --- PERBAIKAN ---
        // Menghapus aturan 'after_or_equal:jam_in_edit' untuk memperbolehkan input shift malam.
        $validator = Validator::make($request->all(), [
            'tgl_presensi_edit' => 'required|date',
            'jam_in_edit' => 'nullable|date_format:H:i:s',
            'jam_out_edit' => 'nullable|date_format:H:i:s',
        ]);
        // --- AKHIR PERBAIKAN ---

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $original_jam_in = $presensi->jam_in;
            $original_jam_out = $presensi->jam_out;

            $updateData = [
                'tgl_presensi' => new UTCDateTime(Carbon::parse($request->tgl_presensi_edit)->startOfDay()->timestamp * 1000),
                'jam_in' => $request->jam_in_edit ?: null,
                'jam_out' => $request->jam_out_edit ?: null,
            ];

            if (is_null($original_jam_in) && !empty($request->jam_in_edit)) {
                $updateData['foto_in'] = 'admin';
                $updateData['lokasi_in'] = 'admin';
            }

            if (is_null($original_jam_out) && !empty($request->jam_out_edit)) {
                $updateData['foto_out'] = 'admin';
                $updateData['lokasi_out'] = 'admin';
            }

            $presensi->update($updateData);

            return redirect()->route('admin.presensi.rekapitulasi')->with('success', 'Data presensi berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui data presensi: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memperbarui data presensi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mereset data presensi masuk.
     */
    public function resetMasuk($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->update([
                'jam_in' => null,
                'foto_in' => null,
                'lokasi_in' => null,
            ]);
            return redirect()->back()->with('success', 'Data presensi masuk berhasil direset.');
        } catch (\Exception $e) {
            Log::error('Gagal reset presensi masuk: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mereset data presensi masuk.');
        }
    }

    /**
     * Mereset data presensi pulang.
     */
    public function resetPulang($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->update([
                'jam_out' => null,
                'foto_out' => null,
                'lokasi_out' => null,
            ]);
            return redirect()->back()->with('success', 'Data presensi pulang berhasil direset.');
        } catch (\Exception $e) {
            Log::error('Gagal reset presensi pulang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mereset data presensi pulang.');
        }
    }

    /**
     * Menghapus data presensi.
     */
    public function destroy($id)
    {
        try {
            $presensi = Presensi::findOrFail($id);
            $presensi->delete();
            return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus presensi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data presensi.');
        }
    }
}
