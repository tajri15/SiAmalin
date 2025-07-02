@extends('admin.layouts.app')

@section('title', 'Data Fakultas')

@push('styles')
<style>
    .card-fakultas {
        border: 1px solid #e0e0e0;
        transition: box-shadow 0.2s ease-in-out;
        background-color: #fff;
        margin-bottom: 1rem;
    }
    .card-fakultas:hover {
        box-shadow: 0 .25rem .75rem rgba(0,0,0,.08)!important;
    }
    .card-header-fakultas {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .card-header-fakultas h6 {
        font-size: 0.95rem;
        color: #333;
        margin-bottom: 0;
    }
    .card-body-fakultas {
        padding: 1rem;
        font-size: 0.85rem;
    }
    .card-body-fakultas h6.text-secondary {
        font-size: 0.8rem;
        color: #555 !important;
        margin-top: 0.75rem;
        margin-bottom: 0.4rem;
        font-weight: 500;
    }
    .list-prodi {
        padding-left: 0;
        list-style: none;
        margin-bottom: 0.75rem;
    }
    .list-prodi li {
        padding: .2rem 0;
        color: #454545;
    }
    .list-prodi li:not(:last-child) {
         border-bottom: 1px solid #f0f0f0;
    }

    .badge-jenjang {
        width: auto;
        padding: 0.2em 0.5em;
        font-size: 0.75em;
        font-weight: 500;
    }
    .prodi-teknik-item {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
    }
    .prodi-teknik-item:not(:last-child) {
        border-bottom: 1px solid #f0f0f0;
    }
    .prodi-teknik-item .prodi-name {
        font-weight: 500;
        color: #333;
        display: block;
        margin-bottom: 0.25rem;
    }
    .info-lokasi-prodi {
        margin-top: 0.5rem;
        padding-left: 0;
    }
    .info-lokasi-prodi p {
        margin-bottom: 0.1rem;
    }
    .info-lokasi-prodi small.text-muted {
        font-size: 0.8rem;
        color: #666 !important;
        line-height: 1.4;
    }
    hr.my-3 {
        margin-top: 0.75rem !important;
        margin-bottom: 0.75rem !important;
        border-top: 1px solid #eee;
    }
    .collapse-icon::after {
        content: '\f282'; /* Bootstrap Icons chevron-down */
        font-family: 'bootstrap-icons';
        display: inline-block;
        margin-left: auto;
        transition: transform 0.2s ease-in-out;
        font-size: 0.9rem;
    }
    .collapse-icon[aria-expanded="true"]::after {
        transform: rotate(-180deg);
    }
    .action-buttons a, .action-buttons button {
        margin-left: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800" style="font-size: 1.5rem;">Data Fakultas</h1>
        <a href="{{ route('admin.fakultas.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Fakultas
        </a>
    </div>

    <div class="accordion" id="accordionFakultas">
        @forelse ($fakultasData as $index => $fakultas)
        <div class="card card-fakultas">
            <div class="card-header-fakultas" id="heading{{ $fakultas->_id }}" data-bs-toggle="collapse" data-bs-target="#collapse{{ $fakultas->_id }}" aria-expanded="false" aria-controls="collapse{{ $fakultas->_id }}">
                <h6 class="m-0 font-weight-bold">{{ $index + 1 }}. {{ $fakultas->nama }}</h6>
                <div class="action-buttons">
                    <a href="{{ route('admin.fakultas.edit', $fakultas->_id) }}" class="btn btn-sm btn-outline-warning py-0 px-1" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" title="Hapus"
                            data-bs-toggle="modal" data-bs-target="#deleteFakultasModal{{ $fakultas->_id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                    <span class="collapse-icon ms-2"></span>
                </div>
            </div>

            <div id="collapse{{ $fakultas->_id }}" class="collapse" aria-labelledby="heading{{ $fakultas->_id }}" data-bs-parent="#accordionFakultas">
                <div class="card-body card-body-fakultas">
                    @if ($fakultas->tipe_fakultas === 'Teknik')
                        <h6 class="text-secondary">Program Studi:</h6>
                        @php $detailProdi = $fakultas->detail_prodi; @endphp
                        @if(is_array($detailProdi) && count($detailProdi) > 0)
                            @foreach ($detailProdi as $prodi)
                            <div class="prodi-teknik-item">
                                <div class="prodi-name">
                                    <span class="badge bg-info badge-jenjang me-2">{{ $prodi['jenjang'] ?? 'S1' }}</span> {{ $prodi['nama_prodi'] ?? 'N/A' }}
                                </div>
                                <div class="info-lokasi-prodi">
                                    <p class="card-text mb-1"><small class="text-muted">Koordinat: {{ $prodi['koordinat'] ?? 'N/A' }}</small></p>
                                    <p class="card-text"><small class="text-muted">Radius: {{ $prodi['radius'] ?? 'N/A' }}</small></p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted small"><em>Belum ada program studi.</em></p>
                        @endif
                    @else {{-- Untuk fakultas tipe 'Non-Teknik' --}}
                        <h6 class="text-secondary">Program Studi:</h6>
                        @php $programStudi = $fakultas->program_studi; @endphp
                        @if(is_array($programStudi) && count($programStudi) > 0)
                        <ul class="list-prodi">
                            @foreach ($programStudi as $prodi)
                            <li>
                                <span class="badge bg-info badge-jenjang me-2">{{ $prodi['jenjang'] ?? 'N/A' }}</span> {{ $prodi['nama_prodi'] ?? 'N/A' }}
                            </li>
                            @endforeach
                        </ul>
                        @else
                            <p class="text-muted small"><em>Belum ada program studi.</em></p>
                        @endif

                        @if(isset($fakultas->koordinat_fakultas) && isset($fakultas->radius_fakultas))
                        <hr class="my-3">
                        <h6 class="text-secondary">Informasi Lokasi Fakultas:</h6>
                        <p class="card-text mb-1"><small class="text-muted">Koordinat: {{ $fakultas->koordinat_fakultas }}</small></p>
                        <p class="card-text"><small class="text-muted">Radius: {{ $fakultas->radius_fakultas }}</small></p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteFakultasModal{{ $fakultas->_id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $fakultas->_id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $fakultas->_id }}">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus fakultas <strong>{{ $fakultas->nama }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('admin.fakultas.destroy', $fakultas->_id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light text-center border">
                Belum ada data fakultas yang tersedia. Klik "Tambah Fakultas" untuk memulai.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
