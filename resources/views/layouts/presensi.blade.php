<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Dashboard</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}?v=3" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <link rel="manifest" href="{{ asset('__manifest.json') }}">
</head>

<body style="background-color:#e9ecef;">

    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    @yield('header')
    <div id="appCapsule">
        @yield('content')
    </div>
    @include('layouts.bottomNav')
    @include('layouts.script')

</body>

</html>