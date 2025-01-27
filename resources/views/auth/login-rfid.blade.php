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
                                        <p class="text-center small">Please sign-in to your account and start the
                                            adventure!</p>
                                    </div>
                                    <form class="row g-3 needs-validation" action="{{ route('postloginrfid') }}"
                                        method="POST" novalidate>
                                        @csrf
                                        <div class="col-12">
                                            <label for="RFID" class="form-label">RFID</label>
                                            <input type="text" name="RFID"
                                                class="form-control @error('RFID') is-invalid @enderror" id="RFID"
                                                value="{{ old('RFID') }}" required autofocus>
                                            @error('RFID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary fw-bold w-100"
                                                    style="font-size: 0.875rem; padding: 4px 8px;">
                                                    Login
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <a href="{{ route('login') }}" class="btn btn-outline-secondary"
                                                    style="font-size: 0.775rem; padding: 3px 8px;">
                                                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                                                </a>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="credits">
                            <div class="copyright" style="text-align: center">
                                Copyright_HRGA System<strong><span> &copy;2024</span></strong>
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
                width: '400px', // Ukuran popup medium
                padding: '20px',
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                customClass: {
                    popup: 'small-swal-popup'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{!! session('error') !!}',
                width: '400px', // Ukuran popup medium
                padding: '20px',
                confirmButtonText: 'OK',
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut'
                },
                customClass: {
                    popup: 'small-swal-popup'
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.confirmButtonText) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: 'Silakan isi form kembali.',
                        width: '350px', // Ukuran popup kecil
                        padding: '15px',
                        timer: 1800,
                        timerProgressBar: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeIn'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut'
                        },
                        customClass: {
                            popup: 'small-swal-popup'
                        }
                    });
                }
            });
        @endif

        @if (session('alert'))
            Swal.fire({
                icon: '{{ session('alert.type') }}',
                title: 'Pemberitahuan',
                text: '{{ session('alert.message') }}',
                width: '400px', // Ukuran popup medium
                padding: '20px',
                customClass: {
                    popup: 'small-swal-popup'
                }
            });
        @endif
    </script>

    <style>
        /* CSS untuk mengatur ukuran swal agar lebih modern */
        .small-swal-popup {
            font-size: 14px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .swal2-title {
            font-size: 18px;
            font-weight: 600;
        }

        .swal2-popup {
            border-radius: 10px;
        }

        .swal2-confirm {
            background-color: #28a745 !important;
            font-size: 14px;
            padding: 10px 20px;
        }

        .swal2-cancel {
            background-color: #dc3545 !important;
            font-size: 14px;
            padding: 10px 20px;
        }

        /* Responsif untuk tampilan mobile */
        @media (max-width: 576px) {
            .swal2-popup {
                width: 320px !important; /* Untuk tampilan mobile */
            }
        }
    </style>


    <script>
        function submitForm() {
            document.getElementById('partNumberForm').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const RFID = document.getElementById('RFID');
            RFID.focus(); // Fokus pada input saat halaman dimuat
            // Menjaga fokus tetap pada input meskipun mengklik di luar form
            document.addEventListener('click', function(event) {
                if (event.target !== RFID) {
                    RFID.focus();
                }
            });
        });

        // Fungsi untuk memfokus kembali input jika form telah dikirim
        window.onfocus = function() {
            const partNumberInput = document.getElementById('RFID');
            RFID.focus();
        };
    </script>

    {{-- refresh halaman --}}
    <script>
        let idleTime = 0;

        // Reset idle timer saat pengguna berinteraksi
        function resetIdleTime() {
            idleTime = 0;
        }

        // Deteksi aktivitas pengguna (desktop dan mobile)
        document.addEventListener('mousemove', resetIdleTime); // Desktop: Mouse bergerak
        document.addEventListener('keypress', resetIdleTime); // Desktop: Ketik keyboard
        document.addEventListener('scroll', resetIdleTime); // Semua: Scroll halaman
        document.addEventListener('click', resetIdleTime); // Semua: Klik layar
        document.addEventListener('touchstart', resetIdleTime); // Mobile/Tablet: Sentuhan awal
        document.addEventListener('touchmove', resetIdleTime); // Mobile/Tablet: Sentuhan bergerak

        // Cek setiap 1 menit jika pengguna idle selama 5 menit
        setInterval(function() {
            idleTime++;
            if (idleTime >= 5) { // 5 menit tidak ada aktivitas
                Swal.fire({
                    title: "Perhatian",
                    text: "Anda tidak melakukan aktivitas selama 5 menit. Halaman akan direfresh, ingin tetap di halaman ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan",
                    cancelButtonText: "Tidak",
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        }, 300000); // Cek setiap 5 menit
    </script>
    {{-- end --}}
</body>

</html>
