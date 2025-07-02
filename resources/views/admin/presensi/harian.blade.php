@extends('admin.layouts.app')

@section('title', 'Laporan Presensi Harian')

@push('styles')
<style>
    .table-sm th, .table-sm td {
        font-size: 0.85rem;
        padding: 0.4rem;
        vertical-align: middle;
    }
    .img-thumbnail-xs {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    .form-control-sm, .form-select-sm {
        font-size: 0.875rem;
    }
    /* Menghapus highlight untuk keterlambatan jika tidak diperlukan lagi */
    /* .table-warning td {
        background-color: #fff3cd !important; 
    } */
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Laporan Presensi Harian</h1>
        <a href="{{ route('admin.presensi.rekapitulasi') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekapitulasi Bulanan
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pilih Tanggal Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.presensi.harian') }}">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal" class="form-label mb-1 small">Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ $tanggal ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Presensi Tanggal: {{ \Carbon\Carbon::parse($tanggal ?? date('Y-m-d'))->isoFormat('dddd, D MMMM<x_bin_534>') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTablePresensiHarian" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Jam Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Lokasi Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Lokasi Pulang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($presensiHarian as $index => $data)
                        {{-- Menghapus class 'table-warning' untuk keterlambatan --}}
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data->nik }}</td>
                            <td>
                                @if($data->karyawan)
                                    <a href="{{ route('admin.karyawan.show', $data->karyawan->_id) }}" target="_blank">
                                        {{ $data->karyawan->nama_lengkap }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $data->karyawan->jabatan ?? 'N/A' }}</td>
                            <td class="text-center">{{ $data->jam_in ?? '-' }}</td>
                            <td class="text-center">
                                @if($data->foto_in)
                                <a href="{{ asset('storage/' . $data->foto_in) }}" data-bs-toggle="tooltip" title="Lihat Foto Masuk" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_in) }}" alt="Masuk" class="img-thumbnail-xs">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td><small>{{ $data->lokasi_in ?? '-' }}</small></td>
                            <td class="text-center">{{ $data->jam_out ?? '-' }}</td>
                            <td class="text-center">
                                @if($data->foto_out)
                                <a href="{{ asset('storage/' . $data->foto_out) }}" data-bs-toggle="tooltip" title="Lihat Foto Pulang" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_out) }}" alt="Pulang" class="img-thumbnail-xs">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td><small>{{ $data->lokasi_out ?? '-' }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('admin.presensi.edit', $data->_id) }}" class="btn btn-warning btn-sm py-0 px-1" data-bs-toggle="tooltip" title="Edit Presensi">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada data presensi untuk tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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