@extends('layouts.admin')

@section('title', 'Detail & Verifikasi Booking ' . $booking->booking_code . ' - Badminton BookPlay')
@section('page_title', 'Detail & Verifikasi Transaksi')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Booking
        </a>
        <h4 class="fw-bold text-dark mb-0 mt-1">Booking: {{ $booking->booking_code }}</h4>
    </div>
    <div class="d-flex gap-2">
        @if($booking->status === 'confirmed')
            <a href="{{ route('bookings.ticket', $booking->id) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Cetak E-Ticket
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left: Booking & Customer Info -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-info-circle text-primary me-2"></i> Informasi Jadwal & Pemesan
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Status Pesanan:</span>
                    <span class="badge {{ $booking->status_badge_class }} px-3 py-2 rounded-pill mt-1">
                        {{ $booking->status_label }}
                    </span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Waktu Pesan:</span>
                    <strong class="text-dark">{{ $booking->created_at->translatedFormat('d M Y, H:i') }} WIB</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Nama Pemesan:</span>
                    <strong class="text-dark fs-6">{{ $booking->customer_display_name }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">No. WhatsApp / HP:</span>
                    @if($booking->customer_display_phone && $booking->customer_display_phone !== '-')
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $booking->customer_display_phone)) }}" target="_blank" class="text-success fw-bold text-decoration-none">
                            <i class="bi bi-whatsapp me-1"></i> {{ $booking->customer_display_phone }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="col-12"><hr class="my-1"></div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Lapangan:</span>
                    <strong class="text-dark fs-6">{{ $booking->court ? $booking->court->name : '-' }}</strong>
                    <div class="badge bg-light text-dark border small mt-1">{{ $booking->court ? $booking->court->floor_type : '' }}</div>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tarif per Jam:</span>
                    <strong class="text-dark">{{ $booking->court ? $booking->court->formatted_price : '-' }}</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tanggal Main:</span>
                    <strong class="text-dark fs-6">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Jam Main:</span>
                    <strong class="text-primary fs-6">{{ $booking->formatted_time_range }}</strong>
                </div>

                <div class="col-sm-6">
                    <span class="text-muted small d-block">Durasi Sewa:</span>
                    <strong class="text-dark">{{ $booking->duration }} Jam</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Total Tagihan:</span>
                    <span class="fs-4 fw-extrabold text-primary">{{ $booking->formatted_total_price }}</span>
                </div>

                @if($booking->notes)
                    <div class="col-12">
                        <span class="text-muted small d-block">Catatan Pemesan:</span>
                        <div class="p-2 bg-light rounded text-dark small fst-italic">{{ $booking->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Verification & Payment Proof -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-credit-card-2-front text-success me-2"></i> Data Bukti Pembayaran
            </h5>

            @if($booking->payment)
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Metode Pembayaran:</span>
                        <strong class="text-dark">{{ $booking->payment->payment_method }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Nama Pemilik Rekening:</span>
                        <strong class="text-dark">{{ $booking->payment->account_name ?: '-' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Waktu Bayar:</span>
                        <strong class="text-dark">{{ $booking->payment->payment_date ? $booking->payment->payment_date->translatedFormat('d M Y, H:i') : '-' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Diverifikasi Oleh:</span>
                        <strong class="text-dark">{{ $booking->payment->verifier ? $booking->payment->verifier->name : '-' }}</strong>
                    </div>

                    @if($booking->payment->proof_url)
                        <div class="col-12 mt-3">
                            <span class="text-muted small d-block mb-2 fw-semibold">Foto Bukti Transfer:</span>
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <a href="{{ $booking->payment->proof_url }}" target="_blank">
                                    <img src="{{ $booking->payment->proof_url }}" alt="Bukti Transfer" class="img-fluid rounded shadow-sm" style="max-height: 250px; object-fit: contain;">
                                </a>
                                <div class="mt-2 small text-muted">Klik gambar untuk melihat ukuran penuh</div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-secondary py-2 small mb-0">Tidak ada lampiran foto bukti bayar (manual/walk-in).</div>
                        </div>
                    @endif
                </div>
            @else
                <div class="alert alert-warning py-3 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Pelanggan belum mengunggah bukti pembayaran.
                </div>
            @endif

            <!-- Verification Action Box -->
            <div class="pt-3 border-top mt-auto">
                <h6 class="fw-bold text-dark mb-3">Tindakan Verifikasi Pengelola:</h6>

                @if($booking->status === 'confirmed')
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                        <div>
                            <strong>Pembayaran Terkonfirmasi Lunas.</strong>
                            <div class="small">Pesanan ini telah diverifikasi dan slot lapangan terkunci.</div>
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Confirm Form -->
                        <form action="{{ route('admin.bookings.verify', $booking->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran pesanan ini sebagai LUNAS?')">
                            @csrf
                            <input type="hidden" name="action" value="confirm">
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Konfirmasi Lunas
                            </button>
                        </form>

                        <!-- Reject Modal Trigger Button -->
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg me-1"></i> Tolak Pesanan
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak Pembayaran -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.bookings.verify', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">

                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Tolak Pembayaran Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Silakan berikan alasan penolakan agar pelanggan mengetahui mengapa pembayarannya ditolak (misal: nominal tidak sesuai, bukti transfer palsu/tidak terbaca).
                    </p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="Contoh: Bukti transfer tidak valid atau dana belum masuk ke mutasi rekening." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
