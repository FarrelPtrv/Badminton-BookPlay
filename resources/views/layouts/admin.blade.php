<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Badminton BookPlay')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --admin-sidebar-bg: #0f172a;
            --admin-sidebar-hover: #1e293b;
            --admin-sidebar-active: #0f766e;
            --admin-primary: #0f766e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background-color: var(--admin-sidebar-bg);
            min-height: 100vh;
            color: #94a3b8;
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .nav-link-admin {
            color: #cbd5e1;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.5rem;
            margin: 0.2rem 0.75rem;
            font-weight: 500;
            font-size: 0.925rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-admin:hover {
            color: #ffffff;
            background-color: var(--admin-sidebar-hover);
        }

        .nav-link-admin.active {
            color: #ffffff;
            background-color: var(--admin-sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.4);
        }

        .card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .btn-primary:hover {
            background-color: #115e59;
            border-color: #115e59;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                margin-left: -260px;
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar d-flex flex-column" id="adminSidebar">
        <div class="px-4 py-4 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                <span class="bg-teal text-white rounded-3 p-2" style="background-color: var(--admin-primary);">
                    <i class="bi bi-dribbble fs-5"></i>
                </span>
                <div>
                    <div class="fw-bold fs-6 text-white leading-tight">Badminton BookPlay</div>
                    <small class="text-secondary" style="font-size: 11px;">Admin Control Panel</small>
                </div>
            </a>
            <button class="btn btn-sm btn-dark text-white d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="py-3 flex-grow-1">
            <div class="px-3 pb-2 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Menu Utama</div>

            <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 fs-5"></i> Dashboard
            </a>

            <a href="{{ route('admin.courts.index') }}" class="nav-link-admin {{ request()->routeIs('admin.courts.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap-fill fs-5"></i> Data Lapangan
            </a>

            <div class="px-3 pt-3 pb-2 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Transaksi & Jadwal</div>

            <a href="{{ route('admin.bookings.index') }}" class="nav-link-admin {{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff fs-5"></i> Semua Booking
            </a>

            <a href="{{ route('admin.bookings.walkin') }}" class="nav-link-admin {{ request()->routeIs('admin.bookings.walkin*') ? 'active' : '' }}">
                <i class="bi bi-person-plus-fill fs-5"></i> Booking Walk-in
            </a>

            <a href="{{ route('admin.bookings.block') }}" class="nav-link-admin {{ request()->routeIs('admin.bookings.block*') ? 'active' : '' }}">
                <i class="bi bi-slash-circle-fill fs-5"></i> Blokir Jadwal
            </a>

            <div class="px-3 pt-3 pb-2 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Laporan & Analitik</div>

            <a href="{{ route('admin.reports.index') }}" class="nav-link-admin {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill fs-5"></i> Laporan Keuangan
            </a>

            <div class="px-3 pt-3 pb-2 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Lainnya</div>

            <a href="{{ route('home') }}" class="nav-link-admin" target="_blank">
                <i class="bi bi-box-arrow-up-right fs-5"></i> Lihat Website
            </a>
        </div>

        <div class="p-3 border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2 mb-3 px-2">
                <div class="bg-teal text-white rounded-circle d-flex align-items-center justify-content-center" style="background-color: var(--admin-primary); width: 36px; height: 36px;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white text-truncate fw-semibold small">{{ Auth::user()->name }}</div>
                    <div class="text-secondary text-truncate" style="font-size: 11px;">Administrator</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm rounded-pill d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between sticky-top">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small">
                    <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Sistem Online
                </span>
                <span class="text-secondary small d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}
                </span>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Ada beberapa kesalahan input:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Content Body -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>

        <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
            &copy; {{ date('Y') }} Badminton BookPlay - Panel Pengelola Lapangan
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
