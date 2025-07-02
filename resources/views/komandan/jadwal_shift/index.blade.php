@extends('admin.layouts.app')

@section('title', 'Pengaturan Jadwal Shift - Fakultas ' . $fakultasKomandan)

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Tambahkan CSRF token untuk AJAX --}}
<style>
    .table-jadwal th, .table-jadwal td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.85rem;
        padding: 0.5rem;
        min-width: 100px; /* Agar kolom hari tidak terlalu sempit */
    }
    .table-jadwal th.nama-karyawan, .table-jadwal td.nama-karyawan {
        text-align: left;
        min-width: 180px;
        background-color: #f8f9fc; /* Light background for name column */
        position: sticky;
        left: 0;
        z-index: 1;
        box-shadow: 2px 0 5px -2px #ccc; /* Optional: shadow for sticky column */
    }
     .table-responsive {
        max-height: 70vh; /* Batasi tinggi tabel agar bisa discroll */
    }
    .shift-cell {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .shift-cell:hover {
        background-color: #e9ecef;
    }
    .shift-pagi { background-color: #d1e7dd !important; color: #0f5132 !important; } 
    .shift-malam { background-color: #cfe2ff !important; color: #084298 !important; } 
    .shift-libur { background-color: #f8f9fa !important; color: #6c757d !important; } 
    .shift-custom { background-color: #e2e3e5 !important; color: #495057 !important; } 

    .shift-badge {
        display: block;
        padding: 0.3em 0.5em;
        border-radius: 0.25rem;
        font-weight: 500;
        font-size: 0.8em;
    }
    .filter-form .form-control-sm, .filter-form .btn-sm {
        font-size: 0.875rem;
    }
    .modal-body p strong {
        color: #4e73df; /* Warna biru untuk info penting di modal */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Jadwal Shift</h1>
        <span class="badge bg-info text-white p-2">Fakultas: {{ $fakultasKomandan }}</span>
    </div>
    <p class="mb-4">Atur jadwal shift mingguan untuk petugas keamanan di fakultas Anda. Klik pada sel untuk mengatur shift.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Minggu</h6>
        </div>
        <div class="card-body filter-form">
            <form method="GET" action="{{ route('komandan.jadwalshift.index') }}" class="row gx-2 gy-2 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal" class="form-label mb-1 small">Pilih Tanggal (dalam minggu yang diinginkan):</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ $selectedDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Tampilkan</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('komandan.jadwalshift.index', ['tanggal' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-chevron-left"></i> Minggu Sebelumnya
                    </a>
                </div>
                <div class="col-md-3">
                     <a href="{{ route('komandan.jadwalshift.index', ['tanggal' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm w-100">
                        Minggu Berikutnya <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Jadwal Shift Minggu: {{ $startOfWeek->isoFormat('D MMM YY') }} - {{ $endOfWeek->isoFormat('D MMM YY') }}
            </h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tambahan tulisan informasi --}}
            <div class="alert alert-info small mb-3" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                Setelah menerapkan atau mengubah jadwal, tabel akan diperbarui secara otomatis. Jika ada kendala, silakan refresh halaman ini.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm table-jadwal" id="tableJadwalShift">
                    <thead class="table-light">
                        <tr>
                            <th class="nama-karyawan">Nama Petugas</th>
                            @foreach ($datesOfWeek as $date)
                                <th class="text-center">
                                    {{ $date->isoFormat('dddd') }}<br>
                                    <small>{{ $date->isoFormat('D MMM') }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalMingguan as $nik => $dataJadwal)
                        <tr>
                            <td class="nama-karyawan">
                                {{ $dataJadwal['nama_lengkap'] }}
                                <small class="d-block text-muted">NIK: {{ $nik }}</small>
                            </td>
                            @foreach ($datesOfWeek as $tanggal)
                                @php
                                    $shift = $dataJadwal['shifts'][$tanggal->format('Y-m-d')] ?? null;
                                    $shiftClass = '';
                                    $shiftText = 'Kosong';
                                    if ($shift) {
                                        $shiftNamaUpper = strtoupper($shift->shift_nama);
                                        if ($shiftNamaUpper === 'PAGI') $shiftClass = 'shift-pagi';
                                        elseif ($shiftNamaUpper === 'MALAM') $shiftClass = 'shift-malam';
                                        elseif ($shiftNamaUpper === 'LIBUR') $shiftClass = 'shift-libur';
                                        elseif ($shiftNamaUpper === 'CUSTOM') $shiftClass = 'shift-custom';
                                        else $shiftClass = 'shift-custom'; 
                                        
                                        $shiftText = $shift->shift_nama;
                                        if ($shift->jam_mulai && $shift->jam_selesai) {
                                            $shiftText .= "<br><small>(" . substr($shift->jam_mulai, 0, 5) . "-" . substr($shift->jam_selesai, 0, 5) . ")</small>";
                                        } elseif ($shiftNamaUpper === 'CUSTOM' && (!$shift->jam_mulai || !$shift->jam_selesai) ) {
                                            $shiftText .= "<br><small>(Jam Belum Lengkap)</small>";
                                        } elseif ($shiftNamaUpper !== 'LIBUR') {
                                             $shiftText .= "<br><small>(Jam Tidak Ditentukan)</small>";
                                        }
                                    }
                                @endphp
                                <td class="shift-cell {{ $shiftClass }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#setShiftModal"
                                    data-nik="{{ $nik }}"
                                    data-nama="{{ $dataJadwal['nama_lengkap'] }}"
                                    data-tanggal="{{ $tanggal->format('Y-m-d') }}"
                                    data-tanggal-display="{{ $tanggal->isoFormat('dddd, D MMMM YYYY') }}"
                                    data-shift-nama="{{ $shift->shift_nama ?? '' }}"
                                    data-jam-mulai="{{ $shift->jam_mulai ?? '' }}"
                                    data-jam-selesai="{{ $shift->jam_selesai ?? '' }}"
                                    data-keterangan="{{ $shift->keterangan ?? '' }}">
                                    {!! $shift ? $shiftText : '<span class="text-muted fst-italic">Kosong</span>' !!}
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($datesOfWeek) + 1 }}" class="text-center">
                                Belum ada petugas keamanan di fakultas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set/Edit Shift -->
<div class="modal fade" id="setShiftModal" tabindex="-1" aria-labelledby="setShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formSetShift" method="POST" action="{{ route('komandan.jadwalshift.store') }}">
                @csrf
                <input type="hidden" name="karyawan_nik" id="modal_karyawan_nik">
                <input type="hidden" name="tanggal" id="modal_tanggal">

                <div class="modal-header">
                    <h5 class="modal-title" id="setShiftModalLabel">Atur Shift untuk <strong id="modal_nama_karyawan_display"></strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Tanggal: <strong id="modal_tanggal_display_text"></strong></p>
                    
                    <div class="mb-3">
                        <label for="modal_shift_nama" class="form-label">Jenis Shift <span class="text-danger">*</span></label>
                        <select name="shift_nama" id="modal_shift_nama" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($definedShifts as $key => $shiftInfo)
                                <option value="{{ $key }}">{{ $shiftInfo['label'] }}</option>
                            @endforeach
                            <option value="CUSTOM">Kustom Waktu</option>
                        </select>
                    </div>

                    <div id="custom_time_fields" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modal_jam_mulai" class="form-label">Jam Mulai (HH:MM) <span id="jam_mulai_star" class="text-danger" style="display:none;">*</span></label>
                                <input type="time" name="jam_mulai" id="modal_jam_mulai" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="modal_jam_selesai" class="form-label">Jam Selesai (HH:MM) <span id="jam_selesai_star" class="text-danger" style="display:none;">*</span></label>
                                <input type="time" name="jam_selesai" id="modal_jam_selesai" class="form-control form-control-sm">
                            </div>
                        </div>
                         <small class="form-text text-muted">Untuk shift malam yang melewati tengah malam (misal 19:00 - 07:00), sistem akan menanganinya dengan benar.</small>
                    </div>

                    <div class="mb-3">
                        <label for="modal_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="modal_keterangan" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div id="modalErrorMessage" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const setShiftModalElement = document.getElementById('setShiftModal');
    const setShiftModal = setShiftModalElement ? new bootstrap.Modal(setShiftModalElement) : null;
    
    const modalForm = document.getElementById('formSetShift');
    const modalShiftNamaSelect = document.getElementById('modal_shift_nama');
    const customTimeFields = document.getElementById('custom_time_fields');
    const modalJamMulaiInput = document.getElementById('modal_jam_mulai');
    const modalJamSelesaiInput = document.getElementById('modal_jam_selesai');
    const jamMulaiStar = document.getElementById('jam_mulai_star');
    const jamSelesaiStar = document.getElementById('jam_selesai_star');
    const modalErrorMessage = document.getElementById('modalErrorMessage');

    let activeCellElement = null; // Pindahkan ke scope yang lebih luas jika diperlukan di luar event listener

    $('#tableJadwalShift').on('click', '.shift-cell', function () {
        if (!setShiftModal) {
            console.error("Modal #setShiftModal tidak ditemukan.");
            return;
        }
        activeCellElement = this; // Simpan elemen sel yang diklik saat ini

        const nik = $(this).data('nik');
        const nama = $(this).data('nama');
        const tanggal = $(this).data('tanggal');
        const tanggalDisplay = $(this).data('tanggal-display');
        let shiftNama = $(this).data('shift-nama') || '';
        let jamMulai = $(this).data('jam-mulai') || '';
        let jamSelesai = $(this).data('jam-selesai') || '';
        const keterangan = $(this).data('keterangan') || '';

        $('#modal_karyawan_nik').val(nik);
        $('#modal_nama_karyawan_display').text(nama); 
        $('#modal_tanggal').val(tanggal);
        $('#modal_tanggal_display_text').text(tanggalDisplay); 
        
        let currentShiftKey = shiftNama.toUpperCase();
        const definedShiftsFromBlade = typeof $definedShifts !== 'undefined' ? @json($definedShifts) : {};
        const definedShiftKeys = Object.keys(definedShiftsFromBlade).map(k => k.toUpperCase());

        if (!definedShiftKeys.includes(currentShiftKey) && currentShiftKey !== 'CUSTOM' && (jamMulai || jamSelesai)) {
            currentShiftKey = 'CUSTOM';
        } else if (!definedShiftKeys.includes(currentShiftKey) && currentShiftKey !== 'CUSTOM') {
            currentShiftKey = '';
        }
        
        $('#modal_shift_nama').val(currentShiftKey);
        $('#modal_keterangan').val(keterangan);
        
        modalErrorMessage.style.display = 'none';
        modalErrorMessage.innerHTML = '';

        if (currentShiftKey === 'CUSTOM') {
            customTimeFields.style.display = 'block';
            modalJamMulaiInput.value = jamMulai;
            modalJamSelesaiInput.value = jamSelesai;
            modalJamMulaiInput.required = true;
            modalJamSelesaiInput.required = true;
            jamMulaiStar.style.display = 'inline';
            jamSelesaiStar.style.display = 'inline';
        } else {
            customTimeFields.style.display = 'none';
            modalJamMulaiInput.value = '';
            modalJamSelesaiInput.value = '';
            modalJamMulaiInput.required = false;
            modalJamSelesaiInput.required = false;
            jamMulaiStar.style.display = 'none';
            jamSelesaiStar.style.display = 'none';
        }
        setShiftModal.show();
    });

    if (modalShiftNamaSelect) {
        modalShiftNamaSelect.addEventListener('change', function() {
            if (this.value === 'CUSTOM') {
                customTimeFields.style.display = 'block';
                modalJamMulaiInput.required = true;
                modalJamSelesaiInput.required = true;
                jamMulaiStar.style.display = 'inline';
                jamSelesaiStar.style.display = 'inline';
            } else {
                customTimeFields.style.display = 'none';
                modalJamMulaiInput.required = false;
                modalJamSelesaiInput.required = false;
                jamMulaiStar.style.display = 'none';
                jamSelesaiStar.style.display = 'none';
            }
        });
    }

    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = $(this).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            modalErrorMessage.style.display = 'none';
            modalErrorMessage.innerHTML = '';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json().then(data => ({ status: response.status, ok: response.ok, body: data }));
                } else {
                    return response.text().then(text => { 
                        console.error("Server response was not JSON:", text);
                        return { status: response.status, ok: false, body: { message: "Respon server tidak valid.", _raw: text.substring(0,500) } };
                    });
                }
            })
            .then(responseObj => {
                const data = responseObj.body;
                if (responseObj.ok && data.success) {
                    if(setShiftModal) setShiftModal.hide();
                    // Tidak perlu Swal.fire jika sudah ada info refresh
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Berhasil!',
                    //     text: data.message,
                    //     timer: 1000, 
                    //     showConfirmButton: false
                    // });

                    if (activeCellElement && data.shift) {
                        const newShift = data.shift;
                        let shiftText = newShift.shift_nama;
                        let shiftClass = '';
                        const shiftNamaUpper = newShift.shift_nama.toUpperCase();

                        if (shiftNamaUpper === 'PAGI') shiftClass = 'shift-pagi';
                        else if (shiftNamaUpper === 'MALAM') shiftClass = 'shift-malam';
                        else if (shiftNamaUpper === 'LIBUR') shiftClass = 'shift-libur';
                        else if (shiftNamaUpper === 'CUSTOM') shiftClass = 'shift-custom';
                        else shiftClass = 'shift-custom'; 

                        if (newShift.jam_mulai && newShift.jam_selesai) {
                            shiftText += `<br><small>(${newShift.jam_mulai.substring(0,5)}-${newShift.jam_selesai.substring(0,5)})</small>`;
                        } else if (shiftNamaUpper === 'CUSTOM' && (!newShift.jam_mulai || !newShift.jam_selesai)) {
                            shiftText += `<br><small>(Jam Belum Lengkap)</small>`;
                        } else if (shiftNamaUpper !== 'LIBUR') {
                            shiftText += `<br><small>(Jam Tidak Ditentukan)</small>`;
                        }
                        
                        $(activeCellElement).html(shiftText);
                        activeCellElement.className = 'shift-cell'; 
                        if(shiftClass) $(activeCellElement).addClass(shiftClass);

                        $(activeCellElement).data('shift-nama', newShift.shift_nama);
                        $(activeCellElement).data('jam-mulai', newShift.jam_mulai || '');
                        $(activeCellElement).data('jam-selesai', newShift.jam_selesai || '');
                        $(activeCellElement).data('keterangan', newShift.keterangan || '');
                        
                        // Tambahkan notifikasi sukses kecil di atas tabel
                        const successAlertHtml = `
                            <div class="alert alert-success alert-dismissible fade show small" role="alert" id="shiftUpdateSuccessAlert">
                                ${data.message} Silakan refresh halaman jika perubahan tidak langsung terlihat.
                                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                        // Hapus alert lama jika ada, lalu tambahkan yang baru
                        $('#shiftUpdateSuccessAlert').remove(); 
                        $('.card-body .table-responsive').before(successAlertHtml);
                        window.setTimeout(() => { $('#shiftUpdateSuccessAlert').alert('close'); }, 5000);


                    }
                } else {
                    let errorMsgText = "Terjadi kesalahan."; 
                    if (typeof data === 'object' && data !== null) { 
                        errorMsgText = data.message || 'Data tidak valid.';
                        if(data.errors){
                            errorMsgText += '<br><ul class="list-unstyled text-start mt-2">';
                            for(const field in data.errors){
                                data.errors[field].forEach(err => {
                                    errorMsgText += `<li><small>${err}</small></li>`;
                                });
                            }
                            errorMsgText += '</ul>';
                        }
                    } else if (typeof data === 'string') { 
                         errorMsgText = "Gagal memproses permintaan. Respon server tidak valid.";
                         console.error("Raw server response (not JSON):", data);
                    }
                    
                    modalErrorMessage.innerHTML = errorMsgText;
                    modalErrorMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                modalErrorMessage.innerHTML = 'Terjadi kesalahan jaringan atau server. <br><small>' + error.message + '</small>';
                modalErrorMessage.style.display = 'block';
            })
            .finally(() => {
                 submitButton.prop('disabled', false).html(originalButtonText);
            });
        });
    }
});
</script>
@endpush
