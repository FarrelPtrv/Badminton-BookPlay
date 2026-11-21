@extends('layouts.app')

@section('title', 'Daftar Lapangan Badminton - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Daftar Lapangan GOR</h2>
            <p class="text-muted mb-0">Pilihan lapangan dengan berbagai spesifikasi lantai standar resmi</p>
        </div>
        <a href="{{ route('schedule.index') }}" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-calendar3 me-1"></i> Cek Jadwal Ketersediaan
        </a>
    </div>

    <div class="row g-4">
        @forelse($courts as $court)
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column">
                    <div class="position-relative">
                        <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                        <span class="badge position-absolute top-0 end-0 m-3 {{ $court->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $court->status === 'available' ? 'Tersedia' : 'Maintenance' }}
                        </span>
                        <span class="badge position-absolute bottom-0 start-0 m-3 bg-dark bg-opacity-75">
                            <i class="bi bi-layers-fill me-1"></i> {{ $court->floor_type }}
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        <h4 class="card-title fw-bold text-dark mb-2">{{ $court->name }}</h4>
                        <p class="card-text text-muted small flex-grow-1 mb-3">
                            {{ $court->description ?: 'Lapangan bulutangkis dengan penerangan profesional dan fasilitas lengkap.' }}
                        </p>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Tarif Sewa:</span>
                                <span class="fw-bold text-primary fs-5">{{ $court->formatted_price }}<small class="text-muted fw-normal" style="font-size: 13px;"> / jam</small></span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-auto">
                            @if($court->status === 'available')
                                <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                    <i class="bi bi-cart-plus me-1"></i> Booking Sekarang
                                </a>
                            @else
                                <button class="btn btn-secondary rounded-pill py-2 fw-semibold" disabled>
                                    <i class="bi bi-cone-striped me-1"></i> Sedang Maintenance
                                </button>
                            @endif
                            <a href="{{ route('courts.show', $court->id) }}" class="btn btn-outline-secondary rounded-pill py-1 small">
                                Lihat Rincian Lapangan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">Belum ada data lapangan yang terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
