@extends('admin.layouts.app')

@section('title', 'Detail Patroli Petugas - ' . $departemen)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #mapDetailPatroli { height: 450px; border-radius: .375rem; border: 1px solid #e3e6f0; }
    .info-table th { width: 30%; font-weight: 500; background-color: #f8f9fc; }
    .info-table td, .info-table th { padding: .6rem .75rem; font-size: 0.9rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Detail Patroli Petugas</h1>
        <a href="{{ route('ketua-departemen.patroli.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Histori
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
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
                    </table>
                </div>
                <div class="col-lg-7">
                    <h5 class="mb-3">Jejak Patroli</h5>
                    <div id="mapDetailPatroli"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pathData = @json($pathForMap ?? []);
        const mapElement = document.getElementById('mapDetailPatroli');
        
        if (pathData && pathData.length > 0) {
            const centerLat = pathData[Math.floor(pathData.length / 2)][0];
            const centerLng = pathData[Math.floor(pathData.length / 2)][1];
            
            // Inisialisasi peta dengan ID yang benar
            const map = L.map('mapDetailPatroli').setView([centerLat, centerLng], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Tambahkan polyline untuk jejak patroli
            const polyline = L.polyline(pathData, { 
                color: '#007bff', 
                weight: 5 
            }).addTo(map);

            // Marker titik awal (hijau)
            if (pathData.length > 0) {
                L.marker(pathData[0], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Mulai Patroli');
            }

            // Marker titik akhir (merah)
            if (pathData.length > 1) {
                L.marker(pathData[pathData.length - 1], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Selesai Patroli');
            }

            // Fit bounds jika polyline valid
            if (polyline.getBounds().isValid()) {
                map.fitBounds(polyline.getBounds().pad(0.1));
            }
        } else {
            // Tampilkan pesan jika tidak ada data
            mapElement.innerHTML = '<p class="text-center text-muted p-5">Tidak ada data jejak patroli yang tersedia.</p>';
        }
    });
</script>
@endpush