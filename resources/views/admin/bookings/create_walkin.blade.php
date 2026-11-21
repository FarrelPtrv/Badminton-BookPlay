@extends('layouts.admin')

@section('title', 'Manual Booking Walk-in - Badminton BookPlay')
@section('page_title', 'Input Pesanan Langsung (Walk-in)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-person-plus-fill text-primary me-2"></i> Form Pesanan Walk-in (Di Tempat)</h5>
                    <p class="text-muted small mb-0">Input booking instan bagi pelanggan yang datang langsung tanpa membuat akun online</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.bookings.walkin.store') }}" method="POST" id="walkinForm">
                @csrf

                <!-- Data Pelanggan Walk-in -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="customer_name" class="form-label fw-semibold small">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Contoh: Pak Hendra / Tim Rajawali" required>
                        @error('customer_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="customer_phone" class="form-label fw-semibold small">No. HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" placeholder="081234567890" required>
                        @error('customer_phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Lapangan & Jadwal -->
                <div class="mb-3">
                    <label for="court_id" class="form-label fw-semibold small">Pilih Lapangan <span class="text-danger">*</span></label>
                    <select name="court_id" id="court_id" class="form-select @error('court_id') is-invalid @enderror" required onchange="calculateWalkinTotal()">
                        <option value="" disabled selected>-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" data-price="{{ $court->price_per_hour }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                {{ $court->name }} ({{ $court->floor_type }} &bull; {{ $court->formatted_price }}/jam)
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="booking_date" class="form-label fw-semibold small">Tanggal Main <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" id="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        @error('booking_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                        <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror" required onchange="calculateWalkinTotal()">
                            @for($h = 8; $h <= 22; $h++)
                                @php $hourStr = sprintf('%02d:00', $h); @endphp
                                <option value="{{ $hourStr }}" {{ old('start_time', '19:00') === $hourStr ? 'selected' : '' }}>
                                    {{ $hourStr }} WIB
                                </option>
                            @endfor
                        </select>
                        @error('start_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="duration" class="form-label fw-semibold small">Durasi Sewa (Jam) <span class="text-danger">*</span></label>
                        <select name="duration" id="duration" class="form-select @error('duration') is-invalid @enderror" required onchange="calculateWalkinTotal()">
                            @for($d = 1; $d <= 5; $d++)
                                <option value="{{ $d }}" {{ old('duration', 1) == $d ? 'selected' : '' }}>{{ $d }} Jam</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_method" class="form-label fw-semibold small">Metode Pembayaran Langsung <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="Tunai / Cash (Walk-in)" {{ old('payment_method') === 'Tunai / Cash (Walk-in)' ? 'selected' : '' }}>Tunai / Cash di Meja Resepsionis</option>
                            <option value="QRIS" {{ old('payment_method') === 'QRIS' ? 'selected' : '' }}>QRIS Statis Kasir</option>
                            <option value="Transfer BCA" {{ old('payment_method') === 'Transfer BCA' ? 'selected' : '' }}>Transfer BCA Langsung</option>
                            <option value="Transfer Mandiri" {{ old('payment_method') === 'Transfer Mandiri' ? 'selected' : '' }}>Transfer Mandiri Langsung</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label fw-semibold small">Catatan Pesanan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <!-- Price Box -->
                <div class="p-3 bg-light rounded-3 mb-4 border d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small d-block">Status Setelah Dibuat:</span>
                        <span class="badge bg-success">Otomatis Terkonfirmasi (LUNAS)</span>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small d-block">Total Tarif Sewa:</span>
                        <span class="fs-4 fw-extrabold text-primary" id="totalDisplay">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Booking & Kunci Jadwal
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function calculateWalkinTotal() {
        const court = document.getElementById('court_id');
        const duration = parseInt(document.getElementById('duration').value || 1);
        let price = 0;

        if (court.selectedIndex > 0) {
            price = parseInt(court.options[court.selectedIndex].getAttribute('data-price') || 0);
        }

        const total = price * duration;
        document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', calculateWalkinTotal);
</script>
@endpush
