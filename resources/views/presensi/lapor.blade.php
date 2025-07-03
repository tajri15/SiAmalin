@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="#" onclick="goBackOrFallback()" class="headerButton goBack" style="color: white;">
            <ion-icon name="chevron-back-outline" style="font-size: 20px;"></ion-icon>
        </a>
    </div>
    <div class="pageTitle text-center" style="font-weight: 600; letter-spacing: 0.5px;">Daftar Laporan Saya</div>
    <div class="right">
        <a href="{{ route('laporan.create') }}" class="headerButton" style="color: white;">
            <ion-icon name="add-circle-outline" style="font-size: 24px;"></ion-icon>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="section full mt-2" style="padding-bottom: 70px;">
    <div class="section-title">Laporan Anda</div>
    <div class="wide-block p-0">

        @if (session('success'))
            <div class="alert alert-success m-2">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger m-2">
                {{ session('error') }}
            </div>
        @endif

        @if($laporan->isEmpty())
        <div class="text-center p-4 mt-3">
            <img src="{{ asset('assets/img/sample/photos/no-data.png') }}" alt="Tidak ada data" class="imaged w160 mb-2" style="max-width: 120px;" onerror="this.onerror=null;this.src='https://placehold.co/120x120/EBF4FF/0D6EFD?text=No+Data';">
            <h4 class="mt-2" style="font-weight: 500; color: #555;">Belum Ada Laporan</h4>
            <p class="text-muted small">Anda belum membuat laporan apapun. Klik tombol '+' di kanan atas untuk membuat laporan baru.</p>
        </div>
        @else
        <ul class="listview image-listview flush">
            @foreach ($laporan as $item)
            <li>
                <a href="#" class="item show-laporan-detail"
                    data-id="{{ $item->_id }}"
                    data-jenis-laporan="{{ $item->jenis_laporan }}"
                    data-tanggal-jam="{{ $item->tanggal_formatted }}, {{ $item->jam }}"
                    data-lokasi="{{ $item->lokasi }}"
                    data-keterangan="{{ $item->keterangan }}"
                    data-foto-url="{{ $item->foto_url ?? '' }}"
                    data-face-verification-url="{{ $item->face_verification_url ?? '' }}"
                    data-status-admin="{{ $item->status_admin ?? '' }}"
                    data-catatan-admin="{{ $item->catatan_admin ?? '' }}"
                    data-tanggal-peninjauan-admin="{{ $item->tanggal_peninjauan_admin ? \Carbon\Carbon::parse($item->tanggal_peninjauan_admin)->isoFormat('D MMM YY, HH:mm') : '' }}">
                    <div class="icon-box {{ $item->jenis_laporan == 'masalah' ? 'bg-danger' : ($item->jenis_laporan == 'kegiatan' ? 'bg-success' : 'bg-primary') }} text-light">
                        @if($item->jenis_laporan == 'harian')
                            <ion-icon name="document-text-outline"></ion-icon>
                        @elseif($item->jenis_laporan == 'kegiatan')
                            <ion-icon name="briefcase-outline"></ion-icon>
                        @elseif($item->jenis_laporan == 'masalah')
                            <ion-icon name="warning-outline"></ion-icon>
                        @else
                            <ion-icon name="document-outline"></ion-icon>
                        @endif
                    </div>
                    <div class="in">
                        <div>
                            <strong>{{ ucfirst($item->jenis_laporan) }}: {{ \Illuminate\Support\Str::limit($item->keterangan, 40) }}</strong>
                            <div class="text-muted small">
                                {{ $item->tanggal_formatted }} - {{ $item->jam }}
                            </div>
                            @if($item->status_admin)
                                <span class="badge
                                    @if($item->status_admin == 'Diterima') bg-success
                                    @elseif($item->status_admin == 'Ditolak') bg-danger
                                    @elseif($item->status_admin == 'Perlu Revisi') bg-warning text-dark
                                    @else bg-secondary @endif mt-1">
                                    {{ $item->status_admin }}
                                </span>
                            @else
                                <span class="badge bg-secondary mt-1">Belum Ditinjau</span>
                            @endif
                        </div>
                        {{-- <span class="text-muted"> --}}
                            {{-- <ion-icon name="chevron-forward-outline"></ion-icon> --}}
                        {{-- </span> --}}
                        {{-- Hapus span di atas agar chevron tidak ganda, karena framework UI biasanya sudah menambahkannya secara otomatis untuk tautan daftar. --}}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

