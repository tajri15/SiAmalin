@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="#" onclick="window.history.back(); return false;" class="headerButton goBack" style="color: white;">
            <ion-icon name="chevron-back-outline" style="font-size: 20px;"></ion-icon>
        </a>
    </div>
    <div class="pageTitle text-center" style="font-weight: 600; letter-spacing: 0.5px;">Profile</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section mt-2 text-center" style="padding-top: 90px;">
    <div class="avatar" style="margin-bottom: 1.5rem;">
    @php
        $user = Auth::guard('karyawan')->user();
        $foto = $user->foto ?? null;
    @endphp

    <div class="avatar" style="margin-bottom: 1.5rem;">
        <img 
            src="{{ !empty($foto) ? asset('storage/uploads/karyawan/' . $foto) : asset('assets/img/sample/avatar/avatar1.jpg') }}" 
            alt="avatar" 
            class="imaged w120 rounded-circle shadow" 
            style="border: 3px solid #4e73df; padding: 4px; box-shadow: 0 6px 20px rgba(78, 115, 223, 0.3); transition: all 0.3s ease;">
    </div>

    <h3 style="font-weight: 600; color: #343a40; margin-bottom: 5px;">{{ $user->nama_lengkap }}</h3>
    <p style="color: #6c757d; margin-bottom: 10px; font-size: 16px;">{{ $user->jabatan }}</p>
    
    <div class="profile-info" style="background: rgba(78, 115, 223, 0.05); border-radius: 15px; padding: 15px; max-width: 300px; margin: 0 auto 20px; text-align: left;">
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <ion-icon name="call-outline" style="color: #4e73df; font-size: 18px; margin-right: 10px;"></ion-icon>
            <span style="color: #495057;">{{ $user->no_hp }}</span>
        </div>
        <div style="display: flex; align-items: center;">
            <ion-icon name="mail-outline" style="color: #4e73df; font-size: 18px; margin-right: 10px;"></ion-icon>
            <span style="color: #495057;">{{ $user->email ?? 'Email belum diisi' }}</span>
        </div>
    </div>

    <div class="mt-3">
        <a href="/editprofile" class="btn btn-primary" style="border-radius: 30px; padding: 12px 30px; font-weight: 500; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3); transition: all 0.3s ease;">
            <ion-icon name="create-outline" style="vertical-align: middle; margin-right: 8px;"></ion-icon> Edit Profile
        </a>
    </div>
</div>
@endsection

@push('myscript')
<script>
    // Fallback jika history.back tidak bekerja
    $(document).on('click', '.goBack', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/dashboard'; // Fallback ke dashboard
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Avatar hover effect */
    .avatar img:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4) !important;
    }
    
    /* Button hover effect */
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78, 115, 223, 0.4) !important;
    }
    
    /* Profile info card */
    .profile-info {
        transition: all 0.3s ease;
    }
    
    .profile-info:hover {
        background: rgba(78, 115, 223, 0.1) !important;
        transform: translateY(-3px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .appHeader {
            padding-top: 15px;
            padding-bottom: 15px;
        }
        
        .pageTitle {
            font-size: 18px;
        }
    }
</style>
@endpush
