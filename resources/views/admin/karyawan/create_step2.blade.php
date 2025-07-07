@extends('admin.layouts.app')

@section('title', 'Registrasi Wajah Karyawan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registrasi Wajah untuk {{ session('karyawan_temp_data.nama_lengkap') }}</h6>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pastikan wajah karyawan terlihat jelas, menghadap lurus ke kamera, dan berada dalam pencahayaan yang baik.
            </div>

            <div class="text-center mb-4">
                <div id="camera-container" style="width: 500px; height: 375px; margin: 0 auto; border: 3px solid #ddd; position: relative; background-color: #000;">
                    <video id="video" width="500" height="375" autoplay playsinline style="display: none;"></video>
                    <canvas id="canvas" width="500" height="375" style="position: absolute; top: 0; left: 0;"></canvas>
                    <div id="preview-container" style="width: 100%; height: 100%; display: none;">
                        <img id="preview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div id="status-text" class="mt-2 fw-bold">Mempersiapkan kamera...</div>
            </div>
            
            <form id="registration-form" action="{{ route('admin.karyawan.complete_registration') }}" method="POST">
                @csrf
                <input type="hidden" id="face_image" name="face_image">
                <div class="text-center">
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                    <button type="button" id="captureBtn" class="btn btn-primary" disabled>
                        <i class="fas fa-camera"></i> Ambil Gambar
                    </button>
                    <button type="button" id="retakeBtn" class="btn btn-warning" style="display: none;">
                        <i class="fas fa-sync-alt"></i> Ulangi
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-success" style="display: none;">
                        <i class="fas fa-check-circle"></i> Selesaikan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const statusText = document.getElementById('status-text');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const submitBtn = document.getElementById('submitBtn');
        const registrationForm = document.getElementById('registration-form');
        const faceImageInput = document.getElementById('face_image');
        const previewContainer = document.getElementById('preview-container');
        const previewImg = document.getElementById('preview');

        let stream = null;
        let detectionInterval = null;

        statusText.textContent = "Memuat model deteksi wajah...";
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models')
            ]);
        } catch (e) {
            statusText.textContent = "Gagal memuat model. Periksa konsol untuk detail.";
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;
            video.style.display = 'block';
        } catch (err) {
            statusText.textContent = "Error: Tidak dapat mengakses kamera. Pastikan izin telah diberikan.";
            return;
        }

        video.addEventListener('play', () => {
            statusText.textContent = "Arahkan wajah ke kamera...";
            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions());
                const context = canvas.getContext('2d');
                context.clearRect(0, 0, canvas.width, canvas.height);

                if (detections) {
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    statusText.textContent = "Wajah terdeteksi. Silakan ambil gambar.";
                    statusText.classList.remove('text-danger');
                    statusText.classList.add('text-success');
                    captureBtn.disabled = false;
                } else {
                    statusText.textContent = "Wajah tidak terdeteksi. Posisikan wajah di tengah kamera.";
                    statusText.classList.remove('text-success');
                    statusText.classList.add('text-danger');
                    captureBtn.disabled = true;
                }
            }, 500);
        });

        captureBtn.addEventListener('click', () => {
            clearInterval(detectionInterval);
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, video.width, video.height);
            const dataUrl = canvas.toDataURL('image/jpeg');

            faceImageInput.value = dataUrl;
            previewImg.src = dataUrl;
            
            video.style.display = 'none';
            previewContainer.style.display = 'block';

            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-block';
            submitBtn.style.display = 'inline-block';
            statusText.textContent = "Gambar berhasil diambil. Klik 'Selesaikan' untuk menyimpan.";
        });
        
        retakeBtn.addEventListener('click', () => {
            video.style.display = 'block';
            previewContainer.style.display = 'none';
            
            captureBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
            submitBtn.style.display = 'none';
            
            video.play(); // Re-trigger 'play' event to start detection
        });

    });
</script>
@endsection