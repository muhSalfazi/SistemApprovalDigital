<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Default Title')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/icon-kbi.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    @vite([
        'resources/assets/css/style.css',
         'resources/assets/vendor/bootstrap/css/bootstrap.min.css'
         ])

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
    /* Header tetap di atas */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 500;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        left: 0;
        top: 60px;
        width: 300px;
        height: 150vh;
        transition: transform 0.3s ease;
        z-index: 400;
        /* Lebih rendah dari header */
    }

    /* Sidebar tersembunyi */
    .toggle-sidebar .sidebar {
        transform: translateX(-100%);
    }

    /* Konten utama bergeser ketika sidebar terbuka */
    .main-content {
        margin-top: 60px;
        /* Pastikan konten turun sesuai tinggi header */
        margin-left: 0;
        transition: margin-left 0.3s ease;
    }

    body.sidebar-expanded .main-content {
        margin-left: 250px;
        /* Bergeser ketika sidebar terbuka */
    }

    .toggle-sidebar .sidebar {
        transform: translateX(-100%);
        /* Sidebar keluar layar */
    }

    body.toggle-sidebar {
        margin-left: 0;
        /* Konten tetap di posisi awal */
    }

    body.sidebar-expanded {
        margin-left: 250px;
        /* Konten bergeser saat sidebar terbuka */
    }

    /* Custom untuk layar besar */
    @media (min-width: 1920px) {
        .swal2-popup {
            font-size: 1.5rem !important;
            /* Perbesar font untuk layar besar */
        }

        .swal2-title {
            font-size: 2rem !important;
            /* Perbesar judul */
        }

        .swal2-content {
            font-size: 1.5rem !important;
            /* Perbesar konten */
        }

        .swal2-confirm {
            font-size: 1.25rem !important;
            /* Perbesar tombol */
        }
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
        .swal2-popup {
            font-size: 0.875rem !important;
        }
    }

    .table-responsive {
        overflow-x: auto;
        margin-top: 20px;
        /* Menambahkan margin atas untuk estetika */
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        /* Memastikan tabel mengisi ruang */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* Menambahkan bayangan pada tabel */
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 15px;
        /* Menambahkan padding untuk ruang di dalam sel */
        text-align: center;
        /* Memastikan teks rata tengah */
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        /* Efek transisi yang lebih halus */
    }

    .table tr {
        transition: background-color 0.3s;
        /* Menambahkan transisi untuk baris */
    }

    .table tr:hover {
        background-color: #007bff;
        /* Warna latar belakang saat hover */
        color: white;
        /* Mengubah warna teks menjadi putih saat hover */
        transform: scale(1.02);
        /* Memberikan efek zoom sedikit saat hover */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Menambahkan bayangan saat hover */
    }

    .table th {
        background-color: #f97b61;
        /* Warna gelap untuk header */
        color: white;
        /* Warna teks header */
        font-weight: bold;
        text-transform: uppercase;
        /* Mengubah teks header menjadi huruf kapital */
    }

    /* Gaya untuk tabel striping */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2;
        /* Warna striping */
    }

    .loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.998);
        /* Optional: semi-transparent background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        /* Ensure it is on top */
        opacity: 1;
        /* Make sure the loader is visible */
        transition: opacity 0.5s ease, visibility 0.5s ease;
        /* Smooth transition when fading out */
    }

    .loading.hidden {
        opacity: 0;
        visibility: hidden;
        /* Hide visibility for scroll */
    }

    /* Loader Animation */
    .pl {
        width: 6em;
        height: 6em;
    }

    /* Loader Animation */
    .pl {
        width: 6em;
        height: 6em;
    }

    .pl__ring {
        animation: ringA 2s linear infinite;
    }

    .pl__ring--a {
        stroke: #f42f25;
    }

    .pl__ring--b {
        animation-name: ringB;
        stroke: #f49725;
    }

    .pl__ring--c {
        animation-name: ringC;
        stroke: #255ff4;
    }

    .pl__ring--d {
        animation-name: ringD;
        stroke: #f42582;
    }

    /* Animations */
    @keyframes ringA {

        from,
        4% {
            stroke-dasharray: 0 660;
            stroke-width: 20;
            stroke-dashoffset: -330;
        }

        12% {
            stroke-dasharray: 60 600;
            stroke-width: 30;
            stroke-dashoffset: -335;
        }

        32% {
            stroke-dasharray: 60 600;
            stroke-width: 30;
            stroke-dashoffset: -595;
        }

        40%,
        54% {
            stroke-dasharray: 0 660;
            stroke-width: 20;
            stroke-dashoffset: -660;
        }

        62% {
            stroke-dasharray: 60 600;
            stroke-width: 30;
            stroke-dashoffset: -665;
        }

        82% {
            stroke-dasharray: 60 600;
            stroke-width: 30;
            stroke-dashoffset: -925;
        }

        90%,
        to {
            stroke-dasharray: 0 660;
            stroke-width: 20;
            stroke-dashoffset: -990;
        }
    }

    @keyframes ringB {

        from,
        12% {
            stroke-dasharray: 0 220;
            stroke-width: 20;
            stroke-dashoffset: -110;
        }

        20% {
            stroke-dasharray: 20 200;
            stroke-width: 30;
            stroke-dashoffset: -115;
        }

        40% {
            stroke-dasharray: 20 200;
            stroke-width: 30;
            stroke-dashoffset: -195;
        }

        48%,
        62% {
            stroke-dasharray: 0 220;
            stroke-width: 20;
            stroke-dashoffset: -220;
        }

        70% {
            stroke-dasharray: 20 200;
            stroke-width: 30;
            stroke-dashoffset: -225;
        }

        90% {
            stroke-dasharray: 20 200;
            stroke-width: 30;
            stroke-dashoffset: -305;
        }

        98%,
        to {
            stroke-dasharray: 0 220;
            stroke-width: 20;
            stroke-dashoffset: -330;
        }
    }

    @keyframes ringC {
        from {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: 0;
        }

        8% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -5;
        }

        28% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -175;
        }

        36%,
        58% {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: -220;
        }

        66% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -225;
        }

        86% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -395;
        }

        94%,
        to {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: -440;
        }
    }

    @keyframes ringD {

        from,
        8% {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: 0;
        }

        16% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -5;
        }

        36% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -175;
        }

        44%,
        50% {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: -220;
        }

        58% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -225;
        }

        78% {
            stroke-dasharray: 40 400;
            stroke-width: 30;
            stroke-dashoffset: -395;
        }

        86%,
        to {
            stroke-dasharray: 0 440;
            stroke-width: 20;
            stroke-dashoffset: -440;
        }
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Hide loader after page load
    $(window).on('load', function() {
        $('.loading').addClass('hidden'); // Add hidden class to fade out
        setTimeout(() => {
            $('.loading').remove(); // Remove loader from DOM after fade out
        }, 500); // Match this time with the CSS transition duration
    });