<!-- Detail Laporan Modal -->
<div class="modal fade dialogbox" id="detailLaporanModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="detailLaporanModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            
            <!-- Modal Header dengan ikon x -->
            <div class="modal-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-bottom: none; padding: 20px;">
                <h5 class="modal-title text-white" style="font-weight: 600; font-size: 1.3em;">
                    <ion-icon name="document-text-outline" class="me-2"></ion-icon>
                    Detail Laporan
                </h5>
                <a href="#" class="text-white close-modal-btn" style="font-size: 1.5em; text-decoration: none;">
                    <ion-icon name="close-outline"></ion-icon>
                </a>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" style="padding: 0;">
                <!-- Main Content -->
                <div style="padding: 20px;">
                    <!-- Report Type Badge -->
                    <div id="reportTypeBadge" class="mb-3" style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 500; font-size: 0.85em;"></div>
                    
                    <!-- Detail Cards -->
                    <div class="detail-card mb-3" style="background: #f8fafc; border-radius: 12px; padding: 15px;">
                        <div class="detail-row mb-2">
                            <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500; margin-bottom: 4px;">Tanggal & Jam</div>
                            <div class="detail-value" id="modalTanggalJam" style="font-weight: 500; color: #2d3748;"></div>
                        </div>
                        
                        <div class="detail-row mb-2">
                            <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500; margin-bottom: 4px;">Lokasi</div>
                            <div class="detail-value" id="modalLokasi" style="font-weight: 500; color: #2d3748;">
                                <ion-icon name="location-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                                <span id="locationText"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description Card -->
                    <div class="detail-card mb-3" style="background: #f8fafc; border-radius: 12px; padding: 15px;">
                        <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500; margin-bottom: 8px;">Keterangan</div>
                        <div class="detail-value" id="modalKeterangan" style="line-height: 1.6; white-space: pre-wrap;"></div>
                    </div>
                    
                    <!-- Photo Evidence Section -->
                    <div id="modalFotoBuktiContainer" style="display: none; margin-bottom: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500;">Foto Bukti</div>
                            <a href="#" id="fullscreenPhotoBtn" style="font-size: 0.8em; color: #4e73df; text-decoration: none;">
                                <ion-icon name="expand-outline" style="vertical-align: middle;"></ion-icon> Lihat penuh
                            </a>
                        </div>
                        <img id="modalFotoBukti" src="" alt="Foto Bukti" style="width: 100%; border-radius: 12px; border: 1px solid #e2e8f0; cursor: pointer;" class="shadow-sm">
                    </div>
                    
                    <!-- Face Verification Section -->
                    <div id="modalFotoVerifikasiContainer" style="display: none; margin-bottom: 20px;">
                        <div class="detail-label mb-2" style="color: #718096; font-size: 0.85em; font-weight: 500;">Verifikasi Wajah</div>
                        <div style="text-align: center;">
                            <img id="modalFotoVerifikasi" src="" alt="Foto Verifikasi" style="max-height: 150px; border-radius: 12px; border: 1px solid #e2e8f0;" class="shadow-sm">
                        </div>
                    </div>
                    
                    <!-- Admin Review Section -->
                    <div id="adminSection" style="display: none;">
                        <hr style="border-color: #e2e8f0; margin: 20px 0;">
                        
                        <h6 style="font-weight: 600; color: #4a5568; margin-bottom: 15px; display: flex; align-items: center;">
                            <ion-icon name="shield-checkmark-outline" class="me-2" style="color: #4e73df;"></ion-icon>
                            Tinjauan Admin
                        </h6>
                        
                        <div class="detail-card mb-3" style="background: #f8fafc; border-radius: 12px; padding: 15px;">
                            <div class="detail-row mb-2">
                                <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500; margin-bottom: 4px;">Status</div>
                                <div class="detail-value">
                                    <span id="modalStatusAdminBadge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px; font-size: 0.85em; font-weight: 500;"></span>
                                </div>
                            </div>
                            
                            <div class="detail-row" id="modalCatatanAdminContainer" style="display: none;">
                                <div class="detail-label" style="color: #718096; font-size: 0.85em; font-weight: 500; margin-bottom: 4px;">Catatan</div>
                                <div class="detail-value" id="modalCatatanAdmin" style="line-height: 1.6; white-space: pre-wrap; background: white; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;"></div>
                            </div>
                        </div>
                        
                        <div id="modalTanggalTinjauAdminContainer" style="text-align: right;">
                            <small class="text-muted" id="modalTanggalTinjauAdmin" style="font-size: 0.8em;"></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 15px 20px;">
                <button type="button" id="printReportBtn" class="btn btn-primary btn-sm" style="border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                    <ion-icon name="print-outline" class="me-1"></ion-icon> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Photo Modal -->
