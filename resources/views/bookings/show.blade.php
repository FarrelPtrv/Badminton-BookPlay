@extends('layouts.app')

@section('title', 'Detail Booking ' . $booking->booking_code . ' - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('bookings.index') }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                    </a>
                    <h3 class="fw-bold mb-0 mt-1">Detail Pesanan Lapangan</h3>
                </div>

                <div class="d-flex gap-2">
                    @if($booking->status === 'confirmed')
                        <a href="{{ route('bookings.ticket', $booking->id) }}" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="bi bi-printer me-1"></i> Cetak E-Ticket / Invoice
                        </a>
                    @endif
                </div>
            </div>

            <!-- Status Alert Banner -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle {{ $booking->status === 'confirmed' ? 'bg-success-subtle text-success' : ($booking->status === 'waiting_verification' ? 'bg-info-subtle text-info' : ($booking->status === 'cancelled' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning')) }}">
                            <i class="bi {{ $booking->status === 'confirmed' ? 'bi-check-circle-fill' : ($booking->status === 'waiting_verification' ? 'bi-hourglass-split' : ($booking->status === 'cancelled' ? 'bi-x-circle-fill' : 'bi-exclamation-circle-fill')) }} fs-2"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Status Pesanan:</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $booking->status_label }}</h4>
                            <span class="font-monospace text-muted small">Kode: {{ $booking->booking_code }}</span>
                        </div>
                    </div>

                    @if($booking->status === 'pending' && $booking->expires_at)
                        <div class="text-end bg-warning-subtle p-3 rounded-3 border border-warning border-opacity-25">
                            <div class="small text-warning-emphasis fw-semibold"><i class="bi bi-clock-history me-1"></i> Sisa Waktu Pembayaran:</div>
                            <div class="fs-4 fw-bold text-danger countdown-timer" data-expire="{{ $booking->expires_at->toISOString() }}">
                                <span class="timer-display">--:--</span>
                            </div>
                        </div>
                    @endif
                </div>

                @if($booking->status === 'cancelled' && $booking->payment && $booking->payment->rejection_reason)
                    <div class="alert alert-danger mt-3 mb-0 rounded-3 border-0">
                        <div class="fw-bold"><i class="bi bi-info-circle-fill me-1"></i> Alasan Penolakan dari Admin:</div>
                        <div>{{ $booking->payment->rejection_reason }}</div>
                    </div>
                @endif
            </div>

            <div class="row g-4">
                <!-- Left: Booking Summary -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                            <i class="bi bi-info-circle text-primary me-2"></i> Rincian Jadwal & Lapangan
                        </h5>

                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                            <img src="{{ $booking->court->image_url }}" alt="{{ $booking->court->name }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">{{ $booking->court->name }}</h6>
                                <div class="badge bg-primary rounded-pill small">{{ $booking->court->floor_type }}</div>
                                <div class="text-muted small mt-1">Tarif: {{ $booking->court->formatted_price }} / jam</div>
                            </div>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-6">
                                <span class="text-muted d-block">Tanggal Main:</span>
                                <strong class="text-dark fs-6">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Jam Main:</span>
                                <strong class="text-dark fs-6">{{ $booking->formatted_time_range }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Durasi Sewa:</span>
                                <strong class="text-dark">{{ $booking->duration }} Jam</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Pemesan:</span>
                                <strong class="text-dark">{{ $booking->customer_display_name }}</strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block">No. WhatsApp / HP:</span>
                                <strong class="text-dark">{{ $booking->customer_display_phone }}</strong>
                            </div>
                            @if($booking->notes)
                                <div class="col-12">
                                    <span class="text-muted d-block">Catatan:</span>
                                    <div class="p-2 bg-light rounded text-dark fst-italic">{{ $booking->notes }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">Total Biaya Sewa:</span>
                                <span class="fs-4 fw-extrabold text-primary">{{ $booking->formatted_total_price }}</span>
                            </div>
                        </div>

                        @if($booking->status === 'pending')
                            <div class="mt-4 pt-3 border-top">
                                <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan Ini
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Payment & Verification -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                            <i class="bi bi-wallet2 text-success me-2"></i> Instruksi & Pembayaran
                        </h5>

                        @if($booking->status === 'pending')
                            <!-- Bank Accounts & QRIS Info -->
                            <div class="mb-4">
                                <p class="text-muted small mb-3">Silakan lakukan transfer sebesar <strong class="text-primary">{{ $booking->formatted_total_price }}</strong> ke salah satu rekening resmi Badminton BookPlay:</p>

                                <div class="p-3 bg-light rounded-3 mb-2 border d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="badge bg-primary mb-1">Transfer Bank BCA</span>
                                        <div class="fw-bold fs-5 font-monospace text-dark">123-456-7890</div>
                                        <small class="text-muted">a.n. Badminton BookPlay Indonesia</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('1234567890'); alert('Nomor rekening BCA disalin!');">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>

                                <div class="p-3 bg-light rounded-3 mb-3 border d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="badge bg-warning text-dark mb-1">Transfer Bank Mandiri</span>
                                        <div class="fw-bold fs-5 font-monospace text-dark">987-00-1234567-8</div>
                                        <small class="text-muted">a.n. Badminton BookPlay Indonesia</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('9870012345678'); alert('Nomor rekening Mandiri disalin!');">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>

                                <div class="text-center p-3 border rounded-3 bg-light mb-4">
                                    <span class="badge bg-dark mb-2">Atau Pembayaran via QRIS</span>
                                    <div class="my-2">
                                        <!-- Stylized Static QRIS representation -->
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=BOOKPLAY-{{ $booking->booking_code }}-{{ $booking->total_price }}" alt="QRIS Badminton BookPlay" class="img-fluid rounded border bg-white p-2" style="width: 150px; height: 150px;">
                                    </div>
                                    <small class="text-muted d-block">Scan dengan GoPay, OVO, Dana, ShopeePay, BCA Mobile, dll</small>
                                </div>
                            </div>

                            <!-- Upload Payment Proof Form -->
                            <div class="card bg-teal bg-opacity-10 border border-teal rounded-4 p-4" style="background-color: #f0fdfa; border-color: #99f6e4;">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Unggah Bukti Transfer</h6>
                                
                                <form action="{{ route('bookings.payment', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label fw-semibold small text-muted">Metode Pembayaran</label>
                                        <select name="payment_method" id="payment_method" class="form-select form-select-sm" required>
                                            <option value="Transfer BCA">Transfer Bank BCA</option>
                                            <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
                                            <option value="QRIS">QRIS All Payment</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="account_name" class="form-label fw-semibold small text-muted">Nama Pengirim / Pemilik Rekening</label>
                                        <input type="text" name="account_name" id="account_name" class="form-control form-control-sm" placeholder="Contoh: Budi Santoso" value="{{ old('account_name', Auth::user()->name) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="proof_of_payment" class="form-label fw-semibold small text-muted">File Bukti Bayar (JPG, PNG, maks 2MB)</label>
                                        <input type="file" name="proof_of_payment" id="proof_of_payment" class="form-control form-control-sm" accept="image/*" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> Kirim Bukti Pembayaran
                                    </button>
                                </form>
                            </div>
                        @elseif($booking->status === 'waiting_verification')
                            <div class="text-center py-4">
                                <div class="d-inline-flex p-3 rounded-circle bg-info-subtle text-info mb-3">
                                    <i class="bi bi-clock-history fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Bukti Pembayaran Terkirim!</h5>
                                <p class="text-muted small">
                                    Admin kami sedang memeriksa mutasi pembayaran Anda. Jadwal lapangan Anda telah diamankan sementara menunggu konfirmasi pengelola.
                                </p>

                                @if($booking->payment && $booking->payment->proof_url)
                                    <div class="mt-4 p-3 bg-light rounded-3 text-start">
                                        <span class="text-muted small d-block mb-2 fw-semibold">Bukti yang Diunggah:</span>
                                        <a href="{{ $booking->payment->proof_url }}" target="_blank">
                                            <img src="{{ $booking->payment->proof_url }}" alt="Bukti Transfer" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                                        </a>
                                        <div class="small text-muted mt-2">
                                            Metode: <strong>{{ $booking->payment->payment_method }}</strong> (a.n {{ $booking->payment->account_name }})
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($booking->status === 'confirmed')
                            <div class="text-center py-4">
                                <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success mb-3">
                                    <i class="bi bi-patch-check-fill fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Pembayaran LUNAS & Terverifikasi</h5>
                                <p class="text-muted small">
                                    Terima kasih! Jadwal lapangan telah resmi Anda sewa. Silakan tunjukkan E-Ticket atau sebutkan Kode Booking saat tiba di lokasi GOR.
                                </p>
                                <a href="{{ route('bookings.ticket', $booking->id) }}" target="_blank" class="btn btn-success btn-lg rounded-pill px-4 mt-2 fw-bold">
                                    <i class="bi bi-qr-code me-2"></i> Buka E-Ticket / Invoice
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle text-danger mb-3">
                                    <i class="bi bi-x-circle-fill fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Pesanan Dibatalkan</h5>
                                <p class="text-muted small">
                                    Pesanan ini telah dibatalkan karena melewati batas waktu pembayaran atau ditolak oleh pengelola.
                                </p>
                                <a href="{{ route('bookings.create') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                                    Pesan Jadwal Baru
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateCountdown() {
        const timerContainer = document.querySelector('.countdown-timer');
        if (!timerContainer) return;

        const expireStr = timerContainer.getAttribute('data-expire');
        if (!expireStr) return;

        const expireDate = new Date(expireStr).getTime();
        const now = new Date().getTime();
        const distance = expireDate - now;

        const display = timerContainer.querySelector('.timer-display');

        if (distance < 0) {
            display.innerText = "00:00 (Waktu Habis)";
            window.location.reload();
        } else {
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            display.innerText = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
        }
    }

    setInterval(updateCountdown, 1000);
    document.addEventListener('DOMContentLoaded', updateCountdown);
</script>
@endpush
