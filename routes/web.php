<?php
// File: routes/web.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PatroliController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminKaryawanController;
use App\Http\Controllers\Admin\AdminPresensiController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminFakultasController;
use App\Http\Controllers\Admin\AdminPatroliController;
use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Komandan\KomandanDashboardController;
use App\Http\Controllers\Komandan\KomandanJadwalShiftController;
use App\Http\Controllers\Komandan\KomandanLaporanKinerjaController;
use App\Http\Controllers\KetuaDepartemen\KetuaDepartemenDashboardController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        if (Auth::guard('karyawan')->check()) {
            $user = Auth::guard('karyawan')->user();
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_komandan) {
                return redirect()->route('komandan.dashboard');
            } elseif ($user->is_ketua_departemen) {
                return redirect()->route('ketua-departemen.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return app(AuthController::class)->showLoginForm();
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login.form');
    Route::post('/admin/login', [AuthController::class, 'prosesAdminLogin'])->name('admin.login.proses');
    Route::post('/face/verify', [AuthController::class, 'verifyFace'])->name('face.verify');
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');
    
    Route::get('/profile', function () {
        $karyawan = Auth::guard('karyawan')->user();
        return view('presensi.profile', compact('karyawan'));
    })->name('profile');
    Route::get('/editprofile', function () {
        $karyawan = Auth::guard('karyawan')->user();
        return view('presensi.editprofile', compact('karyawan'));
    })->name('editprofile');
    Route::post('/presensi/updateprofile', [PresensiController::class, 'updateprofile'])->name('updateprofile');

    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/create', [PresensiController::class, 'create'])->name('create');
        Route::post('/store', [PresensiController::class, 'store'])->name('store');
        Route::get('/histori', [PresensiController::class, 'histori'])->name('histori');
        Route::post('/gethistori', [PresensiController::class, 'gethistori'])->name('gethistori');
    });

    Route::prefix('laporan')->name('laporan.')->group(function(){
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/buat', [LaporanController::class, 'create'])->name('create');
        Route::post('/store', [LaporanController::class, 'store'])->name('store');
    });

    Route::prefix('patroli')->name('patroli.')->group(function () {
        Route::get('/', [PatroliController::class, 'index'])->name('index');
        Route::post('/start', [PatroliController::class, 'startPatrol'])->name('start');
        Route::post('/store-point', [PatroliController::class, 'storePoint'])->name('store_point');
        Route::post('/pause', [PatroliController::class, 'pausePatrol'])->name('pause');
        Route::post('/resume', [PatroliController::class, 'resumePatrol'])->name('resume');
        Route::post('/stop', [PatroliController::class, 'stopPatrol'])->name('stop');
        Route::get('/histori', [PatroliController::class, 'historiPatroli'])->name('histori');
        Route::get('/histori/{patrolId}', [PatroliController::class, 'detailHistoriPatroli'])->name('histori.detail');
        
        // Route untuk cek radius real-time
        Route::post('/check-radius', [PatroliController::class, 'checkRadius'])->name('check_radius');
        
        // NEW: Route untuk verifikasi wajah patroli
        Route::post('/verify-face', [PatroliController::class, 'verifyFace'])->name('verify_face');
    });

    Route::post('/set-face-verified-session', [AuthController::class, 'setFaceVerifiedSession'])->name('session.set_face_verified');
    Route::get('/check-face-verified-session', [AuthController::class, 'checkFaceVerifiedSession'])->name('session.check_face_verified');

    // PANEL ADMIN
    Route::prefix('panel')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('karyawan')->name('karyawan.')->group(function () {
            Route::get('/', [AdminKaryawanController::class, 'index'])->name('index');
            Route::get('/create', [AdminKaryawanController::class, 'create'])->name('create');
            Route::post('/step1', [AdminKaryawanController::class, 'storeStep1'])->name('store_step1');
            Route::get('/face-registration', [AdminKaryawanController::class, 'showFaceRegistration'])->name('face_registration');
            Route::post('/complete-registration', [AdminKaryawanController::class, 'completeRegistration'])->name('complete_registration');
            Route::get('/{id}', [AdminKaryawanController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AdminKaryawanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminKaryawanController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminKaryawanController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/face-data', [AdminKaryawanController::class, 'viewFaceData'])->name('face_data');
            Route::post('/{id}/reset-face', [AdminKaryawanController::class, 'resetFaceData'])->name('reset_face');
            Route::get('/{id}/reset-location', [AdminKaryawanController::class, 'resetOfficeLocation'])->name('reset_location');
        });

        Route::prefix('fakultas')->name('fakultas.')->group(function () {
            Route::get('/', [AdminFakultasController::class, 'index'])->name('index');
            Route::get('/create', [AdminFakultasController::class, 'create'])->name('create');
            Route::post('/', [AdminFakultasController::class, 'store'])->name('store');
            Route::get('/{fakultas}/edit', [AdminFakultasController::class, 'edit'])->name('edit');
            Route::put('/{fakultas}', [AdminFakultasController::class, 'update'])->name('update');
            Route::delete('/{fakultas}', [AdminFakultasController::class, 'destroy'])->name('destroy');
            Route::get('/get-details-for-karyawan/{nama_fakultas}', [AdminFakultasController::class, 'getFakultasDetails'])->name('get_details_for_karyawan');
        });
        
        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [AdminPresensiController::class, 'rekapitulasi'])->name('rekapitulasi');
            Route::get('/harian', [AdminPresensiController::class, 'laporanHarian'])->name('harian');
            Route::get('/karyawan/{nik}', [AdminPresensiController::class, 'detailKaryawan'])->name('detail_karyawan');
            Route::get('/edit/{id}', [AdminPresensiController::class, 'editPresensi'])->name('edit');
            Route::put('/update/{id}', [AdminPresensiController::class, 'updatePresensi'])->name('update');
            
            // --- PERBAIKAN FINAL RUTE ---
            // Menggunakan notasi titik (dot) pada nama rute agar konsisten
            Route::post('/reset-masuk/{id}', [AdminPresensiController::class, 'resetMasuk'])->name('reset.masuk');
            Route::post('/reset-pulang/{id}', [AdminPresensiController::class, 'resetPulang'])->name('reset.pulang');
            Route::delete('/hapus/{id}', [AdminPresensiController::class, 'destroy'])->name('hapus');
        });
        
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [AdminLaporanController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminLaporanController::class, 'show'])->name('show');
            Route::post('/{id}/update-status', [AdminLaporanController::class, 'updateStatus'])->name('update_status');
            Route::delete('/{id}', [AdminLaporanController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('patroli')->name('patroli.')->group(function () {
            Route::get('/', [AdminPatroliController::class, 'index'])->name('index');
            Route::get('/show/{patrolId}', [AdminPatroliController::class, 'show'])->name('show');
            Route::delete('/destroy/{patrolId}', [AdminPatroliController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [AdminBackupController::class, 'index'])->name('index');
            Route::post('/process', [AdminBackupController::class, 'processBackup'])->name('process');
        });
    });

    // PANEL KOMANDAN
    Route::prefix('komandan')->name('komandan.')->middleware(['komandan'])->group(function() {
        Route::get('/dashboard', [KomandanDashboardController::class, 'index'])->name('dashboard');
        Route::get('/karyawan', [KomandanDashboardController::class, 'dataKaryawan'])->name('karyawan.index');

        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [KomandanDashboardController::class, 'rekapPresensi'])->name('rekapitulasi');
            Route::get('/harian', [KomandanDashboardController::class, 'laporanHarianPresensi'])->name('harian');
            Route::get('/karyawan/{nik}', [KomandanDashboardController::class, 'detailPresensiKaryawan'])->name('detail_karyawan');
            Route::get('/edit/{id}', [KomandanDashboardController::class, 'editPresensi'])->name('edit');
            Route::put('/update/{id}', [KomandanDashboardController::class, 'updatePresensi'])->name('update');
        });

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [KomandanDashboardController::class, 'laporanKaryawan'])->name('index');
            Route::get('/show/{id}', [KomandanDashboardController::class, 'showLaporanKaryawan'])->name('show');
            Route::post('/update-status/{id}', [KomandanDashboardController::class, 'updateStatusLaporan'])->name('update_status');
        });
        
        Route::prefix('patroli')->name('patroli.')->group(function () {
            Route::get('/', [KomandanDashboardController::class, 'patroliKaryawan'])->name('index');
            Route::get('/show/{patrolId}', [KomandanDashboardController::class, 'showPatroliKaryawan'])->name('show');
            Route::get('/monitoring', [KomandanDashboardController::class, 'monitoring'])->name('monitoring');
            Route::get('/live-data', [KomandanDashboardController::class, 'getLivePatrolData'])->name('live_data');
        });

        Route::prefix('jadwal-shift')->name('jadwalshift.')->group(function () {
            Route::get('/', [KomandanJadwalShiftController::class, 'index'])->name('index');
            Route::post('/store', [KomandanJadwalShiftController::class, 'storeOrUpdate'])->name('store');
        });

        Route::prefix('laporan-kinerja')->name('laporankinerja.')->group(function () {
            Route::get('/', [KomandanLaporanKinerjaController::class, 'index'])->name('index');
            Route::get('/cetak', [KomandanLaporanKinerjaController::class, 'cetak'])->name('cetak');
        });
    });

    // PANEL KETUA DEPARTEMEN
    Route::prefix('ketua-departemen')->name('ketua-departemen.')->middleware(['ketua.departemen'])->group(function() {
        Route::get('/dashboard', [KetuaDepartemenDashboardController::class, 'index'])->name('dashboard');
        Route::get('/karyawan', [KetuaDepartemenDashboardController::class, 'dataKaryawan'])->name('karyawan.index');

        Route::prefix('presensi')->name('presensi.')->group(function () {
            Route::get('/', [KetuaDepartemenDashboardController::class, 'rekapPresensi'])->name('rekapitulasi');
            Route::get('/harian', [KetuaDepartemenDashboardController::class, 'laporanHarianPresensi'])->name('harian');
            Route::get('/karyawan/{nik}', [KetuaDepartemenDashboardController::class, 'detailPresensiKaryawan'])->name('detail_karyawan');
        });

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [KetuaDepartemenDashboardController::class, 'laporanKaryawan'])->name('index');
            Route::get('/show/{id}', [KetuaDepartemenDashboardController::class, 'showLaporanKaryawan'])->name('show');
            Route::post('/update-status/{id}', [KetuaDepartemenDashboardController::class, 'updateStatusLaporan'])->name('update_status');
        });
        
        Route::prefix('patroli')->name('patroli.')->group(function () {
            Route::get('/', [KetuaDepartemenDashboardController::class, 'patroliKaryawan'])->name('index');
            Route::get('/show/{patrolId}', [KetuaDepartemenDashboardController::class, 'showPatroliKaryawan'])->name('show');
            Route::get('/monitoring', [KetuaDepartemenDashboardController::class, 'monitoring'])->name('monitoring');
            Route::get('/live-data', [KetuaDepartemenDashboardController::class, 'getLivePatrolData'])->name('live_data');
        });

        Route::prefix('jadwal-shift')->name('jadwalshift.')->group(function () {
            Route::get('/', [KetuaDepartemenDashboardController::class, 'jadwalShift'])->name('index');
        });

        Route::prefix('laporan-kinerja')->name('laporankinerja.')->group(function () {
            Route::get('/', [KetuaDepartemenDashboardController::class, 'laporanKinerja'])->name('index');
            Route::get('/cetak', [KetuaDepartemenDashboardController::class, 'cetakLaporanKinerja'])->name('cetak');
        });
    });

});