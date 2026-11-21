<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Badminton BookPlay ({{ $startDate }} s/d {{ $endDate }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #000;
            background-color: #fff;
            font-size: 13px;
        }

        .report-header {
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body class="p-4">

    <div class="no-print text-end mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm px-3 ms-2">Tutup</button>
    </div>

    <!-- Company Header -->
    <div class="report-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0">BADMINTON BOOKPLAY JUARA</h3>
            <p class="mb-0 text-muted small">Jl. Olahraga Juara No. 88, Kota Anda &bull; Telp / WA: 0812-3456-7890</p>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-uppercase mb-1">Laporan Transaksi & Pendapatan</h5>
            <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</small>
        </div>
    </div>

    <!-- Filter Meta -->
    <div class="row g-2 mb-3 small">
        <div class="col-6">
            Filter Status: <strong>{{ $status === 'all' ? 'Semua Status' : ucfirst($status) }}</strong>
        </div>
        <div class="col-6 text-end">
            Tanggal Cetak: <strong>{{ date('d F Y, H:i') }} WIB</strong>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light text-center small">
            <tr>
                <th style="width: 35px;">No</th>
                <th>Kode Booking</th>
                <th>Tgl Main</th>
                <th>Jam</th>
                <th>Lapangan</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="text-end">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-monospace fw-bold">{{ $b->booking_code }}</td>
                    <td class="text-center">{{ $b->booking_date->format('d/m/Y') }}</td>
                    <td class="text-center">{{ substr($b->start_time, 0, 5) }}-{{ substr($b->end_time, 0, 5) }}</td>
                    <td>{{ $b->court ? $b->court->name : '-' }}</td>
                    <td>{{ $b->customer_display_name }} ({{ $b->customer_display_phone }})</td>
                    <td>{{ $b->payment ? $b->payment->payment_method : '-' }}</td>
                    <td class="text-center">
                        {{ $b->status_label }}
                    </td>
                    <td class="text-end">{{ number_format($b->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-3">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold table-light">
                <td colspan="8" class="text-end">TOTAL PENDAPATAN LUNAS:</td>
                <td class="text-end">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Signoff -->
    <div class="row mt-5 pt-4 text-center">
        <div class="col-6">
            <p class="mb-5 text-muted">Dibuat Oleh,</p>
            <p class="fw-bold mb-0 text-decoration-underline">{{ Auth::user()->name }}</p>
            <small class="text-muted">Staf Administrasi GOR</small>
        </div>
        <div class="col-6">
            <p class="mb-5 text-muted">Disetujui Oleh,</p>
            <p class="fw-bold mb-0 text-decoration-underline">(............................................)</p>
            <small class="text-muted">Manajer Operasional GOR</small>
        </div>
    </div>

</body>
</html>
