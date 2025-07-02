@if ($histori->isEmpty())
    <div class="empty-histori">
        <ion-icon name="calendar-outline" style="color: #4e73df;"></ion-icon>
        <h5 style="color: #4e73df; text-shadow: 0 2px 4px rgba(78, 115, 223, 0.15);">Belum Ada Presensi</h5>
        <p style="color: #6c757d;">Tidak ditemukan data presensi untuk periode ini</p>
    </div>
@else
    @foreach ($histori as $d)
    <div class="card presensi-card">
        <div class="card-body">
            <div class="date-header">
                <div class="date-text">
                    <ion-icon name="calendar-outline" style="color: #4e73df;"></ion-icon>
                    <span style="color: #2e3a4d; font-weight: 700;">{{ date('d M Y', strtotime($d->tgl_presensi)) }}</span>
                </div>
                <div class="status-badge {{ $d->jam_out ? 'status-complete' : ($d->jam_in ? 'status-incomplete' : 'status-missing') }}">
                    {{ $d->jam_out ? 'Lengkap' : ($d->jam_in ? 'Belum Pulang' : 'Tidak Absen') }}
                </div>
            </div>
            
            <div class="day-info" style="color: #6c757d;">
                <ion-icon name="time-outline" style="color: #6c757d;"></ion-icon>
                <span>{{ date('l', strtotime($d->tgl_presensi)) }}</span>
            </div>
            
            @if($d->jam_in)
            <div class="time-entry">
                <div class="time-row">
                    <ion-icon name="enter-outline" class="time-icon in" style="color: #1cc88a;"></ion-icon>
                    <span class="time-text" style="color: #1cc88a; font-weight: 600; text-shadow: 0 2px 4px rgba(28, 200, 138, 0.1);">
                        Masuk: <span style="font-weight: 700;">{{ $d->jam_in }}</span>
                    </span>
                </div>
            </div>
            @endif
            
            @if($d->jam_out)
            <div class="time-entry">
                <div class="time-row">
                    <ion-icon name="exit-outline" class="time-icon out" style="color: #4e73df;"></ion-icon>
                    <span class="time-text" style="color: #4e73df; font-weight: 600; text-shadow: 0 2px 4px rgba(78, 115, 223, 0.1);">
                        Pulang: <span style="font-weight: 700;">{{ $d->jam_out }}</span>
                    </span>
                </div>
            </div>
            @endif
            
            @if(!$d->jam_in && !$d->jam_out)
            <div class="time-entry missing">
                <div class="time-row">
                    <ion-icon name="close-circle-outline" class="time-icon" style="color: #e74a3b;"></ion-icon>
                    <span class="time-text" style="color: #e74a3b; font-weight: 600;">
                        Tidak ada data presensi
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
@endif