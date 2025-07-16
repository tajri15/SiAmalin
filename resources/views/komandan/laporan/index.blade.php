{{--resources\views\komandan\laporan\index.blade.php--}}

@extends('admin.layouts.app')

@section('title', 'Laporan Petugas - Fakultas ' . $fakultasKomandan)

@push('styles')
<style>
    .table-sm th, .table-sm td {
        font-size: 0.85rem;
        padding: 0.5rem; /* Slightly increase padding for readability */
        vertical-align: middle; /* Center content vertically */
    }
    .table td {
        line-height: 1.4; /* Improve line spacing */
    }
    .form-select-sm, .form-control-sm { 
        font-size: 0.875rem; 
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Laporan Petugas Keamanan</h1>
    <p class="mb-4">Menampilkan semua laporan yang dikirim oleh petugas keamanan di {{ $fakultasKomandan }}.</p>

    {{-- Card Filter --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('komandan.laporan.index') }}">
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
                        <label for="nik_karyawan" class="form-label">Username Petugas:</label>
                        <input type="text" name="nik_karyawan" id="nik_karyawan" class="form-control form-control-sm" value="{{ request('nik_karyawan') }}" placeholder="Username">
                    </div>
                     <div class="col-md-3 mb-3">
                        <label for="nama_karyawan" class="form-label">Nama Petugas:</label>
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
                        <label for="status_laporan" class="form-label">Status Laporan:</label>
                        <select name="status_laporan" id="status_laporan" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="belum_ditinjau" {{ request('status_laporan') == 'belum_ditinjau' ? 'selected' : '' }}>Belum Ditinjau</option>
                            <option value="Diterima" {{ request('status_laporan') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ request('status_laporan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Perlu Revisi" {{ request('status_laporan') == 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>
                    </div>
                    <div class="col-md-3 align-self-end mb-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel-fill"></i> Filter</button>
                        <a href="{{ route('komandan.laporan.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Card Daftar Laporan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Petugas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTableLaporanKomandan" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            {{-- Menambahkan class "text-center" pada semua header --}}
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-center" style="width: 15%;">Waktu Laporan</th>
                            <th class="text-center" style="width: 20%;">Petugas</th>
                            <th class="text-center" style="width: 10%;">Jenis</th>
                            <th class="text-center">Keterangan</th>
                            <th class="text-center" style="width: 12%;">Status</th>
                            <th class="text-center" style="width: 8%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            use Illuminate\Support\Str;
                        @endphp
                        @forelse ($laporans as $index => $laporan)
                        <tr>
                            {{-- Menambahkan "text-center" untuk kolom No --}}
                            <td class="text-center">{{ $laporans->firstItem() + $index }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->isoFormat('D MMM YY') }}
                                <br>
                                <small class="text-muted">{{ $laporan->jam }}</small>
                            </td>
                            <td>
                                {{ $laporan->karyawan->nama_lengkap ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $laporan->nik }}</small>
                            </td>
                            {{-- Menambahkan "text-center" untuk kolom Jenis --}}
                            <td class="text-center">
                                @php
                                    $badgeClass = 'bg-secondary'; // Default color
                                    if ($laporan->jenis_laporan == 'harian') $badgeClass = 'bg-primary';
                                    elseif ($laporan->jenis_laporan == 'kegiatan') $badgeClass = 'bg-success';
                                    elseif ($laporan->jenis_laporan == 'masalah') $badgeClass = 'bg-danger';
                                @endphp
                                <span class="badge {{ $badgeClass }} text-capitalize">{{ $laporan->jenis_laporan }}</span>
                            </td>
                            <td>{{ Str::limit($laporan->keterangan, 70) }}</td>
                            {{-- Menambahkan "text-center" untuk kolom Status --}}
                            <td class="text-center">
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
                            {{-- Menambahkan "text-center" untuk kolom Aksi --}}
                            <td class="text-center">
                                <a href="{{ route('komandan.laporan.show', $laporan->_id) }}" 
                                   class="btn btn-info btn-sm py-0 px-1" 
                                   title="Lihat Detail Laporan">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data laporan yang sesuai dengan filter.</td>
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

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush