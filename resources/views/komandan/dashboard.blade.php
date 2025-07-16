@extends('admin.layouts.app')

@section('title', 'Dashboard Komandan')

@push('styles')
<style>
    .info-box .info-box-icon { font-size: 2.5rem; }
    .info-box .info-box-content { text-align: right; }
    .border-left-primary { border-left: .25rem solid #4e73df!important; }
    .border-left-success { border-left: .25rem solid #1cc88a!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
    .border-left-warning { border-left: .25rem solid #f6c23e!important; }
    .border-left-danger { border-left: .25rem solid #e74a3b!important; } 
    .text-gray-300 { color: #dddfeb!important; }
    .text-gray-800 { color: #5a5c69!important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Komandan</h1>
        <span class="badge bg-primary text-white p-2">Fakultas: {{ $fakultasKomandan ?? 'N/A' }}</span>
    </div>

    <p>Selamat datang di panel Komandan SiAmalin.</p>

    <div class="row">
        {{-- Total Petugas Keamanan --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Petugas Keamanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPetugas ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hadir Hari Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hadir Hari Ini (Petugas)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hadirHariIni ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Laporan Belum Ditinjau --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan Baru (Petugas)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $laporanBelumDitinjau ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-medical-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Laporan Bulan Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Laporan Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLaporanBulanIni ?? 0 }}</div>
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
        {{-- Total Patroli Selesai Bulan Ini --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Patroli Selesai (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPatroliBulanIni ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-signpost-split-fill fs-2 text-gray-300"></i>
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
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Presensi Petugas Bulan Ini ({{ $fakultasKomandan }})</h6>
                </div>
                <div class="card-body">
                    @if($rekapPresensiBulanan && ($rekapPresensiBulanan['totalHadir'] ?? 0) > 0)
                    <p>Total Kehadiran Petugas: <strong>{{ $rekapPresensiBulanan['totalHadir'] ?? 0 }}</strong></p>
                    <div style="position: relative; height:300px; width:100%;">
                        <canvas id="presensiKomandanChart"></canvas>
                    </div>
                    @else
                    <p>Belum ada data presensi petugas untuk bulan ini di fakultas Anda.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Akses Cepat</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="{{ route('komandan.karyawan.index') }}">Data Petugas Keamanan</a></li>
                        <li class="list-group-item"><a href="{{ route('komandan.presensi.rekapitulasi') }}">Monitoring Presensi Petugas</a></li>
                        <li class="list-group-item"><a href="{{ route('komandan.laporan.index') }}">Laporan Petugas</a></li>
                        <li class="list-group-item"><a href="{{ route('komandan.patroli.index') }}">Patroli Petugas</a></li>
                        <li class="list-group-item"><a href="{{ route('komandan.jadwalshift.index') }}">Atur Jadwal Shift</a></li>
                        <li class="list-group-item"><a href="{{ route('komandan.laporankinerja.index') }}">Laporan Kinerja Bulanan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(isset($rekapPresensiBulanan) && ($rekapPresensiBulanan['totalHadir'] ?? 0) > 0)
    const ctxKomandan = document.getElementById('presensiKomandanChart');
    if (ctxKomandan) {
        new Chart(ctxKomandan, {
            type: 'bar', 
            data: {
                labels: ['Hadir'],
                datasets: [{
                    label: 'Jumlah Presensi Petugas Bulan Ini',
                    data: [
                        {{ $rekapPresensiBulanan['totalHadir'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.5)'
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
