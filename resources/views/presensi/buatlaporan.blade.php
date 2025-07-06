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
            {{-- Input tersembunyi untuk menyimpan data yang akan dikirim --}}
            <input type="hidden" name="face_image" id="face_image">
            <input type="hidden" name="foto" id="fotoInput">
            <input type="hidden" name="nik" value="{{ Auth::guard('karyawan')->user()->nik }}">
            {{-- Input ini akan diisi dengan deskriptor wajah oleh JavaScript --}}
            <input type="hidden" name="face_descriptor" id="face_descriptor">

            {{-- Card Verifikasi Wajah --}}
            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <h5 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                        <ion-icon name="person-circle-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                        Langkah 1: Verifikasi Wajah
                    </h5>

                    <div class="webcam-container mb-3" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #000;">
                        <video id="videoElement" autoplay playsinline style="width: 100%; display: block;"></video>
                        <canvas id="canvasElement" style="display: none;"></canvas>
                    </div>

                    <div class="text-center">
                        {{-- PERUBAHAN DI SINI: tambahkan 'disabled' dan ubah teks awal --}}
                        <button type="button" id="verifyBtn" class="btn btn-primary" style="border-radius: 30px; padding: 10px 25px;" disabled>
                            <ion-icon name="hourglass-outline" style="vertical-align: middle;"></ion-icon> Memuat Model...
                        </button>
                    </div>

                    <div id="verificationStatus" class="text-center mt-2"></div>
                </div>
            </div>

            {{-- Card Foto Bukti --}}
            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <h5 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                        <ion-icon name="camera-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                        Langkah 2: Ambil Foto Bukti
                    </h5>

                    <div class="camera-container mb-3" style="display: none; position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #000;">
                        <video id="photoCamera" autoplay playsinline style="width: 100%; display: block;"></video>
                        <canvas id="photoCanvas" style="display: none;"></canvas>
                        <div id="photoOverlay" style="position: absolute; bottom: 10px; left: 10px; right: 10px; padding: 8px; background: rgba(0,0,0,0.6); color: white; border-radius: 8px; font-size: 11px; display: flex; justify-content: space-between; font-family: 'Courier New', monospace; font-weight: bold;">
                            <span id="photoTimestamp"></span>
                            <span id="photoLocation">Lokasi: Loading...</span>
                        </div>
                    </div>

                    <div id="photoPreviewContainer" class="text-center mb-3" style="display: none;">
                        <img id="photoPreview" src="" alt="Foto Bukti" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                    </div>

                    <div class="button-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: 15px 0;">
                        <button type="button" id="startCameraBtn" class="btn btn-outline-primary" style="border-radius: 30px; padding: 10px 20px; min-width: 140px;">
                            <ion-icon name="camera-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;"> Buka Kamera</span>
                        </button>
                        <button type="button" id="capturePhotoBtn" class="btn btn-success" style="border-radius: 30px; padding: 10px 20px; min-width: 140px; display: none;">
                            <ion-icon name="aperture-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;"> Ambil Gambar</span>
                        </button>
                        <button type="button" id="retryPhotoBtn" class="btn btn-warning" style="border-radius: 30px; padding: 10px 20px; min-width: 140px; display: none;">
                            <ion-icon name="refresh-outline" style="vertical-align: middle;"></ion-icon>
                            <span style="vertical-align: middle;"> Ulangi</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Detail Laporan --}}
            <div class="card mb-3" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <h5 style="font-weight: 600; color: #343a40; margin-bottom: 15px;">
                        <ion-icon name="document-text-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                        Langkah 3: Isi Detail Laporan
                    </h5>
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
                        <select name="jenis_laporan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"%236c757d\" height=\"24\" viewBox=\"0 0 24 24\" width=\"24\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 15px center; background-size: 15px;" required>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Inisialisasi Variabel dan Elemen DOM ---
        const today = new Date().toISOString().split('T')[0];
        $('#tgl_laporan').val(today);
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        $('#jam_laporan').val(`${hours}:${minutes}`);

        // Elemen Verifikasi Wajah
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const verifyBtn = document.getElementById('verifyBtn');
        const verificationStatus = document.getElementById('verificationStatus');
        const faceImageInput = document.getElementById('face_image');
        const faceDescriptorInput = document.getElementById('face_descriptor');

        // Elemen Foto Bukti
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
        
        // Elemen Form
        const submitBtn = document.getElementById('submitBtn');

        // Variabel State
        let faceStream = null;
        let photoStream = null;
        let currentLocation = null;
        let isFaceVerified = false;
        let faceDescriptor = null;

        // --- Fungsi Helper ---

        async function getLocationName(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                return data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            } catch (error) {
                console.error("Geocoding Error:", error);
                return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
        }

        async function updateLocation() {
            try {
                photoLocation.textContent = "Lokasi: Memperbarui...";
                const position = await new Promise((resolve, reject) => navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }));
                currentLocation = { lat: position.coords.latitude, lng: position.coords.longitude };
                coordinatesDisplay.textContent = `${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}`;
                const address = await getLocationName(currentLocation.lat, currentLocation.lng);
                photoLocation.textContent = `Lokasi: ${address}`;
                $('#lokasi').val(address);
            } catch (error) {
                photoLocation.textContent = "Lokasi: Gagal";
                $('#lokasi').val("Lokasi tidak tersedia");
                coordinatesDisplay.textContent = "Gagal mendapatkan lokasi";
                Swal.fire({ icon: 'error', title: 'Gagal Mendapatkan Lokasi', text: 'Pastikan GPS dan izin lokasi telah diaktifkan.' });
            }
        }

        function updateTimestamp() {
            photoTimestamp.textContent = new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' });
        }
        const timestampInterval = setInterval(updateTimestamp, 1000);

        // --- Logika Utama ---

        async function initFaceCamera() {
            try {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Memuat Model...`;
                verificationStatus.innerHTML = `<div class="alert alert-info small p-2">Menyiapkan kamera, harap tunggu...</div>`;

                if (faceStream) faceStream.getTracks().forEach(track => track.stop());
                
                faceStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240, facingMode: 'user' }, audio: false });
                video.srcObject = faceStream;

                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);

                verifyBtn.disabled = false;
                verifyBtn.innerHTML = `<ion-icon name="scan-circle-outline"></ion-icon> Verifikasi Wajah`;
                verificationStatus.innerHTML = `<div class="alert alert-primary small p-2">Sistem siap. Posisikan wajah Anda di depan kamera.</div>`;

            } catch (error) {
                console.error("Gagal memuat model atau kamera:", error);
                let errorMessage = "Terjadi kesalahan. Coba muat ulang halaman.";
                if (error.name === 'NotAllowedError') errorMessage = "Akses kamera ditolak. Harap izinkan akses kamera.";
                else if (error.name === 'NotFoundError') errorMessage = "Kamera tidak ditemukan.";

                verificationStatus.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
                verifyBtn.innerHTML = `<ion-icon name="alert-circle-outline"></ion-icon> Gagal Memuat`;
            }
        }

        verifyBtn.addEventListener('click', async function() {
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memverifikasi...`;
            verificationStatus.innerHTML = '';
            
            try {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = canvas.toDataURL('image/jpeg', 0.8);

                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                if (!detection) {
                    throw new Error("Wajah tidak terdeteksi. Posisikan wajah Anda dengan jelas.");
                }

                // Simpan deskriptor untuk dikirim
                faceDescriptor = Array.from(detection.descriptor);
                const nik = '{{ Auth::guard("karyawan")->user()->nik }}';
                
                // --- PERUBAHAN UTAMA: Lakukan verifikasi ke server SEKARANG ---
                const response = await fetch('/face/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        face_descriptor: faceDescriptor,
                        nik: nik
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Verifikasi gagal dari server.');
                }

                // Periksa hasil 'match' dari server
                if (data.match) {
                    // Jika cocok, baru set status verified dan lanjutkan
                    isFaceVerified = true;
                    faceImageInput.value = imageData; // Simpan gambar untuk log
                    faceDescriptorInput.value = JSON.stringify(faceDescriptor); // Simpan deskriptor untuk submit akhir
                    checkFormCompletion();

                    verificationStatus.innerHTML = `
                        <div class="alert alert-success">
                            Verifikasi berhasil! (Jarak: ${data.distance.toFixed(4)})<br>Silakan lengkapi sisa form.
                        </div>
                    `;
                    verifyBtn.innerHTML = '<ion-icon name="checkmark-circle"></ion-icon> Verifikasi Sukses';
                    verifyBtn.classList.replace('btn-primary', 'btn-success');
                    if (faceStream) faceStream.getTracks().forEach(track => track.stop());
                } else {
                    // Jika tidak cocok, lempar error dengan pesan dari server
                    throw new Error(`Wajah tidak cocok. (Jarak: ${data.distance.toFixed(4)}, Batas: ${data.threshold})`);
                }

            } catch (error) {
                // Blok ini sekarang akan menangani semua jenis kegagalan
                verificationStatus.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<ion-icon name="scan-circle-outline"></ion-icon> Verifikasi Ulang';
                faceDescriptor = null;
                isFaceVerified = false;
                checkFormCompletion();
            }
        });

        startCameraBtn.addEventListener('click', async function() {
            try {
                if (photoStream) photoStream.getTracks().forEach(track => track.stop());
                photoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'environment' }, audio: false });
                photoCamera.srcObject = photoStream;
                cameraContainer.style.display = 'block';
                photoPreviewContainer.style.display = 'none';
                startCameraBtn.style.display = 'none';
                capturePhotoBtn.style.display = 'inline-block';
                retryPhotoBtn.style.display = 'none';
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal Akses Kamera', text: 'Pastikan izin kamera belakang diberikan.' });
            }
        });

        capturePhotoBtn.addEventListener('click', function() {
            photoCanvas.width = photoCamera.videoWidth;
            photoCanvas.height = photoCamera.videoHeight;
            const ctx = photoCanvas.getContext('2d');
            ctx.drawImage(photoCamera, 0, 0, photoCanvas.width, photoCanvas.height);
            
            ctx.font = 'bold 16px Courier New';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
            ctx.strokeStyle = 'rgba(0, 0, 0, 0.9)';
            ctx.lineWidth = 3;
            const timestampText = photoTimestamp.textContent;
            const locationText = photoLocation.textContent.replace('Lokasi: ', '');
            ctx.strokeText(timestampText, 10, photoCanvas.height - 35);
            ctx.fillText(timestampText, 10, photoCanvas.height - 35);
            ctx.strokeText(locationText, 10, photoCanvas.height - 15);
            ctx.fillText(locationText, 10, photoCanvas.height - 15);

            const photoDataUrl = photoCanvas.toDataURL('image/jpeg', 0.8);
            photoPreview.src = photoDataUrl;
            fotoInput.value = photoDataUrl;

            cameraContainer.style.display = 'none';
            photoPreviewContainer.style.display = 'block';
            capturePhotoBtn.style.display = 'none';
            retryPhotoBtn.style.display = 'inline-block';

            if (photoStream) photoStream.getTracks().forEach(track => track.stop());
            checkFormCompletion();
        });

        retryPhotoBtn.addEventListener('click', function() {
            photoPreviewContainer.style.display = 'none';
            startCameraBtn.style.display = 'inline-block';
            retryPhotoBtn.style.display = 'none';
            fotoInput.value = '';
            checkFormCompletion();
        });

        function checkFormCompletion() {
            submitBtn.disabled = !(isFaceVerified && fotoInput.value);
        }

        $('#laporanForm').submit(function(e) {
            e.preventDefault();
            
            if (!isFaceVerified || !faceDescriptor) {
                Swal.fire('Gagal', 'Verifikasi wajah diperlukan sebelum mengirim laporan.', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';
            
            const formData = new FormData(this);
            // formData sudah mengambil face_descriptor dari input hidden
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                } else {
                    throw new Error(response.message || 'Gagal mengirim laporan');
                }
            })
            .fail(function(jqXHR) {
                let errorTitle = 'Oops... Terjadi Kesalahan!';
                let errorMsg = 'Gagal mengirim laporan. Silakan coba lagi.';

                if (jqXHR.status === 422 && jqXHR.responseJSON) {
                    errorTitle = jqXHR.responseJSON.message || 'Validasi Gagal';
                    const errors = jqXHR.responseJSON.errors;
                    let errorList = '<ul class="list-unstyled text-start mt-2" style="padding-left: 10px;">';
                    for (const field in errors) {
                        errors[field].forEach(err => {
                            errorList += `<li class="mb-1"><small>${err}</small></li>`;
                        });
                    }
                    errorList += '</ul>';
                    errorMsg = errorList;
                } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMsg = jqXHR.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: errorTitle, html: errorMsg, confirmButtonColor: '#4e73df' });
            })
            .always(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<ion-icon name="send-outline"></ion-icon> Kirim Laporan';
            });
        });

        // --- Inisialisasi Awal & Cleanup ---
        initFaceCamera();
        updateLocation();
        
        window.addEventListener('beforeunload', function() {
            clearInterval(timestampInterval);
            if (faceStream) faceStream.getTracks().forEach(track => track.stop());
            if (photoStream) photoStream.getTracks().forEach(track => track.stop());
        });
    });
</script>
@endpush