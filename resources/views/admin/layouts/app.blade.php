<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SiAmalin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @stack('styles')
<style>
    body {
        overflow-x: hidden;
    }

    #wrapper {
        display: flex;
    }

    #sidebar-wrapper {
        min-height: 100vh;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        background-color: #1c2950;  
        transition: margin-left .25s ease-out;
        overflow-y: auto;
    }

    .sidebar-heading {
        padding: 1rem 1.25rem;  
        font-size: 1.25rem;  
        font-weight: 600;  
        color: #ffffff;  
        display: flex;  
        align-items: center;  
    }
    .sidebar-heading .logo-icon {  
        font-size: 1.8rem;  
        margin-right: 10px;  
        color: #87cefa;  
    }


    #sidebar-wrapper .list-group-item-action {
        color: rgba(255, 255, 255, 0.75);  
        background-color: transparent;  
        border: none;  
        padding: 0.9rem 1.25rem;  
        font-size: 0.95rem;  
    }

    #sidebar-wrapper .list-group-item-action:hover,
    #sidebar-wrapper .list-group-item-action:focus {
        color: #ffffff;  
        background-color: rgba(255, 255, 255, 0.1);  
    }

    #sidebar-wrapper .list-group-item.active {
        color: #ffffff;  
        background-color: #0d6efd;  
        font-weight: 500;
    }

    .sidebar-nav .list-group-item-action[data-bs-toggle="collapse"]::after {  
        content: '\f282';  
        font-family: 'bootstrap-icons';
        display: inline-block;
        margin-left: auto;
        transition: transform 0.2s ease-in-out;
        color: rgba(255, 255, 255, 0.75);
    }

    .sidebar-nav .list-group-item-action[data-bs-toggle="collapse"][aria-expanded="true"]::after {
        transform: rotate(-180deg);
    }

    .sidebar-nav .collapse .list-group-item-action,
    .sidebar-nav .collapsing .list-group-item-action {
        padding-left: 2.5rem;  
        font-size: 0.9em;
        color: rgba(255, 255, 255, 0.65);  
        background-color: rgba(0,0,0,0.1);  
    }
    .sidebar-nav .collapse .list-group-item-action:hover,
    .sidebar-nav .collapsing .list-group-item-action:hover {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.15);
    }
    .sidebar-nav .collapse .list-group-item-action.active {
        color: #ffffff;
        font-weight: 500;
        background-color: #0b5ed7;  
    }

    #page-content-wrapper {
        flex-grow: 1;
        padding-top: 56px;  
        margin-left: 250px;
        transition: margin-left .25s ease-out;
        min-width: 0;  
        overflow-y: auto;  
    }

    #page-content-wrapper .navbar {
        position: fixed;  
        top: 0;
        right: 0;  
        left: 250px;  
        z-index: 999;  
        transition: left .25s ease-out;
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -250px;  
    }

    #wrapper.toggled #page-content-wrapper {
        margin-left: 0;  
    }

    #wrapper.toggled #page-content-wrapper .navbar {
        left: 0;  
    }

    @media (max-width: 768px) {
        #sidebar-wrapper {
            margin-left: -250px;  
        }
        #page-content-wrapper {
            margin-left: 0;  
        }
        #page-content-wrapper .navbar {
            left: 0;  
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;  
        }
        #wrapper.toggled #page-content-wrapper {
            margin-left: 250px;  
        }
         #wrapper.toggled #page-content-wrapper .navbar {
            left: 250px;  
        }
    }
