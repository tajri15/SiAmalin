@extends('admin.layouts.app')

@section('title', 'Manajemen Laporan Karyawan')

@push('styles')
<style>
    .table-sm th, .table-sm td {
        font-size: 0.85rem;
        padding: 0.4rem;
    }
    .img-thumbnail-xs {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Manajemen Laporan Karyawan</h1>
    <p class="mb-4">Menampilkan semua laporan yang dikirim oleh karyawan.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir:</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nik_karyawan" class="form-label">NIK Karyawan:</label>
                        <input type="text" name="nik_karyawan" id="nik_karyawan" class="form-control form-control-sm" value="{{ request('nik_karyawan') }}" placeholder="NIK">
                    </div>
                     <div class="col-md-3 mb-3">
                        <label for="nama_karyawan" class="form-label">Nama Karyawan:</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control form-control-sm" value="{{ request('nama_karyawan') }}" placeholder="Nama">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="jenis_laporan" class="form-label">Jenis Laporan:</label>
                        <select name="jenis_laporan" id="jenis_laporan" class="form-select form-select-sm">
                            <option value="">Semua Jenis</option>
                            <option value="harian" {{ request('jenis_laporan') == 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="kegiatan" {{ request('jenis_laporan') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                            <option value="masalah" {{ request('jenis_laporan') == 'masalah' ? 'selected' : '' }}>Masalah</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status_admin" class="form-label">Status Admin:</label>
                        <select name="status_admin" id="status_admin" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="belum_ditinjau" {{ request('status_admin') == 'belum_ditinjau' ? 'selected' : '' }}>Belum Ditinjau</option>
                            <option value="Diterima" {{ request('status_admin') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ request('status_admin') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Perlu Revisi" {{ request('status_admin') == 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>
                    </div>
                    <div class="col-md-3 align-self-end mb-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel-fill"></i> Filter</button>
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTableLaporan" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tgl Laporan</th>
                            <th>Jam</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Jenis</th>
                            <th>Keterangan (Singkat)</th>
                            <th>Status Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Import Str class
                            use Illuminate\Support\Str;
                        @endphp
                        @forelse ($laporans as $index => $laporan)
                        <tr>
                            <td>{{ $laporans->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->isoFormat('D MMM YY') }}</td> {{-- Disingkat agar tidak terlalu lebar --}}
                            <td>{{ $laporan->jam }}</td>
                            <td>{{ $laporan->nik }}</td>
                            <td>{{ $laporan->karyawan->nama_lengkap ?? 'N/A' }}</td>
                            <td><span class="badge bg-info text-capitalize">{{ $laporan->jenis_laporan }}</span></td>
                            <td>{{ Str::limit($laporan->keterangan, 50) }}</td>
                            <td>
                                @if($laporan->status_admin)
                                    <span class="badge 
                                        @if($laporan->status_admin == 'Diterima') bg-success
                                        @elseif($laporan->status_admin == 'Ditolak') bg-danger
                                        @elseif($laporan->status_admin == 'Perlu Revisi') bg-warning text-dark
                                        @else bg-secondary @endif">
                                        {{ $laporan->status_admin }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Ditinjau</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.laporan.show', $laporan->_id) }}" class="btn btn-info btn-sm py-0 px-1" title="Lihat Detail Laporan">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data laporan yang sesuai dengan filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $laporans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
