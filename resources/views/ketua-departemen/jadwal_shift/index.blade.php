@extends('admin.layouts.app')

@section('title', 'Jadwal Shift Petugas - ' . $ketua->departemen)

@push('styles')
<style>
    .table-jadwal th, .table-jadwal td {
        vertical-align: middle;
        text-align: center;
        font-size: 0.85rem;
        padding: 0.5rem;
        min-width: 100px;
    }
    .table-jadwal th.nama-karyawan, .table-jadwal td.nama-karyawan {
        text-align: left;
        min-width: 180px;
        background-color: #f8f9fc;
        position: sticky;
        left: 0;
        z-index: 1;
        box-shadow: 2px 0 5px -2px #ccc;
    }
     .table-responsive {
        max-height: 70vh;
    }
    .shift-pagi { background-color: #d1e7dd !important; color: #0f5132 !important; } 
    .shift-malam { background-color: #cfe2ff !important; color: #084298 !important; } 
    .shift-libur { background-color: #f8f9fa !important; color: #6c757d !important; } 
    .shift-custom { background-color: #e2e3e5 !important; color: #495057 !important; } 

    .filter-form .form-control-sm, .filter-form .btn-sm {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Jadwal Shift Petugas</h1>
        <div>
            <span class="badge bg-primary text-white p-2">Fakultas: {{ $ketua->unit }}</span>
            <span class="badge bg-info text-white p-2">Departemen: {{ $ketua->departemen }}</span>
        </div>
    </div>
    <p class="mb-4">Menampilkan jadwal shift mingguan untuk petugas keamanan di departemen Anda.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Minggu</h6>
        </div>
        <div class="card-body filter-form">
            <form method="GET" action="{{ route('ketua-departemen.jadwalshift.index') }}" class="row gx-2 gy-2 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal" class="form-label mb-1 small">Pilih Tanggal:</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ $selectedDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Tampilkan</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('ketua-departemen.jadwalshift.index', ['tanggal' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-chevron-left"></i> Minggu Sebelumnya
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('ketua-departemen.jadwalshift.index', ['tanggal' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm w-100">
                        Minggu Berikutnya <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Jadwal Shift Minggu: {{ $startOfWeek->isoFormat('D MMM YY') }} - {{ $endOfWeek->isoFormat('D MMM YY') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm table-jadwal">
                    <thead class="table-light">
                        <tr>
                            <th class="nama-karyawan">Nama Petugas</th>
                            @foreach ($datesOfWeek as $date)
                                <th class="text-center">
                                    {{ $date->isoFormat('dddd') }}<br>
                                    <small>{{ $date->isoFormat('D MMM') }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalMingguan as $nik => $dataJadwal)
                        <tr>
                            <td class="nama-karyawan">
                                {{ $dataJadwal['nama_lengkap'] }}
                                <small class="d-block text-muted">NIK: {{ $nik }}</small>
                            </td>
                            @foreach ($datesOfWeek as $tanggal)
                                @php
                                    $shift = $dataJadwal['shifts'][$tanggal->format('Y-m-d')] ?? null;
                                    $shiftClass = '';
                                    $shiftText = 'Kosong';
                                    if ($shift) {
                                        $shiftNamaUpper = strtoupper($shift->shift_nama);
                                        if ($shiftNamaUpper === 'PAGI') $shiftClass = 'shift-pagi';
                                        elseif ($shiftNamaUpper === 'MALAM') $shiftClass = 'shift-malam';
                                        elseif ($shiftNamaUpper === 'LIBUR') $shiftClass = 'shift-libur';
                                        else $shiftClass = 'shift-custom'; 
                                        
                                        $shiftText = $shift->shift_nama;
                                        if ($shift->jam_mulai && $shift->jam_selesai) {
                                            $shiftText .= "<br><small>(" . substr($shift->jam_mulai, 0, 5) . "-" . substr($shift->jam_selesai, 0, 5) . ")</small>";
                                        }
                                    }
                                @endphp
                                <td class="{{ $shiftClass }}">
                                    {!! $shift ? $shiftText : '<span class="text-muted fst-italic">Kosong</span>' !!}
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($datesOfWeek) + 1 }}" class="text-center">
                                Belum ada petugas keamanan di departemen ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
