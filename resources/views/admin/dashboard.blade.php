@extends('layouts.admin')

@section('title', 'Admin Dashboard - Badminton BookPlay')
@section('page_title', 'Ringkasan Dashboard Pengelola')

@section('content')
<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Today's Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Pendapatan Hari Ini</span>
                <div class="p-2 rounded-3 bg-success-subtle text-success">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
            <span class="small text-muted"><i class="bi bi-calendar-check me-1 text-success"></i> Transaksi terkonfirmasi hari ini</span>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Pendapatan Bulan Ini</span>
                <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
            <span class="small text-muted"><i class="bi bi-calendar3 me-1 text-primary"></i> Bulan {{ date('F Y') }}</span>
        </div>
    </div>

    <!-- Active Bookings -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Booking Aktif</span>
                <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $activeBookingsCount }}</h3>
            <span class="small text-muted">Jadwal berjalan & menunggu bayar</span>
        </div>
    </div>

    <!-- Registered Customers -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Pelanggan</span>
                <div class="p-2 rounded-3 bg-info-subtle text-info">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $totalCustomers }}</h3>
            <span class="small text-muted">Pengguna terdaftar di sistem</span>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="card border-0 shadow-sm p-4 mb-4 bg-white">
    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Aksi Cepat Pengelola:</h6>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.bookings.walkin') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-person-plus-fill me-1"></i> Input Booking Walk-in
        </a>
        <a href="{{ route('admin.bookings.block') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-slash-circle-fill me-1"></i> Blokir Jadwal / Maintenance
        </a>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Tambah Lapangan Baru
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-bar-chart-line-fill me-1"></i> Rekap Laporan Keuangan
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Waiting Verification Alert Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header bg-transparent py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <h6 class="fw-bold mb-0 text-dark">Perlu Verifikasi Pembayaran ({{ $waitingVerifications->count() }})</h6>
                </div>
                <a href="{{ route('admin.bookings.index', ['status' => 'waiting_verification']) }}" class="small text-decoration-none">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-4">Kode / Pemesan</th>
                            <th>Jadwal Main</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waitingVerifications as $wb)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold font-monospace">{{ $wb->booking_code }}</div>
                                    <div class="text-muted small">{{ $wb->customer_display_name }} ({{ $wb->customer_display_phone }})</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $wb->court ? $wb->court->name : '-' }}</div>
                                    <div class="text-muted small">{{ $wb->booking_date->format('d/m/Y') }} &bull; {{ $wb->formatted_time_range }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $wb->formatted_total_price }}</div>
                                    <div class="text-muted small">{{ $wb->payment ? $wb->payment->payment_method : 'Transfer' }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.bookings.show', $wb->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
                                        Periksa Bukti
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">
                                    <i class="bi bi-check2-all fs-3 text-success d-block mb-1"></i>
                                    Tidak ada pembayaran yang menunggu verifikasi saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Courts Overview Today -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header bg-transparent py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-fill text-primary me-2"></i> Okupansi Lapangan Hari Ini</h6>
            </div>
            <div class="card-body p-4">
                @foreach($courts as $c)
                    <div class="mb-3 pb-3 border-bottom last-border-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">{{ $c->name }}</span>
                            <span class="badge {{ $c->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $c->status === 'available' ? 'Tersedia' : 'Maintenance' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Jenis: {{ $c->floor_type }}</span>
                            <span><strong>{{ $c->bookings_count }}</strong> Jadwal Terisi Hari Ini</span>
                        </div>
                    </div>
                @endforeach
                <a href="{{ route('admin.courts.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill mt-2">
                    Kelola Semua Lapangan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-transparent py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i> Transaksi Booking Terbaru</h6>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            Lihat Semua Pesanan
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th class="ps-4">Kode Booking</th>
                    <th>Pelanggan</th>
                    <th>Lapangan</th>
                    <th>Jadwal Main</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $rb)
                    <tr>
                        <td class="ps-4">
                            <span class="font-monospace fw-bold text-dark">{{ $rb->booking_code }}</span>
                            <div class="text-muted small" style="font-size: 11px;">{{ $rb->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $rb->customer_display_name }}</div>
                            <div class="text-muted small">{{ $rb->customer_display_phone }}</div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $rb->court ? $rb->court->name : '-' }}</span>
                        </td>
                        <td>
                            <div class="text-dark">{{ $rb->booking_date->format('d/m/Y') }}</div>
                            <div class="text-muted small">{{ $rb->formatted_time_range }} ({{ $rb->duration }} Jam)</div>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">{{ $rb->formatted_total_price }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $rb->status_badge_class }} px-3 py-2 rounded-pill small">
                                {{ $rb->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.bookings.show', $rb->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
