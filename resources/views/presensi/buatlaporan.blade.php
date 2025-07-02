@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="{{ route('laporan.index') }}" class="headerButton goBack" style="color: white;">
            <ion-icon name="chevron-back-outline" style="font-size: 20px;"></ion-icon>
        </a>
    </div>
    <div class="pageTitle text-center" style="font-weight: 600; letter-spacing: 0.5px;">Buat Laporan</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px; padding: 0 15px 80px;">
    <div class="col">
        <form id="laporanForm" method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="face_image" id="face_image">
            <input type="hidden" name="foto" id="fotoInput">
            <input type="hidden" name="nik" value="{{ Auth::guard('karyawan')->user()->nik }}">

            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <h5 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                        <ion-icon name="camera-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                        Verifikasi Wajah
                    </h5>

                    <div class="webcam-container mb-3" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #000;">
                        <div class="video-wrapper">
                            <video id="videoElement" autoplay playsinline style="width: 100%; display: block;"></video>
                            <canvas id="canvasElement" style="display: none;"></canvas>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="verifyBtn" class="btn btn-primary" style="border-radius: 30px; padding: 10px 25px;">
                            <ion-icon name="checkmark-circle-outline" style="vertical-align: middle;"></ion-icon> Verifikasi Wajah
                        </button>
                    </div>

                    <div id="verificationStatus" class="text-center mt-2"></div>
                </div>
            </div>

            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <h5 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                        <ion-icon name="camera-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                        Foto Bukti
                    </h5>

                    <div class="camera-container mb-3" style="display: none; position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #000;">
                        <div class="video-wrapper">
                            <video id="photoCamera" autoplay playsinline></video>
                            <canvas id="photoCanvas" style="display: none;"></canvas>
                            <div id="photoOverlay" style="position: absolute; bottom: 10px; left: 10px; right: 10px; padding: 8px; background: rgba(0,0,0,0.5); color: white; border-radius: 5px; font-size: 12px; display: flex; justify-content: space-between; font-family: 'Courier New', monospace; font-weight: bold;">
                                <span id="photoTimestamp"></span>
                                <span id="photoLocation">Lokasi: Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div id="photoPreviewContainer" class="text-center mb-3" style="display: none;">
                        <img id="photoPreview" src="" alt="Captured Photo" class="img-fluid rounded" style="max-height: 300px;">
                    </div>

                    <div class="button-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: 15px 0;">
                        <button type="button" id="startCameraBtn" class="btn btn-primary" style="border-radius: 30px; padding: 10px 20px; min-width: 140px;">
                            <ion-icon name="camera-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;">Ambil Foto</span>
                        </button>

                        <button type="button" id="capturePhotoBtn" class="btn btn-success" style="border-radius: 30px; padding: 10px 20px; min-width: 140px; display: none;">
                            <ion-icon name="aperture-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;">Ambil Gambar</span>
                        </button>

                        <button type="button" id="retryPhotoBtn" class="btn btn-warning" style="border-radius: 30px; padding: 10px 20px; min-width: 140px; display: none;">
                            <ion-icon name="refresh-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;">Ulangi</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <div class="form-group mb-3">
                        <label style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Tanggal Laporan</label>
                        <input type="date" name="tgl_laporan" id="tgl_laporan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px;" required>
                    </div>

                    <div class="form-group mb-3">
                        <label style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Jam Laporan</label>
                        <input type="time" name="jam" id="jam_laporan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px;" required>
                    </div>

                    <div class="form-group mb-3">
                        <label style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Jenis Laporan</label>
                        <select name="jenis_laporan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill="%236c757d" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>'); background-repeat: no-repeat; background-position: right 15px center; background-size: 15px;" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="harian">Harian</option>
                            <option value="kegiatan">Kegiatan</option>
                            <option value="masalah">Masalah</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 15px;" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px;" required>
                        <small class="text-muted" id="locationHelp">Koordinat: <span id="coordinatesDisplay">Belum tersedia</span></small>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary btn-block" style="border-radius: 10px; height: 45px; font-weight: 500; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);" disabled>
                        <ion-icon name="send-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    #photoLocation:after {
        content: '...';
        animation: dots 1.5s steps(5, end) infinite;
    }

    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60%, 100% { content: '...'; }
    }

    #locationHelp {
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
</style>
<script>
    $(document).ready(function() {
        // Set default date and time
        const today = new Date().toISOString().split('T')[0];
        $('#tgl_laporan').val(today);

        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        $('#jam_laporan').val(`${hours}:${minutes}`);

        // Face Verification Elements
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const verifyBtn = document.getElementById('verifyBtn');
        const verificationStatus = document.getElementById('verificationStatus');
        const faceImageInput = document.getElementById('face_image');
        const submitBtn = document.getElementById('submitBtn');

        // Photo Evidence Elements
        const photoCamera = document.getElementById('photoCamera');
        const photoCanvas = document.getElementById('photoCanvas');
        const photoPreview = document.getElementById('photoPreview');
        const photoPreviewContainer = document.getElementById('photoPreviewContainer');
        const cameraContainer = document.querySelector('.camera-container');
        const photoTimestamp = document.getElementById('photoTimestamp');
        const photoLocation = document.getElementById('photoLocation');
        const startCameraBtn = document.getElementById('startCameraBtn');
        const capturePhotoBtn = document.getElementById('capturePhotoBtn');
        const retryPhotoBtn = document.getElementById('retryPhotoBtn');
        const fotoInput = document.getElementById('fotoInput');
        const coordinatesDisplay = document.getElementById('coordinatesDisplay');

        let faceStream = null;
        let photoStream = null;
        let currentLocation = null;
        let isFaceVerified = false;

        // Function untuk mendapatkan nama lokasi dari koordinat
        async function getLocationName(lat, lng) {
            try {
                // Menggunakan OpenStreetMap Nominatim (gratis)
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                const data = await response.json();

                if (data.error) {
                    console.error("Geocoding Error:", data.error);
                    return {
                        address: `${lat.toFixed(6)}, ${lng.toFixed(6)}`,
                        details: "Alamat tidak ditemukan"
                    };
                }

                // Format alamat dari response
                let address = "";
                if (data.address.road) address += data.address.road + ", ";
                if (data.address.village) address += data.address.village + ", ";
                if (data.address.suburb) address += data.address.suburb + ", ";
                if (data.address.city_district) address += data.address.city_district + ", ";
                if (data.address.city) address += data.address.city;

                // Jika tidak ada alamat spesifik, kembalikan koordinat
                return {
                    address: address || `${lat.toFixed(6)}, ${lng.toFixed(6)}`,
                    details: JSON.stringify(data.address, null, 2)
                };

            } catch (error) {
                console.error("Geocoding Error:", error);
                return {
                    address: `${lat.toFixed(6)}, ${lng.toFixed(6)}`,
                    details: "Gagal mendapatkan alamat"
                };
            }
        }

        // Function untuk mendapatkan lokasi
        async function getCurrentLocation() {
            return new Promise((resolve, reject) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => resolve(position),
                        error => reject(error),
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else {
                    reject(new Error("Geolocation tidak didukung"));
                }
            });
        }

        // Update lokasi dan alamat
        async function updateLocation() {
            try {
                photoLocation.textContent = "Lokasi: Memperbarui...";

                const position = await getCurrentLocation();
                currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Update koordinat display
                coordinatesDisplay.textContent = `${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}`;

                // Dapatkan nama lokasi
                const locationInfo = await getLocationName(currentLocation.lat, currentLocation.lng);

                // Update tampilan
                photoLocation.textContent = `Lokasi: ${locationInfo.address}`;
                $('#lokasi').val(locationInfo.address);

                return locationInfo;
            } catch (error) {
                console.error("Location Error:", error);
                photoLocation.textContent = "Lokasi: Tidak tersedia";
                $('#lokasi').val("Lokasi tidak tersedia");
                coordinatesDisplay.textContent = "Belum tersedia";

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendapatkan Lokasi',
                    text: error.message || 'Pastikan izin lokasi diberikan'
                });

                return null;
            }
        }

        // Update timestamp continuously
        function updateTimestamp() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            photoTimestamp.textContent = `${dateStr} ${timeStr}`;
            setTimeout(updateTimestamp, 1000);
        }
        updateTimestamp();

        // Initialize Face Verification Camera
        async function initFaceCamera() {
            try {
                if (faceStream) {
                    faceStream.getTracks().forEach(track => track.stop());
                }

                faceStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: 320,
                        height: 240,
                        facingMode: 'user'
                    },
                    audio: false
                });

                video.srcObject = faceStream;

                // Load face-api.js models
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

            } catch (error) {
                console.error("Camera Error:", error);
                verificationStatus.innerHTML = `
                    <div class="alert alert-danger">
                        Gagal mengakses kamera: ${error.message || 'Pastikan izin kamera diberikan'}
                    </div>
                `;
            }
        }

        // Face Verification
        verifyBtn.addEventListener('click', async function() {
            try {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memverifikasi...';

                // Capture face image
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = canvas.toDataURL('image/jpeg', 0.8);
                const nik = '{{ Auth::guard("karyawan")->user()->nik }}';

                // Verify with server
                const response = await fetch('/face/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        face_image: imageData,
                        nik: nik
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Verifikasi gagal');
                }

                if (data.success && data.match) {
                    faceImageInput.value = imageData;
                    isFaceVerified = true;
                    checkFormCompletion();

                    verificationStatus.innerHTML = `
                        <div class="alert alert-success">
                            Verifikasi berhasil! Kemiripan: ${(data.similarity * 100).toFixed(2)}%
                        </div>
                    `;

                    verifyBtn.innerHTML = '<ion-icon name="checkmark-circle-outline"></ion-icon> Verifikasi Berhasil';
                    verifyBtn.classList.remove('btn-primary');
                    verifyBtn.classList.add('btn-success');

                    // Save verification status in session
                    await fetch('/set-face-verified-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            verified: true,
                            timestamp: new Date().getTime()
                        })
                    });

                    // Stop face camera
                    if (faceStream) {
                        faceStream.getTracks().forEach(track => track.stop());
                    }
                } else {
                    throw new Error(data.message || 'Verifikasi wajah gagal');
                }

            } catch (error) {
                console.error("Verification Error:", error);
                verificationStatus.innerHTML = `
                    <div class="alert alert-danger">
                        ${error.message}
                    </div>
                `;

                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<ion-icon name="checkmark-circle-outline"></ion-icon> Verifikasi Wajah';
            }
        });

        // Photo Evidence Camera
        startCameraBtn.addEventListener('click', async function() {
            try {
                if (photoStream) {
                    photoStream.getTracks().forEach(track => track.stop());
                }

                photoStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: 320,
                        height: 240,
                        facingMode: 'environment'
                    },
                    audio: false
                });

                photoCamera.srcObject = photoStream;
                cameraContainer.style.display = 'block';
                photoPreviewContainer.style.display = 'none';

                // Sembunyikan tombol Ambil Foto, tampilkan tombol Ambil Gambar
                startCameraBtn.style.display = 'none';
                capturePhotoBtn.style.display = 'inline-block';
                retryPhotoBtn.style.display = 'none';

            } catch (error) {
                console.error("Camera Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengakses Kamera',
                    text: 'Pastikan izin kamera diberikan dan perangkat memiliki kamera'
                });
            }
        });

        // Capture Photo
        capturePhotoBtn.addEventListener('click', function() {
            photoCanvas.width = photoCamera.videoWidth;
            photoCanvas.height = photoCamera.videoHeight;
            const ctx = photoCanvas.getContext('2d');

            // Draw the current frame from video
            ctx.drawImage(photoCamera, 0, 0, photoCanvas.width, photoCanvas.height);

            // Add timestamp and location overlay
            ctx.font = '14px Arial';
            ctx.fillStyle = 'white';
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 2;

            const timestampText = photoTimestamp.textContent;
            const locationText = photoLocation.textContent.replace('Lokasi: ', '');
            const textX = 10;
            const textY1 = photoCanvas.height - 30;
            const textY2 = photoCanvas.height - 10;

            // Draw with outline
            ctx.strokeText(timestampText, textX, textY1);
            ctx.strokeText(locationText, textX, textY2);

            // Draw main text
            ctx.fillText(timestampText, textX, textY1);
            ctx.fillText(locationText, textX, textY2);

            // Convert to data URL and show preview
            const photoDataUrl = photoCanvas.toDataURL('image/jpeg', 0.8);
            photoPreview.src = photoDataUrl;
            fotoInput.value = photoDataUrl;

            // Show preview and hide camera
            cameraContainer.style.display = 'none';
            photoPreviewContainer.style.display = 'block';

            // Setelah ambil gambar:
            // - Sembunyikan tombol Ambil Gambar
            // - Tampilkan tombol Ulangi
            // - Sembunyikan tombol Ambil Foto utama
            capturePhotoBtn.style.display = 'none';
            retryPhotoBtn.style.display = 'inline-block';
            startCameraBtn.style.display = 'none';

            // Stop camera
            if (photoStream) {
                photoStream.getTracks().forEach(track => track.stop());
                photoStream = null;
            }

            checkFormCompletion();
        });

        // Retry Photo
        retryPhotoBtn.addEventListener('click', function() {
            // Sembunyikan preview foto
            photoPreviewContainer.style.display = 'none';

            // Tampilkan tombol Ambil Gambar
            // Sembunyikan tombol Ulangi
            // Tampilkan tombol Ambil Foto utama
            capturePhotoBtn.style.display = 'inline-block';
            retryPhotoBtn.style.display = 'none';
            startCameraBtn.style.display = 'inline-block';

            // Start kamera kembali
            startCameraBtn.click();
        });

        // Check if form can be submitted
        function checkFormCompletion() {
            if (isFaceVerified && fotoInput.value) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        // Form submission handler
        $('#laporanForm').submit(function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

            // Check if face is verified in session
            fetch('/check-face-verified-session', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include' // Important for sessions/cookies
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    // If response is not ok, use the error message from server or default
                    const errorMsg = data.message || `Server error: ${response.status}`;
                    throw new Error(errorMsg);
                }

                return data;
            })
            .then(data => {
                if (!data.verified) {
                    throw new Error('Harap verifikasi wajah terlebih dahulu sebelum mengirim laporan');
                }

                // Create FormData object
                const formData = new FormData(this);

                // Send AJAX request
                return $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });
            })
            .then(response => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('laporan.index') }}";
                    });
                } else {
                    throw new Error(response.message || 'Gagal mengirim laporan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Terjadi kesalahan saat mengirim laporan'
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<ion-icon name="send-outline"></ion-icon> Kirim Laporan';
            });
        });

        // Initialize face camera on page load
        initFaceCamera();

        // Get initial location
        updateLocation();

        // Cleanup when leaving page
        window.addEventListener('beforeunload', function() {
            if (faceStream) {
                faceStream.getTracks().forEach(track => track.stop());
            }
            if (photoStream) {
                photoStream.getTracks().forEach(track => track.stop());
            }
        });
    });
</script>
@endpush
