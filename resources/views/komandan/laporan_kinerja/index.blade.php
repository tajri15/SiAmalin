@extends('admin.layouts.app')

@section('title', 'Laporan Kinerja Bulanan - Fakultas ' . $fakultasKomandan)

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
    .text-success { color: #1cc88a !important; }
    .text-primary { color: #4e73df !important; }
    .text-muted-light { color: #858796 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Laporan Kinerja Bulanan Petugas</h1>
        <span class="badge bg-info text-white p-2">Fakultas: {{ $fakultasKomandan }}</span>
    </div>
    <p class="mb-4">Menampilkan ringkasan kinerja bulanan petugas keamanan di fakultas Anda.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('komandan.laporankinerja.index') }}">
                <div class="row gx-2 gy-2 align-items-end">
                    <div class="col-md-3">
                        <label for="bulan" class="form-label mb-1 small">Bulan:</label>
                        <select name="bulan" id="bulan" class="form-select form-select-sm">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $namaBulan[$b] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tahun" class="form-label mb-1 small">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select form-select-sm">
                            @for ($t = date('Y'); $t >= date('Y') - 5; $t--)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Tampilkan</button>
                    </div>
                     <div class="col-md-2">
                        <a href="{{ route('komandan.laporankinerja.index') }}" class="btn btn-secondary btn-sm w-100"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                    {{-- Tombol Cetak Baru --}}
                    <div class="col-md-2">
                        <a href="{{ route('komandan.laporankinerja.cetak', request()->query()) }}" target="_blank" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-printer-fill"></i> Cetak
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Laporan Kinerja - {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="tableLaporanKinerja" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Petugas</th>
                            <th>Hari Kerja Terjadwal</th>
                            <th>Hari Hadir Aktual</th>
                            <th>Persentase Kehadiran</th>
                            <th>Total Jam Kerja Terjadwal</th>
                            <th>Total Jam Kerja Aktual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanKinerjaData as $index => $data)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data['nik'] }}</td>
                            <td>{{ $data['nama_lengkap'] }}</td>
                            <td class="text-center">{{ $data['jumlah_hari_kerja_terjadwal'] }} hari</td>
                            <td class="text-center">{{ $data['jumlah_hari_hadir'] }} hari</td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px; font-size: 0.75rem;">
                                    <div class="progress-bar bg-{{ $data['persentase_kehadiran'] >= 80 ? 'success' : ($data['persentase_kehadiran'] >= 60 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $data['persentase_kehadiran'] }}%;" 
                                         aria-valuenow="{{ $data['persentase_kehadiran'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $data['persentase_kehadiran'] }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $data['total_jam_kerja_jadwal_format'] }}</td>
                            <td class="text-center">{{ $data['total_jam_kerja_aktual_format'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data kinerja untuk ditampilkan pada periode ini.</td>
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
{{-- Script tambahan jika diperlukan, misal untuk export atau sorting tabel client-side --}}
@endpush