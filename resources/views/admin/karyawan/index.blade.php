@extends('admin.layouts.app')

@section('title', 'Manajemen Karyawan')

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
        border-radius: 0.25rem;
    }
    .card-header .form-control-sm {
        max-width: 300px;
    }
    .sortable-link {
        color: inherit;
        text-decoration: none;
    }
    .sortable-link:hover {
        color: #4e73df;
    }
    .sortable-link .bi {
        font-size: 1.1em;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Daftar Karyawan</h1>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Karyawan
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Seluruh Karyawan</h6>
            <form action="{{ route('admin.karyawan.index') }}" method="GET" class="d-inline-block">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama, Jabatan..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-danger" title="Reset Pencarian"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTableKaryawan" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>
                                <a href="{{ route('admin.karyawan.index', ['sort_by' => 'nik', 'sort_order' => $sortBy == 'nik' && $sortOrder == 'asc' ? 'desc' : 'asc'] + request()->except(['sort_by', 'sort_order'])) }}" class="sortable-link" data-bs-toggle="tooltip" title="Urutkan berdasarkan NIK">
                                    NIK
                                    @if($sortBy == 'nik')
                                        <i class="bi bi-sort-numeric-{{ $sortOrder == 'asc' ? 'down' : 'up-alt' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.karyawan.index', ['sort_by' => 'nama_lengkap', 'sort_order' => $sortBy == 'nama_lengkap' && $sortOrder == 'asc' ? 'desc' : 'asc'] + request()->except(['sort_by', 'sort_order'])) }}" class="sortable-link" data-bs-toggle="tooltip" title="Urutkan berdasarkan Nama">
                                    Nama Lengkap
                                    @if($sortBy == 'nama_lengkap')
                                        <i class="bi bi-sort-alpha-{{ $sortOrder == 'asc' ? 'down' : 'up-alt' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.karyawan.index', ['sort_by' => 'jabatan', 'sort_order' => $sortBy == 'jabatan' && $sortOrder == 'asc' ? 'desc' : 'asc'] + request()->except(['sort_by', 'sort_order'])) }}" class="sortable-link" data-bs-toggle="tooltip" title="Urutkan berdasarkan Jabatan">
                                    Jabatan
                                    @if($sortBy == 'jabatan')
                                        <i class="bi bi-sort-alpha-{{ $sortOrder == 'asc' ? 'down' : 'up-alt' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>No. HP</th>
                            <th>Foto</th>
                            <th>Unit Kerja (Fakultas & Prodi)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawans as $index => $karyawan)
                        <tr>
                            <td class="text-center">{{ $karyawans->firstItem() + $index }}</td>
                            <td>{{ $karyawan->nik }}</td>
                            <td>{{ $karyawan->nama_lengkap }}</td>
                            <td class="text-center">
                                @if($karyawan->is_admin)
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($karyawan->is_komandan)
                                    <span class="badge bg-info">Komandan</span>
                                @elseif($karyawan->is_ketua_departemen)
                                    <span class="badge bg-warning text-dark">Ketua Departemen</span>
                                @else
                                    <span class="badge bg-success">Petugas Keamanan</span>
                                @endif
                            </td>
                            <td>{{ $karyawan->no_hp }}</td>
                            <td class="text-center">
                                @if($karyawan->foto)
                                    <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto {{ $karyawan->nama_lengkap }}" class="img-thumbnail-xs">
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($karyawan->is_admin)
                                    <span class="text-muted fst-italic">N/A</span>
                                @elseif($karyawan->is_komandan)
                                    Fakultas: {{ $karyawan->unit ?? 'N/A' }}
                                @elseif($karyawan->is_ketua_departemen)
                                    Fakultas: {{ $karyawan->unit ?? 'N/A' }} <br>
                                    Prodi: {{ $karyawan->departemen ?? 'N/A' }}
                                @elseif($karyawan->jabatan == 'Petugas Keamanan')
                                    Fakultas: {{ $karyawan->unit ?? 'N/A' }} <br>
                                    Prodi: {{ $karyawan->departemen ?? 'N/A' }}
                                    @if($karyawan->office_radius)
                                        <br><small class="text-muted">Radius: {{ $karyawan->office_radius }} meter</small>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.karyawan.show', $karyawan->_id) }}" class="btn btn-info btn-sm py-0 px-1 my-1" title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.karyawan.edit', $karyawan->_id) }}" class="btn btn-warning btn-sm py-0 px-1 my-1" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm py-0 px-1 my-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $karyawan->_id }}" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                                <div class="modal fade" id="deleteModal{{ $karyawan->_id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $karyawan->_id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $karyawan->_id }}">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                Apakah Anda yakin ingin menghapus karyawan <strong>{{ $karyawan->nama_lengkap }}</strong> (NIK: {{ $karyawan->nik }})?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.karyawan.destroy', $karyawan->_id) }}" method="POST" style="display: inline;">
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
                            <td colspan="8" class="text-center">Belum ada data karyawan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $karyawans->appends(request()->except('page'))->links() }}
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