<div class="modal fade" id="fullscreenPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-header" style="border: none; position: absolute; top: 10px; right: 10px; z-index: 1;">
                <button type="button" class="btn btn-sm btn-dark rounded-circle" data-bs-dismiss="modal" aria-label="Close" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <ion-icon name="close-outline" style="font-size: 1.2em;"></ion-icon>
                </button>
            </div>
            <div class="modal-body p-0" style="display: flex; align-items: center; justify-content: center;">
                <img id="fullscreenPhoto" src="" alt="Foto Bukti" style="max-width: 100%; max-height: 90vh; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>

@push('myscript')
<script>
    $(document).ready(function() {
        // Tracking halaman sebelumnya
        let previousPage = sessionStorage.getItem('previousPage');
        const currentPage = window.location.href;
        
        // Simpan halaman saat ini sebagai referensi
        if (!previousPage || previousPage === currentPage) {
            sessionStorage.setItem('previousPage', document.referrer || '/dashboard');
        }

        // Fungsi handle back button dengan kontrol lebih ketat
        $(document).on('click', '.goBack', function(e) {
            e.preventDefault();
            
            // 1. Coba kembali ke halaman spesifik yang kita track
            const trackedPrevious = sessionStorage.getItem('previousPage');
            if (trackedPrevious && 
                trackedPrevious !== currentPage &&
                trackedPrevious.includes(window.location.hostname) &&
                !trackedPrevious.includes('laporan/create')) {
                window.location.href = trackedPrevious;
                return;
            }
            
            // 2. Coba history.back() dengan timeout kontrol
            if (window.history.length > 1) {
                const currentState = window.history.state;
                window.history.back();
                
                // Jika tidak berpindah setelah 300ms, gunakan fallback
                setTimeout(() => {
                    if (window.history.state === currentState) {
                        proceedToFallback();
                    }
                }, 300);
                return;
            }
            
            // 3. Fallback ke halaman yang aman
            proceedToFallback();
        });

        function proceedToFallback() {
            // Cek referrer yang valid
            const validReferrer = document.referrer && 
                                document.referrer.includes(window.location.hostname) &&
                                !document.referrer.includes('laporan/create') &&
                                document.referrer !== currentPage;
            
            if (validReferrer) {
                window.location.href = document.referrer;
                return;
            }
            
            // Cek apakah ada halaman sebelumnya di sessionStorage
            const lastValidPage = sessionStorage.getItem('lastValidPage') || '/dashboard';
            if (lastValidPage !== currentPage) {
                window.location.href = lastValidPage;
                return;
            }
            
            // Ultimate fallback
            window.location.href = '/dashboard';
        }

        // Simpan halaman valid terakhir
        $(window).on('beforeunload', function() {
            // Hanya simpan jika bukan halaman create laporan
            if (!window.location.href.includes('laporan/create')) {
                sessionStorage.setItem('lastValidPage', window.location.href);
            }
        });

        // Inisialisasi modal dan lainnya
        var detailModal = new bootstrap.Modal(document.getElementById('detailLaporanModal'));
        
        function closeDetailModal() {
            detailModal.hide();
        }
        
        $(document).on('click', '.close-modal-btn', function(e) {
            e.preventDefault();
            closeDetailModal();
        });

        // Event listener untuk detail laporan
        $(document).on('click', '.show-laporan-detail', function(e) {
            e.preventDefault();
            
            const jenisLaporan = $(this).data('jenis-laporan');
            const tanggalJam = $(this).data('tanggal-jam');
            const lokasi = $(this).data('lokasi');
            const keterangan = $(this).data('keterangan');
            const fotoUrl = $(this).data('foto-url');
            const faceVerificationUrl = $(this).data('face-verification-url');
            const statusAdmin = $(this).data('status-admin');
            const catatanAdmin = $(this).data('catatan-admin');
            const tanggalPeninjauanAdmin = $(this).data('tanggal-peninjauan-admin');
            
            // Set report type badge
            let badgeText = '';
            let badgeColor = '';
            let badgeIcon = '';
            
            switch(jenisLaporan) {
                case 'harian':
                    badgeText = 'Laporan Harian';
                    badgeColor = 'background: rgba(78, 115, 223, 0.1); color: #4e73df;';
                    badgeIcon = 'document-text-outline';
                    break;
                case 'kegiatan':
                    badgeText = 'Laporan Kegiatan';
                    badgeColor = 'background: rgba(28, 200, 138, 0.1); color: #1cc88a;';
                    badgeIcon = 'calendar-outline';
                    break;
                case 'masalah':
                    badgeText = 'Laporan Masalah';
                    badgeColor = 'background: rgba(231, 74, 59, 0.1); color: #e74a3b;';
                    badgeIcon = 'alert-circle-outline';
                    break;
                default:
                    badgeText = 'Laporan';
                    badgeColor = 'background: rgba(108, 117, 125, 0.1); color: #6c757d;';
                    badgeIcon = 'document-outline';
            }
            
            $('#reportTypeBadge').html(`
                <ion-icon name="${badgeIcon}" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                ${badgeText}
            `).attr('style', badgeColor + ' border-radius: 20px; padding: 6px 12px;');
            
            // Isi konten detail
            $('#modalTanggalJam').text(tanggalJam);
            $('#modalLokasi').text(lokasi);
            $('#locationText').text(lokasi);
            $('#modalKeterangan').text(keterangan);
            
            // Foto Bukti
            if (fotoUrl && fotoUrl !== 'null') {
                $('#modalFotoBuktiContainer').show();
                $('#modalFotoBukti').attr('src', fotoUrl);
                $('#fullscreenPhotoBtn').off('click').on('click', function(e) {
                    e.preventDefault();
                    $('#fullscreenPhoto').attr('src', fotoUrl);
                    new bootstrap.Modal(document.getElementById('fullscreenPhotoModal')).show();
                });
                $('#modalFotoBukti').off('click').on('click', function() {
                    $('#fullscreenPhoto').attr('src', fotoUrl);
                    new bootstrap.Modal(document.getElementById('fullscreenPhotoModal')).show();
                });
            } else {
                $('#modalFotoBuktiContainer').hide();
            }
            
            // Face Verification
            if (faceVerificationUrl && faceVerificationUrl !== 'null') {
                $('#modalFotoVerifikasiContainer').show();
                $('#modalFotoVerifikasi').attr('src', faceVerificationUrl);
            } else {
                $('#modalFotoVerifikasiContainer').hide();
            }
            
            // Bagian Admin
            if (statusAdmin) {
                $('#adminSection').show();
                
                let badgeHtml = '';
                let badgeStyle = '';
                
                if (statusAdmin === 'Diterima') {
                    badgeHtml = `<ion-icon name="checkmark-circle-outline"></ion-icon> ${statusAdmin}`;
                    badgeStyle = 'background: rgba(28, 200, 138, 0.1); color: #1cc88a;';
                } else if (statusAdmin === 'Ditolak') {
                    badgeHtml = `<ion-icon name="close-circle-outline"></ion-icon> ${statusAdmin}`;
                    badgeStyle = 'background: rgba(231, 74, 59, 0.1); color: #e74a3b;';
                } else if (statusAdmin === 'Perlu Revisi') {
                    badgeHtml = `<ion-icon name="alert-circle-outline"></ion-icon> ${statusAdmin}`;
                    badgeStyle = 'background: rgba(246, 194, 62, 0.1); color: #f6c23e;';
                } else {
                    badgeHtml = `<ion-icon name="time-outline"></ion-icon> ${statusAdmin}`;
                    badgeStyle = 'background: rgba(108, 117, 125, 0.1); color: #6c757d;';
                }
                
                $('#modalStatusAdminBadge').html(badgeHtml).attr('style', badgeStyle);
                
                if (catatanAdmin) {
                    $('#modalCatatanAdminContainer').show();
                    $('#modalCatatanAdmin').text(catatanAdmin);
                } else {
                    $('#modalCatatanAdminContainer').hide();
                }
                
                if (tanggalPeninjauanAdmin) {
                    $('#modalTanggalTinjauAdminContainer').show();
                    $('#modalTanggalTinjauAdmin').text('Ditinjau pada: ' + tanggalPeninjauanAdmin);
                } else {
                    $('#modalTanggalTinjauAdminContainer').hide();
                }
            } else {
                $('#adminSection').hide();
            }
            
            // Tampilkan modal
            detailModal.show();
            
            // Scroll to top
            $('#detailLaporanModal .modal-body').scrollTop(0);
        });
        
        // Print button functionality
        $('#printReportBtn').on('click', function() {
            window.print();
        });
    });
</script>
@endpush