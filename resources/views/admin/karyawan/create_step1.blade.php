@extends('admin.layouts.app')

@section('title', 'Tambah Karyawan Baru - Step 1')

@push('styles')
<style>
    .form-section { margin-top: 1.5rem; }
    .form-section-title { margin-bottom: 1rem; font-weight: 500; color: #4e73df; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Karyawan Baru - Data Diri</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
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
            <h6 class="m-0 font-weight-bold text-primary">Form Data Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.karyawan.store_step1') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nik" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" required>
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('jabatan') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section for Non-Admin roles --}}
                <div id="fakultas_section" class="form-section" style="display: none;">
                    <h5 class="form-section-title">Detail Penugasan</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fakultas_nama" class="form-label">Fakultas <span id="fakultas_required_star" class="text-danger" style="display:none;">*</span></label>
                                <select class="form-select @error('fakultas_nama') is-invalid @enderror" id="fakultas_nama" name="fakultas_nama">
                                    <option value="">-- Pilih Fakultas --</option>
                                    @foreach($fakultasOptions as $namaFakultas)
                                        <option value="{{ $namaFakultas }}" {{ old('fakultas_nama') == $namaFakultas ? 'selected' : '' }}>
                                            {{ $namaFakultas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fakultas_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6" id="program_studi_field" style="display: none;">
                            <div class="mb-3">
                                <label for="program_studi_nama" class="form-label">Program Studi <span id="prodi_required_star" class="text-danger" style="display:none;">*</span></label>
                                <select class="form-select @error('program_studi_nama') is-invalid @enderror" id="program_studi_nama" name="program_studi_nama">
                                    <option value="">-- Pilih Fakultas Terlebih Dahulu --</option>
                                </select>
                                @error('program_studi_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6" id="office_radius_field" style="display: none;">
                            <div class="mb-3">
                                <label for="office_radius" class="form-label">Radius Kantor (meter) <span id="radius_required_star" class="text-danger" style="display:none;">*</span></label>
                                <input type="number" class="form-control @error('office_radius') is-invalid @enderror" id="office_radius" name="office_radius" value="{{ old('office_radius', 100) }}" min="10" readonly>
                                @error('office_radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Akan terisi otomatis berdasarkan Fakultas/Prodi.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-6" id="foto_profil_field_container">
                        <div class="mb-3" id="foto_profil_field" style="display: none;">
                            <label for="foto" class="form-label">Foto Profil (Opsional)</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                
                <div id="admin_specific_fields" class="form-section" style="display:none;">
                     <h5 class="form-section-title">Pengaturan Admin</h5>
                     <div class="alert alert-info small">Admin tidak memerlukan data fakultas, program studi, radius, atau registrasi wajah.</div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" id="submitButton">Lanjut</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jabatanSelect = document.getElementById('jabatan');
    const fakultasSection = document.getElementById('fakultas_section');
    const fakultasSelect = document.getElementById('fakultas_nama');
    const fakultasRequiredStar = document.getElementById('fakultas_required_star');
    const prodiField = document.getElementById('program_studi_field');
    const prodiSelect = document.getElementById('program_studi_nama');
    const prodiRequiredStar = document.getElementById('prodi_required_star');
    const radiusField = document.getElementById('office_radius_field');
    const radiusInput = document.getElementById('office_radius');
    const radiusRequiredStar = document.getElementById('radius_required_star');
    const fotoProfilField = document.getElementById('foto_profil_field');
    const adminSpecificFields = document.getElementById('admin_specific_fields');
    const submitButton = document.getElementById('submitButton');

    function toggleFields() {
        const selectedJabatan = jabatanSelect.value;

        // Reset all conditional elements
        fakultasSection.style.display = 'none';
        fakultasRequiredStar.style.display = 'none';
        fakultasSelect.required = false;

        prodiField.style.display = 'none';
        prodiRequiredStar.style.display = 'none';
        prodiSelect.required = false;
        
        radiusField.style.display = 'none';
        radiusRequiredStar.style.display = 'none';
        radiusInput.required = false;
        
        fotoProfilField.style.display = 'none';
        adminSpecificFields.style.display = 'none';
        submitButton.textContent = 'Lanjut';

        if (selectedJabatan === 'Admin') {
            adminSpecificFields.style.display = 'block';
            submitButton.textContent = 'Simpan Data Admin';
        } else if (selectedJabatan === 'Komandan') {
            fakultasSection.style.display = 'block';
            fakultasRequiredStar.style.display = 'inline';
            fakultasSelect.required = true;
            fotoProfilField.style.display = 'block';
            submitButton.textContent = 'Simpan Data Komandan';
        } else if (selectedJabatan === 'Ketua Departemen') {
            fakultasSection.style.display = 'block';
            fakultasRequiredStar.style.display = 'inline';
            fakultasSelect.required = true;
            prodiField.style.display = 'block';
            prodiRequiredStar.style.display = 'inline';
            prodiSelect.required = true;
            fotoProfilField.style.display = 'block';
            submitButton.textContent = 'Simpan Data Ketua Departemen';
        } else if (selectedJabatan === 'Petugas Keamanan') {
            fakultasSection.style.display = 'block';
            fakultasRequiredStar.style.display = 'inline';
            fakultasSelect.required = true;
            prodiField.style.display = 'block';
            prodiRequiredStar.style.display = 'inline';
            prodiSelect.required = true;
            radiusField.style.display = 'block';
            radiusRequiredStar.style.display = 'inline';
            radiusInput.required = true;
            fotoProfilField.style.display = 'block';
            submitButton.textContent = 'Lanjut ke Registrasi Wajah';
        }
        
        if (fakultasSelect.value) {
            fakultasSelect.dispatchEvent(new Event('change'));
        } else {
            clearProdiOptions(); 
        }
    }

    function clearProdiOptions(message = '-- Pilih Program Studi --') {
        prodiSelect.innerHTML = `<option value="">${message}</option>`;
        if (jabatanSelect.value === 'Petugas Keamanan') { 
            // radiusInput.value = ''; // Don't reset if there's an old value
        }
    }

    function populateProdi(programStudiData, selectedProdiNama = '') {
        clearProdiOptions();
        if (programStudiData && programStudiData.length > 0) {
            prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
            programStudiData.forEach(function(prodi) {
                const option = document.createElement('option');
                option.value = prodi.nama_prodi;
                option.textContent = prodi.nama_prodi;
                if (prodi.nama_prodi === selectedProdiNama) {
                    option.selected = true;
                }
                if (prodi.radius) option.dataset.radius = String(prodi.radius).replace(/\D/g, '');
                prodiSelect.appendChild(option);
            });
        } else {
            prodiSelect.innerHTML = '<option value="">-- Tidak ada Program Studi --</option>';
        }
    }

    fakultasSelect.addEventListener('change', function() {
        const selectedFakultasNama = this.value;
        clearProdiOptions(); 

        if (selectedFakultasNama) {
            prodiSelect.innerHTML = '<option value="">Loading...</option>';
            fetch(`/panel/fakultas/get-details-for-karyawan/${encodeURIComponent(selectedFakultasNama)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        clearProdiOptions('Error memuat prodi');
                        return;
                    }
                    
                    let prodiListToPopulate = (data.tipe_fakultas === 'Teknik')
                        ? data.program_studi.map(p => ({nama_prodi: p.nama_prodi, radius: p.radius, koordinat: p.koordinat}))
                        : data.program_studi.map(p => ({nama_prodi: p.nama_prodi}));
                    
                    if (jabatanSelect.value === 'Petugas Keamanan' && data.tipe_fakultas !== 'Teknik') {
                         radiusInput.value = data.radius_fakultas || radiusInput.value; 
                    }
                    populateProdi(prodiListToPopulate, "{{ old('program_studi_nama') }}");
                })
                .catch(error => {
                    console.error('Error fetching fakultas details:', error);
                    clearProdiOptions('Gagal memuat prodi');
                });
        }
    });

    prodiSelect.addEventListener('change', function() {
        if (jabatanSelect.value === 'Petugas Keamanan') {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.radius) {
                radiusInput.value = selectedOption.dataset.radius;
            } else if (!selectedOption.value) { 
                radiusInput.value = ''; 
            }
        }
    });

    jabatanSelect.addEventListener('change', toggleFields);
    // Initial call to set form state based on old input or default
    toggleFields(); 
    
    // Trigger change if there's an old value for fakultas to populate prodi
    if (fakultasSelect.value) {
        fakultasSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
