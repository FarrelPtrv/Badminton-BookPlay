@extends('layouts.app')

@section('title', 'Form Pemesanan Lapangan - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Form Pemesanan Lapangan</h3>
                    <p class="text-muted small mb-0">Isi detail jadwal bermain dan durasi sewa yang Anda inginkan</p>
                </div>
                <a href="{{ route('schedule.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-calendar3 me-1"></i> Cek Ketersediaan
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <!-- 1. Pilih Lapangan -->
                    <div class="mb-4">
                        <label for="court_id" class="form-label fw-bold text-dark">
                            <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i> 1. Pilih Lapangan
                        </label>
                        <select name="court_id" id="court_id" class="form-select form-select-lg @error('court_id') is-invalid @enderror" required onchange="updateSummary()">
                            <option value="" disabled {{ !old('court_id', $selectedCourtId) ? 'selected' : '' }}>-- Pilih Lapangan Badminton --</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" 
                                    data-price="{{ $court->price_per_hour }}"
                                    data-floor="{{ $court->floor_type }}"
                                    {{ old('court_id', $selectedCourtId) == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }} ({{ $court->floor_type }} &bull; {{ $court->formatted_price }}/jam)
                                </option>
                            @endforeach
                        </select>
                        @error('court_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Pilih Tanggal & Jam Mulai -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="booking_date" class="form-label fw-bold text-dark">
                                <i class="bi bi-calendar-event-fill text-primary me-1"></i> 2. Tanggal Main
                            </label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control form-control-lg @error('booking_date') is-invalid @enderror" value="{{ old('booking_date', $selectedDate) }}" min="{{ date('Y-m-d') }}" required onchange="updateSummary()">
                            @error('booking_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="start_time" class="form-label fw-bold text-dark">
                                <i class="bi bi-clock-fill text-primary me-1"></i> 3. Jam Mulai
                            </label>
                            <select name="start_time" id="start_time" class="form-select form-select-lg @error('start_time') is-invalid @enderror" required onchange="updateSummary()">
                                <option value="" disabled {{ !old('start_time', $selectedHour) ? 'selected' : '' }}>-- Pilih Jam Mulai --</option>
                                @for($h = 8; $h <= 22; $h++)
                                    @php $hourStr = sprintf('%02d:00', $h); @endphp
                                    <option value="{{ $hourStr }}" {{ old('start_time', $selectedHour) === $hourStr ? 'selected' : '' }}>
                                        {{ $hourStr }} WIB
                                    </option>
                                @endfor
                            </select>
                            @error('start_time')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. Pilih Durasi Main -->
                    <div class="mb-4">
                        <label for="duration" class="form-label fw-bold text-dark">
                            <i class="bi bi-hourglass-split text-primary me-1"></i> 4. Durasi Main (Jam)
                        </label>
                        <select name="duration" id="duration" class="form-select form-select-lg @error('duration') is-invalid @enderror" required onchange="updateSummary()">
                            @for($d = 1; $d <= 5; $d++)
                                <option value="{{ $d }}" {{ old('duration', 1) == $d ? 'selected' : '' }}>
                                    {{ $d }} Jam
                                </option>
                            @endfor
                        </select>
                        <small class="text-muted">Jam operasional GOR berakhir pukul 23:00 WIB.</small>
                        @error('duration')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold text-dark">
                            <i class="bi bi-chat-left-text-fill text-primary me-1"></i> Catatan Khusus (Opsional)
                        </label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Contoh: Butuh tambahan raket sewa / sparring komunitas...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Ringkasan Perhitungan Harga Otomatis -->
                    <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-receipt me-2 text-primary"></i> Ringkasan Biaya Pemesanan</h5>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Tarif Lapangan:</span>
                            <span id="summaryRate" class="fw-semibold text-dark">Rp 0 / jam</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Estimasi Jam Main:</span>
                            <span id="summaryTime" class="fw-semibold text-dark">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Durasi Sewa:</span>
                            <span id="summaryDuration" class="fw-semibold text-dark">1 Jam</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-6 fw-bold text-dark">Total Pembayaran:</span>
                            <span id="summaryTotal" class="fs-4 fw-extrabold text-primary">Rp 0</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-2 mb-4 p-3 bg-warning-subtle text-warning-emphasis rounded-3 small">
                        <i class="bi bi-shield-exclamation fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Batas Waktu Pembayaran:</strong> Setelah pesanan dibuat, Anda memiliki waktu <strong>30 menit</strong> untuk menyelesaikan transfer dan mengunggah bukti pembayaran. Slot jadwal akan otomatis dibatalkan jika melewati batas waktu.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="bi bi-arrow-right-circle-fill me-2"></i> Lanjutkan & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateSummary() {
        const courtSelect = document.getElementById('court_id');
        const durationSelect = document.getElementById('duration');
        const timeSelect = document.getElementById('start_time');

        let price = 0;
        if (courtSelect.selectedIndex > 0) {
            const selectedOption = courtSelect.options[courtSelect.selectedIndex];
            price = parseInt(selectedOption.getAttribute('data-price') || 0);
        }

        const duration = parseInt(durationSelect.value || 1);
        const total = price * duration;

        document.getElementById('summaryRate').innerText = 'Rp ' + price.toLocaleString('id-ID') + ' / jam';
        document.getElementById('summaryDuration').innerText = duration + ' Jam';
        document.getElementById('summaryTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');

        if (timeSelect.value) {
            const startHour = parseInt(timeSelect.value.split(':')[0]);
            const endHour = startHour + duration;
            const endHourStr = (endHour < 10 ? '0' : '') + endHour + ':00';
            document.getElementById('summaryTime').innerText = timeSelect.value + ' - ' + endHourStr + ' WIB';
        } else {
            document.getElementById('summaryTime').innerText = '-';
        }
    }

    document.addEventListener('DOMContentLoaded', updateSummary);
</script>
@endpush
