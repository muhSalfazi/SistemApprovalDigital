<style>

</style>
<aside id="sidebar" class="sidebar hiden">
    <ul class="sidebar-nav" id="sidebar-nav">
        @if (Auth::check() && in_array(Auth::user()->role, ['admin', 'superAdmin']))
            <li class="sidebar-item">
                <a class="nav-link {{ request()->routeIs('dashboard', 'report.closed', 'report.delay') ? 'active' : 'collapsed' }}"
                    href="{{ route('dashboard') }}">
                    <span>
                        <i class="bi bi-columns-gap"></i>
                    </span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <li class="nav-heading">Master</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('parts.index', 'parts.create', 'parts.edit') ? 'active' : 'collapsed' }}"
                    href="{{ route('parts.index') }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Part</span>
                </a>
            </li>
            <li class="nav-heading">Order & Packing Management</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('orders.index', 'orders.create') ? 'active' : 'collapsed' }}"
                    href="{{ route('orders.index') }}">
                    <i class="bi bi-cart4"></i>
                    <span>Order</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.delivery') ? 'active' : 'collapsed' }}"
                    href="{{ route('admin.delivery') }}">
                    <i class="bi bi-truck"></i>
                    <span>Delivery</span>
                </a>
            </li>
            @if (Auth::check() && Auth::user()->role == 'superAdmin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.packing') ? 'active' : 'collapsed' }}"
                    href="{{ route('admin.packing') }}">
                    <i class="fas fa-box-open me-2"></i>
                    <span>Prepared</span>
                </a>
            </li>
            <li class="nav-heading">User Management</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.index', 'users.create') ? 'active' : 'collapsed' }}"
                    href="{{ route('users.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>User</span>
                </a>
            </li>
        @endif
            <li class="nav-heading">Auth</li>
            <li class="nav-item">
                <form id="logout-form" action="{{ route('logout-admin') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="nav-link collapsed" href="#" onclick="logoutConfirm()">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </a>
            </li>
        @endif
        @if (Auth::check() && Auth::user()->role == 'viewer')
            <li class="sidebar-item">
                <a class="nav-link {{ request()->routeIs('dashboard.viewer', 'report.closed', 'report.delay') ? 'active' : 'collapsed' }}"
                    href="{{ route('dashboard.viewer') }}">
                    <span>
                        <i class="bi bi-columns-gap"></i>
                    </span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <li class="nav-heading">Order & Packing Management</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('orders.index.viewer') ? 'active' : 'collapsed' }}"
                    href="{{ route('orders.index.viewer') }}">
                    <i class="bi bi-cart4"></i>
                    <span>Order</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('viewer.delivery') ? 'active' : 'collapsed' }}"
                    href="{{ route('viewer.delivery') }}">
                    <i class="bi bi-truck"></i>
                    <span>Delivery</span>
                </a>
            </li>
            <li class="nav-heading">Auth</li>
            <li class="nav-item">
                <form id="logout-form" action="{{ route('logout-viewer') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="nav-link collapsed" href="#" onclick="logoutConfirm()">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </a>
            </li>
        @endif
    </ul>
</aside>
<!-- End Sidebar-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function logoutConfirm() {
        Swal.fire({
            title: 'Anda yakin ingin logout?',
            text: "Anda akan keluar dari sesi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, logout!',
            cancelButtonText: 'Batal',
            showClass: {
                popup: 'animate__animated animate__jackInTheBox' // Animasi saat muncul
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp' // Animasi saat menghilang
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

<style>
    .sidebar-nav {
        padding: 0;
        margin: 0;
        list-style: none;
        /* Remove default list styles */
    }

    .nav-item {
        position: relative;
        /* Positioning for hover effect */
    }

    .nav-link {
        display: flex;
        /* Flexbox for horizontal alignment */
        align-items: center;
        /* Center align items */
        padding: 15px 20px;
        /* Padding for links */
        color: #aab8c9;
        /* Default text color */
        text-decoration: none;
        /* Remove underline */
        transition: background-color 0.3s, color 0.3s;
        /* Smooth transition */
    }

    .nav-heading {
        color: #ccf1fa;
        /* Heading color */
        padding: 15px 20px;
        /* Padding for headings */
        font-weight: bold;
        /* Bold font for headings */
    }

    .nav-link.active {
        background-color: #8393a4;
        /* Active link background */
        color: #ecf0f1;
        /* Active link text color */
    }

    .nav-link.active i {
        color: #ecf0f1;
        /* Active icon color */
    }

    .nav-link:hover {
        background-color: #adcbe9;
        /* Hover background color */
        color: #ecf0f1;
        /* Hover text color */
    }

    .nav-link i {
        margin-right: 10px;
        /* Spacing between icon and text */
        transition: color 0.3s;
        /* Smooth transition for icons */
    }

    .nav-link:hover i {
        color: #ecf0f1;
        /* Icon color on hover */
    }

    /* Optional: Add a slight shadow to the sidebar for depth */
    .sidebar {
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    }
</style>
