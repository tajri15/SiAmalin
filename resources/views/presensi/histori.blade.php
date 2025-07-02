@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack" style="color: white;">
            <ion-icon name="chevron-back-outline" style="font-size: 20px;"></ion-icon>
        </a>
    </div>
    <div class="pageTitle text-center" style="font-weight: 600; letter-spacing: 0.5px;">Histori Presensi</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="section full" style="padding-top: 80px; padding-bottom: 100px;">
    <!-- Filter Section -->
    <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 15px;">
        <div class="card-body" style="padding: 20px;">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <label for="bulan" style="font-size: 13px; color: #6c757d; margin-bottom: 5px;">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding-left: 15px;">
                            <option value="">Pilih Bulan</option>
                            @for ($i=1; $i<=12; $i++) 
                                <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <label for="tahun" style="font-size: 13px; color: #6c757d; margin-bottom: 5px;">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control" style="border-radius: 10px; height: 45px; border: 1px solid #e0e0e0; padding-left: 15px;">
                            <option value="">Pilih Tahun</option>
                            @php
                            $tahunmulai = 2024;
                            $tahunskrg = date("Y");
                            @endphp
                            @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++) 
                                <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>{{ $tahun }}</option> 
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary btn-block" id="getdata" style="border-radius: 10px; height: 45px; font-weight: 500; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);">
                        <ion-icon name="search-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Animation -->
    <div id="loading" class="text-center" style="display:none; margin: 30px 0;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem; border-width: 0.25em;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p style="margin-top: 15px; color: #6c757d; font-weight: 500;">Memuat data...</p>
    </div>

    <!-- Histori Container -->
    <div id="histori-container" style="padding: 0 15px;">
        <div id="showhistori"></div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function(){
        // Hover effect for search button
        $("#getdata").hover(
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

        $("#getdata").click(function(e){
            e.preventDefault();
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();

            if(bulan == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Bulan harus dipilih',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }

            if(tahun == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tahun harus dipilih',
                    icon: 'warning',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }

            // Show loading animation
            $("#loading").fadeIn(200);
            $("#showhistori").fadeOut(100);

            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.gethistori') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun
                },
                cache: false,
                success: function(respond) {
                    // Hide loading and show data with animation
                    $("#loading").fadeOut(200, function() {
                        $("#showhistori").hide().html(respond).fadeIn(300);
                        
                        // Add animation to each history item
                        $(".presensi-card").each(function(index) {
                            $(this).css({
                                'opacity': '0',
                                'transform': 'translateY(20px)'
                            }).delay(index * 150).animate({
                                'opacity': '1',
                                'transform': 'translateY(0)'
                            }, 300);
                        });
                    });
                },
                error: function(xhr) {
                    $("#loading").fadeOut(200);
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || "Terjadi kesalahan saat memuat data",
                        icon: 'error',
                        confirmButtonColor: '#4e73df'
                    });
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Enhanced Card Styles */
    .presensi-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 18px;
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: white;
        border-left: 4px solid #4e73df;
    }
    
    .presensi-card:hover {
        transform: translateY(-5px) scale(1.01) !important;
        box-shadow: 0 12px 30px rgba(78, 115, 223, 0.15) !important;
    }
    
    .presensi-card .card-body {
        padding: 18px;
    }
    
    /* Vibrant Status Badge */
    .status-badge {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
    }
    
    .status-complete {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        box-shadow: 0 2px 8px rgba(28, 200, 138, 0.2);
    }
    
    .status-incomplete {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        box-shadow: 0 2px 8px rgba(246, 194, 62, 0.2);
    }
    
    .status-missing {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        box-shadow: 0 2px 8px rgba(231, 74, 59, 0.2);
    }
    
    /* Attractive Date Header */
    .date-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #e0e0e0;
    }
    
    .date-text {
        font-weight: 700;
        font-size: 15px;
        color: #2e3a4d;
        display: flex;
        align-items: center;
    }
    
    .date-text ion-icon {
        margin-right: 8px;
        font-size: 18px;
        color: #4e73df;
    }
    
    /* Beautiful Time Blocks */
    .time-info {
        display: flex;
        align-items: center;
        margin: 12px 0;
        padding: 12px;
        background: #f8f9fc;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .time-info:hover {
        background: #f1f5ff;
        transform: translateX(3px);
    }
    
    .time-info ion-icon {
        font-size: 20px;
        margin-right: 12px;
        min-width: 24px;
        text-align: center;
    }
    
    .time-in ion-icon {
        color: #1cc88a;
    }
    
    .time-out ion-icon {
        color: #4e73df;
    }
    
    .time-missing ion-icon {
        color: #e74a3b;
    }
    
    .time-label {
        font-size: 13px;
        color: #6c757d;
        margin-right: 5px;
        font-weight: 500;
    }
    
    .time-value {
        font-weight: 600;
        color: #4e73df;
        letter-spacing: 0.3px;
    }
    
    .time-in .time-value {
        color: #1cc88a;
    }
    
    .time-out .time-value {
        color: #4e73df;
    }
    
    /* Elegant Empty State */
    .empty-histori {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    
    .empty-histori ion-icon {
        font-size: 60px;
        color: #d1d5e0;
        margin-bottom: 15px;
    }
    
    .empty-histori h5 {
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 5px;
    }
    
    .empty-histori p {
        color: #6c757d;
        margin-top: 0;
        font-size: 14px;
    }
    
    /* Responsive Design */
    @media (max-width: 576px) {
        .presensi-card {
            margin-bottom: 15px;
        }
        
        .time-info {
            padding: 10px;
        }
        
        .time-info ion-icon {
            font-size: 18px;
        }
    }
</style>
@endpush
