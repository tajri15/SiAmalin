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
        // Only proceed if office location exists
        @if (!empty($user->office_location))
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
            let isFaceDetected = false;
            let map;
            let userMarker;
            let officeCircle;
            let distance = 0;
            
            // Dynamic office coordinates from database
            const officeLat = {{ $user->office_location['coordinates'][1] }};
            const officeLng = {{ $user->office_location['coordinates'][0] }};
            const maxRadius = {{ $user->office_radius }};
            
            // 1. Load Face API Models
            async function loadModels() {
                try {
                    verifyBtn.disabled = true;
                    verifyText.textContent = "Memuat model...";
                    
                    await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                    await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                    await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                    await faceapi.nets.faceExpressionNet.loadFromUri('/models');
                    
                    startVideo();
                    initMap();
                } catch (error) {
                    console.error("Gagal memuat model:", error);
                    showError("Gagal memuat model pengenalan wajah");
                    verifyBtn.disabled = true;
                }
            }
            
            // 2. Start Video Stream
            async function startVideo() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: {
                            width: 640,
                            height: 480,
                            facingMode: 'user'
                        }, 
                        audio: false 
                    });
                    
                    video.srcObject = stream;
                    
                    // Set canvas size same as video
                    video.onloadedmetadata = () => {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        startFaceDetection();
                    };
                    
                } catch (error) {
                    console.error("Error accessing camera:", error);
                    showError("Tidak dapat mengakses kamera");
                    verifyBtn.disabled = true;
                }
            }
            
            // 3. Initialize Map
            function initMap() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;
                            
                            // Set lokasi input
                            lokasiInput.value = `${userLat},${userLng}`;
                            
                            // Hitung jarak
                            distance = calculateDistance(
                                userLat, userLng, 
                                officeLat, officeLng
                            );
                            
                            // Update distance display
                            distanceValue.textContent = distance.toFixed(0);
                            
                            // Initialize map centered at office location
                            map = L.map('map').setView([officeLat, officeLng], 17);
                            
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);
                            
                            // Add office marker
                            L.marker([officeLat, officeLng])
                                .addTo(map)
                                .bindPopup("Lokasi Kantor")
                                .openPopup();
                            
                            // Add user marker
                            userMarker = L.marker([userLat, userLng])
                                .addTo(map)
                                .bindPopup("Lokasi Anda");
                            
                            // Add radius circle (color depends on distance)
                            officeCircle = L.circle([officeLat, officeLng], {
                                color: distance <= maxRadius ? '#4e73df' : '#dc3545',
                                fillColor: distance <= maxRadius ? '#4e73df' : '#dc3545',
                                fillOpacity: 0.2,
                                radius: maxRadius
                            }).addTo(map);
                            
                            // Update UI based on distance
                            updateDistanceUI(distance);
                        },
                        error => {
                            console.error("Geolocation error:", error);
                            showError("Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.");
                            
                            // Fallback: Show just the office location
                            map = L.map('map').setView([officeLat, officeLng], 17);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);
                            
                            L.marker([officeLat, officeLng])
                                .addTo(map)
                                .bindPopup("Lokasi Kantor")
                                .openPopup();
                            
                            L.circle([officeLat, officeLng], {
                                color: '#4e73df',
                                fillColor: '#4e73df',
                                fillOpacity: 0.2,
                                radius: maxRadius
                            }).addTo(map);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    showError("Browser tidak mendukung geolocation");
                }
            }
            
            // 4. Face Detection
            function startFaceDetection() {
                const displaySize = { width: video.videoWidth, height: video.videoHeight };
                faceapi.matchDimensions(canvas, displaySize);
                
                faceDetectionInterval = setInterval(async () => {
                    try {
                        const detections = await faceapi.detectAllFaces(
                            video, 
                            new faceapi.TinyFaceDetectorOptions()
                        ).withFaceLandmarks().withFaceDescriptors();
                        
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        
                        if (detections.length > 0) {
                            isFaceDetected = true;
                            verifyBtn.disabled = distance > maxRadius;
                            verifyText.textContent = distance > maxRadius 
                                ? "Anda di luar radius" 
                                : "Verifikasi Wajah";
                            
                            const resizedDetections = faceapi.resizeResults(detections, displaySize);
                            
                            // Draw detection boxes and custom info
                            resizedDetections.forEach(detection => {
                                const box = detection.detection.box;
                                const nama = "{{ Auth::guard('karyawan')->user()->nama_lengkap }}";
                                const similarity = (detection.detection.score * 100).toFixed(2);
                                
                                // Calculate required width (add 20px padding)
                                ctx.font = 'bold 14px Poppins';
                                const namaWidth = ctx.measureText(nama).width;
                                ctx.font = '12px Poppins';
                                const similarityWidth = ctx.measureText(`Similarity: ${similarity}%`).width;
                                const boxWidth = Math.max(namaWidth, similarityWidth) + 40; // Extra padding
                                
                                // 1. Draw the face detection box (BLUE rectangle)
                                ctx.strokeStyle = '#007bff';
                                ctx.lineWidth = 2;
                                ctx.strokeRect(box.x, box.y, box.width, box.height);
                                
                                // 2. Draw landmarks (if needed)
                                if (detection.landmarks) {
                                    faceapi.draw.drawFaceLandmarks(canvas, detection);
                                }
                                
                                // 3. Draw custom info box (blue background)
                                const infoBoxX = box.x + (box.width/2) - (boxWidth/2);
                                const infoBoxY = box.y - 55;
                                
                                ctx.fillStyle = 'rgba(0, 123, 255, 0.8)';
                                ctx.fillRect(
                                    infoBoxX,
                                    infoBoxY,
                                    boxWidth,
                                    50
                                );
                                
                                // Draw name (centered)
                                ctx.font = 'bold 14px Poppins';
                                ctx.fillStyle = '#ffffff';
                                ctx.textAlign = 'center';
                                ctx.fillText(
                                    nama,
                                    box.x + (box.width/2),
                                    infoBoxY + 30
                                );
                                
                                // Draw similarity (centered)
                                ctx.font = '12px Poppins';
                                ctx.fillText(
                                    `Similarity: ${similarity}%`,
                                    box.x + (box.width/2),
                                    infoBoxY + 45
                                );
                                
                                // Reset text alignment
                                ctx.textAlign = 'left';
                            });
                            
                        } else {
                            isFaceDetected = false;
                            verifyBtn.disabled = true;
                            verifyText.textContent = "Wajah tidak terdeteksi";
                        }
                    } catch (error) {
                        console.error("Face detection error:", error);
                        clearInterval(faceDetectionInterval);
                    }
                }, 300);
            }
            
            // 5. Verify Face
            verifyBtn.addEventListener('click', async () => {
                if (!isFaceDetected) {
                    showError("Wajah tidak terdeteksi");
                    return;
                }

                if (distance > maxRadius) {
                    showError(`Anda berada ${distance.toFixed(0)} meter dari kantor (max ${maxRadius}m)`);
                    return;
                }

                try {
                    verifyBtn.disabled = true;
                    verifyBtn.classList.add('loading');
                    verifyText.textContent = "Memproses...";

                    // 1. Ambil gambar dari video
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageDataUrl = canvas.toDataURL('image/jpeg'); // untuk disimpan sbg bukti foto

                    // 2. Hasilkan Face Descriptor menggunakan face-api.js
                    const detectionWithDescriptor = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detectionWithDescriptor) {
                        throw new Error("Gagal menghasilkan deskriptor wajah. Coba lagi.");
                    }

                    const faceDescriptor = Array.from(detectionWithDescriptor.descriptor); // Konversi ke array

                    // 3. Kirim data ke server (termasuk deskriptor)
                    const lokasi = lokasiInput.value;
                    const nik = document.getElementById('nik').value;

                    const response = await fetch("{{ route('presensi.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            image: imageDataUrl, // Kirim foto sebagai bukti
                            lokasi: lokasi,
                            nik: nik,
                            face_descriptor: faceDescriptor // KIRIM DESKRIPTOR WAJAH
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || "Verifikasi gagal");
                    }

                    successMessage.textContent = data.success;
                    successModal.classList.add('active');

                    modalCloseBtn.addEventListener('click', function() {
                        successModal.classList.remove('active');
                        redirectAfterSuccess(data.redirect_url);
                    });

                    setTimeout(() => {
                        successModal.classList.remove('active');
                        redirectAfterSuccess(data.redirect_url);
                    }, 3000);

                } catch (error) {
                    console.error("Verification error:", error);
                    showError(error.message);
                    resetVerifyButton();
                }
            });
            
            // Helper functions
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c * 1000;
            }
            
            function updateDistanceUI(distance) {
                distanceValue.textContent = distance.toFixed(0);
                if (distance > maxRadius) {
                    distanceIndicator.style.backgroundColor = 'rgba(220, 53, 69, 0.7)';
                } else {
                    distanceIndicator.style.backgroundColor = 'rgba(40, 167, 69, 0.7)';
                }
            }
            
            function showError(message) {
                errorAlert.textContent = message;
                errorAlert.classList.remove('d-none');
                setTimeout(() => errorAlert.classList.add('d-none'), 5000);
            }
            
            function resetVerifyButton() {
                verifyBtn.disabled = !isFaceDetected || distance > maxRadius;
                verifyBtn.classList.remove('loading');
                verifyText.textContent = distance > maxRadius 
                    ? "Anda di luar radius" 
                    : "Verifikasi Wajah";
            }
            
            function redirectAfterSuccess(redirectUrl) {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    window.location.href = '/dashboard';
                }
            }
            
            // Start the process
            loadModels();
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                if (faceDetectionInterval) clearInterval(faceDetectionInterval);
                if (video.srcObject) video.srcObject.getTracks().forEach(track => track.stop());
            });
        @else
            console.error("Office location not set for user");
        @endif
    });
</script>
@endpush