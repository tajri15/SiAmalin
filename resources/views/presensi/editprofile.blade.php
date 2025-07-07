@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="{{ route('profile') }}" class="headerButton" style="color: white;">
            <ion-icon name="chevron-back-outline" style="font-size: 20px;"></ion-icon>
        </a>
    </div>
    <div class="pageTitle text-center" style="font-weight: 600; letter-spacing: 0.5px;">Edit Profile</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: -2rem; padding-bottom: 80px;">
    <div class="col">
        @php
            $messagesuccess = Session::get('success');
            $messageerror = Session::get('error');
        @endphp
        @if($messagesuccess)
        <div class="alert alert-success" style="margin: 15px; border-radius: 10px; background: rgba(40, 167, 69, 0.1); border: 1px solid rgba(40, 167, 69, 0.3); color: #28a745; padding: 10px 15px;">
            <ion-icon name="checkmark-circle" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
            {{ $messagesuccess }}
        </div>
        @endif
        @if($messageerror)
        <div class="alert alert-danger" style="margin: 15px; border-radius: 10px; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); color: #dc3545; padding: 10px 15px;">
            <ion-icon name="close-circle" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
            {{ $messageerror }}
        </div>
        @endif
    </div>
</div>

<div class="section mt-1" style="padding: 0 15px; margin-bottom: 30px;">
    <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 6px 15px rgba(0,0,0,0.05);">
        <div class="card-body" style="padding: 20px 20px 30px;">
            <form action="{{ route('updateprofile') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf
                
                <!-- Profile Picture Upload -->
                <div class="form-group text-center mb-4">
                    <div class="avatar" style="margin-bottom: 20px;">
                        @php
                            $user = Auth::guard('karyawan')->user();
                            $foto = $user->foto ?? null;
                        @endphp
                        <img 
                            src="{{ !empty($foto) ? asset('storage/' . $foto) : asset('assets/img/sample/avatar/avatar1.jpg') }}" 
                            alt="avatar" 
                            class="imaged w120 rounded-circle shadow" 
                            style="border: 3px solid #4e73df; padding: 4px; box-shadow: 0 6px 20px rgba(78, 115, 223, 0.3);">
                    </div>
                    <div class="file-upload-wrapper" style="position: relative; overflow: hidden; display: inline-block; width: 100%;">
                        <button type="button" class="btn btn-outline-primary btn-block" style="border-radius: 10px; height: 45px; border: 1px dashed #4e73df; background-color: rgba(78, 115, 223, 0.05); position: relative;">
                            <ion-icon name="camera-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                            <span id="file-name" style="font-size: 14px;">Ubah Foto Profil</span>
                        </button>
                        <input type="file" name="foto" id="foto" accept=".png, .jpg, .jpeg" style="position: absolute; font-size: 100px; opacity: 0; right: 0; top: 0; height: 100%; width: 100%; cursor: pointer;">
                    </div>
                    <small class="text-muted" style="font-size: 12px; display: block; margin-top: 5px;">Format: JPG/PNG (Max 2MB)</small>
                </div>
                
                <!-- Nama Lengkap -->
                <div class="form-group mb-3">
                    <label for="nama_lengkap" style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Nama Lengkap</label>
                    <div class="input-with-clear" style="position: relative;">
                        <input type="text" class="form-control" value="{{ $user->nama_lengkap }}" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" autocomplete="off" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; padding-right: 40px;">
                        <span class="clear-field" data-target="#nama_lengkap" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                            <ion-icon name="close-circle" style="color: #6c757d; font-size: 18px;"></ion-icon>
                        </span>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="form-group mb-3">
                    <label for="email" style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Email</label>
                    <div class="input-with-clear" style="position: relative;">
                        <input type="email" class="form-control" value="{{ $user->email ?? '' }}" name="email" id="email" placeholder="Email" autocomplete="off" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; padding-right: 40px;">
                        <span class="clear-field" data-target="#email" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                            <ion-icon name="close-circle" style="color: #6c757d; font-size: 18px;"></ion-icon>
                        </span>
                    </div>
                </div>
                
                <!-- No HP -->
                <div class="form-group mb-3">
                    <label for="no_hp" style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">No. HP</label>
                    <div class="input-with-clear" style="position: relative;">
                        <input type="text" class="form-control" value="{{ $user->no_hp }}" name="no_hp" id="no_hp" placeholder="No. HP" autocomplete="off" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; padding-right: 40px;">
                        <span class="clear-field" data-target="#no_hp" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                            <ion-icon name="close-circle" style="color: #6c757d; font-size: 18px;"></ion-icon>
                        </span>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="form-group mb-4">
                    <label for="password" style="font-size: 14px; color: #6c757d; margin-bottom: 5px; display: block;">Password (Kosongkan jika tidak diubah)</label>
                    <div class="input-with-toggle" style="position: relative;">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="new-password" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding: 0 15px; padding-right: 40px;">
                        <span class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                            <ion-icon name="eye-off-outline" style="color: #6c757d; font-size: 18px;"></ion-icon>
                        </span>
                    </div>
                    <small class="text-muted" style="font-size: 12px;">Minimal 6 karakter</small>
                </div>
                
                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" style="border-radius: 10px; height: 45px; font-weight: 500; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3); transition: all 0.3s ease; margin-top: 10px;">
                        <ion-icon name="refresh-outline" style="vertical-align: middle; margin-right: 8px;"></ion-icon> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Show selected file name
        $('#foto').change(function() {
            var fileName = $(this).val().split('\\').pop();
            $('#file-name').text(fileName || 'Ubah Foto Profil');
            
            // Preview the selected image
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.avatar img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Clear field functionality
        $(".clear-field").click(function() {
            var target = $(this).data('target');
            $(target).val('').focus();
        });
        
        // Toggle password visibility
        $(".toggle-password").click(function() {
            var passwordInput = $("#password");
            var icon = $(this).find("ion-icon");
            
            if (passwordInput.attr("type") === "password") {
                passwordInput.attr("type", "text");
                icon.attr("name", "eye-outline");
            } else {
                passwordInput.attr("type", "password");
                icon.attr("name", "eye-off-outline");
            }
        });
        
        // Form validation
        $("#profileForm").submit(function(e) {
            var nama = $("#nama_lengkap").val();
            var email = $("#email").val();
            var nohp = $("#no_hp").val();
            
            if (nama == "") {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Nama lengkap harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                e.preventDefault();
                return false;
            }
            
            if (email == "") {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Email harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                e.preventDefault();
                return false;
            }
            
            if (nohp == "") {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Nomor HP harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                e.preventDefault();
                return false;
            }
            
            // Validate email format
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Format email tidak valid',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                e.preventDefault();
                return false;
            }
        });
        
        // Button hover effect
        $(".btn-primary").hover(
            function() {
                $(this).css({
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 8px 20px rgba(78, 115, 223, 0.4)'
                });
            },
            function() {
                $(this).css({
                    'transform': 'translateY(0)',
                    'box-shadow': '0 4px 15px rgba(78, 115, 223, 0.3)'
                });
            }
        );
        
        // Clear field hover effect
        $(".clear-field").hover(
            function() {
                $(this).find("ion-icon").css('color', '#e74a3b');
            },
            function() {
                $(this).find("ion-icon").css('color', '#6c757d');
            }
        );
        
        // Toggle password hover effect
        $(".toggle-password").hover(
            function() {
                $(this).find("ion-icon").css('color', '#4e73df');
            },
            function() {
                $(this).find("ion-icon").css('color', '#6c757d');
            }
        );
    });
</script>
@endpush

@push('styles')
<style>
    /* Avatar styles */
    .avatar {
        margin: 0 auto;
        width: 120px;
        height: 120px;
    }
    
    .avatar img {
        transition: all 0.3s ease;
    }
    
    .avatar img:hover {
        transform: scale(1.05);
    }
    
    /* File upload button */
    .btn-outline-primary:hover {
        background-color: rgba(78, 115, 223, 0.1) !important;
    }
    
    /* Input focus effect */
    .form-control:focus {
        border-color: #4e73df !important;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
    }
    
    /* Input with clear/toggle */
    .input-with-clear,
    .input-with-toggle {
        position: relative;
    }
    
    /* Safe area for mobile devices */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
    }
    
    /* Ensure proper spacing at bottom */
    html, body {
        overflow-x: hidden;
        height: 100%;
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
        
        /* Additional bottom spacing for mobile */
        .row {
            padding-bottom: 100px !important;
        }
        
        .card-body {
            padding-bottom: 40px !important;
        }
    }
</style>
@endpush