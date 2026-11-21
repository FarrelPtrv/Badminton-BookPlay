<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Badminton BookPlay - Sistem Booking Lapangan Online')</title>

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
            --primary-color: #0f766e;
            --primary-hover: #115e59;
            --primary-subtle: #ccfbf1;
            --secondary-color: #0284c7;
            --dark-color: #0f172a;
            --light-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        .badge-slot-available {
            background-color: #10b981;
            color: #ffffff;
        }

        .badge-slot-waiting {
            background-color: #f59e0b;
            color: #ffffff;
        }

        .badge-slot-booked {
            background-color: #ef4444;
            color: #ffffff;
        }

        .badge-slot-maintenance {
            background-color: #64748b;
            color: #ffffff;
        }

        footer {
            margin-top: auto;
            background-color: #0f172a;
            color: #94a3b8;
        }

        footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        footer a:hover {
            color: #38bdf8;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-teal text-white rounded-3 p-2" style="background-color: var(--primary-color);">
                    <i class="bi bi-dribbble fs-5"></i>
                </span>
                <span>Badminton <span class="text-dark">BookPlay</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active text-primary fw-bold' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('schedule.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('schedule.index') }}">
                            <i class="bi bi-calendar2-week me-1"></i> Cek Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courts.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('courts.index') }}">Daftar Lapangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bookings.create') ? 'active text-primary fw-bold' : '' }}" href="{{ route('bookings.create') }}">Booking Lapangan</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                <i class="bi bi-speedometer2 me-1"></i> Panel Admin
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 border" type="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 13px;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="fw-semibold small">{{ Str::words(Auth::user()->name, 2, '') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                    <div class="small text-muted">{{ Auth::user()->email }}</div>
                                    <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-primary' }} mt-1">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('bookings.index') }}">
                                        <i class="bi bi-receipt me-2 text-primary"></i> Riwayat Booking
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                        <i class="bi bi-person-gear me-2 text-secondary"></i> Pengaturan Profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-medium">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-3 fw-medium">Daftar Akun</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications -->
    <div class="container mt-3">
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

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Terjadi kesalahan input:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 pb-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-teal text-white rounded-3 p-2" style="background-color: var(--primary-color);">
                            <i class="bi bi-dribbble fs-5"></i>
                        </span>
                        <h5 class="text-white fw-bold mb-0">Badminton BookPlay</h5>
                    </div>
                    <p class="small text-secondary mb-3">
                        Gedung Olahraga Badminton modern dengan karpet standar turnamen internasional BWF, pencahayaan bebas silau, dan fasilitas lengkap untuk kenyamanan bermain Anda.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-geo-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Navigasi</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}">Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('schedule.index') }}">Cek Jadwal Lapangan</a></li>
                        <li class="mb-2"><a href="{{ route('courts.index') }}">Daftar Lapangan</a></li>
                        <li class="mb-2"><a href="{{ route('bookings.create') }}">Form Pemesanan</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Pelanggan</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('login') }}">Masuk Akun</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Daftar Baru</a></li>
                        <li class="mb-2"><a href="{{ route('bookings.index') }}">Cek Tiket / Booking</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Jam Operasional & Kontak</h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2"><i class="bi bi-clock me-2 text-warning"></i> Setiap Hari: 08:00 - 23:00 WIB</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2 text-success"></i> WhatsApp: 0812-3456-7890</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2 text-danger"></i> Jl. Olahraga Juara No. 88, Kota Anda</li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-secondary">
                <div>&copy; {{ date('Y') }} Badminton BookPlay. Hak Cipta Dilindungi.</div>
                <div>Sistem Informasi & Booking Lapangan Badminton</div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>

    @stack('scripts')
</body>
</html>
