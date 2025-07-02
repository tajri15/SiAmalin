@extends('admin.layouts.app')

@section('title', 'Detail Karyawan: ' . $karyawan->nama_lengkap)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Detail Karyawan</h1>
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data {{ $karyawan->nama_lengkap }} (NIK: {{ $karyawan->nik }})</h6>
            <div>
                <a href="{{ route('admin.karyawan.edit', $karyawan->_id) }}" class="btn btn-warning btn-sm" title="Edit Data Karyawan">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto {{ $karyawan->nama_lengkap }}" class="img-thumbnail mb-3" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                    @else
                        <div class="img-thumbnail d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; background-color: #f8f9fa;">
                            <span class="text-muted">Tidak Ada Foto</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th style="width: 25%;">NIK</th>
                            <td>: {{ $karyawan->nik }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>: {{ $karyawan->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>: {{ $karyawan->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>Nomor HP</th>
                            <td>: {{ $karyawan->no_hp }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $karyawan->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Admin</th>
                            <td>: {!! $karyawan->is_admin ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Kantor</th>
                            <td>
                                @if(isset($karyawan->office_location['coordinates']))
                                    : Lat: {{ $karyawan->office_location['coordinates'][1] }}, Lng: {{ $karyawan->office_location['coordinates'][0] }}
                                    (Radius: {{ $karyawan->office_radius ?? 'N/A' }} m)
                                    <form action="{{ route('admin.karyawan.reset_location', $karyawan->_id) }}" method="GET" class="d-inline ms-2" onsubmit="return confirm('Anda yakin ingin mereset lokasi kantor karyawan ini? Karyawan perlu mengatur ulang lokasi kantornya.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-1" title="Reset Lokasi Kantor"><i class="bi bi-geo-alt-fill"></i> Reset</button>
                                    </form>
                                @else
                                    : <span class="text-muted">Belum diatur</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Data Wajah</th>
                            <td>
                                @if($karyawan->face_embedding)
                                    : <span class="badge bg-info">Terdaftar</span>
                                    <form action="{{ route('admin.karyawan.reset_face', $karyawan->_id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Anda yakin ingin mereset data wajah karyawan ini? Karyawan perlu melakukan registrasi wajah kembali.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-1" title="Reset Data Wajah"><i class="bi bi-person-bounding-box"></i> Reset</button>
                                    </form>
                                @else
                                    : <span class="badge bg-warning">Belum Terdaftar</span>
                                @endif
                            </td>
                        </tr>
                         <tr>
                            <th>Foto Verifikasi Wajah Terakhir (Presensi)</th>
                            <td>:
                                @php
                                    $lastPresensiWithFace = \App\Models\Presensi::where('nik', $karyawan->nik)
                                                            ->whereNotNull('foto_in') // atau foto_out jika relevan
                                                            ->orderBy('tgl_presensi', 'desc')
                                                            ->first();
                                @endphp
                                @if($lastPresensiWithFace && $lastPresensiWithFace->foto_in)
                                    <a href="{{ asset('storage/' . $lastPresensiWithFace->foto_in) }}" target="_blank">Lihat Foto Masuk Terakhir</a>
                                    (@if($lastPresensiWithFace->foto_out)
                                    , <a href="{{ asset('storage/' . $lastPresensiWithFace->foto_out) }}" target="_blank">Lihat Foto Pulang Terakhir</a>
                                    @endif)
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Foto Verifikasi Wajah Terakhir (Laporan)</th>
                            <td>:
                                @php
                                    $lastLaporanWithFace = \App\Models\Laporan::where('nik', $karyawan->nik)
                                                            ->whereNotNull('face_verification_image')
                                                            ->orderBy('created_at', 'desc')
                                                            ->first();
                                @endphp
                                @if($lastLaporanWithFace && $lastLaporanWithFace->face_verification_image)
                                    <a href="{{ asset('storage/' . $lastLaporanWithFace->face_verification_image) }}" target="_blank">Lihat Foto Verifikasi Laporan Terakhir</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tambahkan bagian untuk menampilkan histori presensi atau laporan karyawan jika diperlukan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Histori Presensi Bulan Ini</h6>
        </div>
        <div class="card-body">
            <a href="{{ route('admin.presensi.detail_karyawan', ['nik' => $karyawan->nik, 'bulan' => date('m'), 'tahun' => date('Y')]) }}" class="btn btn-sm btn-info">
                Lihat Detail Presensi Karyawan Ini
            </a>
            {{-- Atau tampilkan tabel singkat di sini --}}
        </div>
    </div>

</div>
@endsection
