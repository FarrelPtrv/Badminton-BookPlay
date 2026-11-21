@extends('layouts.admin')

@section('title', 'Kelola Transaksi Booking - Badminton BookPlay')
@section('page_title', 'Semua Transaksi Booking')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Transaksi Pemesanan</h4>
        <p class="text-muted small mb-0">Kelola dan verifikasi pembayaran pemesanan lapangan oleh pelanggan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.walkin') }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Booking Walk-in
        </a>
        <a href="{{ route('admin.bookings.block') }}" class="btn btn-outline-danger rounded-pill px-3">
            <i class="bi bi-slash-circle me-1"></i> Blokir Jadwal
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm bg-white p-3 mb-4">
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode booking / nama..." value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Semua Status --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="waiting_verification" {{ request('status') === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi (Lunas)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="court_id" class="form-select form-select-sm">
                <option value="">-- Semua Lapangan --</option>
                @foreach($courts as $c)
                    <option value="{{ $c->id }}" {{ request('court_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-dark btn-sm w-100">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Bookings Table Card -->
<div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th class="ps-4">Kode Booking</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Jadwal Main</th>
                    <th>Biaya & Metode</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td class="ps-4">
                            <span class="font-monospace fw-bold text-dark">{{ $b->booking_code }}</span>
                            <div class="text-muted small" style="font-size: 11px;">{{ $b->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->customer_display_name }}</div>
                            <div class="small">
                                @if($b->customer_display_phone && $b->customer_display_phone !== '-')
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $b->customer_display_phone)) }}" target="_blank" class="text-success text-decoration-none">
                                        <i class="bi bi-whatsapp me-1"></i> {{ $b->customer_display_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->court ? $b->court->name : '-' }}</div>
                            <span class="badge bg-light text-dark border small">{{ $b->court ? $b->court->floor_type : '' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $b->booking_date->translatedFormat('d M Y') }}</div>
                            <div class="text-muted small">{{ $b->formatted_time_range }} ({{ $b->duration }} Jam)</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $b->formatted_total_price }}</div>
                            <div class="text-muted small">{{ $b->payment ? $b->payment->payment_method : '-' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $b->status_badge_class }} px-3 py-2 rounded-pill small">
                                {{ $b->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                Detail / Verifikasi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data booking yang sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="p-4 border-top">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
