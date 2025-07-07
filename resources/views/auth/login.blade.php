<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
  <title>SiAmalin - Login</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #dfe9f3, #ffffff);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    /* BARU: Menambahkan class container untuk pembungkus utama */
    .login-container {
        max-width: 400px;
        width: 100%;
    }

    .card {
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 2rem;
      background: #fff;
      animation: fadeIn 1s ease forwards;
      transform: translateY(30px);
      opacity: 0;
      width: 100%;
    }

    .form-image {
      display: block;
      margin: 0 auto 1.5rem;
      max-width: 150px;
      width: 100%;
    }

    .card h1 {
      font-weight: 700;
      color: #0d6efd;
    }

    .btn-primary {
      background: #0d6efd;
      border: none;
      padding: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: 0.3s ease;
      border-radius: 10px;
    }

    .btn-primary:hover {
      background: #0b5ed7;
      transform: scale(1.02); /* Sedikit disesuaikan agar lebih halus */
    }

    input.form-control {
      height: 50px;
      border-radius: 10px;
      border: 1px solid #ced4da;
      background-color: #f8f9fa;
      transition: 0.3s;
      padding-right: 2.5rem;
    }

    input.form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }

    .input-icon {
      position: absolute;
      top: 50%;
      right: 1rem;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      font-size: 1.2rem;
    }

    .input-icon.bi-x-circle {
      font-size: 1.1rem;
    }

    .alert {
      border-radius: 10px;
    }
    
    .footer-link a {
        color: #003972;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-link a:hover {
        color: #002b5c;
        text-decoration: underline;
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
  {{-- PERUBAHAN UTAMA ADA DI SINI --}}
  <div class="login-container">
    <div class="card p-4">

      <div class="text-center">
        <img src="{{ asset('assets/img/login/login.jpg') }}" alt="Login Illustration" class="form-image" onerror="this.onerror=null;this.src='https://placehold.co/150x150/EBF4FF/0D6EFD?text=SiAmalin';">
      </div>

      <div class="text-center mb-3">
        <h1 class="text-primary">SiAmalin</h1>
        <h4>Silahkan Login</h4>
      </div>

      <div>
        @if (session('success'))
          <div id="successMessage" class="alert alert-success">
            {{ session('success') }}
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

        <form action="{{ route('proseslogin') }}" method="POST">
          @csrf

          <div class="mb-3 position-relative">
            <input type="text" name="nik" id="nik" class="form-control" placeholder="Masukkan NIK" required value="{{ old('nik') }}">
            <i class="bi bi-x-circle input-icon" onclick="clearField('nik')" style="cursor: pointer; display: none;" id="clearNikIcon"></i>
          </div>

          <div class="mb-3 position-relative">
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password" required>
            <i id="togglePasswordIcon" class="bi bi-eye-slash input-icon" onclick="togglePassword()" style="cursor: pointer;"></i>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
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
        if (id === 'nik') {
            clearNikIcon.style.display = 'none';
        }
    }

    if(nikInput && clearNikIcon){
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

    if (document.getElementById('successMessage')) {
      setTimeout(function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.style.display = 'none', 500);
        }
      }, 3000);
    }
  </script>

</body>
</html>