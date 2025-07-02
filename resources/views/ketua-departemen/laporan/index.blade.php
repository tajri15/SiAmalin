@extends('admin.layouts.app')

@section('title', 'Laporan Petugas - ' . $departemen)

@push('styles')
<style>
    .table-sm th, .table-sm td { font-size: 0.85rem; padding: 0.4rem; vertical-align: middle; }
    .form-select-sm, .form-control-sm { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Laporan Petugas Keamanan</h1>
         <div>
            <span class="badge bg-primary text-white p-2">Fakultas: {{ $fakultas }}</span>
            <span class="badge bg-info text-white p-2">Departemen: {{ $departemen }}</span>
        </div>
    </div>
    <p class="mb-4">Menampilkan laporan yang dikirim oleh petugas di departemen Anda.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ketua-departemen.laporan.index') }}">
                {{-- Form filter (sama seperti Komandan) --}}
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Tgl Laporan</th>
                            <th>Jam</th>
                            <th>NIK</th>
                            <th>Nama Petugas</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php use Illuminate\Support\Str; @endphp
                        @forelse ($laporans as $index => $laporan)
                        <tr>
                            <td class="text-center">{{ $laporans->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->isoFormat('D MMM YY') }}</td>
                            <td class="text-center">{{ $laporan->jam }}</td>
                            <td>{{ $laporan->nik }}</td>
                            <td>{{ $laporan->karyawan->nama_lengkap ?? 'N/A' }}</td>
                            <td class="text-center"><span class="badge bg-info text-capitalize">{{ $laporan->jenis_laporan }}</span></td>
                            <td>{{ Str::limit($laporan->keterangan, 50) }}</td>
                            <td class="text-center">
                                @if($laporan->status_admin)
                                    <span class="badge @if($laporan->status_admin == 'Diterima') bg-success @elseif($laporan->status_admin == 'Ditolak') bg-danger @else bg-secondary @endif">
                                        {{ $laporan->status_admin }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Ditinjau</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ketua-departemen.laporan.show', $laporan->_id) }}" class="btn btn-info btn-sm py-0 px-1" title="Lihat Detail Laporan">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data laporan yang sesuai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $laporans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
