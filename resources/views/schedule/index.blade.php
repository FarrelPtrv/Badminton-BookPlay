@extends('layouts.app')

@section('title', 'Cek Jadwal Lapangan Real-Time - Badminton BookPlay')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Cek Jadwal Ketersediaan Lapangan</h2>
            <p class="text-muted mb-0">Pantau ketersediaan slot jam main secara real-time sebelum melakukan booking</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-cart-plus me-1"></i> Buat Pesanan Baru
        </a>
    </div>

    <!-- Date and Court Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <form action="{{ route('schedule.index') }}" method="GET" id="scheduleFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-muted"><i class="bi bi-calendar3 me-1"></i> Tanggal Main</label>
                    <div class="input-group">
                        <input type="date" name="date" id="dateInput" class="form-control" value="{{ $selectedDate }}" min="{{ date('Y-m-d') }}" onchange="document.getElementById('scheduleFilterForm').submit()">
                    </div>
                    <!-- Quick Date Pills -->
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 small {{ $selectedDate === date('Y-m-d') ? 'active bg-secondary text-white' : '' }}" onclick="setDate('{{ date('Y-m-d') }}')">Hari Ini</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 small {{ $selectedDate === date('Y-m-d', strtotime('+1 day')) ? 'active bg-secondary text-white' : '' }}" onclick="setDate('{{ date('Y-m-d', strtotime('+1 day')) }}')">Besok</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 small {{ $selectedDate === date('Y-m-d', strtotime('+2 day')) ? 'active bg-secondary text-white' : '' }}" onclick="setDate('{{ date('Y-m-d', strtotime('+2 day')) }}')">Lusa</button>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-muted"><i class="bi bi-grid-3x3-gap me-1"></i> Pilih Lapangan Spesifik</label>
                    <select name="court_id" class="form-select" onchange="document.getElementById('scheduleFilterForm').submit()">
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ $selectedCourt && $selectedCourt->id == $court->id ? 'selected' : '' }}>
                                {{ $court->name }} ({{ $court->floor_type }} - {{ $court->formatted_price }}/jam)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i> Segarkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Status Indicator Legend (PRD 4.3 Requirement) -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.5px;">Indikator Status:</span>
            <div class="d-flex flex-wrap gap-4 align-items-center small">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: #10b981;"></span>
                    <span class="fw-semibold text-dark">Hijau: Tersedia</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: #f59e0b;"></span>
                    <span class="fw-semibold text-dark">Kuning: Menunggu Pembayaran / Verifikasi</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: #ef4444;"></span>
                    <span class="fw-semibold text-dark">Merah: Sudah Dibooking (Booked)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: #64748b;"></span>
                    <span class="fw-semibold text-dark">Abu-abu: Maintenance</span>
                </div>
            </div>
        </div>
    </div>

    @if($selectedCourt)
        <!-- Single Court Hour Slots Visualizer -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-primary px-3 py-1 rounded-pill small mb-1">{{ $selectedCourt->floor_type }}</span>
                    <h4 class="fw-bold text-dark mb-0">{{ $selectedCourt->name }}</h4>
                    <div class="text-muted small mt-1">
                        Jadwal Tanggal: <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong>
                        &bull; Tarif: <strong>{{ $selectedCourt->formatted_price }} / jam</strong>
                    </div>
                </div>

                <a href="{{ route('bookings.create', ['court_id' => $selectedCourt->id, 'date' => $selectedDate]) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-cart-plus me-1"></i> Pesan Lapangan Ini
                </a>
            </div>

            <div class="row g-3">
                @foreach($scheduleData as $slot)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        @if($slot['status'] === 'available')
                            <a href="{{ route('bookings.create', ['court_id' => $selectedCourt->id, 'date' => $selectedDate, 'hour' => $slot['time_start']]) }}" class="text-decoration-none">
                                <div class="p-3 rounded-4 text-center border border-success bg-success bg-opacity-10 transition hover-shadow" style="cursor: pointer;">
                                    <div class="fw-bold text-success fs-6 mb-1">{{ $slot['label'] }}</div>
                                    <span class="badge bg-success rounded-pill px-2 py-1 small">
                                        <i class="bi bi-check-circle me-1"></i> Tersedia
                                    </span>
                                </div>
                            </a>
                        @elseif($slot['status'] === 'waiting')
                            <div class="p-3 rounded-4 text-center border border-warning bg-warning bg-opacity-10">
                                <div class="fw-bold text-warning-emphasis fs-6 mb-1">{{ $slot['label'] }}</div>
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                </span>
                            </div>
                        @elseif($slot['status'] === 'booked')
                            <div class="p-3 rounded-4 text-center border border-danger bg-danger bg-opacity-10">
                                <div class="fw-bold text-danger fs-6 mb-1">{{ $slot['label'] }}</div>
                                <span class="badge bg-danger rounded-pill px-2 py-1 small">
                                    <i class="bi bi-x-circle me-1"></i> Booked
                                </span>
                            </div>
                        @else
                            <div class="p-3 rounded-4 text-center border border-secondary bg-secondary bg-opacity-10">
                                <div class="fw-bold text-muted fs-6 mb-1">{{ $slot['label'] }}</div>
                                <span class="badge bg-secondary rounded-pill px-2 py-1 small">
                                    <i class="bi bi-cone-striped me-1"></i> Maintenance
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
                <i class="bi bi-info-circle-fill text-primary me-1"></i> Klik pada kotak slot jam berwarna <strong>Hijau</strong> untuk langsung mengisi form pemesanan dengan jam tersebut.
            </div>
        </div>
    @endif

    <!-- All Courts Comparative Matrix Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h4 class="fw-bold text-dark mb-2"><i class="bi bi-table me-2 text-primary"></i> Matriks Jadwal Semua Lapangan</h4>
        <p class="text-muted small mb-4">Perbandingan ketersediaan slot jam operasional di seluruh lapangan untuk tanggal: <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong></p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-start" style="width: 140px;">Jam Main</th>
                        @foreach($courts as $court)
                            <th>
                                <div class="fw-bold">{{ $court->name }}</div>
                                <div class="small text-muted fw-normal">{{ $court->floor_type }} &bull; {{ $court->formatted_price }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hours = range(8, 22);
                    @endphp
                    @foreach($hours as $h)
                        @php
                            $slotLabel = sprintf('%02d:00 - %02d:00', $h, $h + 1);
                            $timeStart = sprintf('%02d:00', $h);
                        @endphp
                        <tr>
                            <td class="fw-bold text-start table-light">
                                <i class="bi bi-clock me-1 text-muted"></i> {{ $slotLabel }}
                            </td>
                            @foreach($courts as $court)
                                @php
                                    $courtSlots = $allCourtsMatrix[$court->id]['slots'] ?? [];
                                    $currentSlot = collect($courtSlots)->firstWhere('hour', $h);
                                    $status = $currentSlot['status'] ?? 'available';
                                @endphp
                                <td>
                                    @if($status === 'available')
                                        <a href="{{ route('bookings.create', ['court_id' => $court->id, 'date' => $selectedDate, 'hour' => $timeStart]) }}" class="badge bg-success text-decoration-none px-3 py-2 rounded-pill d-inline-block">
                                            <i class="bi bi-check-lg"></i> Tersedia
                                        </a>
                                    @elseif($status === 'waiting')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill d-inline-block">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </span>
                                    @elseif($status === 'booked')
                                        <span class="badge bg-danger px-3 py-2 rounded-pill d-inline-block">
                                            <i class="bi bi-x-lg"></i> Booked
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill d-inline-block">
                                            Maintenance
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setDate(dateStr) {
        document.getElementById('dateInput').value = dateStr;
        document.getElementById('scheduleFilterForm').submit();
    }
</script>
@endpush
