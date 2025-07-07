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
                        <canvas id="canvasElement" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
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
                        <select name="jenis_laporan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px;" required>
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
        // Hanya jalankan skrip jika data penting dari controller tersedia
        @if (isset($faceDescriptor) && $faceDescriptor !== 'null' && isset($karyawan))

        // --- DEKLARASI VARIABEL DAN ELEMEN DOM ---
        const video = document.getElementById('videoElement');
        const canvas = document.getElementById('canvasElement');
        const verificationStatus = document.getElementById('verificationStatus');
        const faceImageInput = document.getElementById('face_image');
        const faceDescriptorInput = document.getElementById('face_descriptor');
        const submitBtn = document.getElementById('submitBtn');
        const fotoInput = document.getElementById('fotoInput');
        const startCameraBtn = document.getElementById('startCameraBtn');
        const capturePhotoBtn = document.getElementById('capturePhotoBtn');
        const retryPhotoBtn = document.getElementById('retryPhotoBtn');
        const photoCamera = document.getElementById('photoCamera');
        const photoCanvas = document.getElementById('photoCanvas');
        const photoPreview = document.getElementById('photoPreview');
        const photoPreviewContainer = document.getElementById('photoPreviewContainer');
        const cameraContainer = document.querySelector('.camera-container');
        const photoTimestamp = document.getElementById('photoTimestamp');
        const photoLocation = document.getElementById('photoLocation');
        const coordinatesDisplay = document.getElementById('coordinatesDisplay');
        const laporanForm = document.getElementById('laporanForm');
        
        let faceStream = null;
        let photoStream = null;
        let isFaceVerified = false;
        let faceDetectionInterval;
        let verificationSucceeded = false; // Penanda untuk mengunci status verifikasi
        
        const faceApiThreshold = 0.5; // Threshold yang lebih ketat untuk akurasi
        const storedDescriptor = new Float32Array(Object.values(JSON.parse('{!! $faceDescriptor !!}')));

        // --- FUNGSI-FUNGSI HELPER ---

        function stopStream(stream) {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }

        function updateLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const { latitude, longitude } = position.coords;
                    const locationString = `Lat: ${latitude.toFixed(5)}, Lng: ${longitude.toFixed(5)}`;
                    document.getElementById('lokasi').value = locationString;
                    coordinatesDisplay.textContent = locationString;
                    photoLocation.textContent = `Lokasi: ${latitude.toFixed(3)}, ${longitude.toFixed(3)}`;
                });
            }
        }

        function updateTimestamp() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateString = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            photoTimestamp.textContent = `${dateString} ${timeString}`;
        }

        function checkFormCompletion() {
            const isFormComplete = isFaceVerified &&
                fotoInput.value &&
                document.getElementById('tgl_laporan').value &&
                document.getElementById('jam_laporan').value &&
                document.querySelector('select[name="jenis_laporan"]').value &&
                document.querySelector('textarea[name="keterangan"]').value &&
                document.getElementById('lokasi').value;
            submitBtn.disabled = !isFormComplete;
        }

        // --- LOGIKA UTAMA APLIKASI ---

        async function initFaceCamera() {
            try {
                verificationStatus.innerHTML = `<div class="alert alert-info small p-2">Mempersiapkan kamera verifikasi...</div>`;
                stopStream(faceStream);
                faceStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240, facingMode: 'user' }, audio: false });
                video.srcObject = faceStream;
                video.onloadedmetadata = () => {
                    verificationStatus.innerHTML = `<div class="alert alert-primary small p-2">Sistem siap. Posisikan wajah Anda.</div>`;
                    startFaceDetection();
                };
            } catch (error) {
                verificationStatus.innerHTML = `<div class="alert alert-danger">Kamera Error: ${error.message}</div>`;
            }
        }

        function startFaceDetection() {
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);

            faceDetectionInterval = setInterval(async () => {
                if (verificationSucceeded) return; // Jika sudah berhasil, hentikan proses

                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                
                if (detection) {
                    const distance = faceapi.euclideanDistance(storedDescriptor, detection.descriptor);
                    if (distance < faceApiThreshold) {
                        verificationSucceeded = true; // Kunci status menjadi berhasil
                        clearInterval(faceDetectionInterval);

                        verificationStatus.innerHTML = `<div class="alert alert-success p-2 small"><strong>Verifikasi Berhasil!</strong> Silakan lanjutkan.</div>`;
                        video.style.border = '3px solid #1cc88a';
                        
                        const canvasForSaving = document.createElement('canvas');
                        canvasForSaving.width = video.videoWidth;
                        canvasForSaving.height = video.videoHeight;
                        canvasForSaving.getContext('2d').drawImage(video, 0, 0);
                        
                        faceImageInput.value = canvasForSaving.toDataURL('image/jpeg');
                        faceDescriptorInput.value = JSON.stringify(Array.from(detection.descriptor));
                        
                        isFaceVerified = true;
                        checkFormCompletion();
                        stopStream(faceStream);
                        faceStream = null;
                    } else {
                        if (!verificationSucceeded) verificationStatus.innerHTML = `<div class="alert alert-warning p-2 small">Wajah tidak dikenali. Coba lagi.</div>`;
                    }
                } else {
                    if (!verificationSucceeded) verificationStatus.innerHTML = `<div class="alert alert-secondary p-2 small">Arahkan wajah ke kamera...</div>`;
                }
            }, 1000);
        }

        startCameraBtn.addEventListener('click', async function() {
            try {
                stopStream(photoStream);
                photoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'environment' }, audio: false });
                photoCamera.srcObject = photoStream;
                cameraContainer.style.display = 'block';
                startCameraBtn.style.display = 'none';
                capturePhotoBtn.style.display = 'inline-block';
                retryPhotoBtn.style.display = 'none';
                updateLocation();
                setInterval(updateTimestamp, 1000);
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal Akses Kamera Belakang', text: 'Pastikan izin kamera telah diberikan dan tidak ada aplikasi lain yang menggunakan kamera.' });
            }
        });

        capturePhotoBtn.addEventListener('click', function() {
            photoCanvas.width = photoCamera.videoWidth;
            photoCanvas.height = photoCamera.videoHeight;
            const ctx = photoCanvas.getContext('2d');
            ctx.drawImage(photoCamera, 0, 0, photoCanvas.width, photoCanvas.height);
            
            const timestampText = photoTimestamp.textContent;
            const locationText = photoLocation.textContent;
            ctx.font = "14px Poppins";
            ctx.fillStyle = "rgba(255, 255, 255, 0.8)";
            ctx.fillText(timestampText, 10, photoCanvas.height - 30);
            ctx.fillText(locationText, 10, photoCanvas.height - 10);
            
            const photoDataUrl = photoCanvas.toDataURL('image/jpeg', 0.8);
            photoPreview.src = photoDataUrl;
            fotoInput.value = photoDataUrl;

            cameraContainer.style.display = 'none';
            photoPreviewContainer.style.display = 'block';
            capturePhotoBtn.style.display = 'none';
            retryPhotoBtn.style.display = 'inline-block';
            
            stopStream(photoStream);
            photoStream = null;
            
            checkFormCompletion();
        });

        retryPhotoBtn.addEventListener('click', function() {
            photoPreviewContainer.style.display = 'none';
            startCameraBtn.style.display = 'inline-block';
            retryPhotoBtn.style.display = 'none';
            fotoInput.value = '';
            checkFormCompletion();
        });

        laporanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false
                        }).then(() => { window.location.href = response.redirect_url; });
                    } else {
                        throw new Error(response.message || 'Gagal mengirim laporan');
                    }
                },
                error: function(jqXHR) {
                    let errorMsg = jqXHR.responseJSON ? jqXHR.responseJSON.message : "Terjadi kesalahan.";
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<ion-icon name="send-outline"></ion-icon> Kirim Laporan';
                },
                complete: function() {
                    // Logic is handled in success/error now
                }
            });
        });
        
        // Initial setup
        const now = new Date();
        document.getElementById('tgl_laporan').value = now.toISOString().split('T')[0];
        document.getElementById('jam_laporan').value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        laporanForm.querySelectorAll('input, select, textarea').forEach(el => el.addEventListener('input', checkFormCompletion));
        window.addEventListener('beforeunload', () => { stopStream(faceStream); stopStream(photoStream); });

        // Inisialisasi awal model face-api
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset("models") }}'),
            faceapi.nets.faceLandmark68Net.loadFromUri('{{ asset("models") }}'),
            faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset("models") }}')
        ]).then(initFaceCamera).catch(err => {
            verificationStatus.innerHTML = `<div class="alert alert-danger">Gagal memuat model. Periksa koneksi dan muat ulang.</div>`;
        });

        updateLocation();

        @else
            $('#verificationStatus').html(`<div class="alert alert-danger">Anda tidak dapat membuat laporan karena data wajah atau lokasi kantor belum diatur. Harap hubungi admin.</div>`);
            laporanForm.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = true);
        @endif
    });
</script>
@endpush