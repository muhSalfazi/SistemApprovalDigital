<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Approval System</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/icon-kbi.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-body">
                        <img src="{{ asset('assets/img/kyoraku-baru.png') }}" alt="Company Logo" class="logo mt-1">
                        <h5 class="card-title text-center fw-bold text-secondary  mb-1">HRGA System Scan QR Code</h5>

                        {{-- <div id="reader"class="text-center mb-4"
                            style="width: 100%; max-width: 500px; margin: auto;"></div> --}}

                        <div id="reader" class="text-center mb-4 mx-auto" style="width: 100%; max-width: 500px;">
                        </div>


                        <form id="qrForm" method="POST" action="{{ route('validate.qrcode') }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="qrCodeInput" class="form-label fw-semibold">QR Code Data</label>
                                <input type="text" name="qr_code" id="qrCodeInput" class="form-control text-center"
                                    required readonly>
                                <div class="invalid-feedback">QR Code tidak boleh kosong.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg fw-bold">
                                    Validasi QR Code <i class="bi bi-check-circle-fill"></i>
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary fw-bold mt-3">
                                    <i class="bi bi-arrow-left"></i> Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrScanner = new Html5Qrcode("reader");

            // Tentukan ukuran QR box yang fleksibel berdasarkan lebar layar
            const qrboxSize = window.innerWidth < 768 ? 250 : 400;

            qrScanner.start({
                    facingMode: "environment"
                }, // Gunakan kamera belakang
                {
                    fps: 15, // Menyesuaikan frame rate
                    qrbox: {
                        width: qrboxSize,
                        height: qrboxSize
                    }, // Kotak pemindaian
                    aspectRatio: 1.0 // Menjaga aspek rasio agar kotak tetap proporsional
                },
                function onScanSuccess(decodedText) {
                    document.getElementById('qrCodeInput').value = decodedText;
                    document.getElementById('qrForm').submit();
                },
                function onScanError(errorMessage) {
                    console.warn(`QR code scan error: ${errorMessage}`);
                }
            ).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Dapat Diakses',
                    text: 'Pastikan kamera diizinkan di browser dan perangkat ini.',
                });
            });
        });
        // Validasi Form sebelum Submit
        document.getElementById('qrForm').addEventListener('submit', function(event) {
            if (document.getElementById('qrCodeInput').value.trim() === "") {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'QR Code belum dipindai, silakan scan terlebih dahulu.',
                });
            }
        });
    </script>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <style>
        h5 {
            font-size: 20px;
            text-transform: uppercase;
            font-weight: 200;
            border-radius: 5px;
            color: #2c3e50;
        }

        body {
            background: #f5f7fa;
            font-family: 'Open Sans', sans-serif;
        }

        .logo {
            width: 200px;
            margin: 0 auto 5px;
            display: block;
        }

        .card {
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        #reader {
            position: relative;
            width: 100%;
            max-width: 500px;
            /* Maksimal untuk desktop */
            aspect-ratio: 1 / 1;
            /* Menjaga bentuk kotak */
            background: rgba(0, 0, 0, 0.8);
            margin: auto;
        }


        #reader .corner-top-left {
            top: 8px;
            left: 5px;
            border-right: none;
            border-bottom: none;
        }

        #reader .corner-top-right {
            top: 10px;
            right: 6px;
            border-left: none;
            border-bottom: none;
        }

        #reader .corner-bottom-left {
            bottom: 10px;
            left: 10px;
            border-right: none;
            border-top: none;
        }

        #reader .corner-bottom-right {
            bottom: 10px;
            right: 10px;
            border-left: none;
            border-top: none;
        }

        @media (max-width: 768px) {
            #reader {
                max-width: 300px;
                /* Ukuran lebih kecil untuk mobile */
            }

            #reader .corner-top-left,
            #reader .corner-top-right,
            #reader .corner-bottom-left,
            #reader .corner-bottom-right {
                width: 30px;
                height: 30px;
            }
        }


        @media (max-width: 768px) {
            .logo {
                width: 150px;
                /* Perkecil logo untuk tampilan mobile */
            }

            h5 {
                font-size: 16px;
                /* Sesuaikan ukuran teks */
            }

            .card {
                padding: 15px;
            }

            .btn-lg {
                font-size: 16px;
                /* Sesuaikan ukuran tombol */
            }
        }
    </style>

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
                        text: 'Silakan isi Scan kembali.',
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

        @if (session('alert'))
            Swal.fire({
                icon: '{{ session('alert.type') }}', // Tipe alert (success, warning, error, info)
                title: 'Pemberitahuan',
                text: '{{ session('alert.message') }}',
            });
        @endif
    </script>

</body>

</html>
