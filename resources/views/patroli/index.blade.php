@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="{{ route('dashboard') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Mulai Patroli</div>
    <div class="right">
        <a href="{{ route('patroli.histori') }}" class="headerButton">
            <ion-icon name="list-outline" style="font-size: 24px;"></ion-icon>
        </a>
    </div>
</div>
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #map {
        width: 100%;
        height: 300px; /* Adjusted height for better mobile view */
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    .patrol-controls {
        display: flex;
        justify-content: space-around; /* Space out buttons */
        align-items: center; /* Vertically align items */
        flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
        gap: 10px; /* Add gap between buttons */
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    .patrol-controls .btn {
        flex-grow: 1; /* Allow buttons to grow and fill space */
        min-width: 120px; /* Minimum width for buttons */
        margin: 5px; /* Add some margin around buttons */
        border-radius: 20px; /* Rounded buttons */
        font-weight: 500;
        padding: 10px 15px;
    }
    .patrol-info {
        text-align: center;
        padding: 15px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    }
    .patrol-info p {
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    .patrol-info strong {
        color: #007bff; /* Primary color for values */
    }
    .status-badge {
        padding: 0.5em 0.75em;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    .status-aktif { background-color: #28a745; color: white; } /* Green */
    .status-jeda { background-color: #ffc107; color: black; } /* Yellow */
    .status-berhenti { background-color: #dc3545; color: white; } /* Red */
    .status-tidak-aktif { background-color: #6c757d; color: white; } /* Gray */

    /* Styling untuk tombol agar lebih menarik */
    .btn-start-patrol { background: linear-gradient(135deg, #28a745 0%, #218838 100%); border: none; color: white !important; }
    .btn-pause-patrol { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border: none; color: black !important; }
    .btn-resume-patrol { background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%); border: none; color: white !important; }
    .btn-stop-patrol { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; color: white !important; }

    .btn ion-icon {
        font-size: 1.2em;
        margin-right: 5px;
        vertical-align: middle;
    }
     /* SweetAlert custom styling */
    .swal2-popup {
        font-family: 'Poppins', sans-serif;
        border-radius: 15px !important;
    }
    .swal2-title {
        font-size: 1.5em !important;
        font-weight: 600 !important;
    }
    .swal2-confirm {
        border-radius: 8px !important;
        padding: 0.6em 1.5em !important;
    }
</style>
@endsection

@section('content')
<div class="section full" style="padding-top: 70px; padding-bottom: 70px;">
    <div class="wide-block pt-2 pb-2">

        <div class="patrol-info mb-3">
            <p>Status: <span id="statusPatroli" class="status-badge status-tidak-aktif">Tidak Aktif</span></p>
            <p>Jarak: <strong id="jarakPatroli">0.00 km</strong></p>
            <p>Durasi: <strong id="durasiPatroli">00:00:00</strong></p>
        </div>

        <div id="map"></div>

        <div class="patrol-controls">
            <button id="btnMulai" class="btn btn-start-patrol">
                <ion-icon name="play-circle-outline"></ion-icon> Mulai
            </button>
            <button id="btnJeda" class="btn btn-pause-patrol" style="display: none;">
                <ion-icon name="pause-circle-outline"></ion-icon> Jeda
            </button>
            <button id="btnLanjutkan" class="btn btn-resume-patrol" style="display: none;">
                <ion-icon name="play-forward-circle-outline"></ion-icon> Lanjutkan
            </button>
            <button id="btnHentikan" class="btn btn-stop-patrol" style="display: none;">
                <ion-icon name="stop-circle-outline"></ion-icon> Hentikan
            </button>
        </div>
        <input type="hidden" id="currentPatrolId" value="{{ $patrolData['id'] ?? '' }}">
    </div>
</div>
@endsection

@push('myscript')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let map;
        let userMarker;
        let patrolPath = @json($patrolData['path'] ?? []); // Path dari server [[lat,lng],[lat,lng]]
        let patrolPolyline;
        let patrolStatus = @json($patrolData['status'] ?? 'tidak_aktif');
        let watchId = null;
        let totalDistance = parseFloat(@json($patrolData['total_distance_meters'] ?? 0));
        
        // Waktu mulai patroli keseluruhan (dari server jika ada, atau saat tombol Mulai ditekan)
        let overallStartTime = patrolStatus !== 'tidak_aktif' && {{ isset($patrolData['start_time']) ? 'true' : 'false' }} ? new Date(parseInt("{{ $patrolData['start_time'] ?? 'null' }}")) : null;
        // Waktu mulai segmen aktif saat ini (saat Mulai atau Lanjutkan ditekan)
        let currentSegmentStartTime = null; 
        // Akumulasi durasi aktif yang sudah tercatat di DB atau sebelum jeda terakhir
        let accumulatedActiveDurationSeconds = parseInt(@json($patrolData['duration_seconds'] ?? 0));

        let timerInterval = null;
        let currentPatrolId = document.getElementById('currentPatrolId').value;

        const statusPatroliEl = document.getElementById('statusPatroli');
        const jarakPatroliEl = document.getElementById('jarakPatroli');
        const durasiPatroliEl = document.getElementById('durasiPatroli');

        const btnMulai = document.getElementById('btnMulai');
        const btnJeda = document.getElementById('btnJeda');
        const btnLanjutkan = document.getElementById('btnLanjutkan');
        const btnHentikan = document.getElementById('btnHentikan');

        async function sendDataToServer(url, data) {
            // ... (fungsi tetap sama)
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Server error: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Error sending data to server:', error);
                Swal.fire('Error Server', error.message || 'Gagal berkomunikasi dengan server.', 'error');
                throw error;
            }
        }
        
        function initMap(lat, lng) {
            // ... (fungsi tetap sama)
            if (map) map.remove();
            map = L.map('map').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            userMarker = L.marker([lat, lng]).addTo(map).bindPopup('Posisi Anda').openPopup();

            if (patrolPath.length > 0) {
                 patrolPolyline = L.polyline(patrolPath.map(p => [p[0], p[1]]), { color: 'blue', weight: 5 }).addTo(map);
                 if(patrolPath.length > 0) {
                    map.panTo(patrolPath[patrolPath.length -1]);
                 }
            }
        }

        function updateUI() {
            // ... (fungsi tetap sama)
            statusPatroliEl.textContent = patrolStatus.charAt(0).toUpperCase() + patrolStatus.slice(1).replace('_', ' ');
            statusPatroliEl.className = `status-badge status-${patrolStatus.replace('_', '-')}`;
            
            btnMulai.style.display = patrolStatus === 'tidak_aktif' ? 'inline-flex' : 'none';
            btnJeda.style.display = patrolStatus === 'aktif' ? 'inline-flex' : 'none';
            btnLanjutkan.style.display = patrolStatus === 'jeda' ? 'inline-flex' : 'none';
            btnHentikan.style.display = (patrolStatus === 'aktif' || patrolStatus === 'jeda') ? 'inline-flex' : 'none';
        }

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                if (patrolStatus === 'aktif' && currentSegmentStartTime) {
                    const now = new Date();
                    const currentSegmentElapsedMs = now - currentSegmentStartTime;
                    const totalElapsedSeconds = accumulatedActiveDurationSeconds + Math.floor(currentSegmentElapsedMs / 1000);

                    const hours = String(Math.floor(totalElapsedSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((totalElapsedSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(totalElapsedSeconds % 60).padStart(2, '0');
                    durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;
                } else if (patrolStatus === 'jeda') {
                    // Tampilkan durasi yang sudah terakumulasi saat dijeda
                    const hours = String(Math.floor(accumulatedActiveDurationSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((accumulatedActiveDurationSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(accumulatedActiveDurationSeconds % 60).padStart(2, '0');
                    durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;
                }
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
            timerInterval = null;
        }
        
        function resetPatrolInfo() {
            jarakPatroliEl.textContent = '0.00 km';
            durasiPatroliEl.textContent = '00:00:00';
            totalDistance = 0;
            patrolPath = [];
            if (patrolPolyline) {
                map.removeLayer(patrolPolyline);
                patrolPolyline = null;
            }
            overallStartTime = null;
            currentSegmentStartTime = null;
            accumulatedActiveDurationSeconds = 0;
        }

        function haversineDistance(coords1, coords2) {
            // ... (fungsi tetap sama)
            function toRad(x) { return x * Math.PI / 180; }
            const R = 6371; 
            const dLat = toRad(coords2[0] - coords1[0]);
            const dLon = toRad(coords2[1] - coords1[1]);
            const lat1Rad = toRad(coords1[0]);
            const lat2Rad = toRad(coords2[0]);
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1Rad) * Math.cos(lat2Rad); 
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            return R * c * 1000; 
        }

        function updateMapPath(latlngArray) { 
            // ... (fungsi tetap sama)
            patrolPath.push(latlngArray);
            if (patrolPolyline) {
                patrolPolyline.setLatLngs(patrolPath);
            } else {
                patrolPolyline = L.polyline(patrolPath, { color: 'blue', weight: 5 }).addTo(map);
            }
            if (patrolPath.length > 1) {
                const prevPoint = patrolPath[patrolPath.length - 2];
                totalDistance += haversineDistance(prevPoint, latlngArray);
                jarakPatroliEl.textContent = (totalDistance / 1000).toFixed(2) + ' km';
            }
            if (map && userMarker) { // Pastikan map dan marker ada
                 map.panTo(latlngArray);
            }
        }

        btnMulai.addEventListener('click', async () => {
            if (navigator.geolocation) {
                resetPatrolInfo();
                try {
                    const data = await sendDataToServer("{{ route('patroli.start') }}", {});
                    if (data.success) {
                        currentPatrolId = data.patrol_id;
                        overallStartTime = new Date(data.start_time); 
                        currentSegmentStartTime = new Date(data.start_time); // Mulai segmen aktif pertama
                        accumulatedActiveDurationSeconds = 0; // Patroli baru
                        patrolStatus = 'aktif';
                        startTimer();
                        updateUI();
                        Swal.fire('Patroli Dimulai', data.message, 'success');
                        console.log("Patroli dimulai dengan ID:", currentPatrolId);

                        if (userMarker) {
                            map.removeLayer(userMarker);
                            userMarker = null;
                        }

                        watchId = navigator.geolocation.watchPosition(
                            async (position) => {
                                const { latitude, longitude, accuracy, speed } = position.coords;
                                const currentLatLngArray = [latitude, longitude];

                                if (!userMarker && map) { // Pastikan map ada
                                     userMarker = L.marker(currentLatLngArray, {icon: L.icon({iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png', iconSize: [25,41], iconAnchor: [12,41]}) }).addTo(map).bindPopup('Posisi Anda Saat Ini');
                                } else if (userMarker) {
                                    userMarker.setLatLng(currentLatLngArray);
                                }

                                if (patrolStatus === 'aktif') {
                                    updateMapPath(currentLatLngArray);
                                    try {
                                        await sendDataToServer("{{ route('patroli.store_point') }}", {
                                            patrol_id: currentPatrolId,
                                            latitude: latitude,
                                            longitude: longitude,
                                            timestamp: new Date().getTime(), 
                                            accuracy: accuracy,
                                            speed: speed === null ? 0 : speed // Handle speed null
                                        });
                                    } catch (error) {
                                        console.warn("Gagal mengirim titik patroli:", error.message);
                                    }
                                }
                            },
                            (error) => {
                                console.error("Error watchPosition:", error);
                                Swal.fire('Error Lokasi', 'Tidak dapat mendapatkan lokasi Anda secara berkelanjutan.', 'error');
                            },
                            { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
                        );
                    }
                } catch (error) { /* Error ditangani di sendDataToServer */ }
            } else {
                Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini.', 'error');
            }
        });

        btnJeda.addEventListener('click', async () => {
            if (!currentPatrolId) return;
            stopTimer(); // Hentikan timer dulu
            if (patrolStatus === 'aktif' && currentSegmentStartTime) {
                const now = new Date();
                const currentSegmentDurationMs = now - currentSegmentStartTime;
                accumulatedActiveDurationSeconds += Math.floor(currentSegmentDurationMs / 1000);
            }
            currentSegmentStartTime = null; // Reset start time segmen

            try {
                const data = await sendDataToServer("{{ route('patroli.pause') }}", { patrol_id: currentPatrolId });
                if (data.success) {
                    patrolStatus = 'jeda';
                    updateUI(); // Update UI setelah status berubah
                    // Tampilkan durasi yang sudah terakumulasi
                    const hours = String(Math.floor(accumulatedActiveDurationSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((accumulatedActiveDurationSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(accumulatedActiveDurationSeconds % 60).padStart(2, '0');
                    durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;
                    Swal.fire('Patroli Dijeda', data.message, 'info');
                } else {
                    startTimer(); // Jika gagal, lanjutkan timer
                }
            } catch (error) {
                 startTimer(); // Jika gagal, lanjutkan timer
            }
        });

        btnLanjutkan.addEventListener('click', async () => {
            if (!currentPatrolId) return;
            try {
                const data = await sendDataToServer("{{ route('patroli.resume') }}", { patrol_id: currentPatrolId });
                if (data.success) {
                    patrolStatus = 'aktif';
                    currentSegmentStartTime = new Date(); // Mulai segmen aktif baru
                    // accumulatedActiveDurationSeconds sudah berisi total durasi aktif sebelum jeda ini
                    startTimer(); 
                    updateUI();
                    Swal.fire('Patroli Dilanjutkan', data.message, 'info');
                }
            } catch (error) { /* Error ditangani di sendDataToServer */ }
        });

        btnHentikan.addEventListener('click', async () => {
            if (!currentPatrolId) return;
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            let finalDurationSeconds = accumulatedActiveDurationSeconds;
            if (patrolStatus === 'aktif' && currentSegmentStartTime) { // Jika dihentikan saat aktif
                const now = new Date();
                const lastSegmentMs = now - currentSegmentStartTime;
                finalDurationSeconds += Math.floor(lastSegmentMs / 1000);
            }
            // Jika dihentikan saat jeda, finalDurationSeconds sudah benar dari accumulatedActiveDurationSeconds terakhir

            stopTimer(); 
            durasiPatroliEl.textContent = (() => { // Update tampilan durasi final
                const hours = String(Math.floor(finalDurationSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((finalDurationSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(finalDurationSeconds % 60).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            })();

            try {
                const data = await sendDataToServer("{{ route('patroli.stop') }}", {
                    patrol_id: currentPatrolId,
                    total_distance_meters: totalDistance,
                    duration_seconds: finalDurationSeconds 
                });
                if (data.success) {
                    patrolStatus = 'tidak_aktif'; 
                    updateUI(); 
                    Swal.fire('Patroli Selesai', `Jarak: ${(totalDistance/1000).toFixed(2)} km, Durasi: ${durasiPatroliEl.textContent}`, 'success')
                        .then(() => {
                             resetPatrolInfo(); 
                             currentPatrolId = null; 
                        });
                }
            } catch (error) {
                 updateUI(); // Kembalikan UI jika gagal stop
            }
        });

        // Inisialisasi awal
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    initMap(latitude, longitude);
                    
                    // Logic untuk melanjutkan patroli yang ada
                    if (currentPatrolId && patrolStatus !== 'tidak_aktif') {
                        jarakPatroliEl.textContent = (totalDistance / 1000).toFixed(2) + ' km';
                        
                        const hours = String(Math.floor(accumulatedActiveDurationSeconds / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((accumulatedActiveDurationSeconds % 3600) / 60)).padStart(2, '0');
                        const seconds = String(accumulatedActiveDurationSeconds % 60).padStart(2, '0');
                        durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;

                        if (patrolStatus === 'aktif') {
                            currentSegmentStartTime = new Date(); // Anggap segmen aktif baru dimulai sekarang
                            startTimer();
                            // Lanjutkan watchPosition jika patroli aktif
                             watchId = navigator.geolocation.watchPosition(
                                async (pos) => {
                                    const { latitude: lat, longitude: lng, accuracy, speed } = pos.coords;
                                    const currentLatLngArray = [lat, lng];
                                    if (userMarker) userMarker.setLatLng(currentLatLngArray); else userMarker = L.marker(currentLatLngArray).addTo(map);
                                    if (patrolStatus === 'aktif') {
                                        updateMapPath(currentLatLngArray);
                                        try { await sendDataToServer("{{ route('patroli.store_point') }}", { patrol_id: currentPatrolId, latitude: lat, longitude: lng, timestamp: new Date().getTime(), accuracy: accuracy, speed: speed === null ? 0 : speed }); } catch (err) { console.warn("Gagal kirim titik saat lanjut patroli:", err.message); }
                                    }
                                }, (err) => console.error("Error watchPosition lanjut:", err), { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
                            );
                        }
                    } else {
                        resetPatrolInfo(); // Jika tidak ada patroli aktif, pastikan UI bersih
                    }
                    updateUI();
                },
                (error) => {
                    console.error("Error getCurrentPosition:", error);
                    Swal.fire('Error Lokasi', 'Tidak dapat mendapatkan lokasi awal Anda. Menggunakan lokasi default.', 'warning');
                    initMap(-6.966667, 110.416664); 
                    updateUI();
                }
            );
        } else {
            Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini.', 'error');
            initMap(-6.966667, 110.416664); 
            updateUI();
        }

        window.addEventListener('beforeunload', () => {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            stopTimer();
        });
    });
</script>
@endpush
