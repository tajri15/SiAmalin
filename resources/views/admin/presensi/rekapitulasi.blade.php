{{-- File: resources/views/admin/presensi/rekapitulasi.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Rekapitulasi Presensi')

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
    .form-select-sm, .form-control-sm {
        font-size: 0.875rem;
    }
    .action-buttons .btn, .action-buttons .dropdown-item {
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Rekapitulasi Presensi</h1>
    <p class="mb-4">Menampilkan data presensi karyawan berdasarkan filter yang dipilih.</p>

    {{-- Card Filter --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Presensi</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.presensi.rekapitulasi') }}">
                <div class="row gx-2 gy-2 align-items-end">
                    <div class="col-md-3">
                        <label for="bulan" class="form-label mb-1 small">Bulan:</label>
                        <select name="bulan" id="bulan" class="form-select form-select-sm">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulanIni == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $namaBulan[$b] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="tahun" class="form-label mb-1 small">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select form-select-sm">
                            @for ($t = date('Y'); $t >= date('Y') - 5; $t--)
                                <option value="{{ $t }}" {{ $tahunIni == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="nik" class="form-label mb-1 small">Username Karyawan:</label>
                        <input type="text" name="nik" id="nik" class="form-control form-control-sm" value="{{ $searchNik ?? '' }}" placeholder="Masukkan Username">
                    </div>
                     <div class="col-md-3">
                        <label for="nama" class="form-label mb-1 small">Nama Karyawan:</label>
                        <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="{{ $searchNama ?? '' }}" placeholder="Masukkan Nama">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100" title="Filter"><i class="bi bi-funnel-fill"></i></button>
                    </div>
                </div>
                 <div class="mt-3">
                    <a href="{{ route('admin.presensi.harian', ['tanggal' => date('Y-m-d')]) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-calendar-day"></i> Laporan Harian Ini
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Card Data Presensi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Presensi ({{ $namaBulan[(int)$bulanIni] }} {{ $tahunIni }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTablePresensi" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Jam Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($presensiData as $index => $data)
                        <tr> 
                            <td class="text-center">{{ $presensiData->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('dddd, D MMM YY') }}</td>
                            <td>{{ $data->nik }}</td>
                            <td>
                                @if($data->karyawan)
                                <a href="{{ route('admin.presensi.detail_karyawan', $data->karyawan->nik) }}?bulan={{$bulanIni}}&tahun={{$tahunIni}}">
                                    {{ $data->karyawan->nama_lengkap }}
                                </a>
                                @else
                                    {{ 'N/A' }}
                                @endif
                            </td>
                            <td class="text-center">{{ $data->jam_in ?? '-' }}</td>
                            <td class="text-center">
                                @if($data->foto_in == 'admin')
                                    <span class="badge bg-info">Admin</span>
                                @elseif($data->foto_in)
                                <a href="{{ asset('storage/' . $data->foto_in) }}" data-bs-toggle="tooltip" title="Lihat Foto Masuk" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_in) }}" alt="Masuk" class="img-thumbnail-xs">
                                </a>
                                @else - @endif
                            </td>
                            <td class="text-center">{{ $data->jam_out ?? '-' }}</td>
                            <td class="text-center">
                                {{-- PERBAIKAN: Menampilkan "Admin" jika foto_out berisi 'admin' --}}
                                @if($data->foto_out == 'admin')
                                    <span class="badge bg-info">Admin</span>
                                @elseif($data->foto_out)
                                <a href="{{ asset('storage/' . $data->foto_out) }}" data-bs-toggle="tooltip" title="Lihat Foto Pulang" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_out) }}" alt="Pulang" class="img-thumbnail-xs">
                                </a>
                                @else - @endif
                            </td>
                            <td class="text-center action-buttons">
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton-{{ $data->_id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $data->_id }}">
                                        <li><a class="dropdown-item" href="{{ route('admin.presensi.edit', $data->_id) }}"><i class="bi bi-pencil-square me-2"></i>Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetMasukModal-{{ $data->_id }}"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Masuk</a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPulangModal-{{ $data->_id }}"><i class="bi bi-box-arrow-left me-2"></i>Reset Pulang</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $data->_id }}"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Reset Masuk -->
                        <div class="modal fade" id="resetMasukModal-{{ $data->_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Reset Presensi Masuk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin mereset data presensi masuk untuk <strong>{{ $data->karyawan->nama_lengkap ?? $data->nik }}</strong> pada tanggal {{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('D MMM YY') }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.presensi.reset.masuk', $data->_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">Ya, Reset</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Reset Pulang -->
                        <div class="modal fade" id="resetPulangModal-{{ $data->_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Reset Presensi Pulang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin mereset data presensi pulang untuk <strong>{{ $data->karyawan->nama_lengkap ?? $data->nik }}</strong> pada tanggal {{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('D MMM YY') }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.presensi.reset.pulang', $data->_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">Ya, Reset</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div class="modal fade" id="deleteModal-{{ $data->_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus Presensi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus data presensi untuk <strong>{{ $data->karyawan->nama_lengkap ?? $data->nik }}</strong> pada tanggal {{ \Carbon\Carbon::parse($data->tgl_presensi)->isoFormat('D MMM YY') }}? Tindakan ini tidak dapat dibatalkan.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.presensi.hapus', $data->_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data presensi untuk periode dan filter ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $presensiData->appends(request()->query())->links() }}
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
