{{-- File: resources/views/komandan/patroli/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Histori Patroli Petugas - Fakultas ' . $fakultasKomandan)

@push('styles')
<style>
    .table-sm th, .table-sm td {
        font-size: 0.85rem;
        padding: 0.5rem;
        vertical-align: middle;
    }
    .form-select-sm, .form-control-sm {
        font-size: 0.875rem;
    }
    .action-buttons .btn {
        margin-right: 0.25rem;
    }
    .fixed-alert {
        position: fixed;
        top: 70px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        min-width: 300px;
        max-width: 600px;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    /* PERBAIKAN: CSS untuk mengatur ulang layout paginasi */
    .pagination-container nav > div {
        display: flex;
        flex-direction: column-reverse; /* Membalik urutan: link di atas, teks di bawah */
        align-items: center;
        gap: 0.5rem; /* Memberi jarak antara link dan teks */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Histori Patroli Petugas</h1>
    </div>
    <p class="mb-4">Menampilkan histori patroli petugas keamanan di {{ $fakultasKomandan }}.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fixed-alert" role="alert" id="pageSuccessMessage">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
         <div class="alert alert-danger alert-dismissible fade show fixed-alert" role="alert" id="pageErrorMessage">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Patroli</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('komandan.patroli.index') }}">
                <div class="row gx-2 gy-2 align-items-end">
                    <div class="col-md-3">
                        <label for="nik_karyawan" class="form-label mb-1 small">Username Petugas:</label>
                        <input type="text" name="nik_karyawan" id="nik_karyawan" class="form-control form-control-sm" value="{{ request('nik_karyawan') }}" placeholder="Masukkan Username">
                    </div>
                    <div class="col-md-3">
                        <label for="nama_karyawan" class="form-label mb-1 small">Nama Petugas:</label>
                        <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control form-control-sm" value="{{ request('nama_karyawan') }}" placeholder="Masukkan Nama">
                    </div>
                    <div class="col-md-2">
                        <label for="tanggal_mulai" class="form-label mb-1 small">Tgl Mulai:</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="tanggal_akhir" class="form-label mb-1 small">Tgl Akhir:</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100" title="Filter"><i class="bi bi-funnel-fill"></i></button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('komandan.patroli.index') }}" class="btn btn-secondary btn-sm w-100" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Patroli Selesai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTablePatroliKomandan" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Petugas (Username)</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th>Jarak (km)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patrols as $index => $patrol)
                        <tr>
                            <td class="text-center">{{ $patrols->firstItem() + $index }}</td>
                            <td>
                                {{ $patrol->karyawan->nama_lengkap ?? 'N/A' }}
                                <small class="d-block text-muted">({{ $patrol->karyawan_nik }})</small>
                            </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($patrol->start_time)->isoFormat('D MMM YY, HH:mm') }}</td>
                            <td class="text-center">{{ $patrol->end_time ? \Carbon\Carbon::parse($patrol->end_time)->isoFormat('D MMM YY, HH:mm') : '-' }}</td>
                            <td class="text-center">
                                @php
                                    $duration = $patrol->duration_seconds ?? 0;
                                    $hours = floor($duration / 3600);
                                    $minutes = floor(($duration % 3600) / 60);
                                    $seconds = $duration % 60;
                                    echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                @endphp
                            </td>
                            <td class="text-center">{{ number_format(($patrol->total_distance_meters ?? 0) / 1000, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $patrol->status == 'selesai' ? 'success' : ($patrol->status == 'dibatalkan' ? 'danger' : 'secondary') }} text-capitalize">
                                    {{ $patrol->status }}
                                </span>
                            </td>
                            <td class="text-center action-buttons">
                                <a href="{{ route('komandan.patroli.show', $patrol->_id) }}" class="btn btn-info btn-sm py-0 px-1" title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                {{-- Tombol Hapus Dihilangkan --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data patroli yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- PERBAIKAN TAMPILAN PAGINASI --}}
            @if ($patrols->hasPages())
            <div class="pagination-container mt-3">
                {{ $patrols->appends(request()->query())->links() }}
            </div>
            @endif
            {{-- AKHIR PERBAIKAN --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk tooltip jika digunakan
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Script untuk auto-hide alert setelah beberapa detik
    window.setTimeout(function() {
        $(".fixed-alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 4000); // 4 detik
</script>
@endpush
