<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>SiAmalin - Login</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}?v=3">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}?v=3">
  <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}?v=3" type="image/x-icon">
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

    /* KODE BARU: Menambahkan .login-container untuk memusatkan form */
    .login-container {
        max-width: 400px;
        width: 100%;
        padding: 0 15px; /* Memberi sedikit padding di layar kecil */
    }

    .card {
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 2rem;
      background: #fff;
      animation: fadeIn 1s ease forwards;
      transform: translateY(30px);
      opacity: 0;
      width: 100%; /* Kartu mengisi .login-container */
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
      transform: scale(1.05);
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
    
    .panel-login-link {
      font-size: 0.85rem;
    }
    .panel-login-link a {
      color: #0d6efd;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .panel-login-link a:hover {
      color: #0b5ed7;
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
            <i class="bi bi-x-circle input-icon" onclick="clearField('nik')" style="cursor: pointer;"></i>
          </div>

          <div class="mb-3 position-relative">
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password" required>
            <i id="togglePasswordIcon" class="bi bi-eye input-icon" onclick="togglePassword()" style="cursor: pointer;"></i>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>

          <div class="text-center mt-3 panel-login-link">
              <a href="{{ route('admin.login.form') }}">Login sebagai Admin/Komandan/Kadept</a>
          </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function clearField(id) {
      document.getElementById(id).value = '';
      document.getElementById(id).focus();
    }

    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePasswordIcon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
      }
    }

    if (document.getElementById('successMessage')) {
      setTimeout(function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
          successMessage.style.display = 'none';
        }
      }, 3000); 
    }
  </script>

</body>
</html>