@extends('layouts.admin')

@section('title', 'Blokir Jadwal Lapangan - Badminton BookPlay')
@section('page_title', 'Blokir Jadwal / Maintenance Khusus')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-slash-circle-fill text-danger me-2"></i> Form Pemblokiran Slot Jadwal</h5>
                    <p class="text-muted small mb-0">Tutup slot jam tertentu untuk keperluan turnamen, sparring internal, atau perbaikan lapangan</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.bookings.block.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="court_id" class="form-label fw-semibold small">Pilih Lapangan yang Diblokir <span class="text-danger">*</span></label>
                    <select name="court_id" id="court_id" class="form-select @error('court_id') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ old('court_id') == $court->id ? 'selected' : '' }}>
                                {{ $court->name }} ({{ $court->floor_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="booking_date" class="form-label fw-semibold small">Tanggal Blokir <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" id="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date', date('Y-m-d')) }}" required>
                        @error('booking_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_time" class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                        <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror" required>
                            @for($h = 8; $h <= 22; $h++)
                                @php $hourStr = sprintf('%02d:00', $h); @endphp
                                <option value="{{ $hourStr }}" {{ old('start_time', '08:00') === $hourStr ? 'selected' : '' }}>
                                    {{ $hourStr }} WIB
                                </option>
                            @endfor
                        </select>
                        @error('start_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label fw-semibold small">Durasi Blokir (Jam) <span class="text-danger">*</span></label>
                    <select name="duration" id="duration" class="form-select @error('duration') is-invalid @enderror" required>
                        @for($d = 1; $d <= 15; $d++)
                            <option value="{{ $d }}" {{ old('duration', 3) == $d ? 'selected' : '' }}>
                                {{ $d }} Jam
                            </option>
                        @endfor
                    </select>
                    <small class="text-muted">Pilih hingga 15 jam jika ingin memblokir satu hari penuh (08:00 - 23:00).</small>
                    @error('duration')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="reason" class="form-label fw-semibold small">Alasan Pemblokiran <span class="text-danger">*</span></label>
                    <input type="text" name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason') }}" placeholder="Contoh: Turnamen PBSI Cabang / Pengecatan Garis Lapangan" required>
                    @error('reason')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-warning small mb-4">
                    <i class="bi bi-info-circle-fill me-1"></i> Slot jam ini akan berstatus <strong>Booked/Dipesan Internal</strong> pada jadwal publik sehingga pengunjung umum tidak dapat memesan di jam ini.
                </div>

                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold">
                    <i class="bi bi-lock-fill me-1"></i> Terapkan Pemblokiran Jadwal
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