</style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="sidebar-nav" id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="bi bi-buildings-fill logo-icon"></i>
                @if(Auth::guard('karyawan')->user()->is_admin)
                    <span>SiAmalin Admin</span>
                @elseif(Auth::guard('karyawan')->user()->is_komandan)
                    <span>SiAmalin Komandan</span>
                @elseif(Auth::guard('karyawan')->user()->is_ketua_departemen)
                    <span>SiAmalin Kadept</span>
                @endif
            </div>
            <div class="list-group list-group-flush">
                @if(Auth::guard('karyawan')->user()->is_admin)
                    {{-- Menu Admin --}}
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                    </a>
                    <a class="list-group-item list-group-item-action p-3" data-bs-toggle="collapse" href="#dataMasterCollapseAdmin" role="button" aria-expanded="{{ request()->routeIs('admin.karyawan.*') || request()->routeIs('admin.fakultas.*') ? 'true' : 'false' }}" aria-controls="dataMasterCollapseAdmin">
                        <i class="bi bi-archive-fill me-2"></i>Data Master
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.karyawan.*') || request()->routeIs('admin.fakultas.*') ? 'show' : '' }}" id="dataMasterCollapseAdmin">
                        <a href="{{ route('admin.karyawan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill me-2"></i>Karyawan
                        </a>
                        <a href="{{ route('admin.fakultas.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.fakultas.*') ? 'active' : '' }}">
                            <i class="bi bi-building me-2"></i>Fakultas
                        </a>
                    </div>
                    <a href="{{ route('admin.presensi.rekapitulasi') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill me-2"></i>Presensi
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Laporan
                    </a>
                    <a href="{{ route('admin.patroli.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.patroli.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i>Histori Patroli
                    </a>
                    <a href="{{ route('admin.backup.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                        <i class="bi bi-database-down me-2"></i>Backup Data
                    </a>

                @elseif(Auth::guard('karyawan')->user()->is_komandan)
                    {{-- Menu Komandan --}}
                    <a href="{{ route('komandan.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Komandan
                    </a>
                    <a href="{{ route('komandan.karyawan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.karyawan.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i>Data Petugas
                    </a>
                    <a href="{{ route('komandan.presensi.rekapitulasi') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.presensi.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3-week-fill me-2"></i>Presensi Petugas
                    </a>
                     <a href="{{ route('komandan.laporan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-card-list me-2"></i>Laporan Petugas
                    </a>
                     <a class="list-group-item list-group-item-action p-3" data-bs-toggle="collapse" href="#patroliCollapseKomandan" role="button" aria-expanded="{{ request()->routeIs('komandan.patroli.*') ? 'true' : 'false' }}" aria-controls="patroliCollapseKomandan">
                        <i class="bi bi-signpost-split-fill me-2"></i>Patroli Petugas
                    </a>
                    <div class="collapse {{ request()->routeIs('komandan.patroli.*') ? 'show' : '' }}" id="patroliCollapseKomandan">
                        <a href="{{ route('komandan.patroli.monitoring') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.patroli.monitoring') ? 'active' : '' }}">
                            <i class="bi bi-broadcast-pin me-2"></i>Monitoring Patroli
                        </a>
                        <a href="{{ route('komandan.patroli.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.patroli.index') ? 'active' : '' }}">
                            <i class="bi bi-clock-history me-2"></i>Histori Patroli
                        </a>
                    </div>
                     <a href="{{ route('komandan.jadwalshift.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.jadwalshift.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-plus-fill me-2"></i>Jadwal Shift
                    </a>
                     <a href="{{ route('komandan.laporankinerja.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('komandan.laporankinerja.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow me-2"></i>Laporan Kinerja
                    </a>
                @elseif(Auth::guard('karyawan')->user()->is_ketua_departemen)
                    {{-- Menu Ketua Departemen --}}
                    <a href="{{ route('ketua-departemen.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('ketua-departemen.karyawan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.karyawan.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i>Data Petugas
                    </a>
                    <a href="{{ route('ketua-departemen.presensi.rekapitulasi') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.presensi.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3-week-fill me-2"></i>Monitoring Presensi
                    </a>
                     <a href="{{ route('ketua-departemen.laporan.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.laporan.*') ? 'active' : '' }}">
                        <i class="bi bi-card-list me-2"></i>Laporan Petugas
                    </a>
                     <a class="list-group-item list-group-item-action p-3" data-bs-toggle="collapse" href="#patroliCollapseKadept" role="button" aria-expanded="{{ request()->routeIs('ketua-departemen.patroli.*') ? 'true' : 'false' }}" aria-controls="patroliCollapseKadept">
                        <i class="bi bi-signpost-split-fill me-2"></i>Patroli Petugas
                    </a>
                    <div class="collapse {{ request()->routeIs('ketua-departemen.patroli.*') ? 'show' : '' }}" id="patroliCollapseKadept">
                        <a href="{{ route('ketua-departemen.patroli.monitoring') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.patroli.monitoring') ? 'active' : '' }}">
                            <i class="bi bi-broadcast-pin me-2"></i>Monitoring Patroli
                        </a>
                        <a href="{{ route('ketua-departemen.patroli.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.patroli.index') ? 'active' : '' }}">
                            <i class="bi bi-clock-history me-2"></i>Histori Patroli
                        </a>
                    </div>
                     <a href="{{ route('ketua-departemen.jadwalshift.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.jadwalshift.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check me-2"></i>Lihat Jadwal Shift
                    </a>
                     <a href="{{ route('ketua-departemen.laporankinerja.index') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('ketua-departemen.laporankinerja.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow me-2"></i>Laporan Kinerja
                    </a>
                @endif
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary btn-sm" id="menu-toggle"><i class="bi bi-list"></i></button>
                    <div class="ms-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i>{{ Auth::guard('karyawan')->user()->nama_lengkap }}
                                    @if(Auth::guard('karyawan')->user()->is_admin)
                                        <span class="badge bg-danger ms-1">Admin</span>
                                    @elseif(Auth::guard('karyawan')->user()->is_komandan)
                                        <span class="badge bg-info ms-1">Komandan</span>
                                    @elseif(Auth::guard('karyawan')->user()->is_ketua_departemen)
                                        <span class="badge bg-warning text-dark ms-1">Ketua Departemen</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}" target="_blank">Lihat Profil (Karyawan)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('proseslogout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('proseslogout') }}" method="GET" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="container-fluid p-4">
                {{-- PERBAIKAN: Menambahkan ID unik untuk setiap jenis pesan --}}
                @if(session('success'))
                    <div id="sessionSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div id="sessionErrorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                 @if(session('info'))
                    <div id="sessionInfoAlert" class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() { 
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });

            function adjustSidebar() {
                if (window.innerWidth < 768) {
                    // Sidebar akan disembunyikan oleh CSS default jika tidak 'toggled'
                } else {
                     $("#wrapper").removeClass("toggled"); 
                }
            }
            adjustSidebar(); 
            $(window).resize(adjustSidebar); 

            // PERBAIKAN: Script untuk auto-dismiss alert
            function autoDismissAlert(selector, timeout) {
                if ($(selector).length) {
                    setTimeout(function() {
                        $(selector).fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, timeout);
                }
            }
            
            autoDismissAlert("#sessionSuccessAlert", 3000); // 3 detik untuk pesan sukses
            autoDismissAlert("#sessionInfoAlert", 4000); // 4 detik untuk pesan info
            autoDismissAlert("#sessionErrorAlert", 7000); // 7 detik untuk pesan error
        });
    </script>
    @stack('scripts')
</body>
</html>
