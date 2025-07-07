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
    /* ONLY ADDED THIS TO FIX BOTTOM NAV OVERLAP */
    .presensi-content {
        padding-bottom: 80px; /* Adjust based on your bottom nav height */
    }
    /* END OF ADDITION */

    .webcam-container {
        position: relative;
        width: 100%;
        margin: auto;
        border-radius: 15px;
        overflow: hidden;
        background: #000;
    }
    
    #videoElement {
        width: 100%;
        display: block;
    }
    
    #canvasElement {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        font-family: 'Poppins', sans-serif;
    }
    
    .btn-verify {
        transition: all 0.3s ease;
        position: relative;
    }
    
    .btn-verify:disabled {
        opacity: 0.7;
    }
    
    .btn-verify .spinner {
        display: none;
        position: absolute;
        right: 10px;
    }
    
    .btn-verify.loading .spinner {
        display: inline-block;
    }
    
    #map {
        height: 300px;
        width: 100%;
        border-radius: 15px;
        margin-top: 15px;
    }
    
    .distance-indicator {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 12px;
        z-index: 1000;
    }
    
    .info-card {
        margin-top: 15px;
    }

    /* Success Modal Styles */
    .success-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .success-modal.active {
        opacity: 1;
        visibility: visible;
    }
    
    .success-modal-content {
        background-color: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        max-width: 320px;
        width: 90%;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: modalFadeIn 0.4s ease;
    }
    
    .success-modal-icon {
        font-size: 60px;
        color: #28a745;
        margin-bottom: 20px;
        animation: iconBounce 0.6s ease;
    }
    
    .success-modal-message {
        font-size: 18px;
        margin-bottom: 25px;
        font-weight: 500;
        color: #333;
        line-height: 1.4;
    }
    
    .success-modal-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 500;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .success-modal-btn:hover {
        background: #218838;
        transform: translateY(-2px);
    }
    
    @keyframes modalFadeIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes iconBounce {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
    }
