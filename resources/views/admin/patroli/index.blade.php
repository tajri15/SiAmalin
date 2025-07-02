{{-- File: resources/views/admin/patroli/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Histori Patroli Karyawan')

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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Histori Patroli Karyawan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Patroli</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.patroli.index') }}">
                <div class="row gx-2 gy-2 align-items-end">
                    <div class="col-md-3">
                        <label for="nik_karyawan" class="form-label mb-1 small">NIK Karyawan:</label>
                        <input type="text" name="nik_karyawan" id="nik_karyawan" class="form-control form-control-sm" value="{{ request('nik_karyawan') }}" placeholder="Masukkan NIK">
                    </div>
                    <div class="col-md-3">
                        <label for="nama_karyawan" class="form-label mb-1 small">Nama Karyawan:</label>
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
                        <a href="{{ route('admin.patroli.index') }}" class="btn btn-secondary btn-sm w-100" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Patroli</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTablePatroli" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Karyawan (NIK)</th>
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
                                <a href="{{ route('admin.patroli.show', $patrol->_id) }}" class="btn btn-info btn-sm py-0 px-1" title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm py-0 px-1" data-bs-toggle="modal" data-bs-target="#deletePatrolModal{{ $patrol->_id }}" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                                <div class="modal fade" id="deletePatrolModal{{ $patrol->_id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $patrol->_id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $patrol->_id }}">Konfirmasi Hapus Patroli</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus data patroli ini?
                                                <br><small>Karyawan: {{ $patrol->karyawan->nama_lengkap ?? $patrol->karyawan_nik }}</small>
                                                <br><small>Mulai: {{ \Carbon\Carbon::parse($patrol->start_time)->isoFormat('D MMM YY, HH:mm') }}</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.patroli.destroy', $patrol->_id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            <div class="mt-3 d-flex justify-content-center">
                {{ $patrols->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi tooltip Bootstrap jika digunakan
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
