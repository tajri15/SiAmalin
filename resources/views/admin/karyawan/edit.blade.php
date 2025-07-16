@extends('admin.layouts.app')

@section('title', 'Edit Karyawan')

@push('styles')
<style>
    .form-section { margin-top: 1.5rem; }
    .form-section-title { margin-bottom: 1rem; font-weight: 500; color: #4e73df; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Data Karyawan: {{ $karyawan->nama_lengkap }}</h1>

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
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Data Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.karyawan.update', $karyawan->_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nik" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $karyawan->nik) }}" required>
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
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
                                    <option value="{{ $value }}" {{ old('jabatan', $karyawan->jabatan) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" required>
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
                                        <option value="{{ $namaFakultas }}" {{ old('fakultas_nama', $karyawan->unit) == $namaFakultas ? 'selected' : '' }}>
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
                                     {{-- Options will be populated by JS, but we add the old value if available --}}
                                     @if(old('program_studi_nama', $karyawan->departemen))
                                        <option value="{{ old('program_studi_nama', $karyawan->departemen) }}" selected>{{ old('program_studi_nama', $karyawan->departemen) }}</option>
                                     @endif
                                </select>
                                @error('program_studi_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="office_radius_field" style="display: none;">
                            <div class="mb-3">
                                <label for="office_radius" class="form-label">Radius Kantor (meter) <span id="radius_required_star" class="text-danger" style="display:none;">*</span></label>
                                <input type="number" class="form-control @error('office_radius') is-invalid @enderror" id="office_radius" name="office_radius" value="{{ old('office_radius', $karyawan->office_radius) }}" min="10" readonly>
                                @error('office_radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Akan terisi otomatis berdasarkan Fakultas/Prodi.</small>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" id="foto_profil_field_container">
                        <div class="mb-3" id="foto_profil_field" style="display: none;"> 
                            <label for="foto" class="form-label">Foto Profil (Opsional)</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                            @if($karyawan->foto)
                                <small class="form-text text-muted mt-1 d-block">Foto saat ini: <a href="{{ asset('storage/' . $karyawan->foto) }}" target="_blank">Lihat Foto</a>. Upload baru untuk mengganti.</small>
                            @endif
                            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                
                <div id="admin_specific_fields" class="form-section" style="display:none;">
                     <h5 class="form-section-title">Pengaturan Admin</h5>
                     <div class="alert alert-info small">Admin tidak memerlukan data fakultas, program studi, atau radius. Registrasi wajah juga tidak diperlukan. Foto profil bersifat opsional.</div>
                </div>
                
                <div id="komandan_specific_fields" class="form-section" style="display:none;">
                     <h5 class="form-section-title">Pengaturan Komandan</h5>
                     <div class="alert alert-info small">Komandan hanya memerlukan data fakultas. Program studi, radius, dan data wajah tidak digunakan. Foto profil opsional.</div>
                </div>
                
                 <div id="ketua_departemen_specific_fields" class="form-section" style="display:none;">
                     <h5 class="form-section-title">Pengaturan Ketua Departemen</h5>
                     <div class="alert alert-info small">Ketua Departemen memerlukan data fakultas dan program studi. Radius dan data wajah tidak digunakan. Foto profil opsional.</div>
                </div>

                <div id="petugas_face_reset_field" class="form-section" style="display:none;">
                    <h5 class="form-section-title">Pengaturan Wajah (Petugas Keamanan)</h5>
                    @if($karyawan->face_embedding)
                        <p class="text-success"><i class="bi bi-check-circle-fill"></i> Data wajah sudah terdaftar.</p>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetFaceModal">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Data Wajah
                        </button>
                        <small class="d-block text-muted mt-1">Jika direset, karyawan perlu melakukan registrasi wajah kembali.</small>
                    @else
                        <p class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Data wajah belum terdaftar.</p>
                        <small class="text-muted">Karyawan ini perlu melakukan registrasi wajah melalui aplikasi mobile atau fitur khusus jika tersedia.</small>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Wajah -->
@if($karyawan->face_embedding && $karyawan->jabatan == 'Petugas Keamanan')
<div class="modal fade" id="resetFaceModal" tabindex="-1" aria-labelledby="resetFaceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetFaceModalLabel">Konfirmasi Reset Data Wajah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin mereset data wajah untuk karyawan <strong>{{ $karyawan->nama_lengkap }}</strong>? <br>
        Karyawan ini harus melakukan registrasi wajah kembali.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form action="{{ route('admin.karyawan.reset_face', $karyawan->_id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-danger">Ya, Reset Data Wajah</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

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
    const komandanSpecificFields = document.getElementById('komandan_specific_fields');
    const ketuaDepartemenSpecificFields = document.getElementById('ketua_departemen_specific_fields');
    const petugasFaceResetField = document.getElementById('petugas_face_reset_field');

    const currentProdiNama = "{{ old('program_studi_nama', $karyawan->departemen) }}";
    const currentFakultasNama = "{{ old('fakultas_nama', $karyawan->unit) }}";

    function toggleFields() {
        const selectedJabatan = jabatanSelect.value;

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
        komandanSpecificFields.style.display = 'none';
        ketuaDepartemenSpecificFields.style.display = 'none';
        petugasFaceResetField.style.display = 'none';

        if (selectedJabatan === 'Admin') {
            adminSpecificFields.style.display = 'block';
            fotoProfilField.style.display = 'block';
        } else if (selectedJabatan === 'Komandan') {
            fakultasSection.style.display = 'block';
            fakultasRequiredStar.style.display = 'inline';
            fakultasSelect.required = true;
            fotoProfilField.style.display = 'block';
            komandanSpecificFields.style.display = 'block';
        } else if (selectedJabatan === 'Ketua Departemen') {
            fakultasSection.style.display = 'block';
            fakultasRequiredStar.style.display = 'inline';
            fakultasSelect.required = true;
            prodiField.style.display = 'block';
            prodiRequiredStar.style.display = 'inline';
            prodiSelect.required = true;
            fotoProfilField.style.display = 'block';
            ketuaDepartemenSpecificFields.style.display = 'block';
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
            petugasFaceResetField.style.display = 'block';
        }
        
        if (fakultasSelect.value && ['Komandan', 'Ketua Departemen', 'Petugas Keamanan'].includes(selectedJabatan)) {
            fetchAndPopulateProdi(fakultasSelect.value, currentProdiNama);
        } else {
            clearProdiOptions(); 
        }
    }

    function clearProdiOptions(message = '-- Pilih Program Studi --') {
        prodiSelect.innerHTML = `<option value="">${message}</option>`;
    }

    function populateProdi(programStudiData, selectedProdiNama = '') {
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        if (programStudiData && programStudiData.length > 0) {
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
            // Trigger change event if a prodi was pre-selected to update radius etc.
            if(selectedProdiNama) prodiSelect.dispatchEvent(new Event('change'));
        } else {
            prodiSelect.innerHTML = '<option value="">-- Tidak ada Program Studi --</option>';
        }
    }
    
    function fetchAndPopulateProdi(selectedFakultasNama, preselectProdiNama) {
        if (!selectedFakultasNama || !['Ketua Departemen', 'Petugas Keamanan'].includes(jabatanSelect.value)) {
            clearProdiOptions('-- Pilih Fakultas --');
            return;
        }

        prodiSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/panel/fakultas/get-details-for-karyawan/${encodeURIComponent(selectedFakultasNama)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);

                let prodiListToPopulate = (data.tipe_fakultas === 'Teknik')
                    ? data.program_studi.map(p => ({nama_prodi: p.nama_prodi, radius: p.radius}))
                    : data.program_studi.map(p => ({nama_prodi: p.nama_prodi}));

                if (jabatanSelect.value === 'Petugas Keamanan' && data.tipe_fakultas !== 'Teknik') {
                    radiusInput.value = data.radius_fakultas || '{{$karyawan->office_radius}}';
                }
                populateProdi(prodiListToPopulate, preselectProdiNama);
            })
            .catch(error => {
                console.error('Error fetching fakultas details:', error);
                clearProdiOptions('Gagal memuat prodi');
            });
    }

    fakultasSelect.addEventListener('change', function() {
        fetchAndPopulateProdi(this.value, null); 
    });

    prodiSelect.addEventListener('change', function() {
        if (jabatanSelect.value === 'Petugas Keamanan') {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.radius) {
                radiusInput.value = selectedOption.dataset.radius;
            }
        }
    });

    jabatanSelect.addEventListener('change', toggleFields);
    toggleFields(); 
    
    if (currentFakultasNama) {
        fetchAndPopulateProdi(currentFakultasNama, currentProdiNama);
    }
});
</script>
@endpush
@endsection
