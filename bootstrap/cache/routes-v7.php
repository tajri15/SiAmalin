<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::q6oSbJAh2CLU4rew',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/proseslogin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'proseslogin',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/admin/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.login.form',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.login.proses',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/face/verify' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'face.verify',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/proseslogout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'proseslogout',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/editprofile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'editprofile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/presensi/updateprofile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'updateprofile',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/presensi/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'presensi.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/presensi/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'presensi.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/presensi/histori' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'presensi.histori',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/presensi/gethistori' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'presensi.gethistori',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/laporan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'laporan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/laporan/buat' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'laporan.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/laporan/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'laporan.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/start' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.start',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/store-point' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.store_point',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/pause' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.pause',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/resume' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.resume',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/stop' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.stop',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/patroli/histori' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.histori',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/set-face-verified-session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'session.set_face_verified',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/check-face-verified-session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'session.check_face_verified',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/karyawan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/karyawan/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/karyawan/step1' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.store_step1',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/karyawan/face-registration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.face_registration',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/karyawan/complete-registration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.complete_registration',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/fakultas' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/fakultas/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/presensi' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.presensi.rekapitulasi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/presensi/harian' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.presensi.harian',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/laporan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.laporan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/patroli' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.patroli.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/backup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.backup.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/panel/backup/process' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.backup.process',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/karyawan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.karyawan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/presensi' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.presensi.rekapitulasi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/presensi/harian' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.presensi.harian',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/laporan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.laporan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/patroli' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.patroli.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/patroli/monitoring' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.patroli.monitoring',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/patroli/live-data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.patroli.live_data',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/jadwal-shift' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.jadwalshift.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/jadwal-shift/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.jadwalshift.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/laporan-kinerja' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.laporankinerja.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/komandan/laporan-kinerja/cetak' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.laporankinerja.cetak',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/karyawan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.karyawan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/presensi' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.presensi.rekapitulasi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/presensi/harian' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.presensi.harian',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/laporan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.laporan.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/patroli' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.patroli.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/patroli/monitoring' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.patroli.monitoring',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/patroli/live-data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.patroli.live_data',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/jadwal-shift' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.jadwalshift.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/laporan-kinerja' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.laporankinerja.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ketua-departemen/laporan-kinerja/cetak' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.laporankinerja.cetak',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/pa(?|troli/histori/([^/]++)(*:35)|nel/(?|karyawan/([^/]++)(?|(*:69)|/(?|edit(*:84)|face\\-data(*:101)|reset\\-(?|face(*:123)|location(*:139)))|(*:149))|fakultas/(?|([^/]++)(?|/edit(*:186)|(*:194))|get\\-details\\-for\\-karyawan/([^/]++)(*:239))|p(?|resensi/(?|karyawan/([^/]++)(*:280)|edit/([^/]++)(*:301)|update/([^/]++)(*:324))|atroli/(?|show/([^/]++)(*:356)|destroy/([^/]++)(*:380)))|laporan/([^/]++)(?|(*:409)|/update\\-status(*:432))))|/k(?|omandan/(?|p(?|resensi/(?|karyawan/([^/]++)(*:491)|edit/([^/]++)(*:512)|update/([^/]++)(*:535))|atroli/show/([^/]++)(*:564))|laporan/(?|show/([^/]++)(*:597)|update\\-status/([^/]++)(*:628)))|etua\\-departemen/(?|p(?|resensi/karyawan/([^/]++)(*:687)|atroli/show/([^/]++)(*:715))|laporan/(?|show/([^/]++)(*:748)|update\\-status/([^/]++)(*:779))))|/storage/(.*)(*:803))/?$}sDu',
    ),
    3 => 
    array (
      35 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'patroli.histori.detail',
          ),
          1 => 
          array (
            0 => 'patrolId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      69 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      84 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      101 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.face_data',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      123 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.reset_face',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      139 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.reset_location',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      149 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.karyawan.destroy',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      186 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.edit',
          ),
          1 => 
          array (
            0 => 'fakultas',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      194 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.update',
          ),
          1 => 
          array (
            0 => 'fakultas',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.destroy',
          ),
          1 => 
          array (
            0 => 'fakultas',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      239 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.fakultas.get_details_for_karyawan',
          ),
          1 => 
          array (
            0 => 'nama_fakultas',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      280 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.presensi.detail_karyawan',
          ),
          1 => 
          array (
            0 => 'nik',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      301 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.presensi.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      324 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.presensi.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      356 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.patroli.show',
          ),
          1 => 
          array (
            0 => 'patrolId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      380 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.patroli.destroy',
          ),
          1 => 
          array (
            0 => 'patrolId',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      409 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.laporan.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      432 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'admin.laporan.update_status',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      491 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.presensi.detail_karyawan',
          ),
          1 => 
          array (
            0 => 'nik',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      512 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.presensi.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      535 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.presensi.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      564 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.patroli.show',
          ),
          1 => 
          array (
            0 => 'patrolId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      597 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.laporan.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      628 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'komandan.laporan.update_status',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      687 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.presensi.detail_karyawan',
          ),
          1 => 
          array (
            0 => 'nik',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      715 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.patroli.show',
          ),
          1 => 
          array (
            0 => 'patrolId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      748 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.laporan.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      779 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ketua-departemen.laporan.update_status',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      803 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'generated::q6oSbJAh2CLU4rew' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:827:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'C:\\\\laragon\\\\www\\\\SiAmalin\\\\vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Foundation\\\\Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000003e70000000000000000";}}',
        'as' => 'generated::q6oSbJAh2CLU4rew',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:686:"function () {
        if (\\Illuminate\\Support\\Facades\\Auth::guard(\'karyawan\')->check()) {
            $user = \\Illuminate\\Support\\Facades\\Auth::guard(\'karyawan\')->user();
            if ($user->is_admin) {
                return \\redirect()->route(\'admin.dashboard\');
            } elseif ($user->is_komandan) {
                return \\redirect()->route(\'komandan.dashboard\');
            } elseif ($user->is_ketua_departemen) {
                return \\redirect()->route(\'ketua-departemen.dashboard\');
            }
            return \\redirect()->route(\'dashboard\'); // Petugas Keamanan
        }
        return \\app(\\App\\Http\\Controllers\\AuthController::class)->showLoginForm();
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000039f0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'proseslogin' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'proseslogin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@proseslogin',
        'controller' => 'App\\Http\\Controllers\\AuthController@proseslogin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'proseslogin',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.login.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@showAdminLoginForm',
        'controller' => 'App\\Http\\Controllers\\AuthController@showAdminLoginForm',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'admin.login.form',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.login.proses' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'admin/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@prosesAdminLogin',
        'controller' => 'App\\Http\\Controllers\\AuthController@prosesAdminLogin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'admin.login.proses',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'face.verify' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'face/verify',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@verifyFace',
        'controller' => 'App\\Http\\Controllers\\AuthController@verifyFace',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'face.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\DashboardController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'proseslogout' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'proseslogout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@proseslogout',
        'controller' => 'App\\Http\\Controllers\\AuthController@proseslogout',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'proseslogout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:164:"function () {
        $karyawan = \\Illuminate\\Support\\Facades\\Auth::guard(\'karyawan\')->user();
        return \\view(\'presensi.profile\', \\compact(\'karyawan\'));
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000003880000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'editprofile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'editprofile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:168:"function () {
        $karyawan = \\Illuminate\\Support\\Facades\\Auth::guard(\'karyawan\')->user();
        return \\view(\'presensi.editprofile\', \\compact(\'karyawan\'));
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000038a0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'editprofile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'updateprofile' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'presensi/updateprofile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PresensiController@updateprofile',
        'controller' => 'App\\Http\\Controllers\\PresensiController@updateprofile',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'updateprofile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'presensi.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'presensi/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PresensiController@create',
        'controller' => 'App\\Http\\Controllers\\PresensiController@create',
        'as' => 'presensi.create',
        'namespace' => NULL,
        'prefix' => '/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'presensi.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'presensi/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PresensiController@store',
        'controller' => 'App\\Http\\Controllers\\PresensiController@store',
        'as' => 'presensi.store',
        'namespace' => NULL,
        'prefix' => '/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'presensi.histori' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'presensi/histori',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PresensiController@histori',
        'controller' => 'App\\Http\\Controllers\\PresensiController@histori',
        'as' => 'presensi.histori',
        'namespace' => NULL,
        'prefix' => '/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'presensi.gethistori' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'presensi/gethistori',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PresensiController@gethistori',
        'controller' => 'App\\Http\\Controllers\\PresensiController@gethistori',
        'as' => 'presensi.gethistori',
        'namespace' => NULL,
        'prefix' => '/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'laporan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'laporan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\LaporanController@index',
        'controller' => 'App\\Http\\Controllers\\LaporanController@index',
        'as' => 'laporan.index',
        'namespace' => NULL,
        'prefix' => '/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'laporan.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'laporan/buat',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\LaporanController@create',
        'controller' => 'App\\Http\\Controllers\\LaporanController@create',
        'as' => 'laporan.create',
        'namespace' => NULL,
        'prefix' => '/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'laporan.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'laporan/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\LaporanController@store',
        'controller' => 'App\\Http\\Controllers\\LaporanController@store',
        'as' => 'laporan.store',
        'namespace' => NULL,
        'prefix' => '/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'patroli',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@index',
        'controller' => 'App\\Http\\Controllers\\PatroliController@index',
        'as' => 'patroli.index',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.start' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'patroli/start',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@startPatrol',
        'controller' => 'App\\Http\\Controllers\\PatroliController@startPatrol',
        'as' => 'patroli.start',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.store_point' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'patroli/store-point',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@storePoint',
        'controller' => 'App\\Http\\Controllers\\PatroliController@storePoint',
        'as' => 'patroli.store_point',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.pause' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'patroli/pause',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@pausePatrol',
        'controller' => 'App\\Http\\Controllers\\PatroliController@pausePatrol',
        'as' => 'patroli.pause',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.resume' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'patroli/resume',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@resumePatrol',
        'controller' => 'App\\Http\\Controllers\\PatroliController@resumePatrol',
        'as' => 'patroli.resume',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.stop' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'patroli/stop',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@stopPatrol',
        'controller' => 'App\\Http\\Controllers\\PatroliController@stopPatrol',
        'as' => 'patroli.stop',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.histori' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'patroli/histori',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@historiPatroli',
        'controller' => 'App\\Http\\Controllers\\PatroliController@historiPatroli',
        'as' => 'patroli.histori',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'patroli.histori.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'patroli/histori/{patrolId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\PatroliController@detailHistoriPatroli',
        'controller' => 'App\\Http\\Controllers\\PatroliController@detailHistoriPatroli',
        'as' => 'patroli.histori.detail',
        'namespace' => NULL,
        'prefix' => '/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'session.set_face_verified' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'set-face-verified-session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@setFaceVerifiedSession',
        'controller' => 'App\\Http\\Controllers\\AuthController@setFaceVerifiedSession',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'session.set_face_verified',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'session.check_face_verified' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'check-face-verified-session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@checkFaceVerifiedSession',
        'controller' => 'App\\Http\\Controllers\\AuthController@checkFaceVerifiedSession',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'session.check_face_verified',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminDashboardController@index',
        'as' => 'admin.dashboard',
        'namespace' => NULL,
        'prefix' => '/panel',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@index',
        'as' => 'admin.karyawan.index',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@create',
        'as' => 'admin.karyawan.create',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.store_step1' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/karyawan/step1',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@storeStep1',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@storeStep1',
        'as' => 'admin.karyawan.store_step1',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.face_registration' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/face-registration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@showFaceRegistration',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@showFaceRegistration',
        'as' => 'admin.karyawan.face_registration',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.complete_registration' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/karyawan/complete-registration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@completeRegistration',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@completeRegistration',
        'as' => 'admin.karyawan.complete_registration',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@show',
        'as' => 'admin.karyawan.show',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@edit',
        'as' => 'admin.karyawan.edit',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'panel/karyawan/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@update',
        'as' => 'admin.karyawan.update',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'panel/karyawan/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@destroy',
        'as' => 'admin.karyawan.destroy',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.face_data' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/{id}/face-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@viewFaceData',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@viewFaceData',
        'as' => 'admin.karyawan.face_data',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.reset_face' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/karyawan/{id}/reset-face',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@resetFaceData',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@resetFaceData',
        'as' => 'admin.karyawan.reset_face',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.karyawan.reset_location' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/karyawan/{id}/reset-location',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@resetOfficeLocation',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminKaryawanController@resetOfficeLocation',
        'as' => 'admin.karyawan.reset_location',
        'namespace' => NULL,
        'prefix' => 'panel/karyawan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/fakultas',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@index',
        'as' => 'admin.fakultas.index',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/fakultas/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@create',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@create',
        'as' => 'admin.fakultas.create',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/fakultas',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@store',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@store',
        'as' => 'admin.fakultas.store',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/fakultas/{fakultas}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@edit',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@edit',
        'as' => 'admin.fakultas.edit',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'panel/fakultas/{fakultas}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@update',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@update',
        'as' => 'admin.fakultas.update',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'panel/fakultas/{fakultas}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@destroy',
        'as' => 'admin.fakultas.destroy',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.fakultas.get_details_for_karyawan' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/fakultas/get-details-for-karyawan/{nama_fakultas}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@getFakultasDetails',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminFakultasController@getFakultasDetails',
        'as' => 'admin.fakultas.get_details_for_karyawan',
        'namespace' => NULL,
        'prefix' => 'panel/fakultas',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.presensi.rekapitulasi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/presensi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@rekapitulasi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@rekapitulasi',
        'as' => 'admin.presensi.rekapitulasi',
        'namespace' => NULL,
        'prefix' => 'panel/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.presensi.harian' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/presensi/harian',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@laporanHarian',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@laporanHarian',
        'as' => 'admin.presensi.harian',
        'namespace' => NULL,
        'prefix' => 'panel/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.presensi.detail_karyawan' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/presensi/karyawan/{nik}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@detailKaryawan',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@detailKaryawan',
        'as' => 'admin.presensi.detail_karyawan',
        'namespace' => NULL,
        'prefix' => 'panel/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.presensi.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/presensi/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@editPresensi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@editPresensi',
        'as' => 'admin.presensi.edit',
        'namespace' => NULL,
        'prefix' => 'panel/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.presensi.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'panel/presensi/update/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@updatePresensi',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPresensiController@updatePresensi',
        'as' => 'admin.presensi.update',
        'namespace' => NULL,
        'prefix' => 'panel/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.laporan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/laporan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@index',
        'as' => 'admin.laporan.index',
        'namespace' => NULL,
        'prefix' => 'panel/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.laporan.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/laporan/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@show',
        'as' => 'admin.laporan.show',
        'namespace' => NULL,
        'prefix' => 'panel/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.laporan.update_status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/laporan/{id}/update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@updateStatus',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminLaporanController@updateStatus',
        'as' => 'admin.laporan.update_status',
        'namespace' => NULL,
        'prefix' => 'panel/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.patroli.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/patroli',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@index',
        'as' => 'admin.patroli.index',
        'namespace' => NULL,
        'prefix' => 'panel/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.patroli.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/patroli/show/{patrolId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@show',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@show',
        'as' => 'admin.patroli.show',
        'namespace' => NULL,
        'prefix' => 'panel/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.patroli.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'panel/patroli/destroy/{patrolId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@destroy',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminPatroliController@destroy',
        'as' => 'admin.patroli.destroy',
        'namespace' => NULL,
        'prefix' => 'panel/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.backup.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'panel/backup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminBackupController@index',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminBackupController@index',
        'as' => 'admin.backup.index',
        'namespace' => NULL,
        'prefix' => 'panel/backup',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'admin.backup.process' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'panel/backup/process',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'admin',
        ),
        'uses' => 'App\\Http\\Controllers\\Admin\\AdminBackupController@processBackup',
        'controller' => 'App\\Http\\Controllers\\Admin\\AdminBackupController@processBackup',
        'as' => 'admin.backup.process',
        'namespace' => NULL,
        'prefix' => 'panel/backup',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@index',
        'as' => 'komandan.dashboard',
        'namespace' => NULL,
        'prefix' => '/komandan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.karyawan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/karyawan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@dataKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@dataKaryawan',
        'as' => 'komandan.karyawan.index',
        'namespace' => NULL,
        'prefix' => '/komandan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.presensi.rekapitulasi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/presensi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@rekapPresensi',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@rekapPresensi',
        'as' => 'komandan.presensi.rekapitulasi',
        'namespace' => NULL,
        'prefix' => 'komandan/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.presensi.harian' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/presensi/harian',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@laporanHarianPresensi',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@laporanHarianPresensi',
        'as' => 'komandan.presensi.harian',
        'namespace' => NULL,
        'prefix' => 'komandan/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.presensi.detail_karyawan' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/presensi/karyawan/{nik}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@detailPresensiKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@detailPresensiKaryawan',
        'as' => 'komandan.presensi.detail_karyawan',
        'namespace' => NULL,
        'prefix' => 'komandan/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.presensi.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/presensi/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@editPresensi',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@editPresensi',
        'as' => 'komandan.presensi.edit',
        'namespace' => NULL,
        'prefix' => 'komandan/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.presensi.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'komandan/presensi/update/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@updatePresensi',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@updatePresensi',
        'as' => 'komandan.presensi.update',
        'namespace' => NULL,
        'prefix' => 'komandan/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.laporan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/laporan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@laporanKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@laporanKaryawan',
        'as' => 'komandan.laporan.index',
        'namespace' => NULL,
        'prefix' => 'komandan/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.laporan.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/laporan/show/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@showLaporanKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@showLaporanKaryawan',
        'as' => 'komandan.laporan.show',
        'namespace' => NULL,
        'prefix' => 'komandan/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.laporan.update_status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'komandan/laporan/update-status/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@updateStatusLaporan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@updateStatusLaporan',
        'as' => 'komandan.laporan.update_status',
        'namespace' => NULL,
        'prefix' => 'komandan/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.patroli.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/patroli',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@patroliKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@patroliKaryawan',
        'as' => 'komandan.patroli.index',
        'namespace' => NULL,
        'prefix' => 'komandan/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.patroli.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/patroli/show/{patrolId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@showPatroliKaryawan',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@showPatroliKaryawan',
        'as' => 'komandan.patroli.show',
        'namespace' => NULL,
        'prefix' => 'komandan/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.patroli.monitoring' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/patroli/monitoring',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@monitoring',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@monitoring',
        'as' => 'komandan.patroli.monitoring',
        'namespace' => NULL,
        'prefix' => 'komandan/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.patroli.live_data' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/patroli/live-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@getLivePatrolData',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanDashboardController@getLivePatrolData',
        'as' => 'komandan.patroli.live_data',
        'namespace' => NULL,
        'prefix' => 'komandan/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.jadwalshift.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/jadwal-shift',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanJadwalShiftController@index',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanJadwalShiftController@index',
        'as' => 'komandan.jadwalshift.index',
        'namespace' => NULL,
        'prefix' => 'komandan/jadwal-shift',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.jadwalshift.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'komandan/jadwal-shift/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanJadwalShiftController@storeOrUpdate',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanJadwalShiftController@storeOrUpdate',
        'as' => 'komandan.jadwalshift.store',
        'namespace' => NULL,
        'prefix' => 'komandan/jadwal-shift',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.laporankinerja.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/laporan-kinerja',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanLaporanKinerjaController@index',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanLaporanKinerjaController@index',
        'as' => 'komandan.laporankinerja.index',
        'namespace' => NULL,
        'prefix' => 'komandan/laporan-kinerja',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'komandan.laporankinerja.cetak' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'komandan/laporan-kinerja/cetak',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'komandan',
        ),
        'uses' => 'App\\Http\\Controllers\\Komandan\\KomandanLaporanKinerjaController@cetak',
        'controller' => 'App\\Http\\Controllers\\Komandan\\KomandanLaporanKinerjaController@cetak',
        'as' => 'komandan.laporankinerja.cetak',
        'namespace' => NULL,
        'prefix' => 'komandan/laporan-kinerja',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@index',
        'as' => 'ketua-departemen.dashboard',
        'namespace' => NULL,
        'prefix' => '/ketua-departemen',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.karyawan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/karyawan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@dataKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@dataKaryawan',
        'as' => 'ketua-departemen.karyawan.index',
        'namespace' => NULL,
        'prefix' => '/ketua-departemen',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.presensi.rekapitulasi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/presensi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@rekapPresensi',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@rekapPresensi',
        'as' => 'ketua-departemen.presensi.rekapitulasi',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.presensi.harian' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/presensi/harian',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanHarianPresensi',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanHarianPresensi',
        'as' => 'ketua-departemen.presensi.harian',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.presensi.detail_karyawan' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/presensi/karyawan/{nik}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@detailPresensiKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@detailPresensiKaryawan',
        'as' => 'ketua-departemen.presensi.detail_karyawan',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/presensi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.laporan.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/laporan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanKaryawan',
        'as' => 'ketua-departemen.laporan.index',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.laporan.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/laporan/show/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@showLaporanKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@showLaporanKaryawan',
        'as' => 'ketua-departemen.laporan.show',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.laporan.update_status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'ketua-departemen/laporan/update-status/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@updateStatusLaporan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@updateStatusLaporan',
        'as' => 'ketua-departemen.laporan.update_status',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/laporan',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.patroli.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/patroli',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@patroliKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@patroliKaryawan',
        'as' => 'ketua-departemen.patroli.index',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.patroli.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/patroli/show/{patrolId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@showPatroliKaryawan',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@showPatroliKaryawan',
        'as' => 'ketua-departemen.patroli.show',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.patroli.monitoring' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/patroli/monitoring',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@monitoring',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@monitoring',
        'as' => 'ketua-departemen.patroli.monitoring',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.patroli.live_data' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/patroli/live-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@getLivePatrolData',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@getLivePatrolData',
        'as' => 'ketua-departemen.patroli.live_data',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/patroli',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.jadwalshift.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/jadwal-shift',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@jadwalShift',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@jadwalShift',
        'as' => 'ketua-departemen.jadwalshift.index',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/jadwal-shift',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.laporankinerja.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/laporan-kinerja',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanKinerja',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@laporanKinerja',
        'as' => 'ketua-departemen.laporankinerja.index',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/laporan-kinerja',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ketua-departemen.laporankinerja.cetak' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ketua-departemen/laporan-kinerja/cetak',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:karyawan',
          2 => 'ketua.departemen',
        ),
        'uses' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@cetakLaporanKinerja',
        'controller' => 'App\\Http\\Controllers\\KetuaDepartemen\\KetuaDepartemenDashboardController@cetakLaporanKinerja',
        'as' => 'ketua-departemen.laporankinerja.cetak',
        'namespace' => NULL,
        'prefix' => 'ketua-departemen/laporan-kinerja',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:43:"C:\\laragon\\www\\SiAmalin\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:1;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000003d60000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
