<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <div class="logo d-flex align-items-center">
            {{-- <img src="assets/img/icon-kbi.png" alt=""> --}}
            <span class="d-none d-lg-block"><strong>Kyoraku Blowmolding Indonesia</strong></span>
        </div>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <!-- Digital Clock -->
            <div id="digital-clock" class="ms-auto me-4"></div>
            <!-- End Digital Clock -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->
<style>
    /* Header Style */
    .header {
        background-color: #ffffff;
        /* Warna latar belakang header */

        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        /* Efek bayangan untuk tampilan lebih elegan */
        padding: 10px 20px;
        /* Padding untuk memberi ruang di dalam header */
        transition: background-color 0.3s ease;
        /* Transisi halus untuk perubahan warna */
    }

    .header .logo img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        margin-right: 10px;
        /* Beri sedikit jarak antara logo dan teks */
        transition: transform 0.3s ease;
        /* Efek transisi saat hover */
    }

    .header .logo span {
        color: rgb(197, 4, 4);
        /* Warna teks untuk nama masjid */
        font-weight: 600;
        /* Tebal font untuk teks */
        font-size: 18px;
        /* Ukuran font */

    }

    .header .logo:hover img {
        transform: scale(1.1);
        /* Efek perbesaran pada logo saat hover */
    }

    /* Default style */
    .toggle-sidebar-btn {
        transition: transform 0.3s ease;
        /* Transisi untuk animasi rotasi */
    }

    /* Rotated state */
    .sidebar-collapsed .toggle-sidebar-btn {
        transform: rotate(180deg);
        /* Rotasi 180 derajat */
    }


    .header-nav ul {
        display: flex;
        /* Untuk menyejajarkan item dalam nav */
        gap: 15px;
        /* Jarak antar item dalam nav */
    }

    .header-nav ul li a {
        color: #7c7f80;
        /* Warna teks nav */
        font-size: 16px;
        /* Ukuran font nav */
        font-weight: 500;
        /* Tebal font nav */
        transition: color 0.3s ease;
        /* Transisi halus untuk perubahan warna */
    }

    .header-nav ul li a:hover {
        color: #e74c3c;
        /* Warna teks saat hover */
    }

    #digital-clock {
        color: #7c7f80;
        /* Warna teks */
        font-size: 18px;
        /* Ukuran font */
        font-weight: 600;
        /* Tebal font */
        letter-spacing: 1px;
        /* Jarak antar huruf */
        font-family: 'Courier New', Courier, monospace;
        /* Font jam digital */
    }
</style>

<script>
    function updateClock() {
        const clock = document.getElementById('digital-clock');
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clock.textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateClock, 1000); // Update setiap detik
    updateClock(); // Inisialisasi
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.querySelector('.toggle-sidebar-btn');
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;

        toggleButton.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
        });
    });
</script>
