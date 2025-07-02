@extends('admin.layouts.app')

@section('title', 'Registrasi Wajah Karyawan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registrasi Wajah untuk {{ session('karyawan_temp_data.nama_lengkap') }}</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Petunjuk:</strong> Pastikan karyawan berada di tempat dengan pencahayaan yang baik dan wajah terlihat jelas. 
                Minta karyawan untuk menatap kamera langsung tanpa menggunakan aksesoris yang menutupi wajah.
            </div>

            <div class="text-center mb-4">
                <div id="camera-container" style="width: 500px; height: 375px; margin: 0 auto; border: 3px solid #ddd; position: relative;">
                    <video id="video" width="500" height="375" autoplay></video>
                    <canvas id="canvas" width="500" height="375" style="display:none;"></canvas>
                </div>
                <button id="capture-btn" class="btn btn-primary mt-3">
                    <i class="fas fa-camera"></i> Ambil Foto
                </button>
                <button id="retake-btn" class="btn btn-warning mt-3" style="display:none;">
                    <i class="fas fa-redo"></i> Ambil Ulang
                </button>
            </div>

            <div id="preview-container" style="display:none;">
                <h5>Preview Wajah</h5>
                <img id="preview" src="" alt="Preview Wajah" class="img-thumbnail mb-3">
                <p class="text-muted">Pastikan wajah terlihat jelas dan tidak blur sebelum melanjutkan.</p>
            </div>

            <form id="registration-form" action="{{ route('admin.karyawan.complete_registration') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" id="face_image" name="face_image">
                <div class="mt-4">
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Form Data
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Selesaikan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load Face API -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    // Inisialisasi kamera
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const captureBtn = document.getElementById('capture-btn');
    const retakeBtn = document.getElementById('retake-btn');
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('preview-container');
    const registrationForm = document.getElementById('registration-form');
    const faceImageInput = document.getElementById('face_image');

    let stream = null;
    let capturedImage = null;

    // Memuat model face-api.js
    async function loadModels() {
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            return true;
        } catch (e) {
            console.error('Gagal memuat model:', e);
            alert('Gagal memuat model deteksi wajah. Silakan refresh halaman.');
            return false;
        }
    }

    // Memulai kamera
    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: 500, 
                    height: 375,
                    facingMode: 'user' 
                }, 
                audio: false 
            });
            video.srcObject = stream;
        } catch (err) {
            console.error("Error accessing camera:", err);
            alert("Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.");
        }
    }

    // Menangkap gambar dari kamera
    captureBtn.addEventListener('click', async () => {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        capturedImage = canvas.toDataURL('image/jpeg');
        
        // Deteksi wajah
        const detections = await faceapi.detectAllFaces(canvas, 
            new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
        
        if (detections.length === 0) {
            alert('Tidak terdeteksi wajah. Pastikan wajah terlihat jelas di dalam frame.');
            return;
        }
        
        if (detections.length > 1) {
            alert('Terdeteksi lebih dari satu wajah. Pastikan hanya satu wajah yang terlihat.');
            return;
        }

        // Tampilkan preview
        preview.src = capturedImage;
        previewContainer.style.display = 'block';
        registrationForm.style.display = 'block';
        captureBtn.style.display = 'none';
        retakeBtn.style.display = 'inline-block';
        
        // Set nilai untuk form
        faceImageInput.value = capturedImage;
    });

    // Mengambil ulang foto
    retakeBtn.addEventListener('click', () => {
        previewContainer.style.display = 'none';
        registrationForm.style.display = 'none';
        captureBtn.style.display = 'inline-block';
        retakeBtn.style.display = 'none';
    });

    // Inisialisasi
    window.addEventListener('DOMContentLoaded', async () => {
        const modelsLoaded = await loadModels();
        if (modelsLoaded) {
            await startCamera();
        }
    });

    // Membersihkan stream saat keluar dari halaman
    window.addEventListener('beforeunload', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endsection