<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 {{-- <link rel="icon" href="{{ asset($web['web_logo']) }}" type="image/png"> --}}
    {{-- <title>{{ $web['web_nama'] }}</title> --}}
    <!-- Global stylesheets -->
    <link href="{{ asset('template/assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('template/assets/demo/demo_configurator.js') }}"></script>
    <script src="{{ asset('template/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
</head>

<body>

    <!-- Page content -->
    <div class="page-content">

        <!-- Content wrapper -->
        <div class="content-wrapper">

            <!-- Inner content -->
            <div class="content-inner">
                @include('components.demo_config')

                <!-- Content area -->
                <div class="content d-flex justify-content-center align-items-center min-vh-100">

                    <!-- Login form -->
                    <form class="login-form" action="{{ route('auth.login') }}" method="POST">
                        @csrf

                        <div class="card shadow-lg border-0 rounded-4" style="min-width: 360px;">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">

                                    {{-- <img src="{{ $web['web_logo']}}" class="h-48px mb-2" alt="Logo"> --}}
\
                                    <h3 class="fw-semibold mb-0">Login</h3>
                                    <small class="text-muted">Masukkan kredensial Anda di bawah ini</small>
                                </div>

                                <!-- Flash messages -->
                                @if (session('error_message'))
                                    <div class="alert alert-danger text-center">
                                        {{ session('error_message') }}
                                    </div>
                                @endif
                                @if (session('login_success'))
                                    <div class="alert alert-success text-center">
                                        {{ session('login_success') }}
                                    </div>
                                @endif
                                @if ($errors->has('login_error'))
                                    <div class="alert alert-danger text-center">
                                        {{ $errors->first('login_error') }}
                                    </div>
                                @endif

                                <!-- Name -->
                                <div class="mb-3">
                                    <label class="form-label" for="name">Nama</label>
                                    <div class="form-control-feedback form-control-feedback-start">
                                        <input type="text" id="name" name="name"
                                            class="form-control rounded-3" placeholder="Masukkan nama.." required>
                                        <div class="form-control-feedback-icon">
                                            <i class="ph-user-circle text-muted"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-2">
                                    <label class="form-label" for="passwordInput">Kata Sandi</label>
                                    <div class="form-control-feedback form-control-feedback-start position-relative">
                                        <input type="password" name="password" id="passwordInput"
                                            class="form-control rounded-3" placeholder="•••••••••••" required>
                                        <div class="form-control-feedback-icon">
                                            <i class="ph-lock text-muted"></i>
                                        </div>
                                        <button type="button" onclick="togglePassword()"
                                            class="position-absolute end-0 top-50 translate-middle-y me-3 p-0 bg-transparent border-0"
                                            style="z-index: 10;">
                                            <i id="toggleIcon" class="ph-eye text-muted fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Forgot password -->
                                {{-- <div class="d-flex justify-content-end mb-3">
                                    <a href="" class="text-decoration-none small text-muted">
                                        Lupa password?
                                    </a>
                                </div> --}}

                                <!-- Remember me -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Ingat saya</label>
                                </div>

                                <!-- Submit -->
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- /Login form -->

                </div>
                <!-- /Content area -->

            </div>
            <!-- /Inner content -->

        </div>
        <!-- /Content wrapper -->

    </div>
    <!-- /Page content -->

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        }
    </script>
</body>

</html>
