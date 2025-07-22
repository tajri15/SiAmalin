{{-- resources\views\ketua-departemen\laporan\show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Laporan Petugas - ' . $departemen)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Detail Laporan Petugas</h1>
        <a href="{{ route('ketua-departemen.laporan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Laporan
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan ({{ $fakultas }} - Dept. {{ $departemen }})</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th style="width: 30%;">ID Laporan</th>
                            <td>: {{ $laporan->_id }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Laporan</th>
                            <td>: {{ \Carbon\Carbon::parse($laporan->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</td>
                        </tr>
                        <tr>
                            <th>Jam Laporan</th>
                            <td>: {{ $laporan->jam }}</td>
                        </tr>
                        <tr>
                            <th>Username Petugas</th>
                            <td>: {{ $laporan->nik }}</td>
                        </tr>
                        <tr>
                            <th>Nama Petugas</th>
                            <td>: {{ $laporan->karyawan->nama_lengkap ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>: {{ $laporan->karyawan->jabatan ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Laporan</th>
                            <td>: <span class="badge bg-info text-capitalize">{{ $laporan->jenis_laporan }}</span></td>
                        </tr>
                        <tr>
                            <th>Lokasi Pengiriman</th>
                            <td>: {{ $laporan->lokasi }}</td>
                        </tr>
                        <tr>
                            <th class="align-top">Keterangan</th>
                            <td class="align-top">: <div style="white-space: pre-wrap;">{{ $laporan->keterangan }}</div></td>
                        </tr>
                        <tr>
                            <th>Foto Bukti</th>
                            <td>:
                                @if($laporan->foto)
                                <a href="{{ $laporan->foto_url }}" target="_blank">
                                    <img src="{{ $laporan->foto_url }}" alt="Foto Bukti" class="img-thumbnail mt-1" style="max-width: 250px; max-height: 250px;">
                                </a>
                                @else
                                Tidak ada foto bukti.
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Foto Verifikasi Wajah</th>
                            <td>:
                                @if($laporan->face_verification_image)
                                <a href="{{ $laporan->face_verification_url }}" target="_blank">
                                    <img src="{{ $laporan->face_verification_url }}" alt="Foto Verifikasi Wajah" class="img-thumbnail mt-1" style="max-width: 150px; max-height: 150px;">
                                </a>
                                @else
                                Tidak ada foto verifikasi.
                                @endif
                            </td>
                        </tr>
                         <tr>
                            <th>Tanggal Dibuat</th>
                            <td>: {{ \Carbon\Carbon::parse($laporan->created_at)->isoFormat('D MMM YYYY, HH:mm:ss') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Laporan</h6>
                </div>
                <div class="card-body">
                    <p><strong>Status Saat Ini:</strong>
                        @if($laporan->status_admin)
                            <span class="badge 
                                @if($laporan->status_admin == 'Diterima') bg-success
                                @elseif($laporan->status_admin == 'Ditolak') bg-danger
                                @else bg-secondary @endif">
                                {{ $laporan->status_admin }}
                            </span>
                            @if($laporan->tanggal_peninjauan_admin)
                                <small class="d-block text-muted">Ditinjau pada: {{ \Carbon\Carbon::parse($laporan->tanggal_peninjauan_admin)->isoFormat('D MMM YY, HH:mm') }}</small>
                            @endif
                        @else
                            <span class="badge bg-secondary">Belum Ditinjau</span>
                        @endif
                    </p>
                    @if($laporan->catatan_admin)
                    <p><strong>Catatan:</strong><br>
                        <small class="text-muted" style="white-space: pre-wrap;">{{ $laporan->catatan_admin }}</small>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection