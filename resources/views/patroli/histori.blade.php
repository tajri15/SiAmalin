{{-- File: resources/views/patroli/histori.blade.php --}}
@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="{{ route('dashboard') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Histori Patroli Saya</div>
    <div class="right">
         <a href="{{ route('patroli.index') }}" class="headerButton">
            <ion-icon name="play-outline" style="font-size: 24px;"></ion-icon>
        </a>
    </div>
</div>
<style>
    .patrol-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        margin-bottom: 1rem;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        background: white;
    }
    .patrol-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .patrol-card .card-body {
        padding: 1rem 1.25rem;
    }
    .patrol-card .patrol-date {
        font-weight: 600;
        color: #007bff; /* Primary color */
        font-size: 1.1em;
        margin-bottom: 0.3rem;
    }
    .patrol-card .patrol-meta {
        font-size: 0.9em;
        color: #6c757d; /* Muted color */
        margin-bottom: 0.2rem;
    }
    .patrol-card .patrol-meta ion-icon {
        vertical-align: middle;
        margin-right: 5px;
        font-size: 1.1em;
    }
    .patrol-card .patrol-status {
        font-size: 0.85em;
        font-weight: 500;
        padding: 0.3em 0.6em;
        border-radius: 0.25rem;
    }
    .status-selesai { background-color: #28a745; color: white; }
    .status-dibatalkan { background-color: #dc3545; color: white; }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin: 15px;
    }
    .empty-state ion-icon {
        font-size: 4rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }
    .empty-state h5 {
        font-weight: 600;
        color: #495057;
    }
    .empty-state p {
        color: #6c757d;
        font-size: 0.95em;
    }
    .alert-fixed-top {
        position: fixed;
        top: 70px; /* Disesuaikan dengan tinggi appHeader + sedikit margin */
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        width: auto;
        min-width: 300px;
        max-width: 90%;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: .5rem;
    }
    /* Tambahan style untuk tombol dengan ikon */
    .btn-with-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-with-icon ion-icon {
        font-size: 1.2em;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="section full" style="padding-top: 70px; padding-bottom: 70px;">
    <div class="wide-block pt-2 pb-2">

        {{-- Menampilkan flash message jika ada --}}
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show alert-fixed-top m-2" role="alert" id="flashErrorMessage">
                {{ session('error') }}
            </div>
        @endif
        @if(session()->has('success'))
             <div class="alert alert-success alert-dismissible fade show alert-fixed-top m-2" role="alert" id="flashSuccessMessage">
                {{ session('success') }}
            </div>
        @endif


        @if($patrols->isEmpty())
            <div class="empty-state">
                <ion-icon name="map-outline"></ion-icon>
                <h5>Belum Ada Histori Patroli</h5>
                <p>Mulai patroli pertama Anda untuk melihat riwayatnya di sini.</p>
                <a href="{{ route('patroli.index') }}" class="btn btn-primary mt-2 d-inline-flex align-items-center" style="border-radius: 20px; padding: 8px 20px;">
                    <ion-icon name="play-circle-outline" class="me-2" style="font-size: 1.2em; position: relative; top: 8px;"></ion-icon>
                    Mulai Patroli Sekarang
                </a>
            </div>
        @else
            @foreach ($patrols as $patrol)
            <a href="{{ route('patroli.histori.detail', $patrol->_id) }}" style="text-decoration: none; color: inherit;">
                <div class="card patrol-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="patrol-date">
                                    {{ \Carbon\Carbon::parse($patrol->start_time)->isoFormat('dddd, D MMMM YYYY') }}
                                </div>
                                <div class="patrol-meta">
                                    <ion-icon name="time-outline"></ion-icon>
                                    {{ \Carbon\Carbon::parse($patrol->start_time)->format('H:i') }} - {{ $patrol->end_time ? \Carbon\Carbon::parse($patrol->end_time)->format('H:i') : 'Berlangsung' }}
                                </div>
                            </div>
                            <span class="patrol-status status-{{ strtolower(str_replace(' ', '-', $patrol->status)) }}">
                                {{ ucfirst($patrol->status) }}
                            </span>
                        </div>
                        <hr style="margin: 0.75rem 0;">
                        <div class="patrol-meta">
                            <ion-icon name="walk-outline"></ion-icon>
                            Jarak: <strong>{{ number_format(($patrol->total_distance_meters ?? 0) / 1000, 2) }} km</strong>
                        </div>
                        <div class="patrol-meta">
                            <ion-icon name="hourglass-outline"></ion-icon>
                            Durasi:
                            @php
                                $duration = $patrol->duration_seconds ?? 0;
                                $hours = floor($duration / 3600);
                                $minutes = floor(($duration % 3600) / 60);
                                $seconds = $duration % 60;
                                echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                            @endphp
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
            <div class="mt-3 d-flex justify-content-center">
                {{ $patrols->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk tooltip jika digunakan (Bootstrap 5)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    function hideFlashMessage(elementId, duration = 3000) { // Default duration 3 detik
        const alertElement = document.getElementById(elementId);
        if (alertElement) {
            // Tunggu durasi tertentu, lalu sembunyikan
            setTimeout(() => {
                // Coba tutup menggunakan instance Bootstrap jika ada untuk animasi fade
                const bsAlert = bootstrap.Alert.getInstance(alertElement);
                if (bsAlert) {
                    bsAlert.close();
                } else {
                    // Fallback jika tidak ada instance Bootstrap (misalnya, elemen sudah dimanipulasi)
                    // atau jika ingin langsung menghapus tanpa animasi Bootstrap.
                    alertElement.style.display = 'none'; // Atau alertElement.remove();
                }
            }, duration);
        }
    }

    // Menghilangkan flash message setelah beberapa detik pada saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        hideFlashMessage('flashErrorMessage', 3000); 
        hideFlashMessage('flashSuccessMessage', 3000);
    });

    // Menghilangkan flash message jika halaman dimuat (termasuk dari bfcache)
    window.addEventListener('pageshow', function(event) {
        hideFlashMessage('flashErrorMessage', 3000); 
        hideFlashMessage('flashSuccessMessage', 3000);
    });
</script>
@endpush
