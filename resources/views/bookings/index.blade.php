@extends('layouts.app')

@section('title', 'Riwayat Booking Saya - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Riwayat Pemesanan Lapangan</h3>
            <p class="text-muted small mb-0">Daftar seluruh transaksi booking lapangan Anda</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Pesan Lapangan Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Kode Booking</th>
                        <th>Lapangan</th>
                        <th>Jadwal Main</th>
                        <th>Durasi & Biaya</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark font-monospace">{{ $b->booking_code }}</span>
                                <div class="text-muted small" style="font-size: 11px;">{{ $b->created_at->translatedFormat('d M Y, H:i') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->court ? $b->court->name : 'Lapangan' }}</div>
                                <span class="badge bg-light text-secondary border small">{{ $b->court ? $b->court->floor_type : '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1 text-primary"></i> {{ $b->booking_date->translatedFormat('d M Y') }}</div>
                                <div class="text-muted small"><i class="bi bi-clock me-1 text-warning"></i> {{ $b->formatted_time_range }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $b->formatted_total_price }}</div>
                                <div class="text-muted small">{{ $b->duration }} Jam Main</div>
                            </td>
                            <td>
                                <span class="badge {{ $b->status_badge_class }} px-3 py-2 rounded-pill small">
                                    {{ $b->status_label }}
                                </span>

                                @if($b->status === 'pending' && $b->expires_at)
                                    <div class="text-danger small mt-1 countdown-timer" data-expire="{{ $b->expires_at->toISOString() }}" style="font-size: 11px;">
                                        <i class="bi bi-hourglass-top me-1"></i> Sisa Waktu: <span class="timer-display fw-bold">--:--</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('bookings.show', $b->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        Detail
                                    </a>

                                    @if($b->status === 'pending')
                                        <a href="{{ route('bookings.show', $b->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-upload me-1"></i> Bayar
                                        </a>
                                    @endif

                                    @if($b->status === 'confirmed')
                                        <a href="{{ route('bookings.ticket', $b->id) }}" target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                            <i class="bi bi-qr-code me-1"></i> E-Ticket
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-receipt-cutoff fs-1 text-muted"></i>
                                <h5 class="fw-bold text-dark mt-2">Belum Ada Riwayat Pemesanan</h5>
                                <p class="text-muted small">Anda belum pernah melakukan pemesanan lapangan bulutangkis.</p>
                                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">
                                    Mulai Pesan Sekarang
                                </a>
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
</div>
@endsection

@push('scripts')
<script>
    function updateCountdowns() {
        document.querySelectorAll('.countdown-timer').forEach(el => {
            const expireStr = el.getAttribute('data-expire');
            if (!expireStr) return;

            const expireDate = new Date(expireStr).getTime();
            const now = new Date().getTime();
            const distance = expireDate - now;

            const display = el.querySelector('.timer-display');

            if (distance < 0) {
                display.innerText = "Kadaluarsa";
                el.classList.add('text-muted');
                el.classList.remove('text-danger');
            } else {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                display.innerText = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            }
        });
    }

    setInterval(updateCountdowns, 1000);
    document.addEventListener('DOMContentLoaded', updateCountdowns);
</script>
@endpush
