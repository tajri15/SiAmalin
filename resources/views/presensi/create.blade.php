{{-- File: resources/views/presensi/create.blade.php --}}
@extends('layouts.presensi')

@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">E-Presensi</div>
    <div class="right"></div>
</div>
<!-- * App Header -->

<style>
    .presensi-content {
        padding-bottom: 80px;
    }
    .webcam-container {
        position: relative;
        width: 100%;
        margin: auto;
        border-radius: 15px;
        overflow: hidden;
        background: #000;
        border: 3px solid #ddd;
        transition: border-color 0.3s ease;
    }
    #videoElement { width: 100%; display: block; }
    #canvasElement { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
    .btn-verify { transition: all 0.3s ease; position: relative; }
    .btn-verify:disabled { opacity: 0.7; }
    .btn-verify .spinner { display: none; position: absolute; right: 10px; }
    .btn-verify.loading .spinner { display: inline-block; }
    #map { height: 300px; width: 100%; border-radius: 15px; margin-top: 15px; }
    .distance-indicator { position: absolute; bottom: 10px; left: 10px; background: rgba(0, 0, 0, 0.7); color: white; padding: 5px 10px; border-radius: 10px; font-size: 12px; z-index: 1000; }
    .info-card { margin-top: 15px; }
    .success-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .success-modal.active { opacity: 1; visibility: visible; }
    .success-modal-content { background-color: white; padding: 25px; border-radius: 15px; text-align: center; max-width: 320px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: modalFadeIn 0.4s ease; }
    .success-modal-icon { font-size: 60px; color: #28a745; margin-bottom: 20px; animation: iconBounce 0.6s ease; }
    .success-modal-message { font-size: 18px; margin-bottom: 25px; font-weight: 500; color: #333; line-height: 1.4; }
    .success-modal-btn { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 500; width: 100%; transition: all 0.3s ease; }
    .success-modal-btn:hover { background: #218838; transform: translateY(-2px); }
    @keyframes modalFadeIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes iconBounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
</style>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="presensi-content">
    <div class="row" style="margin-top: 70px">
        <div class="col">
            @if (!empty($pesanJadwal))
                <div class="alert alert-danger m-2">
                    <h4><ion-icon name="close-circle-outline"></ion-icon> Presensi Tidak Tersedia</h4>
                    <p class="mb-0">{{ $pesanJadwal }}</p>
                </div>
            @elseif (session('error_presensi_create'))
                 <div class="alert alert-danger m-2">
                    <h4><ion-icon name="alert-circle-outline"></ion-icon> Gagal Memuat Presensi</h4>
                    <p class="mb-0">{{ session('error_presensi_create') }}</p>
                </div>
            @else
                <input type="hidden" id="lokasi">
                <input type="hidden" id="nik" value="{{ Auth::guard('karyawan')->user()->nik }}">
                
                <div class="text-center mb-2">
                    @if ($presensiAktif)
                        <h5 style="font-weight: 600;">Absen Pulang</h5>
                    @else
                        <h5 style="font-weight: 600;">Absen Masuk</h5>
                    @endif
                    <div id="verificationStatus" class="text-center mt-2">
                        <small class="text-muted">Posisikan wajah Anda pada kamera</small>
                    </div>
                </div>
                
                <div class="webcam-container mb-3">
                    <video id="videoElement" autoplay playsinline></video>
                    <canvas id="canvasElement"></canvas>
                    <div class="distance-indicator" id="distance-indicator" style="display: none;">
                        Jarak: <span id="distance-value">0</span>m
                    </div>
                </div>
                
                <div class="text-center mb-3">
                    <button id="verifyBtn" class="btn btn-primary btn-block btn-verify" disabled>
                        <ion-icon name="camera-outline"></ion-icon>
                        <span id="verifyText">{{ $presensiAktif ? 'ABSEN PULANG' : 'ABSEN MASUK' }}</span>
                        <span class="spinner spinner-border spinner-border-sm"></span>
                    </button>
                </div>
                
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                
                <div class="alert alert-info">
                    <small>
                        <ion-icon name="information-circle-outline"></ion-icon>
                        Untuk absen, pastikan wajah terlihat jelas dan berada dalam radius {{ $user->office_radius ?? 55 }} meter dari kantor.
                    </small>
                </div>
                
                <div class="row mt-2">
                    <div class="col">
                        <div id="map"></div>
                    </div>
                </div>
                
                <div class="card info-card">
                    <div class="card-body">
                        <h5>Petunjuk:</h5>
                        <ul class="mb-0">
                            <li>Hadapkan wajah ke kamera</li>
                            <li>Pastikan pencahayaan cukup</li>
                            <li>Tombol absen akan aktif jika wajah terverifikasi dan Anda berada di dalam radius kantor.</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="success-modal" id="successModal">
    <div class="success-modal-content">
        <div class="success-modal-icon">
            <ion-icon name="checkmark-circle"></ion-icon>
        </div>
        <div class="success-modal-message" id="successMessage"></div>
        <button class="success-modal-btn" id="modalCloseBtn">OK</button>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('videoElement')) {
            @if (!empty($user->office_location) && isset($faceDescriptor))
                const video = document.getElementById('videoElement');
                const canvas = document.getElementById('canvasElement');
                const webcamContainer = document.querySelector('.webcam-container');
                const verifyBtn = document.getElementById('verifyBtn');
                const verifyText = document.getElementById('verifyText');
                const errorAlert = document.getElementById('errorAlert');
                const lokasiInput = document.getElementById('lokasi');
                const distanceIndicator = document.getElementById('distance-indicator');
                const distanceValue = document.getElementById('distance-value');
                const successModal = document.getElementById('successModal');
                const successMessage = document.getElementById('successMessage');
                const modalCloseBtn = document.getElementById('modalCloseBtn');
                const verificationStatus = document.getElementById('verificationStatus');
                
                let faceStream;
                let faceDetectionInterval;
                let map, userMarker, officeCircle;
                let distance = Infinity;
                let isFaceVerified = false;
                
                const officeLat = {{ $user->office_location['coordinates'][1] }};
                const officeLng = {{ $user->office_location['coordinates'][0] }};
                const maxRadius = {{ $user->office_radius }};
                const faceApiThreshold = 0.5;
                const storedDescriptor = new Float32Array(Object.values(JSON.parse('{!! $faceDescriptor !!}')));

                function stopStream(stream) {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                }

                function calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371e3;
                    const φ1 = lat1 * Math.PI / 180;
                    const φ2 = lat2 * Math.PI / 180;
                    const Δφ = (lat2 - lat1) * Math.PI / 180;
                    const Δλ = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c;
                }

                function updateDistanceUI(dist) {
                    distanceValue.textContent = dist.toFixed(0);
                    distanceIndicator.style.backgroundColor = dist <= maxRadius ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)';
                    if (officeCircle) {
                        officeCircle.setStyle({
                            color: dist <= maxRadius ? '#4e73df' : '#dc3545',
                            fillColor: dist <= maxRadius ? '#4e73df' : '#dc3545'
                        });
                    }
                }
                
                function showError(message) {
                    errorAlert.textContent = message;
                    errorAlert.classList.remove('d-none');
                    setTimeout(() => errorAlert.classList.add('d-none'), 5000);
                }

                async function loadModels() {
                    try {
                        verificationStatus.innerHTML = `<div class="alert alert-info small p-2">Memuat model deteksi wajah...</div>`;
                        const modelPath = '{{ asset("models") }}';
                        await Promise.all([
                            faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                            faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                            faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                        ]);
                        await startVideo();
                        initMap();
                    } catch (error) {
                        console.error("Model loading error:", error);
                        verificationStatus.innerHTML = '';
                        showError("Gagal memuat model. Periksa koneksi dan muat ulang halaman.");
                    }
                }

                async function startVideo() {
                    try {
                        if (faceStream) stopStream(faceStream);
                        faceStream = await navigator.mediaDevices.getUserMedia({ 
                            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }, 
                            audio: false 
                        });
                        video.srcObject = faceStream;
                        video.onloadedmetadata = () => {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            verificationStatus.innerHTML = `<div class="alert alert-primary small p-2">Sistem siap. Posisikan wajah Anda.</div>`;
                            startFaceDetection();
                        };
                    } catch (error) {
                        showError("Tidak dapat mengakses kamera. Pastikan izin telah diberikan.");
                    }
                }

                function initMap() {
                    if (!navigator.geolocation) {
                        showError("Browser tidak mendukung geolocation.");
                        return;
                    }
                    
                    map = L.map('map').setView([officeLat, officeLng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    
                    L.marker([officeLat, officeLng]).addTo(map).bindPopup("Lokasi Kantor").openPopup();
                    officeCircle = L.circle([officeLat, officeLng], {
                        radius: maxRadius
                    }).addTo(map);

                    distanceIndicator.style.display = 'block';

                    navigator.geolocation.watchPosition(
                        position => {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;
                            lokasiInput.value = `${userLat},${userLng}`;
                            distance = calculateDistance(userLat, userLng, officeLat, officeLng);

                            if (!userMarker) {
                                userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup("Lokasi Anda");
                            } else {
                                userMarker.setLatLng([userLat, userLng]);
                            }
                            
                            updateDistanceUI(distance);
                            checkConditionsAndEnableButton();
                        },
                        () => { showError("Tidak dapat mendapatkan lokasi. Pastikan GPS aktif."); },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                    );
                }

                function startFaceDetection() {
                    const displaySize = { width: video.videoWidth, height: video.videoHeight };
                    faceapi.matchDimensions(canvas, displaySize);
                    
                    faceDetectionInterval = setInterval(async () => {
                        if (isFaceVerified) return;

                        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        
                        if (detection) {
                            const distanceMatch = faceapi.euclideanDistance(storedDescriptor, detection.descriptor);
                            const isMatch = distanceMatch < faceApiThreshold;
                            
                            if (isMatch) {
                                isFaceVerified = true;
                                verificationStatus.innerHTML = `<div class="alert alert-success p-2 small"><strong>Verifikasi Berhasil!</strong></div>`;
                                webcamContainer.style.borderColor = '#1cc88a';
                                checkConditionsAndEnableButton();
                            } else {
                                if (!isFaceVerified) verificationStatus.innerHTML = `<div class="alert alert-warning p-2 small">Wajah tidak dikenali. Coba lagi.</div>`;
                            }
                        } else {
                            if (!isFaceVerified) verificationStatus.innerHTML = `<div class="alert alert-secondary p-2 small">Arahkan wajah ke kamera...</div>`;
                        }
                    }, 1000);
                }

                function checkConditionsAndEnableButton() {
                    const originalText = '{{ ($presensiAktif) ? "ABSEN PULANG" : "ABSEN MASUK" }}';
                    if (isFaceVerified && distance <= maxRadius) {
                        verifyBtn.disabled = false;
                        verifyText.textContent = originalText;
                    } else {
                        verifyBtn.disabled = true;
                        if (!isFaceVerified) {
                            verifyText.textContent = "Verifikasi Wajah...";
                        } else if (distance > maxRadius) {
                            verifyText.textContent = "Anda di Luar Jangkauan";
                        } else {
                            verifyText.textContent = originalText;
                        }
                    }
                }

                verifyBtn.addEventListener('click', async () => {
                    if (!isFaceVerified) { showError("Verifikasi wajah belum berhasil."); return; }
                    if (distance > maxRadius) { showError(`Anda berada ${distance.toFixed(0)}m dari kantor (Maksimal: ${maxRadius}m).`); return; }
                    
                    verifyBtn.disabled = true;
                    verifyBtn.classList.add('loading');
                    verifyText.textContent = "Mengirim data...";
                    
                    clearInterval(faceDetectionInterval);
                    
                    const canvasForSaving = document.createElement('canvas');
                    canvasForSaving.width = video.videoWidth;
                    canvasForSaving.height = video.videoHeight;
                    canvasForSaving.getContext('2d').drawImage(video, 0, 0);
                    const imageData = canvasForSaving.toDataURL('image/jpeg');

                    stopStream(faceStream);
                    
                    try {
                        const response = await fetch("{{ route('presensi.store') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ image: imageData, lokasi: lokasiInput.value, nik: "{{ $user->nik }}" })
                        });
                        
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.error || "Gagal mengirim data presensi.");
                        showSuccessModal(data.success, data.redirect_url);
                    } catch (error) {
                        showError(error.message);
                        verifyBtn.classList.remove('loading');
                        isFaceVerified = false; // Reset verification status on error
                        startVideo(); 
                    }
                });

                function showSuccessModal(message, redirectUrl) {
                    successMessage.textContent = message;
                    successModal.classList.add('active');
                    
                    const closeAndRedirect = () => {
                        successModal.classList.remove('active');
                        window.location.href = redirectUrl || '/dashboard';
                    };
                    
                    modalCloseBtn.onclick = closeAndRedirect;
                    setTimeout(closeAndRedirect, 3000);
                }
                
                loadModels();
                
                window.addEventListener('beforeunload', () => stopStream(faceStream));
            @else
                showError("Data wajah atau lokasi kantor tidak ditemukan. Hubungi admin.");
                if(document.getElementById('verifyBtn')) {
                    document.getElementById('verifyBtn').setAttribute('disabled', true);
                }
                if(document.getElementById('verifyText')) {
                    document.getElementById('verifyText').textContent = 'Tidak Dapat Absen';
                }
            @endif
        }
    });
</script>
@endpush
