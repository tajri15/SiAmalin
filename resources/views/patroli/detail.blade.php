@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        {{-- PERUBAHAN: Menambahkan parameter cache-busting --}}
        <a href="{{ route('patroli.histori', ['_cb' => now()->timestamp]) }}" class="headerButton">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Detail Patroli</div>
    <div class="right"></div>
</div>
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #mapDetail {
        width: 100%;
        height: 400px; /* Lebih tinggi untuk detail */
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    .detail-info {
        padding: 1rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    }
    .detail-info p {
        margin-bottom: 0.75rem;
        font-size: 1rem;
        color: #495057;
    }
    .detail-info p strong {
        font-weight: 600;
        color: #007bff;
    }
    .detail-info ion-icon {
        vertical-align: middle;
        margin-right: 8px;
        font-size: 1.2em;
        color: #007bff;
    }
</style>
@endsection

@section('content')
<div class="section full" style="padding-top: 70px; padding-bottom: 70px;">
    <div class="wide-block pt-2 pb-2">

        <div class="detail-info mb-3">
            <p><ion-icon name="person-circle-outline"></ion-icon>Karyawan: <strong>{{ $karyawan->nama_lengkap }} ({{ $karyawan->nik }})</strong></p>
            <p><ion-icon name="calendar-outline"></ion-icon>Tanggal: <strong>{{ \Carbon\Carbon::parse($patrol->start_time)->isoFormat('dddd, D MMMM YYYY') }}</strong></p>
            <p><ion-icon name="time-outline"></ion-icon>Waktu Mulai: <strong>{{ \Carbon\Carbon::parse($patrol->start_time)->format('H:i:s') }}</strong></p>
            <p><ion-icon name="time-outline"></ion-icon>Waktu Selesai: <strong>{{ $patrol->end_time ? \Carbon\Carbon::parse($patrol->end_time)->format('H:i:s') : '-' }}</strong></p>
            <p><ion-icon name="walk-outline"></ion-icon>Jarak Tempuh: <strong>{{ number_format(($patrol->total_distance_meters ?? 0) / 1000, 2) }} km</strong></p>
            <p><ion-icon name="hourglass-outline"></ion-icon>Durasi:
                <strong>
                @php
                    $duration = $patrol->duration_seconds ?? 0;
                    $hours = floor($duration / 3600);
                    $minutes = floor(($duration % 3600) / 60);
                    $seconds = $duration % 60;
                    echo sprintf('%02d jam %02d menit %02d detik', $hours, $minutes, $seconds);
                @endphp
                </strong>
            </p>
            <p><ion-icon name="information-circle-outline"></ion-icon>Status: <strong class="text-capitalize">{{ $patrol->status }}</strong></p>
        </div>

        <div id="mapDetail"></div>

    </div>
</div>
@endsection

@push('myscript')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pathData = @json($pathForMap ?? []); // Path dalam format [[lat, lng], [lat, lng], ...]
        
        if (pathData && pathData.length > 0) {
            // Ambil titik tengah untuk pusat peta, atau titik pertama jika hanya satu titik
            const centerLat = pathData[Math.floor(pathData.length / 2)][0];
            const centerLng = pathData[Math.floor(pathData.length / 2)][1];

            const mapDetail = L.map('mapDetail').setView([centerLat, centerLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapDetail);

            // Gambar jejak patroli
            const patrolPolylineDetail = L.polyline(pathData, { color: '#007bff', weight: 5 }).addTo(mapDetail);
            
            // Tambahkan marker untuk titik awal dan akhir
            if (pathData.length > 0) {
                L.marker(pathData[0], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapDetail).bindPopup('Mulai Patroli');
            }
            if (pathData.length > 1) { // Hanya tambahkan marker akhir jika ada lebih dari 1 titik
                L.marker(pathData[pathData.length - 1], {icon: L.icon({iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]})}).addTo(mapDetail).bindPopup('Selesai Patroli');
            }

            // Sesuaikan zoom peta agar semua jejak terlihat
            if (patrolPolylineDetail.getBounds().isValid()) {
                mapDetail.fitBounds(patrolPolylineDetail.getBounds().pad(0.1)); // pad(0.1) untuk sedikit padding
            }

        } else {
            // Tampilkan pesan jika tidak ada data jejak
            document.getElementById('mapDetail').innerHTML = '<p class="text-center text-muted mt-3">Tidak ada data jejak untuk ditampilkan.</p>';
            // Inisialisasi peta dengan lokasi default jika tidak ada path
            const defaultMap = L.map('mapDetail').setView([-6.966667, 110.416664], 13); // Semarang
             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(defaultMap);
        }
    });
</script>
@endpush
