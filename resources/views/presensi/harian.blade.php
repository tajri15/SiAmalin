@extends('admin.layouts.app')

@section('title', 'Laporan Presensi Harian')

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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Laporan Presensi Harian</h1>
        <a href="{{ route('admin.presensi.rekapitulasi') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekap
        </a>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Tanggal</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.presensi.harian') }}">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal" class="form-label">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel-fill"></i> Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Presensi Tanggal: {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr>
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
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data->nik }}</td>
                            <td>{{ $data->karyawan->nama_lengkap ?? 'N/A' }}</td>
                            <td>{{ $data->karyawan->jabatan ?? 'N/A' }}</td>
                            <td class="{{ $data->jam_in > '07:00:00' && $data->jam_in ? 'table-warning' : '' }}">{{ $data->jam_in ?? '-' }}</td>
                            <td>
                                @if($data->foto_in)
                                <a href="{{ asset('storage/' . $data->foto_in) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_in) }}" alt="Foto Masuk" class="img-thumbnail-xs">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td><small>{{ $data->lokasi_in ?? '-' }}</small></td>
                            <td>{{ $data->jam_out ?? '-' }}</td>
                            <td>
                                @if($data->foto_out)
                                <a href="{{ asset('storage/' . $data->foto_out) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_out) }}" alt="Foto Pulang" class="img-thumbnail-xs">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td><small>{{ $data->lokasi_out ?? '-' }}</small></td>
                            <td>
                                <a href="{{ route('admin.presensi.edit', $data->_id) }}" class="btn btn-warning btn-sm py-0 px-1" title="Edit Presensi">
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
