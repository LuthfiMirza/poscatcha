<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login Admin</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon-catcha.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon-catcha.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <style>
    body {
      min-height: 100vh;
      background:
        radial-gradient(circle at top left, rgba(106, 170, 42, 0.18), transparent 32%),
        radial-gradient(circle at bottom right, rgba(232, 101, 10, 0.16), transparent 30%),
        linear-gradient(135deg, #f7fbf2 0%, #fff7ef 100%);
      font-family: 'Poppins', 'Open Sans', sans-serif;
    }

    .login-shell {
      min-height: 100vh;
    }

    .login-panel {
      width: 100%;
      max-width: 430px;
    }

    .login-brand {
      gap: 12px;
      text-decoration: none;
    }

    .login-brand-logo {
      width: 64px;
      height: 64px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #fff;
      box-shadow: 0 10px 28px rgba(106, 170, 42, 0.24);
    }

    .login-brand-text {
      color: #1a1a1a;
      font-size: 28px;
      font-weight: 800;
      letter-spacing: -0.04em;
    }

    .login-brand-text__cat {
      color: #6aaa2a;
    }

    .login-brand-text__cha {
      color: #e8650a;
    }

    .login-card {
      border: 0;
      border-radius: 24px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
      backdrop-filter: blur(14px);
    }

    .login-card .card-body {
      padding: 34px;
    }

    .login-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 999px;
      background: #eefbe6;
      color: #5b941f;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .login-title {
      color: #172033;
      font-size: 26px;
      font-weight: 800;
      line-height: 1.2;
    }

    .login-subtitle {
      color: #64748b;
      font-size: 14px;
    }

    .login-form .form-label {
      color: #334155;
      font-size: 13px;
      font-weight: 700;
    }

    .login-form .form-control,
    .login-form .input-group-text {
      min-height: 46px;
      border-color: #e2e8f0;
      background: #f8fafc;
      border-radius: 14px;
    }

    .login-form .input-group-text {
      color: #6aaa2a;
      border-right: 0;
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }

    .login-form .input-group .form-control {
      border-left: 0;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }

    .login-form .form-control:focus {
      border-color: #9bd36b;
      background: #fff;
      box-shadow: 0 0 0 0.22rem rgba(106, 170, 42, 0.16);
    }

    .login-button {
      min-height: 48px;
      border: 0;
      border-radius: 14px;
      background: #6aaa2a;
      box-shadow: 0 10px 22px rgba(106, 170, 42, 0.22);
      font-weight: 800;
    }

    .login-button:hover,
    .login-button:focus {
      background: #5b941f;
      box-shadow: 0 12px 24px rgba(106, 170, 42, 0.28);
    }
  </style>

</head>

<body>

  <main>
    <div class="container">

      <section class="section register login-shell d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center login-panel">

              <div class="d-flex justify-content-center py-4">
                <a href="/" class="login-brand d-flex align-items-center w-auto">
                  <img src="{{ asset('assets/img/logocat.jpeg') }}" alt="Logo CATcha" class="login-brand-logo">
                  <span class="login-brand-text d-none d-lg-block"><span class="login-brand-text__cat">CAT</span><span class="login-brand-text__cha">cha</span></span>
                </a>
              </div>

              <div class="card mb-3 login-card">
                <div class="card-body">

                  <div class="pt-2 pb-4 text-center">
                    <span class="login-eyebrow">CATcha POS</span>
                    <h5 class="card-title login-title text-center pb-0 mt-3 mb-2">Admin CATcha</h5>
                    <p class="login-subtitle text-center mb-0">Kelola produk, stok, laporan, dan data kasir.</p>
                  </div>

                  {{-- Session Status --}}
                  @if (session('status'))
                    <div class="alert alert-success small">
                      {{ session('status') }}
                    </div>
                  @endif

                  <form method="POST" action="{{ route('login_admin') }}" class="row g-3 needs-validation login-form" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="col-12">
                      <label for="email" class="form-label">Email</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text">@</span>
                        <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>

                    {{-- Password --}}
                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="password" required>
                      @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100 login-button" type="submit">Masuk Admin</button>
                    </div>
                  </form>

                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