</style>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="presensi-content">
    <div class="row" style="margin-top: 70px">
        <div class="col">
            @if (empty($user->office_location))
                <div class="alert alert-danger">
                    Lokasi kantor belum ditentukan. Hubungi admin.
                </div>
            @else
                <input type="hidden" id="lokasi">
                <input type="hidden" id="nik" value="{{ Auth::guard('karyawan')->user()->nik }}">
                
                <div class="text-center mb-2">
                    <h5 style="font-weight: 600;">Verifikasi Wajah</h5>
                    <small class="text-muted">Pastikan wajah Anda terlihat jelas</small>
                </div>
                
                <div class="webcam-container mb-3">
                    <video id="videoElement" autoplay playsinline></video>
                    <canvas id="canvasElement"></canvas>
                    <div class="distance-indicator" id="distance-indicator">
                        Jarak: <span id="distance-value">0</span>m
                    </div>
                </div>
                
                <div class="text-center mb-3">
                    <button id="verifyBtn" class="btn btn-primary btn-block btn-verify" disabled>
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <span id="verifyText">Verifikasi Wajah</span>
                        <span class="spinner spinner-border spinner-border-sm"></span>
                    </button>
                </div>
                
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                
                <div class="alert alert-info">
                    <small>
                        <ion-icon name="information-circle-outline"></ion-icon>
                        Untuk absen, pastikan wajah terlihat jelas dan berada dalam radius {{ $user->office_radius }} meter dari kantor.
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
                            <li>Anda harus berada dalam radius {{ $user->office_radius }} meter dari kantor</li>
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
    $(document).ready(function() {
        // Hanya lanjutkan jika lokasi kantor dan deskriptor wajah pengguna tersedia
        @if (!empty($user->office_location) && isset($faceDescriptor))
            const video = document.getElementById('videoElement');
            const canvas = document.getElementById('canvasElement');
            const verifyBtn = document.getElementById('verifyBtn');
            const verifyText = document.getElementById('verifyText');
            const errorAlert = document.getElementById('errorAlert');
            const lokasiInput = document.getElementById('lokasi');
            const distanceIndicator = document.getElementById('distance-indicator');
            const distanceValue = document.getElementById('distance-value');
            const successModal = document.getElementById('successModal');
            const successMessage = document.getElementById('successMessage');
            const modalCloseBtn = document.getElementById('modalCloseBtn');
            
            let faceDetectionInterval;
            let isFaceDetectedAndMatched = false;
            let map;
            let userMarker;
            let officeCircle;
            let distance = 0;
            
            const officeLat = {{ $user->office_location['coordinates'][1] }};
            const officeLng = {{ $user->office_location['coordinates'][0] }};
            const maxRadius = {{ $user->office_radius }};
            const faceApiThreshold = {{ config('face_recognition.threshold', 0.6) }};
            
            const storedDescriptor = new Float32Array(Object.values(JSON.parse('{!! $faceDescriptor !!}')));

            async function loadModels() {
                try {
                    verifyBtn.disabled = true;
                    verifyText.textContent = "Memuat model...";
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                    ]);
                    startVideo();
                    initMap();
                } catch (error) {
                    console.error("Gagal memuat model:", error);
                    showError("Gagal memuat model pengenalan wajah. Silakan muat ulang halaman.");
                    verifyBtn.disabled = true;
                }
            }
            
            async function startVideo() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }, 
                        audio: false 
                    });
                    video.srcObject = stream;
                    video.onloadedmetadata = () => {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        startFaceDetection();
                    };
                } catch (error) {
                    showError("Tidak dapat mengakses kamera. Pastikan izin telah diberikan.");
                    verifyBtn.disabled = true;
                }
            }
            
            function initMap() {
                if (!navigator.geolocation) {
                    showError("Browser tidak mendukung geolocation.");
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        lokasiInput.value = `${userLat},${userLng}`;
                        distance = calculateDistance(userLat, userLng, officeLat, officeLng);
                        
                        map = L.map('map').setView([officeLat, officeLng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        
                        L.marker([officeLat, officeLng]).addTo(map).bindPopup("Lokasi Kantor").openPopup();
                        userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup("Lokasi Anda");
                        officeCircle = L.circle([officeLat, officeLng], {
                            color: distance <= maxRadius ? '#4e73df' : '#dc3545',
                            fillColor: distance <= maxRadius ? '#4e73df' : '#dc3545',
                            fillOpacity: 0.2,
                            radius: maxRadius
                        }).addTo(map);
                        updateDistanceUI(distance);
                    },
                    error => {
                        showError("Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.");
                        map = L.map('map').setView([officeLat, officeLng], 17);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        L.marker([officeLat, officeLng]).addTo(map).bindPopup("Lokasi Kantor").openPopup();
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
            
            function startFaceDetection() {
                const displaySize = { width: video.videoWidth, height: video.videoHeight };
                faceapi.matchDimensions(canvas, displaySize);
                
                faceDetectionInterval = setInterval(async () => {
                    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    if (detections) {
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        faceapi.draw.drawDetections(canvas, resizedDetections);
                        
                        const distanceMatch = faceapi.euclideanDistance(storedDescriptor, detections.descriptor);
                        const isMatch = distanceMatch < faceApiThreshold;
                        
                        const text = isMatch ? `Terverifikasi` : "Wajah tidak cocok";
                        new faceapi.draw.DrawTextField([text], detections.detection.box.bottomLeft).draw(canvas);

                        if (isMatch) {
                            isFaceDetectedAndMatched = true;
                            if (distance <= maxRadius) {
                                verifyBtn.disabled = false;
                                verifyText.textContent = "Lakukan Presensi";
                            } else {
                                verifyBtn.disabled = true;
                                verifyText.textContent = "Anda di Luar Jangkauan";
                            }
                        } else {
                            isFaceDetectedAndMatched = false;
                            verifyBtn.disabled = true;
                            verifyText.textContent = "Wajah Tidak Cocok";
                        }
                    } else {
                        isFaceDetectedAndMatched = false;
                        verifyBtn.disabled = true;
                        verifyText.textContent = "Wajah Tidak Terdeteksi";
                    }
                }, 500);
            }
            
            verifyBtn.addEventListener('click', async () => {
                if (!isFaceDetectedAndMatched) {
                    showError("Verifikasi wajah belum berhasil.");
                    return;
                }
                if (distance > maxRadius) {
                    showError(`Anda berada ${distance.toFixed(0)}m dari kantor (maks. ${maxRadius}m)`);
                    return;
                }
                
                verifyBtn.disabled = true;
                verifyBtn.classList.add('loading');
                verifyText.textContent = "Mengirim data...";
                
                // ================== PERBAIKAN DI SINI ==================
                // Rantai pemanggilan fungsi harus lengkap untuk mendapatkan deskriptor.
                const lastDetection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                // =======================================================

                if (!lastDetection) {
                    showError("Wajah hilang saat mengirim. Coba lagi.");
                    resetVerifyButton();
                    return;
                }
                const faceDescriptorToSend = Array.from(lastDetection.descriptor);
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                const imageData = canvas.toDataURL('image/jpeg');
                
                try {
                    const response = await fetch("{{ route('presensi.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            image: imageData,
                            lokasi: lokasiInput.value,
                            nik: "{{ Auth::guard('karyawan')->user()->nik }}",
                            face_descriptor: faceDescriptorToSend
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || "Gagal mengirim data presensi.");
                    }
                    
                    showSuccessModal(data.success, data.redirect_url);
                    
                } catch (error) {
                    showError(error.message);
                    resetVerifyButton();
                }
            });
            
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3;
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }
            
            function updateDistanceUI(dist) {
                distanceValue.textContent = dist.toFixed(0);
                distanceIndicator.style.backgroundColor = dist <= maxRadius ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)';
            }
            
            function showError(message) {
                errorAlert.textContent = message;
                errorAlert.classList.remove('d-none');
                setTimeout(() => errorAlert.classList.add('d-none'), 5000);
            }
            
            function resetVerifyButton() {
                verifyBtn.disabled = !isFaceDetectedAndMatched || distance > maxRadius;
                verifyBtn.classList.remove('loading');
                verifyText.textContent = "Verifikasi Wajah";
            }

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
            
            window.addEventListener('beforeunload', () => {
                if (faceDetectionInterval) clearInterval(faceDetectionInterval);
                if (video.srcObject) video.srcObject.getTracks().forEach(track => track.stop());
            });
        @else
            @if(empty($user->office_location))
                showError("Lokasi kantor Anda belum diatur oleh admin.");
            @else
                showError("Data wajah Anda belum terdaftar. Hubungi admin.");
            @endif
            $('#verifyBtn').prop('disabled', true).text('Tidak Dapat Absen');
        @endif
    });
</script>
@endpush