</script>

<body class="toggle-sidebar">
    <!-- Loader -->
    <div class="loading">
        <svg class="pl" width="240" height="240" viewBox="0 0 240 240">
            <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none" stroke="#000"
                stroke-width="20" stroke-dasharray="0 660" stroke-dashoffset="-330" stroke-linecap="round"></circle>
            <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none" stroke="#000"
                stroke-width="20" stroke-dasharray="0 220" stroke-dashoffset="-110" stroke-linecap="round"></circle>
            <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none" stroke="#000"
                stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
            <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none" stroke="#000"
                stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
        </svg>
    </div>
    @include('layouts.header')

    @include('layouts.sidebar')
    <main id="main" class="main">
        @yield('content')
    </main><!-- End #main -->

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>


    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <script>
        function confirmDelete(partId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                width: '50%', // Atur lebar
                showClass: {
                    popup: 'animate__animated animate__fadeInDown' // Animasi saat muncul
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp' // Animasi saat menghilang
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + partId).submit();
                }
            });
        }
    </script>
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                width: '50%', // Atur lebar
                showClass: {
                    popup: 'animate__animated animate__jackInTheBox', // Animasi saat popup muncul
                    icon: 'animate__animated animate__shakeY' // Animasi pada ikon peringatan
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp' // Animasi saat popup menghilang
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + userId).submit();
                }
            });
        }
    </script>

    <script>
        setInterval(function() {
            fetch('/keep-session-alive').then(response => {
                if (response.ok) {
                    console.log('Session refreshed');
                }
            });
        }, 600000); // Refresh setiap 10 menit (600000 ms)
    </script>



</body>

</html>