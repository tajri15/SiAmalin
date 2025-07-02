{{-- File: resources/views/komandan/patroli/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Patroli Petugas - Fakultas ' . $fakultasKomandan)

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #mapKomandanDetail { /* ID disesuaikan agar unik jika admin dan komandan view berbeda */
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
            {{-- Tombol Hapus Dihilangkan --}}
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
                    </table>
                </div>
                <div class="col-lg-7">
                    <h5 class="mb-3">Jejak Patroli</h5>
                    <div id="mapKomandanDetail"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hapus Dihilangkan --}}

@endsection

@push('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pathData = @json($pathForMap ?? []); 
        
        if (pathData && pathData.length > 0) {
            const centerLat = pathData[Math.floor(pathData.length / 2)][0];
            const centerLng = pathData[Math.floor(pathData.length / 2)][1];

            const mapKomandanDetail = L.map('mapKomandanDetail').setView([centerLat, centerLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapKomandanDetail);

            const patrolPolylineDetail = L.polyline(pathData, { color: '#007bff', weight: 5 }).addTo(mapKomandanDetail);
            
            if (pathData.length > 0) {
                L.marker(pathData[0], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapKomandanDetail).bindPopup('Mulai Patroli');
            }
            if (pathData.length > 1) {
                L.marker(pathData[pathData.length - 1], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapKomandanDetail).bindPopup('Selesai Patroli');
            }

            if (patrolPolylineDetail.getBounds().isValid()) {
                mapKomandanDetail.fitBounds(patrolPolylineDetail.getBounds().pad(0.1));
            }

        } else {
            document.getElementById('mapKomandanDetail').innerHTML = '<p class="text-center text-muted p-5">Tidak ada data jejak untuk ditampilkan.</p>';
             const defaultMap = L.map('mapKomandanDetail').setView([-6.9927, 110.4204], 13); // Default Semarang
             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(defaultMap);
        }
    });
</script>
@endpush
