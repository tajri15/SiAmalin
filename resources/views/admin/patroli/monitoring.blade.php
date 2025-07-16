{{-- File: resources/views/admin/patroli/monitoring.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Monitoring Patroli Real-Time')

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #map-monitoring {
        height: 78vh; /* Tinggi peta disesuaikan */
        border-radius: .375rem;
        border: 1px solid #e3e6f0;
    }
    .active-patrol-list {
        height: 70vh; /* Tinggi daftar disesuaikan */
        overflow-y: auto;
    }
    .active-patrol-list .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s ease-in-out, border-left-color 0.2s ease-in-out;
    }
    .active-patrol-list .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .active-patrol-list .list-group-item.active {
        background-color: #eaf2ff; /* Warna highlight biru lebih muda */
        border-left: 4px solid #4e73df;
        font-weight: 500;
    }
    /* Style untuk status jeda */
    .active-patrol-list .list-group-item.status-jeda {
        background-color: #fff9e6; /* Latar belakang kuning muda */
        border-left: 4px solid #ffc107; /* Border kuning */
    }
    .active-patrol-list .list-group-item.status-jeda:hover {
        background-color: #fff3cd;
    }
    .officer-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    .leaflet-marker-icon.officer-avatar {
        border-radius: 50%;
        border: 2px solid #4e73df; /* Default border biru */
    }
    /* Style border marker untuk status jeda */
    .leaflet-marker-icon.officer-avatar.status-jeda {
        border-color: #ffc107; /* Border kuning */
    }
    .btn-show-all {
        width: 100%;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monitoring Patroli Real-Time</h1>
        <div class="d-flex align-items-center">
            <div class="spinner-grow spinner-grow-sm text-danger me-2" role="status" id="live-indicator">
                <span class="visually-hidden">LIVE</span>
            </div>
            <span class="fw-bold text-danger">LIVE</span>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Daftar Petugas Aktif -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Petugas Berpatroli</h6>
                </div>
                <div class="card-body p-2">
                    <button class="btn btn-sm btn-outline-secondary btn-show-all" id="show-all-btn">
                        <i class="bi bi-arrows-fullscreen"></i> Tampilkan Semua
                    </button>
                    <div class="list-group list-group-flush active-patrol-list" id="active-officers-list">
                        <div id="loading-officers" class="text-center p-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Memuat data...</p>
                        </div>
                        <p id="no-active-patrols" class="text-center text-muted p-3" style="display: none;">Tidak ada petugas yang sedang patroli.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kolom Peta -->
        <div class="col-lg-9 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary" id="map-title">Peta Lokasi Petugas</h6>
                </div>
                <div class="card-body p-0">
                    <div id="map-monitoring"></div>
                </div>
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
    // --- PERBAIKAN: Mengatur level zoom ke 15 ---
    const map = L.map('map-monitoring').setView([-7.05165831256373, 110.44084456583003], 15); // Set view ke Undip dengan zoom 15
    // --- AKHIR PERBAIKAN ---

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let officerMarkers = {};
    let officerPolylines = {};
    let focusedOfficerNik = null;
    let isFirstLoad = true;
    const POLLING_INTERVAL = 7000;

    const mapTitle = document.getElementById('map-title');
    const showAllBtn = document.getElementById('show-all-btn');
    const loadingOfficersEl = document.getElementById('loading-officers');

    function createPopupContent(officer) {
        return `
            <div class="text-center">
                <strong>${officer.nama_lengkap}</strong><br>
                <small>Username: ${officer.nik}</small><br>
                <small>Mulai: ${officer.start_time}</small><br>
                <small>Update: ${officer.last_update}</small><br>
                <span class="badge bg-${officer.status === 'jeda' ? 'warning text-dark' : 'success'}">${officer.status}</span>
            </div>
        `;
    }

    function updateOfficerMarker(officer) {
        const latLng = [officer.latitude, officer.longitude];
        const iconClass = `leaflet-marker-icon officer-avatar ${officer.status === 'jeda' ? 'status-jeda' : ''}`;
        const customIcon = L.icon({
            iconUrl: officer.foto_url,
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38],
            className: iconClass
        });

        if (officerMarkers[officer.nik]) {
            officerMarkers[officer.nik].setLatLng(latLng);
            officerMarkers[officer.nik].setIcon(customIcon);
            officerMarkers[officer.nik].getPopup().setContent(createPopupContent(officer));
        } else {
            officerMarkers[officer.nik] = L.marker(latLng, { icon: customIcon })
                .addTo(map)
                .bindPopup(createPopupContent(officer));
        }

        if (officer.status === 'aktif') {
            if (officerPolylines[officer.nik]) {
                officerPolylines[officer.nik].addLatLng(latLng);
            } else {
                officerPolylines[officer.nik] = L.polyline([latLng], { color: 'blue' }).addTo(map);
            }
        }

        if (focusedOfficerNik === officer.nik) {
            map.panTo(latLng);
        }
    }

    function updateOfficerList(officers) {
        const listContainer = document.getElementById('active-officers-list');
        const noActivePatrolsEl = document.getElementById('no-active-patrols');
        
        listContainer.innerHTML = '';

        if (officers.length === 0) {
            listContainer.appendChild(noActivePatrolsEl);
            noActivePatrolsEl.style.display = 'block';
            loadingOfficersEl.style.display = 'none';
            return;
        }
        
        loadingOfficersEl.style.display = 'none';
        noActivePatrolsEl.style.display = 'none';

        officers.forEach(officer => {
            const listItem = document.createElement('a');
            listItem.href = '#';
            listItem.className = 'list-group-item list-group-item-action d-flex align-items-center';
            listItem.dataset.nik = officer.nik;
            
            if (officer.status === 'jeda') {
                listItem.classList.add('status-jeda');
            }
            
            if (officer.nik === focusedOfficerNik) {
                listItem.classList.add('active');
            }

            listItem.innerHTML = `
                <img src="${officer.foto_url}" alt="${officer.nama_lengkap}" class="officer-avatar me-3">
                <div>
                    <div class="fw-bold">${officer.nama_lengkap}</div>
                    <small class="text-muted">Username: ${officer.nik}</small>
                    ${officer.status === 'jeda' ? '<div class="mt-1"><span class="badge bg-warning text-dark">Jeda</span></div>' : ''}
                </div>
            `;

            listItem.addEventListener('click', function(e) {
                e.preventDefault();
                focusedOfficerNik = this.dataset.nik;
                const marker = officerMarkers[focusedOfficerNik];
                if (marker) {
                    map.flyTo(marker.getLatLng(), 17);
                    marker.openPopup();
                    mapTitle.textContent = `Memantau: ${officer.nama_lengkap}`;
                }
                document.querySelectorAll('.active-patrol-list .list-group-item').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });

            listContainer.appendChild(listItem);
        });
    }

    showAllBtn.addEventListener('click', function() {
        focusedOfficerNik = null;
        mapTitle.textContent = 'Peta Lokasi Petugas';
        document.querySelectorAll('.active-patrol-list .list-group-item').forEach(item => item.classList.remove('active'));
        
        if (Object.keys(officerMarkers).length > 0) {
            const group = new L.featureGroup(Object.values(officerMarkers));
            map.fitBounds(group.getBounds().pad(0.5));
        } else {
            map.setView([-7.05165831256373, 110.44084456583003], 15);
        }
    });

    async function fetchAndUpdatePatrolData() {
        try {
            let liveDataUrl = '';
            @if(request()->is('komandan/*'))
                liveDataUrl = '{{ route("komandan.patroli.live_data") }}';
            @elseif(request()->is('ketua-departemen/*'))
                liveDataUrl = '{{ route("ketua-departemen.patroli.live_data") }}';
            @endif

            if (!liveDataUrl) {
                console.error("Tidak dapat menentukan URL data patroli.");
                loadingOfficersEl.style.display = 'none';
                document.getElementById('no-active-patrols').textContent = 'Kesalahan konfigurasi halaman.';
                document.getElementById('no-active-patrols').style.display = 'block';
                return;
            }
            
            const urlWithCacheBuster = `${liveDataUrl}?_=${new Date().getTime()}`;
            const response = await fetch(urlWithCacheBuster);

            if (!response.ok) throw new Error(`Gagal mengambil data patroli: ${response.statusText}`);
            
            const activeOfficers = await response.json();
            
            const activeNiks = activeOfficers.map(o => o.nik);
            for (const nik in officerMarkers) {
                if (!activeNiks.includes(nik)) {
                    map.removeLayer(officerMarkers[nik]);
                    delete officerMarkers[nik];
                    if (officerPolylines[nik]) {
                        map.removeLayer(officerPolylines[nik]);
                        delete officerPolylines[nik];
                    }
                }
            }

            activeOfficers.forEach(officer => {
                updateOfficerMarker(officer);
            });

            updateOfficerList(activeOfficers);
            
            if (isFirstLoad && activeOfficers.length > 0) {
                const group = new L.featureGroup(Object.values(officerMarkers));
                map.fitBounds(group.getBounds().pad(0.5));
                isFirstLoad = false;
            }

        } catch (error) {
            console.error('Error fetching patrol data:', error);
            const listContainer = document.getElementById('active-officers-list');
            if (listContainer.children.length === 0 || listContainer.querySelector('#loading-officers')) {
                 listContainer.innerHTML = `<p class="text-center text-danger p-3 small">Gagal memuat data. Mencoba lagi...</p>`;
            }
        }
    }

    fetchAndUpdatePatrolData();
    setInterval(fetchAndUpdatePatrolData, POLLING_INTERVAL);
});
</script>
@endpush
