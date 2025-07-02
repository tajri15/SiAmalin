<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
    <title>SiAmalin - Panel Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --panel-primary-color: #003972; /* Biru tua untuk konsistensi panel */
            --panel-hover-color: #002b5c;
            --light-bg: #f4f7f6; 
            --text-dark: #333;
            --text-muted-custom: #6c757d;
        }

        body {
            background: var(--light-bg);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }
        .login-container {
            max-width: 420px;
            width: 100%;
        }
        .card {
            border-radius: 15px; 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12); 
            padding: 2.5rem; 
            background: #fff;
            animation: fadeIn 0.8s ease-out forwards;
            transform: translateY(20px);
            opacity: 0;
            border: none; 
        }
        .form-image {
            display: block;
            margin: 0 auto 1.5rem;
            max-width: 150px;
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .card .title-main {
            font-weight: 700;
            color: var(--panel-primary-color);
            font-size: 1.75rem; 
            margin-bottom: 0.25rem;
        }
        .card .title-sub {
            font-weight: 500;
            color: var(--text-muted-custom);
            font-size: 1rem;
            margin-bottom: 2rem;
        }
        .btn-login {
            background: var(--panel-primary-color);
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border-radius: 8px;
            color: #fff;
        }
        .btn-login:hover {
            background: var(--panel-hover-color); 
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 57, 114, 0.3);
        }
        input.form-control {
            height: 52px;
            border-radius: 8px;
            border: 1px solid #dde2e5;
            background-color: #fdfdff;
            transition: all 0.3s ease;
            padding-left: 1rem;
            padding-right: 3rem; 
            font-size: 0.95rem;
        }
        input.form-control:focus {
            border-color: var(--panel-primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 57, 114, 0.2); 
            background-color: #fff;
        }
        .input-icon-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted-custom);
            font-size: 1.2rem;
            transition: color 0.2s ease;
        }
        .input-icon:hover {
            color: var(--panel-primary-color);
        }
        .input-icon.bi-x-circle {
            font-size: 1.1rem;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .footer-link a {
            color: var(--panel-primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-link a:hover {
            color: var(--panel-hover-color);
            text-decoration: underline;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card p-4">

            <div class="text-center">
                <img src="{{ asset('assets/img/login/login.jpg') }}" alt="Login SiAmalin" class="form-image" 
                     onerror="this.onerror=null;this.src='https://placehold.co/150x120/003972/FFFFFF?text=SiAmalin&font=poppins';">
            </div>

            <div class="text-center mb-4">
                <h1 class="title-main">Panel SiAmalin</h1>
                <h4 class="title-sub">Admin, Komandan, & Ketua Departemen</h4>
            </div>

            <div>
                @if (session('success'))
                <div id="successMessage" class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('info'))
                <div id="infoMessage" class="alert alert-info">
                    {{ session('info') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.login.proses') }}" method="POST">
                    @csrf

                    <div class="mb-3 input-icon-wrapper">
                        <input type="text" name="nik" id="nik" class="form-control" placeholder="NIK" required value="{{ old('nik') }}">
                        <i class="bi bi-x-circle input-icon" onclick="clearField('nik')" style="right: 1rem; display: {{ old('nik') ? 'block' : 'none' }};" id="clearNikIcon"></i>
                    </div>

                    <div class="mb-4 input-icon-wrapper"> 
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        <i id="togglePasswordIcon" class="bi bi-eye-slash input-icon" onclick="togglePassword()"></i>
                    </div>

                    <button type="submit" class="btn btn-login w-100">LOGIN</button>
                </form>

                <div class="text-center mt-4 footer-link">
                    <small><a href="{{ route('login') }}">Login sebagai Petugas Keamanan</a></small>
                </div>
            </div>

        </div>
    </div>

    <script>
        const nikInput = document.getElementById('nik');
        const clearNikIcon = document.getElementById('clearNikIcon');

        function clearField(id) {
            const field = document.getElementById(id);
            field.value = '';
            field.focus();
            if (id === 'nik' && clearNikIcon) {
                clearNikIcon.style.display = 'none';
            }
        }

        if (nikInput && clearNikIcon) {
            nikInput.addEventListener('input', function() {
                clearNikIcon.style.display = this.value.length > 0 ? 'block' : 'none';
            });
            if (nikInput.value.length > 0) {
                clearNikIcon.style.display = 'block';
            }
        }


        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        }

        ['successMessage', 'infoMessage'].forEach(function(id) {
            const messageElement = document.getElementById(id);
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.transition = 'opacity 0.5s ease';
                    messageElement.style.opacity = '0';
                    setTimeout(() => messageElement.style.display = 'none', 500);
                }, 4000); 
            }
        });
    </script>

</body>
</html>
