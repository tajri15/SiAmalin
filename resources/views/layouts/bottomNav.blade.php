    <div class="appBottomMenu">
        <a href="{{ route('dashboard') }}" class="item {{ request()->routeIs('dashboard') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="home-outline"></ion-icon>
                <strong>Home</strong>
            </div>
        </a>
        <a href="{{ route('presensi.histori') }}" class="item {{ request()->routeIs('presensi.histori') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="time-outline"></ion-icon>
                <strong>Histori</strong>
            </div>
        </a>
        <a href="{{ route('presensi.create') }}" class="item {{ request()->routeIs('presensi.create') ? 'active' : '' }}">
            <div class="col">
                <div class="action-button large">
                    <ion-icon name="camera" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
                </div>
            </div>
        </a>
        {{-- PERUBAHAN DI SINI --}}
        <a href="{{ route('laporan.index') }}" class="item {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.create') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="document-text-outline"></ion-icon>
                <strong>Laporan</strong>
            </div>
        </a>
        <a href="{{ route('profile') }}" class="item {{ request()->routeIs('profile') || request()->routeIs('editprofile') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
                <strong>Profile</strong>
            </div>
        </a>
    </div>
    