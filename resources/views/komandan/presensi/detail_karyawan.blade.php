@extends('admin.layouts.app')

@section('title', 'Detail Presensi Petugas: ' . $karyawan->nama_lengkap)

@push('styles')
<style>
    .table-sm th, .table-sm td { font-size: 0.85rem; padding: 0.4rem; vertical-align: middle; }
    .img-thumbnail-xs { width: 40px; height: 40px; object-fit: cover; border-radius: 0.25rem; }
    .form-control-sm, .form-select-sm { font-size: 0.875rem; }
    .profile-header { background-color: #f8f9fc; padding: 1.5rem; border-radius: .5rem; margin-bottom: 1.5rem; border: 1px solid #e3e6f0; }
    .profile-header img { width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); }
    .profile-header h4 { margin-bottom: 0.25rem; font-weight: 600; }
    .profile-header p { margin-bottom: 0; font-size: 0.9rem; color: #5a5c69; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Detail Presensi Petugas</h1>
        <a href="{{ route('komandan.presensi.rekapitulasi') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekapitulasi
        </a>
    </div>

    <div class="profile-header">
        <div class="d-flex align-items-center">
            <img src="{{ $karyawan->foto ? asset('storage/' . $karyawan->foto) : asset('assets/img/sample/avatar/avatar1.jpg') }}" 
                 alt="Foto {{ $karyawan->nama_lengkap }}" class="rounded-circle me-3"
                 onerror="this.onerror=null;this.src='{{ asset('assets/img/sample/avatar/avatar1.jpg') }}';">
            <div>
                <h4>{{ $karyawan->nama_lengkap }}</h4>
                <p class="mb-0">Username: {{ $karyawan->nik }}</p>
                <p>Jabatan: {{ $karyawan->jabatan }}</p>
                <p>Fakultas: {{ $fakultasKomandan }}</p>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Periode Presensi</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('komandan.presensi.detail_karyawan', $karyawan->nik) }}">
                <div class="row align-items-end gx-2">
                    <div class="col-md-4 mb-3">
                        <label for="bulan" class="form-label mb-1 small">Bulan:</label>
                        <select name="bulan" id="bulan" class="form-select form-select-sm">
                            @for ($b = 1; $b <= 12; $b++)
                                <option value="{{ str_pad($b, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($b, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $namaBulan[$b] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tahun" class="form-label mb-1 small">Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select form-select-sm">
                            @for ($t = date('Y'); $t >= date('Y') - 5; $t--)
                                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endfor
                        </select>
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
            <h6 class="m-0 font-weight-bold text-primary">Histori Presensi - {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTableDetailPresensiKomandan" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Lokasi Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Lokasi Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiPresensi as $index => $data)
                        @php $tanggalCarbon = \Carbon\Carbon::parse($data->tgl_presensi); @endphp
                        <tr> 
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tanggalCarbon->isoFormat('D MMM YY') }}</td>
                            <td>{{ $tanggalCarbon->isoFormat('dddd') }}</td>
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
                            <td><small>{{ $data->lokasi_in == 'admin' ? 'Admin' : ($data->lokasi_in ?? '-') }}</small></td>
                            <td class="text-center">{{ $data->jam_out ?? '-' }}</td>
                            <td class="text-center">
                                @if($data->foto_out == 'admin')
                                    <span class="badge bg-info">Admin</span>
                                @elseif($data->foto_out)
                                <a href="{{ asset('storage/' . $data->foto_out) }}" data-bs-toggle="tooltip" title="Lihat Foto Pulang" target="_blank">
                                    <img src="{{ asset('storage/' . $data->foto_out) }}" alt="Pulang" class="img-thumbnail-xs">
                                </a>
                                @else - @endif
                            </td>
                            <td><small>{{ $data->lokasi_out == 'admin' ? 'Admin' : ($data->lokasi_out ?? '-') }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data presensi untuk periode ini.</td>
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
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush