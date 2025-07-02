@extends('layouts.presensi')
@section('content')

<style>
    .logout {
        position: absolute;
        color: rgba(255, 255, 255, 0.8);
        font-size: 24px;
        right: 16px;
        top: 16px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.1);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    .logout:hover {
        color: white;
        transform: scale(1.1);
        background: rgba(255, 255, 255, 0.2);
    }
</style>

<!-- User Section -->
<div class="section" id="user-section" style="padding-bottom: 40px;">
    <a href="/proseslogout" class="logout">
        <ion-icon name="exit-outline"></ion-icon>
    </a>
    <div id="user-detail" class="d-flex align-items-center" style="margin-top: 10px;">
        <div class="avatar" style="margin-left: 20px;">
            @if(!empty(Auth::guard('karyawan')->user()->foto))
                <img src="{{ asset('storage/' . Auth::guard('karyawan')->user()->foto) }}" 
                     alt="avatar" 
                     class="imaged w64 rounded-circle" 
                     style="height:70px; width:70px; object-fit: cover; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.1)">
            @else
                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" 
                     alt="avatar" 
                     class="imaged w64 rounded-circle"
                     style="height:70px; width:70px; object-fit: cover; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.1)">
            @endif
        </div>
        <div id="user-info" class="ms-0 flex-grow-1">
            <h2 id="user-name" style="margin-bottom: 5px; font-weight: 600; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                {{ Auth::guard('karyawan')->user()->nama_lengkap }}
            </h2>
            <span id="user-role" style="color: rgba(255,255,255,0.8); font-size: 14px; display: inline-block; background: rgba(0,0,0,0.15); padding: 3px 10px; border-radius: 20px;">
                {{ Auth::guard('karyawan')->user()->jabatan }}
            </span>
        </div>
    </div>
</div>

<!-- Menu Section -->
<div class="section" id="menu-section" style="margin-top: 0px;">
    <div class="card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: none;">
        <div class="card-body" style="padding: 15px;">
            <div class="list-menu d-flex justify-content-around">
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/profile" class="bg-gradient-primary" style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                            <ion-icon name="person-circle-outline" style="font-size: 28px; color: white;"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name mt-2">
                        <span style="font-size: 12px; font-weight: 500; color: #6c757d;">Profil</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/histori" class="bg-gradient-warning" style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 15px; background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); box-shadow: 0 4px 15px rgba(246, 211, 101, 0.3);">
                            <ion-icon name="time-outline" style="font-size: 28px; color: white;"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name mt-2">
                        <span style="font-size: 12px; font-weight: 500; color: #6c757d;">Histori</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/laporan" class="bg-gradient-danger" style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 15px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);">
                            <ion-icon name="document-text-outline" style="font-size: 28px; color: white;"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name mt-2">
                        <span style="font-size: 12px; font-weight: 500; color: #6c757d;">Laporan</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="{{ route('patroli.index') }}" class="bg-gradient-info" style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 15px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                            <ion-icon name="walk-outline" style="font-size: 28px; color: white;"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name mt-2">
                        <span style="font-size: 12px; font-weight: 500; color: #6c757d;">Patroli</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Presence Section -->
<div class="section mt-2" id="presence-section">
    <div class="todaypresence">
        <div class="row justify-content-center" style="margin-left: 0; margin-right: 0;">
            <div class="col-5" style="padding-left: 0px; padding-right: 0px;">
                <div class="card gradasigreen" style="border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 6px 15px rgba(28, 200, 138, 0.2);">
                    <div class="card-body" style="padding: 15px;">
                        <div class="presencecontent d-flex align-items-center">
                            <div class="iconpresence mr-3" style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <ion-icon name="enter-outline" style="font-size: 24px; color: white;"></ion-icon>
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle mb-1" style="font-size: 14px; color: rgba(255,255,255,0.8);">Masuk</h4>
                                <span style="font-size: 18px; font-weight: 600; color: white;">{{ $presensihariini?->jam_in ?? '--:--' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5" style="padding-left: 0px; padding-right: 0px;">
                <div class="card gradasired" style="border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 6px 15px rgba(231, 74, 59, 0.2);">
                    <div class="card-body" style="padding: 15px;">
                        <div class="presencecontent d-flex align-items-center">
                            <div class="iconpresence mr-3" style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <ion-icon name="exit-outline" style="font-size: 24px; color: white;"></ion-icon>
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle mb-1" style="font-size: 14px; color: rgba(255,255,255,0.8);">Pulang</h4>
                                <span style="font-size: 18px; font-weight: 600; color: white;">{{ ($presensihariini?->jam_out && $presensihariini?->jam_out != '00:00:00') ? $presensihariini->jam_out : '--:--' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absen Button -->
    <div class="row mt-3">
        <div class="col-12">
            @if ($presensihariini == null)
                <a href="/presensi/create" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    ABSEN MASUK
                </a>
            @elseif ($presensihariini->jam_out == null)
                <a href="/presensi/create" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    ABSEN PULANG
                </a>
            @else
                <button class="btn btn-secondary btn-block" disabled>
                    <ion-icon name="checkmark-done-outline"></ion-icon> ABSENSI SELESAI
                </button>
            @endif
        </div>
    </div>

    <!-- Rekap Presensi -->
    <div id="rekappresensi" class="mt-3 mb-2">
        <h3 style="font-weight: 700; position: relative; display: inline-block;">
            <span style="position: relative; z-index: 2;">Rekap Presensi</span>
            <span style="position: absolute; bottom: 5px; left: 0; width: 100%; height: 8px; background: linear-gradient(to right, rgba(78, 115, 223, 0.3), rgba(28, 200, 138, 0.3)); z-index: 1; border-radius: 4px;"></span>
        </h3>
    </div>

    <div class="presencetab mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist" style="border-bottom: none; position: relative;">
                <li class="nav-item" style="flex: 1; text-align: center;">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab" 
                       style="border: none; color: #495057; font-weight: 600; padding: 10px 0; position: relative; overflow: hidden; border-radius: 10px 10px 0 0;">
                        <span style="position: relative; z-index: 2;">Bulan Ini</span>
                        <span class="active-indicator" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #4e73df, #1cc88a); border-radius: 3px 3px 0 0;"></span>
                    </a>
                </li>
                <li class="nav-item" style="flex: 1; text-align: center;">
                    <a class="nav-link" data-toggle="tab" href="#profile" role="tab" 
                       style="border: none; color: #6c757d; font-weight: 500; padding: 10px 0; position: relative; overflow: hidden; border-radius: 10px 10px 0 0;">
                        <span style="position: relative; z-index: 2;">Hari Ini</span>
                        <span class="hover-indicator" style="position: absolute; bottom: 0; left: 0; width: 0; height: 3px; background: linear-gradient(to right, #4e73df, #1cc88a); transition: width 0.3s ease; border-radius: 3px 3px 0 0;"></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content mt-0" style="margin-bottom:100px;">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview" style="border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    @foreach ($historibulanini as $d)
                        @php
                        $warnaJamIn = ($d->jam_in == null || $d->jam_in == '00:00:00') ? 'bg-warning' : 'bg-success';
                        $warnaJamOut = ($d->jam_out == null || $d->jam_out == '00:00:00') ? 'bg-secondary' : 'bg-primary';
                        @endphp
                        <li style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <div class="item" style="padding: 12px 15px;">
                                <div class="icon-box" style="background: rgba(78, 115, 223, 0.1); color: #4e73df; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                    <ion-icon name="calendar-outline" style="font-size: 20px;"></ion-icon>
                                </div>
                                <div class="in d-flex justify-content-between align-items-center" style="flex: 1; margin-left: 15px; margin-bottom: 12px;">
                                    <div style="font-weight: 500; color: #343a40;">
                                        {{ date("d M Y", strtotime($d->tgl_presensi)) }}
                                        <div class="text-muted" style="font-size: 12px; margin-top: 2px;">
                                            {{ date("l", strtotime($d->tgl_presensi)) }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge {{ $warnaJamIn }} mb-1" style="border-radius: 8px; padding: 5px 10px; font-weight: 500; font-size: 12px;">
                                            {{ $d->jam_in == null || $d->jam_in == '00:00:00' ? 'Belum Absen' : $d->jam_in }}
                                        </div>
                                        <div class="badge {{ $warnaJamOut }}" style="border-radius: 8px; padding: 5px 10px; font-weight: 500; font-size: 12px;">
                                            {{ $d->jam_out == null || $d->jam_out == '00:00:00' ? 'Belum Absen' : $d->jam_out }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <ul class="listview image-listview" style="border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    @foreach ($leaderboard as $d)
                        @php
                        $warnaJam = (!isset($d['jam_in']) || $d['jam_in'] == null || $d['jam_in'] == '00:00:00') ? 'bg-warning' : 'bg-success';
                        $jamInText = (!isset($d['jam_in']) || $d['jam_in'] == null || $d['jam_in'] == '00:00:00') ? 'Belum Absen' : $d['jam_in'];
                        @endphp
                        <li style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <div class="item" style="padding: 12px 15px;">
                                <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="image" class="image" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                                <div class="in" style="flex: 1; margin-left: 15px;">
                                    <div>
                                        <b style="color: #343a40;">{{ $d['nama_lengkap'] ?? '-' }}</b><br>
                                        <small class="text-muted">{{ $d['jabatan'] ?? 'N/A' }}</small>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge {{ $warnaJam }}" style="border-radius: 8px; padding: 5px 10px; font-weight: 500; font-size: 12px;">
                                            {{ $jamInText }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Hover effect for menu items
    $('.list-menu .item-menu').hover(
        function() {
            $(this).find('.menu-icon a').css('transform', 'scale(1.1)');
            $(this).find('.menu-name span').css('color', '#4e73df');
        },
        function() {
            $(this).find('.menu-icon a').css('transform', 'scale(1)');
            $(this).find('.menu-name span').css('color', '#6c757d');
        }
    );

    // Hover effect for nav tabs
    $('.nav-tabs .nav-link').hover(
        function() {
            if (!$(this).hasClass('active')) {
                $(this).find('.hover-indicator').css('width', '100%');
            }
        },
        function() {
            $(this).find('.hover-indicator').css('width', '0');
        }
    );

    // Animation for today's presence cards
    $('.todaypresence .card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': index === 0 ? 'translateX(-20px)' : 'translateX(20px)'
        }).delay(index * 100).animate({
            'opacity': '1',
            'transform': 'translateX(0)'
        }, 300);
    });

    // Animation for list items
    $('.listview li').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(10px)'
        }).delay(index * 50).animate({
            'opacity': '1',
            'transform': 'translateY(0)'
        }, 200);
    });
});
</script>
@endpush
