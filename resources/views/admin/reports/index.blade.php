@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Transaksi - Badminton BookPlay')
@section('page_title', 'Rekap Laporan Keuangan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Laporan Transaksi & Pendapatan</h4>
        <p class="text-muted small mb-0">Rekapitulasi keuangan penyewaan lapangan berdasarkan periode tanggal</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.print', request()->all()) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
        </a>
        <a href="{{ route('admin.reports.export_csv', request()->all()) }}" class="btn btn-success rounded-pill px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> Unduh CSV / Excel
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm bg-white p-4 mb-4">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold small text-muted">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small text-muted">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold small text-muted">Status Pembayaran</label>
            <select name="status" class="form-select form-select-sm">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi (Lunas)</option>
                <option value="waiting_verification" {{ $status === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold small text-muted">Lapangan</label>
            <select name="court_id" class="form-select form-select-sm">
                <option value="all" {{ $courtId === 'all' ? 'selected' : '' }}>Semua Lapangan</option>
                @foreach($courts as $c)
                    <option value="{{ $c->id }}" {{ $courtId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan (Lunas)</span>
            <h3 class="fw-bold text-success mb-0 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <span class="small text-muted mt-1 d-block">{{ $confirmedCount }} transaksi berhasil</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Total Pemesanan</span>
            <h3 class="fw-bold text-dark mb-0 mt-2">{{ $totalBookings }}</h3>
            <span class="small text-muted mt-1 d-block">Keseluruhan entri periode ini</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Menunggu Verifikasi</span>
            <h3 class="fw-bold text-warning mb-0 mt-2">{{ $waitingCount }}</h3>
            <span class="small text-muted mt-1 d-block">Memerlukan pengecekan admin</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Pesanan Dibatalkan</span>
            <h3 class="fw-bold text-danger mb-0 mt-2">{{ $cancelledCount }}</h3>
            <span class="small text-muted mt-1 d-block">Expired / ditolak pengelola</span>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Kode Booking</th>
                    <th>Tanggal Main</th>
                    <th>Jam Main</th>
                    <th>Lapangan</th>
                    <th>Pelanggan</th>
                    <th>Metode Bayar</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $idx => $b)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <span class="font-monospace fw-bold text-dark">{{ $b->booking_code }}</span>
                        </td>
                        <td>{{ $b->booking_date->format('d/m/Y') }}</td>
                        <td>{{ $b->formatted_time_range }} ({{ $b->duration }} Jam)</td>
                        <td>{{ $b->court ? $b->court->name : '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $b->customer_display_name }}</div>
                            <div class="text-muted small">{{ $b->customer_display_phone }}</div>
                        </td>
                        <td>{{ $b->payment ? $b->payment->payment_method : '-' }}</td>
                        <td>
                            <span class="badge {{ $b->status_badge_class }} px-2 py-1 small">
                                {{ $b->status_label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold {{ $b->status === 'confirmed' ? 'text-success' : 'text-dark' }}">
                                {{ $b->formatted_total_price }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                            Tidak ada data transaksi pada periode tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($bookings->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="8" class="text-end ps-4">TOTAL PENDAPATAN TERKONFIRMASI (LUNAS):</td>
                        <td class="text-end pe-4 text-success fs-5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
