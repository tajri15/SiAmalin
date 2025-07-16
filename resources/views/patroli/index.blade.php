{{-- File: resources/views/patroli/index.blade.php --}}
@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; box-shadow: 0 4px 20px rgba(79, 172, 254, 0.3);">
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
<!-- * App Header -->

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    #map {
        width: 100%;
        height: 300px;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    .patrol-controls {
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    .patrol-controls .btn {
        flex-grow: 1;
        min-width: 120px;
        margin: 5px;
        border-radius: 20px;
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
        color: #007bff;
    }
    .radius-check {
        padding: 10px;
        margin-bottom: 1rem;
        border-radius: 10px;
        text-align: center;
        font-size: 0.9rem;
    }
    .radius-check.in-range {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .radius-check.out-of-range {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .radius-check.checking {
        background-color: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    /* Outside radius warning during patrol */
    .patrol-warning {
        padding: 15px;
        margin-bottom: 1rem;
        border-radius: 10px;
        text-align: center;
        font-weight: 600;
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
        display: none;
        animation: pulse 2s infinite;
    }
    .patrol-warning.show {
        display: block;
    }

    /* NEW: Face verification styles */
    .face-verification-card {
        padding: 15px;
        margin-bottom: 1rem;
        border-radius: 10px;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        display: none;
    }
    .face-verification-card.show {
        display: block;
    }
    .face-verification-card.verified {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .webcam-container {
        position: relative;
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        background: #000;
        margin-bottom: 10px;
    }
    #faceVideo {
        width: 100%;
        display: block;
    }
    #faceCanvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    .status-badge {
        padding: 0.5em 0.75em;
        border-radius: 0.25rem;
        font-weight: 600;
    }
    .status-aktif { background-color: #28a745; color: white; }
    .status-jeda { background-color: #ffc107; color: black; }
    .status-berhenti { background-color: #dc3545; color: white; }
    .status-tidak-aktif { background-color: #6c757d; color: white; }

    .btn-start-patrol { background: linear-gradient(135deg, #28a745 0%, #218838 100%); border: none; color: white !important; }
    .btn-pause-patrol { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border: none; color: black !important; }
    .btn-resume-patrol { background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%); border: none; color: white !important; }
    .btn-stop-patrol { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; color: white !important; }
    .btn-verify-face { background: linear-gradient(135deg, #6f42c1 0%, #5a2d91 100%); border: none; color: white !important; }

    .btn ion-icon {
        font-size: 1.2em;
        margin-right: 5px;
        vertical-align: middle;
    }
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

        <div class="radius-check checking" id="radiusCheckContainer">
            <p class="mb-0" id="locationStatus">Mengecek lokasi Anda...</p>
            <small id="distanceToOffice"></small>
        </div>

        <!-- Warning when outside radius during patrol -->
        <div class="patrol-warning" id="patrolWarning">
            <ion-icon name="warning-outline" style="font-size: 1.5em; margin-right: 8px;"></ion-icon>
            <div>Anda berada di luar radius kerja!</div>
            <small>Patroli tidak terekam sampai Anda kembali ke area kerja</small>
        </div>

        <!-- NEW: Face Verification Card -->
        <div class="face-verification-card" id="faceVerificationCard">
            <h6 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                <ion-icon name="person-circle-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                Verifikasi Wajah Patroli
            </h6>
            <div class="webcam-container" id="faceWebcamContainer" style="display: none;">
                <video id="faceVideo" autoplay playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>
            <div id="faceVerificationStatus" class="text-center mt-2"></div>
            <div class="text-center mt-3">
                <button type="button" id="startFaceVerificationBtn" class="btn btn-verify-face" style="border-radius: 20px; padding: 8px 20px;">
                    <ion-icon name="scan-outline"></ion-icon> Mulai Verifikasi
                </button>
            </div>
        </div>

        <div id="map"></div>

        <div class="patrol-controls">
            <button id="btnMulai" class="btn btn-start-patrol" disabled>
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
        <input type="hidden" id="userLatitude">
        <input type="hidden" id="userLongitude">
        <input type="hidden" id="faceVerified" value="{{ isset($patrolData['face_verified']) ? ($patrolData['face_verified'] ? 'true' : 'false') : 'false' }}">
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
{{-- Face API --}}
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // State Variables
        let map;
        let userMarker;
        let patrolPath = @json($patrolData['path'] ?? []);
        let patrolPolyline;
        let patrolStatus = @json($patrolData['status'] ?? 'tidak_aktif');
        let watchId = null;
        let totalDistance = parseFloat(@json($patrolData['total_distance_meters'] ?? 0));
        let currentSegmentStartTime = null; 
        let accumulatedActiveDurationSeconds = parseInt(@json($patrolData['duration_seconds'] ?? 0));
        let timerInterval = null;
        let currentPatrolId = document.getElementById('currentPatrolId').value;
        let isBusy = false;
        const MINIMUM_DISTANCE_THRESHOLD = 2;
        
        // Radius tracking variables
        let isCurrentlyInRadius = true;
        let lastRadiusCheckTime = 0;
        let radiusCheckInterval = null;
        let hasShownOutsideWarning = false;
        
        // NEW: Face verification variables
        let faceStream = null;
        let isFaceVerified = document.getElementById('faceVerified').value === 'true';
        let faceDetectionInterval;
        let verificationSucceeded = false;
        const faceApiThreshold = 0.5;
        const storedDescriptor = @if(isset($faceDescriptor) && $faceDescriptor !== 'null') new Float32Array(Object.values(JSON.parse('{!! $faceDescriptor !!}'))) @else null @endif;
        
        const officeLocation = @json($officeLocation);
        const officeRadius = @json($officeRadius);
        const officeLat = officeLocation ? officeLocation.coordinates[1] : null;
        const officeLng = officeLocation ? officeLocation.coordinates[0] : null;

        // DOM Elements
        const statusPatroliEl = document.getElementById('statusPatroli');
        const jarakPatroliEl = document.getElementById('jarakPatroli');
        const durasiPatroliEl = document.getElementById('durasiPatroli');
        const btnMulai = document.getElementById('btnMulai');
        const btnJeda = document.getElementById('btnJeda');
        const btnLanjutkan = document.getElementById('btnLanjutkan');
        const btnHentikan = document.getElementById('btnHentikan');
        const userLatInput = document.getElementById('userLatitude');
        const userLngInput = document.getElementById('userLongitude');
        const radiusCheckContainer = document.getElementById('radiusCheckContainer');
        const locationStatusEl = document.getElementById('locationStatus');
        const distanceToOfficeEl = document.getElementById('distanceToOffice');
        const patrolWarning = document.getElementById('patrolWarning');
        
        // NEW: Face verification elements
        const faceVerificationCard = document.getElementById('faceVerificationCard');
        const faceWebcamContainer = document.getElementById('faceWebcamContainer');
        const faceVideo = document.getElementById('faceVideo');
        const faceCanvas = document.getElementById('faceCanvas');
        const faceVerificationStatus = document.getElementById('faceVerificationStatus');
        const startFaceVerificationBtn = document.getElementById('startFaceVerificationBtn');

        // Utility Functions
        async function sendDataToServer(url, data) {
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
            if (map) map.remove();
            map = L.map('map').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            userMarker = L.marker([lat, lng]).addTo(map).bindPopup('Posisi Anda').openPopup();

            if (officeLat && officeLng) {
                L.circle([officeLat, officeLng], { 
                    radius: officeRadius,
                    color: '#007bff',
                    fillColor: '#007bff',
                    fillOpacity: 0.1,
                    weight: 2
                }).addTo(map);
            }

            if (patrolPath.length > 0) {
                 patrolPolyline = L.polyline(patrolPath.map(p => [p[0], p[1]]), { color: 'blue', weight: 5 }).addTo(map);
                 if(patrolPath.length > 0) {
                    map.panTo(patrolPath[patrolPath.length -1]);
                 }
            }
        }

        function updateUI() {
            statusPatroliEl.textContent = patrolStatus.charAt(0).toUpperCase() + patrolStatus.slice(1).replace('_', ' ');
            statusPatroliEl.className = `status-badge status-${patrolStatus.replace('_', '-')}`;
            
            const isInRadius = radiusCheckContainer.classList.contains('in-range');
            btnMulai.style.display = patrolStatus === 'tidak_aktif' ? 'inline-flex' : 'none';
            btnMulai.disabled = !isInRadius;

            btnJeda.style.display = patrolStatus === 'aktif' ? 'inline-flex' : 'none';
            btnLanjutkan.style.display = patrolStatus === 'jeda' ? 'inline-flex' : 'none';
            btnHentikan.style.display = (patrolStatus === 'aktif' || patrolStatus === 'jeda') ? 'inline-flex' : 'none';
            
            // NEW: Update face verification card visibility
            if (patrolStatus === 'aktif' || patrolStatus === 'jeda') {
                if (!isFaceVerified) {
                    faceVerificationCard.classList.add('show');
                    faceVerificationCard.classList.remove('verified');
                } else {
                    faceVerificationCard.classList.add('show', 'verified');
                    faceVerificationStatus.innerHTML = '<div class="alert alert-success p-2 small"><strong>Wajah Terverifikasi!</strong></div>';
                }
            } else {
                faceVerificationCard.classList.remove('show');
            }
        }

        // NEW: Face verification functions
        async function initFaceVerification() {
            if (!storedDescriptor) {
                faceVerificationStatus.innerHTML = '<div class="alert alert-danger p-2 small">Data wajah tidak tersedia. Hubungi admin.</div>';
                return;
            }

            try {
                faceVerificationStatus.innerHTML = '<div class="alert alert-info p-2 small">Mempersiapkan kamera verifikasi...</div>';
                stopFaceStream();
                faceStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 320, height: 240, facingMode: 'user' }, 
                    audio: false 
                });
                faceVideo.srcObject = faceStream;
                faceWebcamContainer.style.display = 'block';
                startFaceVerificationBtn.style.display = 'none';
                
                faceVideo.onloadedmetadata = () => {
                    faceVerificationStatus.innerHTML = '<div class="alert alert-primary p-2 small">Posisikan wajah Anda di kamera.</div>';
                    startFaceDetection();
                };
            } catch (error) {
                faceVerificationStatus.innerHTML = `<div class="alert alert-danger p-2 small">Error kamera: ${error.message}</div>`;
            }
        }

        function startFaceDetection() {
            const displaySize = { width: faceVideo.videoWidth, height: faceVideo.videoHeight };
            faceapi.matchDimensions(faceCanvas, displaySize);

            faceDetectionInterval = setInterval(async () => {
                if (verificationSucceeded || isFaceVerified) return;

                const detection = await faceapi.detectSingleFace(faceVideo, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks().withFaceDescriptor();
                
                if (detection) {
                    const distance = faceapi.euclideanDistance(storedDescriptor, detection.descriptor);
                    if (distance < faceApiThreshold) {
                        verificationSucceeded = true;
                        clearInterval(faceDetectionInterval);

                        faceVerificationStatus.innerHTML = '<div class="alert alert-success p-2 small"><strong>Verifikasi Berhasil!</strong> Mengirim data...</div>';
                        faceVideo.style.border = '3px solid #1cc88a';
                        
                        // Capture and send face image
                        const canvasForSaving = document.createElement('canvas');
                        canvasForSaving.width = faceVideo.videoWidth;
                        canvasForSaving.height = faceVideo.videoHeight;
                        canvasForSaving.getContext('2d').drawImage(faceVideo, 0, 0);
                        
                        const faceImageData = canvasForSaving.toDataURL('image/jpeg');
                        
                        try {
                            const result = await sendDataToServer("{{ route('patroli.verify_face') }}", {
                                patrol_id: currentPatrolId,
                                face_image: faceImageData
                            });
                            
                            if (result.success) {
                                isFaceVerified = true;
                                faceVerificationCard.classList.add('verified');
                                faceVerificationStatus.innerHTML = '<div class="alert alert-success p-2 small"><strong>Wajah Terverifikasi!</strong></div>';
                                stopFaceStream();
                                faceWebcamContainer.style.display = 'none';
                                Swal.fire('Berhasil!', 'Verifikasi wajah berhasil.', 'success');
                            }
                        } catch (error) {
                            faceVerificationStatus.innerHTML = '<div class="alert alert-danger p-2 small">Gagal mengirim data verifikasi.</div>';
                            verificationSucceeded = false;
                        }
                    } else {
                        if (!verificationSucceeded) {
                            faceVerificationStatus.innerHTML = '<div class="alert alert-warning p-2 small">Wajah tidak dikenali. Coba lagi.</div>';
                        }
                    }
                } else {
                    if (!verificationSucceeded) {
                        faceVerificationStatus.innerHTML = '<div class="alert alert-secondary p-2 small">Arahkan wajah ke kamera...</div>';
                    }
                }
            }, 1000);
        }

        function stopFaceStream() {
            if (faceStream) {
                faceStream.getTracks().forEach(track => track.stop());
                faceStream = null;
            }
            if (faceDetectionInterval) {
                clearInterval(faceDetectionInterval);
            }
        }

        // Event Listeners
        startFaceVerificationBtn.addEventListener('click', initFaceVerification);

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
            currentSegmentStartTime = null;
            accumulatedActiveDurationSeconds = 0;
            
            // Reset radius tracking
            hidePatrolWarning();
            isCurrentlyInRadius = true;
            hasShownOutsideWarning = false;
            
            // NEW: Reset face verification
            isFaceVerified = false;
            verificationSucceeded = false;
            stopFaceStream();
            faceWebcamContainer.style.display = 'none';
            startFaceVerificationBtn.style.display = 'inline-block';
        }

        function haversineDistance(coords1, coords2) {
            function toRad(x) { return x * Math.PI / 180; }
            const R = 6371e3;
            const lat1 = coords1[0];
            const lon1 = coords1[1];
            const lat2 = coords2[0];
            const lon2 = coords2[1];

            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function updateMapPath(latlngArray) { 
            if (patrolPath.length > 0) {
                const prevPoint = patrolPath[patrolPath.length - 1];
                totalDistance += haversineDistance(prevPoint, latlngArray);
                jarakPatroliEl.textContent = (totalDistance / 1000).toFixed(2) + ' km';
            }
            patrolPath.push(latlngArray);

            if (patrolPolyline) {
                patrolPolyline.setLatLngs(patrolPath);
            } else {
                patrolPolyline = L.polyline(patrolPath, { color: 'blue', weight: 5 }).addTo(map);
            }
            
            if (map && userMarker) {
                 map.panTo(latlngArray);
            }
        }

        function showPatrolWarning() {
            patrolWarning.classList.add('show');
        }

        function hidePatrolWarning() {
            patrolWarning.classList.remove('show');
        }

        async function checkCurrentRadius(latitude, longitude) {
            try {
                const response = await fetch("{{ route('patroli.check_radius') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    return data.within_radius;
                }
            } catch (error) {
                console.warn('Error checking radius:', error);
            }
            return true;
        }

        function updateLocationStatus(position) {
            const { latitude, longitude } = position.coords;
            userLatInput.value = latitude;
            userLngInput.value = longitude;

            if (!officeLat || !officeLng) {
                locationStatusEl.textContent = 'Lokasi kantor belum diatur.';
                radiusCheckContainer.className = 'radius-check out-of-range';
                btnMulai.disabled = true;
                return;
            }

            const distance = haversineDistance([latitude, longitude], [officeLat, officeLng]);
            distanceToOfficeEl.textContent = `Jarak: ${distance.toFixed(0)}m dari kantor.`;

            if (distance <= officeRadius) {
                locationStatusEl.textContent = 'Dalam Jangkauan';
                radiusCheckContainer.className = 'radius-check in-range';
                if (patrolStatus === 'tidak_aktif') {
                    btnMulai.disabled = false;
                }
                
                if ((patrolStatus === 'aktif' || patrolStatus === 'jeda') && !isCurrentlyInRadius) {
                    isCurrentlyInRadius = true;
                    hidePatrolWarning();
                    if (!hasShownOutsideWarning) {
                        Swal.fire({
                            title: 'Kembali dalam Radius',
                            text: 'Anda telah kembali ke area kerja. Patroli akan terekam kembali.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            } else {
                locationStatusEl.textContent = 'Luar Jangkauan';
                radiusCheckContainer.className = 'radius-check out-of-range';
                btnMulai.disabled = true;
                
                if ((patrolStatus === 'aktif' || patrolStatus === 'jeda') && isCurrentlyInRadius) {
                    isCurrentlyInRadius = false;
                    showPatrolWarning();
                    if (!hasShownOutsideWarning) {
                        hasShownOutsideWarning = true;
                        Swal.fire({
                            title: 'Keluar dari Radius!',
                            text: 'Anda telah keluar dari area kerja. Patroli tidak akan terekam hingga Anda kembali ke area kerja.',
                            icon: 'warning',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                }
            }
        }

        function startWatchingLocation() {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            watchId = navigator.geolocation.watchPosition(
                async (position) => {
                    const { latitude, longitude, accuracy, speed } = position.coords;
                    const currentLatLngArray = [latitude, longitude];

                    updateLocationStatus(position);

                    if (!userMarker && map) {
                         userMarker = L.marker(currentLatLngArray, {icon: L.icon({iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png', iconSize: [25,41], iconAnchor: [12,41]}) }).addTo(map).bindPopup('Posisi Anda Saat Ini');
                    } else if (userMarker) {
                        userMarker.setLatLng(currentLatLngArray);
                    }

                    if (patrolStatus === 'aktif' && !isBusy && isCurrentlyInRadius) {
                        const lastPoint = patrolPath.length > 0 ? patrolPath[patrolPath.length - 1] : null;
                        if (!lastPoint || haversineDistance(lastPoint, currentLatLngArray) > MINIMUM_DISTANCE_THRESHOLD) {
                            updateMapPath(currentLatLngArray);
                            try {
                                const response = await sendDataToServer("{{ route('patroli.store_point') }}", {
                                    patrol_id: currentPatrolId,
                                    latitude: latitude,
                                    longitude: longitude,
                                    timestamp: new Date().getTime(), 
                                    accuracy: accuracy,
                                    speed: speed === null ? 0 : speed
                                });
                                
                                if (response && response.outside_radius) {
                                    isCurrentlyInRadius = false;
                                    showPatrolWarning();
                                    if (!hasShownOutsideWarning) {
                                        hasShownOutsideWarning = true;
                                        Swal.fire({
                                            title: 'Keluar dari Radius!',
                                            text: 'Anda telah keluar dari area kerja. Patroli tidak akan terekam hingga Anda kembali ke area kerja.',
                                            icon: 'warning',
                                            confirmButtonText: 'Mengerti'
                                        });
                                    }
                                }
                            } catch (error) {
                                console.warn("Gagal mengirim titik patroli:", error.message);
                            }
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

        function stopWatchingLocation() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
        }

        // Event Listeners for Patrol Controls
        btnMulai.addEventListener('click', async () => {
            if (isBusy) return;
            isBusy = true;

            const lat = userLatInput.value;
            const lng = userLngInput.value;

            if (!lat || !lng) {
                Swal.fire('Error', 'Lokasi Anda belum terdeteksi. Mohon tunggu sebentar.', 'error');
                isBusy = false;
                return;
            }

            if (navigator.geolocation) {
                resetPatrolInfo();
                try {
                    const data = await sendDataToServer("{{ route('patroli.start') }}", {
                        latitude: lat,
                        longitude: lng
                    });
                    if (data.success) {
                        currentPatrolId = data.patrol_id;
                        currentSegmentStartTime = new Date(data.start_time);
                        accumulatedActiveDurationSeconds = 0;
                        patrolStatus = 'aktif';
                        isCurrentlyInRadius = true;
                        hasShownOutsideWarning = false;
                        startTimer();
                        updateUI();
                        Swal.fire('Patroli Dimulai', data.message, 'success');
                        
                        if (userMarker) {
                            map.removeLayer(userMarker);
                            userMarker = null;
                        }
                        startWatchingLocation();
                    }
                } catch (error) { /* Handled */ }
            } else {
                Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini.', 'error');
            }
            isBusy = false;
        });
        
        btnJeda.addEventListener('click', async () => {
            if (isBusy || !currentPatrolId) return;
            isBusy = true;
            stopTimer();
            stopWatchingLocation();

            if (patrolStatus === 'aktif' && currentSegmentStartTime) {
                const now = new Date();
                const currentSegmentDurationMs = now - currentSegmentStartTime;
                accumulatedActiveDurationSeconds += Math.floor(currentSegmentDurationMs / 1000);
            }
            currentSegmentStartTime = null;

            try {
                const data = await sendDataToServer("{{ route('patroli.pause') }}", { patrol_id: currentPatrolId });
                if (data.success) {
                    patrolStatus = 'jeda';
                    hidePatrolWarning();
                    updateUI();
                    const hours = String(Math.floor(accumulatedActiveDurationSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((accumulatedActiveDurationSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(accumulatedActiveDurationSeconds % 60).padStart(2, '0');
                    durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;
                    Swal.fire('Patroli Dijeda', data.message, 'info');
                } else {
                    startTimer();
                    startWatchingLocation();
                }
            } catch (error) {
                 startTimer();
                 startWatchingLocation();
            }
            isBusy = false;
        });

        btnLanjutkan.addEventListener('click', async () => {
            if (isBusy || !currentPatrolId) return;
            isBusy = true;
            try {
                const data = await sendDataToServer("{{ route('patroli.resume') }}", { patrol_id: currentPatrolId });
                if (data.success) {
                    patrolStatus = 'aktif';
                    currentSegmentStartTime = new Date();
                    hasShownOutsideWarning = false;
                    startTimer(); 
                    updateUI();
                    startWatchingLocation();
                    Swal.fire('Patroli Dilanjutkan', data.message, 'info');
                }
            } catch (error) { /* Handled */ }
            isBusy = false;
        });

        btnHentikan.addEventListener('click', async () => {
            if (isBusy || !currentPatrolId) return;
            
            // NEW: Check if face verification is required
            if (!isFaceVerified) {
                Swal.fire({
                    title: 'Verifikasi Wajah Diperlukan',
                    text: 'Anda harus melakukan verifikasi wajah sebelum menghentikan patroli.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            isBusy = true;
            stopWatchingLocation();
            
            let finalDurationSeconds = accumulatedActiveDurationSeconds;
            if (patrolStatus === 'aktif' && currentSegmentStartTime) {
                const now = new Date();
                const lastSegmentMs = now - currentSegmentStartTime;
                finalDurationSeconds += Math.floor(lastSegmentMs / 1000);
            }
            
            stopTimer();
            durasiPatroliEl.textContent = (() => {
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
                             updateLocationStatus({coords: {latitude: parseFloat(userLatInput.value), longitude: parseFloat(userLngInput.value)}});
                        });
                } else if (data.face_verification_required) {
                    Swal.fire({
                        title: 'Verifikasi Wajah Diperlukan',
                        text: data.message,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                 updateUI(); 
            }
            isBusy = false;
        });

        function initializePage() {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Geolocation tidak didukung oleh browser ini.', 'error');
                locationStatusEl.textContent = 'Geolocation tidak didukung.';
                radiusCheckContainer.className = 'radius-check out-of-range';
                initMap(-6.966667, 110.4204); 
                updateUI();
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    initMap(latitude, longitude);
                    updateLocationStatus(position);
                    
                    if (currentPatrolId && patrolStatus !== 'tidak_aktif') {
                        jarakPatroliEl.textContent = (totalDistance / 1000).toFixed(2) + ' km';
                        
                        const hours = String(Math.floor(accumulatedActiveDurationSeconds / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((accumulatedActiveDurationSeconds % 3600) / 60)).padStart(2, '0');
                        const seconds = String(accumulatedActiveDurationSeconds % 60).padStart(2, '0');
                        durasiPatroliEl.textContent = `${hours}:${minutes}:${seconds}`;

                        if (patrolStatus === 'aktif') {
                            currentSegmentStartTime = new Date(); 
                            startTimer();
                            startWatchingLocation();
                        }
                    } else {
                        resetPatrolInfo();
                    }
                    updateUI();
                },
                (error) => {
                    console.error("Error getCurrentPosition:", error);
                    Swal.fire('Error Lokasi', 'Tidak dapat mendapatkan lokasi awal Anda. Menggunakan lokasi default.', 'warning');
                    initMap(-6.966667, 110.4204); 
                    updateLocationStatus({coords: {latitude: -6.966667, longitude: 110.4204}});
                    updateUI();
                }
            );
        }

        // Initialize Face API models
        @if(isset($faceDescriptor) && $faceDescriptor !== 'null')
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset("models") }}'),
            faceapi.nets.faceLandmark68Net.loadFromUri('{{ asset("models") }}'),
            faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset("models") }}')
        ]).then(() => {
            console.log('Face API models loaded successfully');
        }).catch(err => {
            console.error('Failed to load Face API models:', err);
            faceVerificationStatus.innerHTML = '<div class="alert alert-danger p-2 small">Gagal memuat model wajah. Periksa koneksi.</div>';
        });
        @endif

        initializePage();

        window.addEventListener('beforeunload', () => {
            stopWatchingLocation();
            stopTimer();
            stopFaceStream();
        });
    });
</script>
@endpush