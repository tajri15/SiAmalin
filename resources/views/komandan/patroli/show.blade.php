{{-- File: resources/views/komandan/patroli/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Patroli Petugas - Fakultas ' . $fakultasKomandan)

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #mapKomandanDetail {
        width: 100%;
        height: 450px;
        border-radius: .375rem; 
        border: 1px solid #e3e6f0; 
    }
    .info-table th {
        width: 30%;
        font-weight: 500;
        background-color: #f8f9fc;
    }
    .info-table td, .info-table th {
        padding: .6rem .75rem;
        font-size: 0.9rem;
    }
    .face-verification-section {
        background-color: #f8f9fc;
        border-radius: .375rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    .face-verification-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: .375rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Detail Patroli Petugas</h1>
        <a href="{{ route('komandan.patroli.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Histori Patroli
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Patroli oleh: {{ $patrol->karyawan->nama_lengkap ?? 'N/A' }} ({{ $patrol->karyawan_nik }})
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <h5 class="mb-3">Informasi Patroli</h5>
                    <table class="table table-bordered table-sm info-table">
                        <tr>
                            <th>ID Patroli</th>
                            <td>{{ $patrol->_id }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Mulai</th>
                            <td>{{ \Carbon\Carbon::parse($patrol->start_time)->isoFormat('dddd, D MMMM YYYY HH:mm:ss') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Selesai</th>
                            <td>{{ $patrol->end_time ? \Carbon\Carbon::parse($patrol->end_time)->isoFormat('dddd, D MMMM YYYY HH:mm:ss') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>
                                @php
                                    $duration = $patrol->duration_seconds ?? 0;
                                    $hours = floor($duration / 3600);
                                    $minutes = floor(($duration % 3600) / 60);
                                    $seconds = $duration % 60;
                                    echo sprintf('%02d jam %02d menit %02d detik', $hours, $minutes, $seconds);
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <th>Jarak Tempuh</th>
                            <td>{{ number_format(($patrol->total_distance_meters ?? 0) / 1000, 2) }} km</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $patrol->status == 'selesai' ? 'success' : ($patrol->status == 'dibatalkan' ? 'danger' : 'secondary') }} text-capitalize">
                                    {{ $patrol->status }}
                                </span>
                            </td>
                        </tr>
                        {{-- NEW: Face verification status --}}
                        <tr>
                            <th>Verifikasi Wajah</th>
                            <td>
                                @if($patrol->face_verified ?? false)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                    </span>
                                    @if($patrol->face_verification_time)
                                        <br><small class="text-muted">
                                            {{ \Carbon\Carbon::parse($patrol->face_verification_time)->isoFormat('D MMM YYYY HH:mm') }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Tidak Terverifikasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    {{-- NEW: Face verification section --}}
                    @if($patrol->face_verified && $patrol->face_verification_image)
                    <div class="face-verification-section">
                        <h6 class="mb-3">
                            <i class="bi bi-person-check me-2"></i>Foto Verifikasi Wajah
                        </h6>
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $patrol->face_verification_image) }}" 
                                 alt="Foto Verifikasi Wajah" 
                                 class="face-verification-image"
                                 onclick="showImageModal(this.src)">
                            @if($patrol->face_verification_time)
                            <p class="text-muted mt-2 mb-0">
                                <small>Diverifikasi pada: {{ \Carbon\Carbon::parse($patrol->face_verification_time)->isoFormat('dddd, D MMMM YYYY HH:mm:ss') }}</small>
                            </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-7">
                    <h5 class="mb-3">Jejak Patroli</h5>
                    <div id="mapKomandanDetail"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for viewing face verification image --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Verifikasi Wajah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Verifikasi" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pathData = @json($pathForMap ?? []);
        const officeLocation = @json($officeLocation ?? null);
        const officeRadius = @json($officeRadius ?? null);
        const officeLat = officeLocation ? officeLocation.coordinates[1] : null;
        const officeLng = officeLocation ? officeLocation.coordinates[0] : null;

        const mapContainer = document.getElementById('mapKomandanDetail');
        if (!mapContainer) return;

        // Tentukan titik tengah peta. Prioritaskan lokasi kantor.
        let centerLat, centerLng;
        if (officeLat && officeLng) {
            centerLat = officeLat;
            centerLng = officeLng;
        } else if (pathData && pathData.length > 0) {
            centerLat = pathData[Math.floor(pathData.length / 2)][0];
            centerLng = pathData[Math.floor(pathData.length / 2)][1];
        } else {
            centerLat = -6.9927; // Default Semarang
            centerLng = 110.4204;
        }

        const mapKomandanDetail = L.map('mapKomandanDetail').setView([centerLat, centerLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapKomandanDetail);

        let bounds = L.latLngBounds();

        // Gambar jejak patroli jika ada
        if (pathData && pathData.length > 0) {
            const patrolPolylineDetail = L.polyline(pathData, { color: '#007bff', weight: 5 }).addTo(mapKomandanDetail);
            bounds.extend(patrolPolylineDetail.getBounds());

            // Marker Mulai
            L.marker(pathData[0], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapKomandanDetail).bindPopup('Mulai Patroli');
            
            // Marker Selesai (jika ada lebih dari 1 titik)
            if (pathData.length > 1) {
                L.marker(pathData[pathData.length - 1], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapKomandanDetail).bindPopup('Selesai Patroli');
            }
        }

        // Gambar lokasi kerja dan radius jika ada
        if (officeLat && officeLng && officeRadius) {
            const officeCircle = L.circle([officeLat, officeLng], {
                color: '#dc3545',
                fillColor: '#dc3545',
                fillOpacity: 0.2,
                radius: officeRadius
            }).addTo(mapKomandanDetail).bindPopup('Radius Lokasi Kerja');
            bounds.extend(officeCircle.getBounds());
        }

        // Sesuaikan zoom peta jika ada objek yang digambar
        if (bounds.isValid()) {
            mapKomandanDetail.fitBounds(bounds.pad(0.2)); // Beri sedikit padding
        } else if (!pathData || pathData.length === 0) {
            // Jika tidak ada data sama sekali
            mapContainer.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><p class="text-muted p-5">Tidak ada data jejak atau lokasi kerja untuk ditampilkan.</p></div>';
        }
    });

    // Function to show image in modal
    function showImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }