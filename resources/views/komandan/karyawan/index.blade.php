@extends('admin.layouts.app') {{-- Sementara menggunakan layout admin --}}

@section('title', 'Data Petugas Keamanan Fakultas ' . $fakultasKomandan)

@push('styles')
<style>
    .table-sm th, .table-sm td {
        font-size: 0.85rem;
        padding: 0.5rem;
        vertical-align: middle;
    }
    .img-thumbnail-xs {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 0.25rem; /* Sedikit rounded corner */
    }
    .card-header .form-control-sm {
        max-width: 300px; /* Batasi lebar search bar */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Data Petugas Keamanan - {{ $fakultasKomandan }}</h1>
        {{-- Komandan mungkin tidak bisa menambah karyawan, tergantung kebutuhan --}}
        {{-- <a href="#" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Petugas (Jika Diperlukan)
        </a> --}}
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Petugas</h6>
            <form action="{{ route('komandan.karyawan.index') }}" method="GET" class="d-inline-block">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK atau Nama..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                    <a href="{{ route('komandan.karyawan.index') }}" class="btn btn-outline-danger" title="Reset Pencarian"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTableKaryawanKomandan" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>No. HP</th>
                            <th>Foto</th>
                            <th>Program Studi</th>
                            <th>Status Wajah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawans as $index => $karyawan)
                        <tr>
                            <td class="text-center">{{ $karyawans->firstItem() + $index }}</td>
                            <td>{{ $karyawan->nik }}</td>
                            <td>{{ $karyawan->nama_lengkap }}</td>
                            <td>{{ $karyawan->jabatan }}</td>
                            <td>{{ $karyawan->no_hp }}</td>
                            <td class="text-center">
                                @if($karyawan->foto)
                                    <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto {{ $karyawan->nama_lengkap }}" class="img-thumbnail-xs">
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $karyawan->departemen ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($karyawan->face_embedding)
                                    <span class="badge bg-success">Terdaftar</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data petugas keamanan di fakultas ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $karyawans->appends(request()->query())->links() }}
            </div>
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
</script>
@endpush
