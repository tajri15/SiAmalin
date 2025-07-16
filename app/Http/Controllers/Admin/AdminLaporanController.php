<?php
//app\Http\Controllers\Admin\AdminLaporanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Karyawan;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            $query->where('nik', 'like', '%' . $request->nik_karyawan . '%');
        }
        
        // Filter berdasarkan Nama Karyawan secara case-insensitive
        if ($request->filled('nama_karyawan')) {
            // Pertama, cari NIK dari karyawan yang namanya cocok
            $niksFromName = Karyawan::where('nama_lengkap', 'regexp', "/.*{$request->nama_karyawan}.*/i")
                                      ->pluck('nik')
                                      ->toArray();

            // Jika tidak ada karyawan yang ditemukan, pastikan query tidak mengembalikan hasil
            if (empty($niksFromName)) {
                $query->whereRaw(fn($q) => $q->where('_id', '=', '0'));
            } else {
                // Filter laporan berdasarkan NIK yang ditemukan
                $query->whereIn('nik', $niksFromName);
            }
        }

        // Filter berdasarkan Jenis Laporan
        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        // Filter berdasarkan Status Admin
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
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_admin' => 'required|string|in:Diterima,Ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $laporan = Laporan::findOrFail($id);
        $laporan->status_admin = $request->status_admin;
        $laporan->catatan_admin = $request->catatan_admin;
        $laporan->admin_peninjau_id = auth()->guard('karyawan')->id();
        $laporan->tanggal_peninjauan_admin = new UTCDateTime(now()->timestamp * 1000);
        $laporan->save();

        return redirect()->route('admin.laporan.show', $id)->with('success', 'Status laporan berhasil diperbarui.');
    }

    /**
     * NEW: Menghapus laporan dan file-file terkait.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $laporan = Laporan::findOrFail($id);
            
            // Hapus file foto utama jika ada
            if ($laporan->foto) {
                $this->deleteFileIfExists($laporan->foto);
            }
            
            // Hapus file foto verifikasi wajah jika ada
            if ($laporan->face_verification_image) {
                $this->deleteFileIfExists($laporan->face_verification_image);
            }
            
            // Hapus data laporan dari database
            $laporan->delete();
            
            Log::info('Laporan berhasil dihapus', [
                'laporan_id' => $id,
                'nik' => $laporan->nik,
                'admin_id' => auth()->guard('karyawan')->id()
            ]);
            
            return redirect()->route('admin.laporan.index')
                           ->with('success', 'Laporan berhasil dihapus beserta file-file terkait.');
                           
        } catch (\Exception $e) {
            Log::error('Error menghapus laporan: ' . $e->getMessage(), [
                'laporan_id' => $id,
                'admin_id' => auth()->guard('karyawan')->id()
            ]);
            
            return redirect()->route('admin.laporan.index')
                           ->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk menghapus file dari storage
     *
     * @param  string  $filePath
     * @return void
     */
    private function deleteFileIfExists($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            Log::info('File berhasil dihapus: ' . $filePath);
        }
    }
}