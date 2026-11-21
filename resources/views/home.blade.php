@extends('layouts.app')

@section('title', 'Badminton BookPlay - Booking Lapangan Badminton Cepat & Praktis')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-white border-bottom position-relative overflow-hidden">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 bg-success-subtle text-success px-3 py-1 rounded-pill small fw-semibold mb-3">
                    <i class="bi bi-patch-check-fill"></i> Standar Internasional BWF • Sistem Booking Real-Time
                </div>
                <h1 class="display-4 fw-extrabold text-dark tracking-tight mb-3" style="line-height: 1.15;">
                    Sewa Lapangan Badminton <span style="color: var(--primary-color);">Bebas Antre & Tanpa Jadwal Bentrok</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    Main badminton lebih nyaman di GOR terbaik dengan lantai karpet vinyl standar turnamen, pencahayaan LED anti-silau, dan jadwal ketersediaan yang bisa dipantau secara langsung kapan saja.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('schedule.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="bi bi-calendar2-week me-2"></i> Cek Jadwal Real-Time
                    </a>
                    <a href="{{ route('courts.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                        Lihat Lapangan & Tarif
                    </a>
                </div>

                <div class="row g-3 mt-4 pt-3 border-top">
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-dark">4 Lapangan</div>
                        <div class="small text-muted">Vinyl & Parquet</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-dark">08:00 - 23:00</div>
                        <div class="small text-muted">Buka Setiap Hari</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-dark">100% Anti Bentrok</div>
                        <div class="small text-muted">Server Auto-Lock</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="position-relative">
                    <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                        <img src="https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=800&q=80" alt="Lapangan Badminton" class="img-fluid" style="height: 380px; object-fit: cover;">
                        <div class="card-img-overlay d-flex flex-column justify-content-end p-4 text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(15,23,42,0.95) 100%);">
                            <span class="badge bg-success w-fit mb-2">Buka Hari Ini</span>
                            <h5 class="fw-bold mb-1">Badminton BookPlay Juara</h5>
                            <p class="small text-light mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Jl. Olahraga Juara No. 88, Kota Anda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Availability Checker Bar -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <form action="{{ route('schedule.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> Pilih Tanggal Main</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-muted mb-1"><i class="bi bi-grid me-1"></i> Pilih Lapangan</label>
                    <select name="court_id" class="form-select">
                        <option value="">Semua Lapangan</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}">{{ $court->name }} ({{ $court->formatted_price }}/jam)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-search me-1"></i> Cek Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Courts Showcase -->
<section class="py-5">
    <div class="container py-lg-3">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small" style="letter-spacing: 1px;">Katalog Lapangan</span>
                <h2 class="fw-bold text-dark mt-1">Pilihan Lapangan Berkualitas</h2>
            </div>
            <a href="{{ route('courts.index') }}" class="btn btn-link text-decoration-none fw-semibold">
                Lihat Semua Lapangan <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($courts as $court)
                <div class="col-md-6 col-lg-3">
                    <div class="card card-hover h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column">
                        <div class="position-relative">
                            <img src="{{ $court->image_url }}" class="card-img-top" alt="{{ $court->name }}" style="height: 190px; object-fit: cover;">
                            <span class="badge position-absolute top-0 end-0 m-3 {{ $court->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $court->status === 'available' ? 'Tersedia' : 'Maintenance' }}
                            </span>
                            <span class="badge position-absolute bottom-0 start-0 m-3 bg-dark bg-opacity-75">
                                {{ $court->floor_type }}
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <h5 class="card-title fw-bold text-dark mb-1">{{ $court->name }}</h5>
                            <p class="card-text text-muted small flex-grow-1 mb-3">
                                {{ Str::limit($court->description, 85) }}
                            </p>
                            <div class="pt-3 border-top mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Tarif Sewa:</span>
                                    <span class="fw-bold text-primary fs-5">{{ $court->formatted_price }}<small class="text-muted fs-6 fw-normal">/jam</small></span>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" class="btn btn-primary btn-sm rounded-pill py-2 fw-semibold">
                                        <i class="bi bi-cart-plus me-1"></i> Booking Lapangan
                                    </a>
                                    <a href="{{ route('courts.show', $court->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill py-1 small">
                                        Detail Lapangan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-lg-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase small" style="letter-spacing: 1px;">Kenyamanan Maksimal</span>
            <h2 class="fw-bold text-dark mt-1">Fasilitas Lengkap GOR</h2>
            <p class="text-muted">Kami menyediakan berbagai fasilitas pendukung untuk memastikan pengalaman bermain bulutangkis Anda menyenangkan.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-primary shadow-sm mb-3">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Karpet BWF</h6>
                    <small class="text-muted">Grip prima & anti selip</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-warning shadow-sm mb-3">
                        <i class="bi bi-lightbulb-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Lampu LED Sorot</h6>
                    <small class="text-muted">Anti silau pandangan</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-success shadow-sm mb-3">
                        <i class="bi bi-cup-hot-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Kantin & Minuman</h6>
                    <small class="text-muted">Snack & isotonic dingin</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-info shadow-sm mb-3">
                        <i class="bi bi-droplet-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Kamar Mandi & Bilas</h6>
                    <small class="text-muted">Bersih & shower lancar</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-primary shadow-sm mb-3">
                        <i class="bi bi-p-square-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Parkir Luas</h6>
                    <small class="text-muted">Aman mobil & motor</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-4 bg-light h-100">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-secondary shadow-sm mb-3">
                        <i class="bi bi-moon-stars-fill fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Musholla Nyaman</h6>
                    <small class="text-muted">Tempat wudhu & sajadah</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How to Book Guide -->
<section class="py-5">
    <div class="container py-lg-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase small" style="letter-spacing: 1px;">Panduan Booking</span>
            <h2 class="fw-bold text-dark mt-1">4 Langkah Praktis Pemesanan</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <div class="badge bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                        1
                    </div>
                    <h5 class="fw-bold mb-2">Cek Jadwal</h5>
                    <p class="text-muted small mb-0">Pilih tanggal dan lapangan, cek slot jam operasional yang bertanda hijau (tersedia).</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <div class="badge bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                        2
                    </div>
                    <h5 class="fw-bold mb-2">Pilih Durasi</h5>
                    <p class="text-muted small mb-0">Tentukan jam mulai dan durasi main. Sistem otomatis menghitung total harga.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <div class="badge bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                        3
                    </div>
                    <h5 class="fw-bold mb-2">Transfer & Bukti</h5>
                    <p class="text-muted small mb-0">Lakukan pembayaran via Transfer Bank / QRIS dan upload foto bukti transfer Anda.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                    <div class="badge bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                        4
                    </div>
                    <h5 class="fw-bold mb-2">Dapat E-Ticket</h5>
                    <p class="text-muted small mb-0">Setelah admin memverifikasi, tunjukkan kode booking / E-Ticket saat tiba di GOR.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-teal text-white" style="background: linear-gradient(135deg, #0f766e 0%, #064e3b 100%);">
    <div class="container text-center py-4">
        <h2 class="display-6 fw-bold mb-3">Siap Bermain Badminton Hari Ini?</h2>
        <p class="lead text-light mb-4" style="max-width: 600px; margin-inline: auto;">
            Slot lapangan cepat terisi terutama di jam sore dan malam hari. Amankan jadwal lapangan favorit Anda sekarang!
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('bookings.create') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold text-dark shadow">
                <i class="bi bi-cart-plus me-1"></i> Pesan Lapangan Sekarang
            </a>
            <a href="{{ route('schedule.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                Lihat Jadwal
            </a>
        </div>
    </div>
</section>
@endsection
