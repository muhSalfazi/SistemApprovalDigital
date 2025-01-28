<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ValidasiQRcode - Approval System</title>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/icon-kbi.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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
                <div class="card shadow-lg border-0 rounded-4 p-4 animate__animated animate__fadeInUp">
                    <div class="text-center">
                        <img src="{{ asset('assets/img/kyoraku-baru.png') }}" alt="Company Logo" class="logo mt-3">
                        <h5 class="mt-3 fw-bold text-dark">HRGA SYSTEM</h5>
                        <p class="text-muted">Silakan pilih metode verifikasi QR Code</p>
                    </div>

                    <div class="d-flex justify-content-center mb-3">
                        <button class="btn btn-outline-primary btn-sm me-2"
                            style="font-size: 0.775rem; padding: 3px 8px;" onclick="toggleMethod('scan')">
                            <i class="bi bi-qr-code-scan"></i> Scan QR Code
                        </button>
                        <button class="btn btn-outline-secondary btn-sm"style="font-size: 0.775rem; padding: 3px 8px;"
                            onclick="toggleMethod('upload')">
                            <i class="bi bi-upload"></i> Upload Gambar
                        </button>
                    </div>

                    <!-- Scan QR Code -->
                    <div id="scan-method" class="text-center">
                        <h6 class="text-primary fw-bold mb-3">Scan QR Code Anda</h6>
                        <div id="reader-container" class="mx-auto">
                            <div id="reader"></div>
                        </div>
                        <form id="qrForm" method="POST" action="{{ route('validate.qrcode') }}" class="mt-3">
                            @csrf
                            <input type="text" name="qr_code" id="qrCodeInput" class="form-control text-center mt-2"
                                placeholder="Hasil QR Code" required readonly>
                            <button type="submit" class="btn btn-success w-100 mt-3 btn-sm" style="font-size: 0.875rem; padding: 3px 8px;">
                                <i class="bi bi-qr-code-scan"></i> Validasi QR Code
                            </button>
                        </form>
                    </div>

                    <!-- Upload Gambar QR Code -->
                    <div id="upload-method" class="d-none">
                        <h6 class="text-center text-secondary fw-bold">Upload Gambar QR Code</h6>
                        <form id="uploadForm" method="POST" enctype="multipart/form-data"
                            action="{{ route('upload.qrcode') }}" class="mt-3">
                            @csrf
                            <div class="text-center mb-3">
                                <input type="file" name="qr_image" id="qrImage" class="form-control"
                                    accept="image/*" required onchange="previewImage(event)">
                                <div class="invalid-feedback">Silakan unggah gambar QR Code.</div>
                            </div>

                            <!-- Thumbnail Preview -->
                            <div id="imagePreviewContainer" class="text-center d-none">
                                <img id="imagePreview" class="img-thumbnail rounded shadow-lg"
                                    alt="Thumbnail Preview" />
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-3 btn-sm"style="font-size: 0.875rem; padding: 3px 8px;">
                                <i class="bi bi-upload"></i> Upload dan Validasi
                            </button>
                        </form>
                    </div>


                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary" style="font-size: 0.875rem; padding: 3px 8px;">
                            <i class="bi bi-arrow-left"></i> Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let formData = new FormData(this);
            let submitButton = document.querySelector('#uploadForm button[type="submit"]');
            submitButton.innerHTML = '<i class="bi bi-upload"></i> Mengupload...';
            submitButton.disabled = true;

            fetch("{{ route('upload.qrcode') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitButton.innerHTML = '<i class="bi bi-upload"></i> Upload dan Validasi';
                    submitButton.disabled = false;

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            showConfirmButton: true,
                            timer: 10000,
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message,
                            showConfirmButton: true,
                        });
                    }
                })
                .catch(error => {
                    submitButton.innerHTML = '<i class="bi bi-upload"></i> Upload dan Validasi';
                    submitButton.disabled = false;

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Terjadi kesalahan saat memproses gambar.',
                        confirmButtonText: 'OK'
                    });
                });
        });

        function toggleMethod(method) {
            if (method === 'scan') {
                document.getElementById('scan-method').classList.remove('d-none');
                document.getElementById('upload-method').classList.add('d-none');
            } else {
                document.getElementById('scan-method').classList.add('d-none');
                document.getElementById('upload-method').classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const qrScanner = new Html5Qrcode("reader");

            function getQrBoxSize() {
                if (window.innerWidth < 768) {
                    return {
                        width: 160,
                        height: 160
                    }; // Ukuran lebih kecil untuk mobile
                }
                return {
                    width: 250,
                    height: 250
                }; // Ukuran default untuk desktop
            }

            qrScanner.start({
                    facingMode: "environment"
                }, // Gunakan kamera belakang jika tersedia
                {
                    fps: 15,
                    qrbox: getQrBoxSize(),
                    aspectRatio: 1.0,
                    disableFlip: false, // Pastikan tidak membalik gambar
                },
                function onScanSuccess(decodedText) {
                    document.getElementById('qrCodeInput').value = decodedText;
                    // document.getElementById('qrForm').submit();
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
    </script>
    {{-- js Thumbnail --}}
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
    {{-- end --}}
    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }

        .logo {
            width: 150px;
            margin-bottom: 10px;
        }

        .card {
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: #fff;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }

        /* Pastikan layout tengah */
        #reader-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 10px;
            border: 3px dashed #007bff;
            border-radius: 10px;
            background-color: #f8f9fa;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        /* Gaya scanner */
        #reader {
            width: 100%;
            height: 400px;
            border-radius: 10px;
        }

        /* Responsif */
        @media (max-width: 768px) {
            #reader-container {
                max-width: 300px;
            }

            #reader {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            #reader-container {
                max-width: 250px;
            }

            #reader {
                height: 250px;
            }
        }

        /* Styling tambahan */
        #qrCodeInput {
            border: 2px solid #007bff;
            border-radius: 8px;
            font-size: 1rem;
            padding: 10px;
        }

        .btn-success {
            background-color: #28a745;
            font-weight: bold;
            padding: 12px 20px;
        }


        @media (max-width: 768px) {
            .logo {
                width: 120px;
            }

            h5 {
                font-size: 18px;
            }

            .card {
                padding: 20px;
            }
        }

        #imagePreview {
            max-width: 100%;
            width: 200px;
            height: auto;
            margin-top: 10px;
            border: 3px solid #007bff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }

        #imagePreview:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            font-size: 1rem;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 20px rgba(0, 91, 187, 0.3);
        }
    </style>

    {{-- sweetalert --}}
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{!! session('success') !!}',
                width: '500px', // Ukuran popup medium
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
                width: '500px', // Ukuran popup medium
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
                        text: 'Silakan isi Validasi kembali.',
                        width: '450px', // Ukuran popup kecil
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
                width: 320px !important;
                /* Untuk tampilan mobile */
            }
        }
    </style>
    {{-- endsweetalert --}}

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
