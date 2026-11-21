@extends('layouts.app')

@section('title', $court->name . ' - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courts.index') }}" class="text-decoration-none">Daftar Lapangan</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $court->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="img-fluid w-100" style="max-height: 420px; object-fit: cover;">
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold mb-3">Deskripsi & Spesifikasi Lapangan</h4>
                <p class="text-secondary leading-relaxed">
                    {{ $court->description ?: 'Lapangan bulutangkis berkualitas tinggi dengan garis lapangan presisi dan standar ukuran resmi PBSI / BWF.' }}
                </p>

                <h5 class="fw-bold mt-4 mb-3">Fasilitas Pada Lapangan Ini:</h5>
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2 text-muted small p-2 bg-light rounded-3">
                            <i class="bi bi-check-circle-fill text-success"></i> Jenis Lantai: <strong>{{ $court->floor_type }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2 text-muted small p-2 bg-light rounded-3">
                            <i class="bi bi-check-circle-fill text-success"></i> Standar Garis Lapangan BWF Resmi
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2 text-muted small p-2 bg-light rounded-3">
                            <i class="bi bi-check-circle-fill text-success"></i> Lampu Sorot LED Anti-Silau
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2 text-muted small p-2 bg-light rounded-3">
                            <i class="bi bi-check-circle-fill text-success"></i> Kursi Wasit & Bangku Istirahat Pemain
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge {{ $court->status === 'available' ? 'bg-success' : 'bg-secondary' }} px-3 py-2 rounded-pill">
                        {{ $court->status === 'available' ? 'Tersedia untuk Booking' : 'Masa Perawatan (Maintenance)' }}
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ $court->floor_type }}</span>
                </div>

                <h3 class="fw-bold text-dark mb-1">{{ $court->name }}</h3>
                <div class="my-3 p-3 bg-light rounded-3">
                    <div class="small text-muted mb-1">Tarif Sewa Per Jam:</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="display-6 fw-bold text-primary">{{ $court->formatted_price }}</span>
                        <span class="text-muted fw-semibold">/ jam</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="bi bi-clock-fill text-warning"></i> Jam Operasional: <strong>08:00 - 23:00 WIB</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <i class="bi bi-shield-check text-primary"></i> Anti Double-Booking: <strong>Jaminan Slot Pasti</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="bi bi-credit-card text-success"></i> Metode Bayar: <strong>BCA, Mandiri, QRIS</strong>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    @if($court->status === 'available')
                        <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" class="btn btn-primary btn-lg rounded-pill fw-bold">
                            <i class="bi bi-cart-plus me-1"></i> Pesan Lapangan Ini
                        </a>
                        <a href="{{ route('schedule.index', ['court_id' => $court->id]) }}" class="btn btn-outline-secondary rounded-pill py-2">
                            <i class="bi bi-calendar-week me-1"></i> Cek Jadwal Lapangan Ini
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg rounded-pill fw-bold" disabled>
                            Lapangan Sedang Maintenance
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
