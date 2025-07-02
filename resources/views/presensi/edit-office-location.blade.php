@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="{{ route('dashboard') }}" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Lokasi Kantor</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div class="row" style="margin-top: 70px; margin-bottom: 70px;">
    <div class="col">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                <form action="{{ route('presensi.update-office-location') }}" method="POST" id="officeLocationForm">
                    @csrf
                    
                    <div class="form-group">
                        <label>Lokasi Kantor Saat Ini</label>
                        <div id="map" style="height: 300px; border-radius: 10px;"></div>
                        <small class="text-muted">Geser map dan klik untuk mengubah lokasi kantor</small>
                        <input type="hidden" id="latitude" name="latitude" value="{{ $karyawan->office_location['coordinates'][1] ?? '' }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ $karyawan->office_location['coordinates'][0] ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Radius (meter)</label>
                        <input type="number" class="form-control" name="radius" id="radius" 
                            value="{{ $karyawan->office_radius ?? 55 }}" min="10" max="500">
                        <small class="text-muted">Jarak maksimum dari kantor untuk bisa melakukan presensi</small>
                    </div>
                    
                    <div class="form-group basic">
                        <button type="submit" class="btn btn-primary btn-block">
                            <ion-icon name="save-outline"></ion-icon> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi map dengan lokasi kantor saat ini
    var currentLat = {{ $karyawan->office_location['coordinates'][1] ?? -2.5489 }};
    var currentLng = {{ $karyawan->office_location['coordinates'][0] ?? 118.0149 }};
    
    var map = L.map('map').setView([currentLat, currentLng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tambahkan marker
    var marker = L.marker([currentLat, currentLng], {
        draggable: true
    }).addTo(map);
    
    // Update marker position when map is clicked
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng);
    });
    
    // Update marker position when dragged
    marker.on('dragend', function(e) {
        updateLocation(marker.getLatLng());
    });
    
    function updateLocation(latlng) {
        $('#latitude').val(latlng.lat);
        $('#longitude').val(latlng.lng);
    }
    
    // Form validation
    $('#officeLocationForm').submit(function(e) {
        if (!$('#latitude').val() || !$('#longitude').val()) {
            e.preventDefault();
            alert('Silahkan tentukan lokasi kantor pada peta');
            return false;
        }
        
        var radius = $('#radius').val();
        if (!radius || radius < 10 || radius > 500) {
            e.preventDefault();
            alert('Radius harus antara 10-500 meter');
            return false;
        }
    });
});
</script>
@endpush