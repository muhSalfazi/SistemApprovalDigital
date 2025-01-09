<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Approval System</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="{{ asset('assets/img/icon-kbi.png') }}" rel="icon">


    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

</head>

<style>
    .invalid-feedback {
        display: block;
        /* Pastikan terlihat */
        color: red;
        /* Opsional */
    }

    .invalid-feedback {
        display: block;
        /* Pastikan terlihat */
        color: red;
        /* Opsional */
    }
</style>

<body>

    <main>
        <div class="container">
            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="https://kyoraku.id/" class="logo d-flex align-items-center w-auto">
                                    <a href="#" class="logo d-flex align-items-center w-auto">
                                        <img src="{{ asset('assets/img/kyoraku-baru.png') }}" alt=""
                                            style="max-width: 100%; height: auto;">
                                    </a>
                                </a>
                            </div><!-- End Logo -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                        <p class="text-center small">Enter your username & password to login</p>
                                    </div>
                                    <form class="row g-3 needs-validation" action="{{ route('postlogin') }}"
                                        method="POST" novalidate>
                                        @csrf
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <div class="input-group has-validation">
                                                <input type="password" name="password"
                                                    class="form-control  @error('password') is-invalid @enderror"
                                                    id="yourPassword" required>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="togglePassword">
                                                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                    </form>

                                    {{-- js hidden+show PW --}}
                                    <script>
                                        document.getElementById('togglePassword').addEventListener('click', function() {
                                            const passwordInput = document.getElementById('yourPassword');
                                            const passwordIcon = document.getElementById('togglePasswordIcon');

                                            if (passwordInput.type === 'password') {
                                                passwordInput.type = 'text';
                                                passwordIcon.classList.remove('bi-eye-slash');
                                                passwordIcon.classList.add('bi-eye');
                                            } else {
                                                passwordInput.type = 'password';
                                                passwordIcon.classList.remove('bi-eye');
                                                passwordIcon.classList.add('bi-eye-slash');
                                            }
                                        });
                                    </script>

                                </div>
                            </div>

                            <div class="credits">
                                <div class="copyright" style="text-align: center">
                                    Copyright_HRGA System<strong><span> &copy;2024</span></strong>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>
        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{!! session('success') !!}',
                // timer: 1500,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__bounceInDown' // Menambahkan animasi muncul
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp' // Menambahkan animasi saat ditutup
                },
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{!! session('error') !!}',
                // timer: 1500,
                confirmButtonText: 'OK',
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeIn' // Animasi muncul
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut' // Animasi saat ditutup
                },
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.confirmButtonText) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Silakan isi form kembali.',
                        // confirmButtonText: 'OK',
                        timer: 1800,
                        timerProgressBar: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeIn' // Animasi muncul
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut' // Animasi saat ditutup
                        },
                    });
                }
            });
        @endif
    </script>
    <style>
        .small-swal-popup {
            width: 300px !important;
        }
    </style>
</body>

</html>
