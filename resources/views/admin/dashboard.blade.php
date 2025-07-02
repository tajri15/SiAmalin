@extends('admin.layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Dashboard Admin</h1>
    <p>Selamat datang di panel admin SiAmalin.</p>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Karyawan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahKaryawan ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hadir Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hadirHariIni ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Terlambat Hari Ini Dihapus --}}

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan Baru
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $laporanBelumDitinjau ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4"> 
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Presensi Bulan Ini</h6>
                </div>
                <div class="card-body">
                    @if($rekapPresensiBulanan)
                    <p>Total Kehadiran: <strong>{{ $rekapPresensiBulanan['totalHadir'] ?? 0 }}</strong></p>
                    
                    <div style="position: relative; height:300px; width:100%;"> 
                        <canvas id="presensiChart"></canvas>
                    </div>
                    @else
                    <p>Belum ada data presensi untuk bulan ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary { border-left: .25rem solid #4e73df!important; }
    .border-left-success { border-left: .25rem solid #1cc88a!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
    .border-left-warning { border-left: .25rem solid #f6c23e!important; }
    .text-gray-300 { color: #dddfeb!important; }
    .text-gray-800 { color: #5a5c69!important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($rekapPresensiBulanan)
    const ctx = document.getElementById('presensiChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: ['Hadir'],
                datasets: [{
                    label: 'Jumlah Presensi Bulan Ini',
                    data: [
                        {{ $rekapPresensiBulanan['totalHadir'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.5)' // Success
                    ],
                    borderColor: [
                        'rgba(28, 200, 138, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false 
            }
        });
    }
    @endif
</script>
@endpush
