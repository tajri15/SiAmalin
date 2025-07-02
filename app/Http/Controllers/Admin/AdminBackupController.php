<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Presensi;
use App\Models\Laporan;
use App\Models\Patrol;
use Illuminate\Support\Facades\Log;

class AdminBackupController extends Controller
{
    /**
     * Menampilkan halaman untuk melakukan backup data.
     */
    public function index()
    {
        // Data hanya dapat di-backup jika lebih dari 3 bulan.
        // Contoh: Jika sekarang Juni, data yg bisa di-backup adalah Februari dan sebelumnya.
        // Maka, bulan maksimum yang bisa dipilih sebagai end_month adalah bulan ke-4 sebelum bulan ini.
        $maxMonth = Carbon::now()->subMonths(3)->format('Y-m');
        return view('admin.backup.index', compact('maxMonth'));
    }

    /**
     * Memproses permintaan backup data.
     */
    public function processBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:presensi,laporan,patroli',
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'required|date_format:Y-m|after_or_equal:start_month',
        ]);

        // Validasi aturan 3 bulan
        $endMonthSelected = Carbon::createFromFormat('Y-m', $request->end_month)->startOfMonth();
        $restrictionDate = Carbon::now()->subMonths(3)->startOfMonth();

        if ($endMonthSelected->gte($restrictionDate)) {
            return redirect()->back()
                ->with('error', 'Gagal! Anda hanya dapat mem-backup data yang usianya lebih dari 3 bulan.');
        }


        $type = $request->type;
        $startDate = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $request->end_month)->endOfMonth();
        
        $model = null;
        $dateColumn = '';
        $originalCollection = '';
        $backupCollection = '';

        switch ($type) {
            case 'presensi':
                $model = new Presensi();
                $dateColumn = 'tgl_presensi';
                $originalCollection = 'presensi';
                $backupCollection = 'presensis_backups';
                break;
            case 'laporan':
                $model = new Laporan();
                $dateColumn = 'tanggal';
                $originalCollection = 'laporan';
                $backupCollection = 'laporans_backups';
                break;
            case 'patroli':
                $model = new Patrol();
                $dateColumn = 'start_time';
                $originalCollection = 'patrols';
                $backupCollection = 'patrols_backups';
                break;
        }

        if (!$model) {
            return redirect()->back()->with('error', 'Jenis data tidak valid.');
        }

        try {
            $documentsToMove = $model->whereBetween($dateColumn, [$startDate, $endDate])->get();
            $count = $documentsToMove->count();

            if ($count === 0) {
                return redirect()->back()->with('info', "Tidak ada data {$type} yang ditemukan pada periode yang dipilih untuk di-backup.");
            }

            // Konversi ke array untuk proses insert
            $documentsArray = $documentsToMove->toArray();
            
            // 1. Insert ke koleksi backup
            // PERBAIKAN: Menggunakan ->table() sebagai alias untuk ->collection()
            DB::connection('mongodb')->table($backupCollection)->insert($documentsArray);

            // 2. Hapus dari koleksi asli setelah berhasil di-insert
            $idsToDelete = $documentsToMove->pluck('_id');
            $model->whereIn('_id', $idsToDelete)->delete();

            Log::info("Backup success: Moved {$count} documents from '{$originalCollection}' to '{$backupCollection}' for period {$startDate->toDateString()} to {$endDate->toDateString()}.");

            return redirect()->route('admin.backup.index')
                             ->with('success', "Berhasil! Sebanyak {$count} data {$type} telah di-backup dan dihapus dari koleksi utama.");

        } catch (\Exception $e) {
            Log::error("Backup failed for '{$type}': " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat proses backup. Silakan coba lagi. Detail: ' . $e->getMessage());
        }
    }
}
