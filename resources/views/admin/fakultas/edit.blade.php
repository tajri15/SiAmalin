@extends('admin.layouts.app')

@section('title', 'Edit Fakultas: ' . $fakultas->nama)

@push('styles')
<style>
    .form-section {
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 5px;
        margin-bottom: 20px;
        background-color: #fdfdfd;
    }
    .program-studi-item, .detail-prodi-item {
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        margin-bottom: 10px;
        background-color: #fff;
    }
    .program-studi-item .row > div, .detail-prodi-item .row > div {
        margin-bottom: 0.5rem;
    }
    .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Fakultas: {{ $fakultas->nama }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops! Terjadi beberapa kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Data Fakultas</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fakultas.update', $fakultas->_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Fakultas <span class="text-danger">*</span></label>
                    <select class="form-select @error('nama') is-invalid @enderror" id="nama" name="nama" required>
                        <option value="">-- Pilih Nama Fakultas --</option>
                        @foreach($namaFakultasOptions as $option)
                            <option value="{{ $option }}" {{ old('nama', $fakultas->nama) == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <input type="hidden" name="tipe_fakultas" id="tipe_fakultas_hidden" value="{{ old('tipe_fakultas', $fakultas->tipe_fakultas) }}">
                 @error('tipe_fakultas')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror


                <div id="fakultas_umum_fields" class="form-section" style="display: {{ old('tipe_fakultas', $fakultas->tipe_fakultas) == 'Non-Teknik' ? 'block' : 'none' }};">
                    <h5 id="fakultas_umum_title">Data Fakultas Non-Teknik</h5>
                    <div class="mb-3">
                        <label for="koordinat_fakultas_umum" class="form-label">Koordinat Fakultas</label>
                        <input type="text" class="form-control @error('koordinat_fakultas') is-invalid @enderror" id="koordinat_fakultas_umum" name="koordinat_fakultas" value="{{ old('koordinat_fakultas', $fakultas->koordinat_fakultas) }}">
                        @error('koordinat_fakultas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="radius_fakultas_umum" class="form-label">Radius Fakultas</label>
                         <div class="input-group">
                            <input type="number" class="form-control @error('radius_fakultas') is-invalid @enderror" id="radius_fakultas_umum" name="radius_fakultas" value="{{ old('radius_fakultas', preg_replace('/[^0-9]/', '', $fakultas->radius_fakultas ?? '')) }}" placeholder="Contoh: 150" min="1">
                            <span class="input-group-text">meter</span>
                        </div>
                        @error('radius_fakultas') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <h6>Program Studi (<span id="jenjang_umum_label">S1</span>)</h6>
                    <div id="program_studi_umum_container">
                        @php
                            $programStudiUmum = old('program_studi', $fakultas->tipe_fakultas == 'Non-Teknik' ? $fakultas->program_studi : null);
                            if (is_string($programStudiUmum)) {
                                $programStudiUmum = json_decode($programStudiUmum, true) ?: [];
                            } elseif (!is_array($programStudiUmum)) {
                                $programStudiUmum = [];
                            }
                        @endphp
                        @if(count($programStudiUmum) > 0)
                            @foreach($programStudiUmum as $key => $prodi)
                            <div class="program-studi-item mb-2">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label class="form-label small">Nama Prodi</label>
                                        <input type="text" name="program_studi[{{$key}}][nama_prodi]" class="form-control form-control-sm" placeholder="Nama Program Studi" value="{{ $prodi['nama_prodi'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-prodi-umum w-100">Hapus</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add_prodi_umum_button" class="btn btn-sm btn-outline-success mt-2">Tambah Program Studi</button>
                </div>

                <div id="fakultas_teknik_fields" class="form-section" style="display: {{ old('tipe_fakultas', $fakultas->tipe_fakultas) == 'Teknik' ? 'block' : 'none' }};">
                    <h5 id="fakultas_teknik_title">Data Fakultas Teknik</h5>
                    <h6>Detail Program Studi (<span id="jenjang_teknik_label">S1</span>)</h6>
                    <div id="detail_prodi_teknik_container">
                         @php
                            $detailProdiTeknik = old('detail_prodi', $fakultas->tipe_fakultas == 'Teknik' ? $fakultas->detail_prodi : null);
                             if (is_string($detailProdiTeknik)) {
                                $detailProdiTeknik = json_decode($detailProdiTeknik, true) ?: [];
                            } elseif (!is_array($detailProdiTeknik)) {
                                $detailProdiTeknik = [];
                            }
                         @endphp
                         @if(count($detailProdiTeknik) > 0)
                            @foreach($detailProdiTeknik as $key => $prodi)
                            <div class="detail-prodi-item mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label small">Nama Prodi</label>
                                        <input type="text" name="detail_prodi[{{$key}}][nama_prodi]" class="form-control form-control-sm" placeholder="Nama Program Studi" value="{{ $prodi['nama_prodi'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label small">Koordinat</label>
                                        <input type="text" name="detail_prodi[{{$key}}][koordinat]" class="form-control form-control-sm" placeholder="-7.xxx, 110.xxx" value="{{ $prodi['koordinat'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Radius</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="detail_prodi[{{$key}}][radius]" class="form-control form-control-sm" placeholder="50" value="{{ old('detail_prodi.'.$key.'.radius', preg_replace('/[^0-9]/', '', $prodi['radius'] ?? '')) }}" min="1">
                                            <span class="input-group-text">meter</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-prodi-teknik w-100">Hapus</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add_prodi_teknik_button" class="btn btn-sm btn-outline-success mt-2">Tambah Program Studi (Teknik)</button>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.fakultas.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Fakultas</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const namaFakultasSelect = document.getElementById('nama');
    const tipeFakultasHiddenInput = document.getElementById('tipe_fakultas_hidden');
    const fakultasUmumFields = document.getElementById('fakultas_umum_fields');
    const fakultasTeknikFields = document.getElementById('fakultas_teknik_fields');
    const jenjangUmumLabel = document.getElementById('jenjang_umum_label');
    const jenjangTeknikLabel = document.getElementById('jenjang_teknik_label');
    const fakultasUmumTitle = document.getElementById('fakultas_umum_title');
    const fakultasTeknikTitle = document.getElementById('fakultas_teknik_title');

    const prodiUmumContainer = document.getElementById('program_studi_umum_container');
    const addProdiUmumButton = document.getElementById('add_prodi_umum_button');
    let prodiUmumIndex = prodiUmumContainer.querySelectorAll('.program-studi-item').length;

    const prodiTeknikContainer = document.getElementById('detail_prodi_teknik_container');
    const addProdiTeknikButton = document.getElementById('add_prodi_teknik_button');
    let prodiTeknikIndex = prodiTeknikContainer.querySelectorAll('.detail-prodi-item').length;

    function determineTipeAndJenjang() {
        const selectedNamaFakultas = namaFakultasSelect.value;
        const selectedNamaFakultasText = namaFakultasSelect.options[namaFakultasSelect.selectedIndex].text;
        let tipe = '';
        let jenjang = 'S1';

        if (selectedNamaFakultas === 'Fakultas Teknik (FT)') {
            tipe = 'Teknik';
        } else if (selectedNamaFakultas === 'Sekolah Vokasi (SV)') {
            tipe = 'Non-Teknik';
            jenjang = 'D4';
        } else if (selectedNamaFakultas) {
            tipe = 'Non-Teknik';
        }

        tipeFakultasHiddenInput.value = tipe;
        jenjangUmumLabel.textContent = jenjang;
        jenjangTeknikLabel.textContent = jenjang;

        if (tipe === 'Non-Teknik') {
            fakultasUmumFields.style.display = 'block';
            fakultasTeknikFields.style.display = 'none';
            if(selectedNamaFakultas){
                fakultasUmumTitle.textContent = 'Data ' + selectedNamaFakultasText;
            } else {
                fakultasUmumTitle.textContent = 'Data Fakultas Non-Teknik';
            }
        } else if (tipe === 'Teknik') {
            fakultasUmumFields.style.display = 'none';
            fakultasTeknikFields.style.display = 'block';
             if(selectedNamaFakultas){
                fakultasTeknikTitle.textContent = 'Data ' + selectedNamaFakultasText;
            } else {
                fakultasTeknikTitle.textContent = 'Data Fakultas Teknik';
            }
        } else {
            fakultasUmumFields.style.display = 'none';
            fakultasTeknikFields.style.display = 'none';
            fakultasUmumTitle.textContent = 'Data Fakultas Non-Teknik';
            fakultasTeknikTitle.textContent = 'Data Fakultas Teknik';
        }
    }

    addProdiUmumButton.addEventListener('click', function () {
        const newItem = `
            <div class="program-studi-item mb-2">
                <div class="row">
                    <div class="col-md-10">
                        <label class="form-label small">Nama Prodi</label>
                        <input type="text" name="program_studi[${prodiUmumIndex}][nama_prodi]" class="form-control form-control-sm" placeholder="Nama Program Studi">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-prodi-umum w-100">Hapus</button>
                    </div>
                </div>
            </div>`;
        prodiUmumContainer.insertAdjacentHTML('beforeend', newItem);
        prodiUmumIndex++;
    });

    addProdiTeknikButton.addEventListener('click', function () {
        const newItem = `
            <div class="detail-prodi-item mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label small">Nama Prodi</label>
                        <input type="text" name="detail_prodi[${prodiTeknikIndex}][nama_prodi]" class="form-control form-control-sm" placeholder="Nama Program Studi">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label class="form-label small">Koordinat</label>
                        <input type="text" name="detail_prodi[${prodiTeknikIndex}][koordinat]" class="form-control form-control-sm" placeholder="-7.xxx, 110.xxx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Radius</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="detail_prodi[${prodiTeknikIndex}][radius]" class="form-control form-control-sm" placeholder="50" min="1">
                            <span class="input-group-text">meter</span>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-prodi-teknik w-100">Hapus</button>
                    </div>
                </div>
            </div>`;
        prodiTeknikContainer.insertAdjacentHTML('beforeend', newItem);
        prodiTeknikIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('remove-prodi-umum') || e.target.classList.contains('remove-prodi-teknik'))) {
            e.target.closest('.program-studi-item, .detail-prodi-item').remove();
        }
    });

    namaFakultasSelect.addEventListener('change', determineTipeAndJenjang);
    determineTipeAndJenjang(); // Initial call
});
</script>
@endpush
@endsection
