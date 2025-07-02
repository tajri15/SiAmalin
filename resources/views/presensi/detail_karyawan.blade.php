@extends('admin.layouts.app')

@section('title', 'Detail Presensi Karyawan: ' . $karyawan->nama_lengkap)

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
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Presensi: {{ $karyawan->nama_lengkap }}</h1>
            <p class="mb-0 text-muted">NIK: {{ $karyawan->nik }} - Jabatan: {{ $karyawan->jabatan }}</p>
        </div>
        <a href="{{ route('admin.presensi.rekapitulasi') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekap
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Periode</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.presensi.detail_karyawan', $karyawan->nik) }}">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="bulan" class="form-label">Bulan:</label>
                        <select name="bulan" id="bulan" class="form-select form-select-sm">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $namaBulan[$b] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tahun" class="form-label">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select form-select-sm">
                            @for ($t = date('Y'); $t >= date('Y') - 5; $t--)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endfor
                        </select>
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
            <h6 class="m-0 font-weight-bold text-primary">Histori Presensi ({{ $namaBulan[(int)$bulan] }} {{ $tahun }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
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
                        @forelse ($historiPresensi as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('dddd') }}</td>
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
                            <td colspan="10" class="text-center">Tidak ada data presensi untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
