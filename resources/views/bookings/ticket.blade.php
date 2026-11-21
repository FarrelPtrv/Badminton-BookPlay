<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket Booking {{ $booking->booking_code }} - Badminton BookPlay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }

        .ticket-card {
            max-width: 680px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 2px dashed #cbd5e1;
            position: relative;
        }

        .ticket-header {
            background: linear-gradient(135deg, #0f766e 0%, #064e3b 100%);
            color: white;
            padding: 30px;
        }

        .stamp-confirmed {
            border: 3px solid #16a34a;
            color: #16a34a;
            padding: 6px 16px;
            font-weight: 900;
            text-transform: uppercase;
            border-radius: 8px;
            display: inline-block;
            transform: rotate(-5deg);
            letter-spacing: 1px;
        }

        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .ticket-card {
                box-shadow: none;
                margin: 0 auto;
                border: 2px solid #334155;
            }
        }
    </style>
</head>
<body>

    <div class="container py-4">
        <div class="text-center no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                <i class="bi bi-printer me-2"></i> Cetak / Simpan sebagai PDF
            </button>
            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4 ms-2">
                Kembali ke Aplikasi
            </a>
        </div>

        <div class="ticket-card">
            <!-- Ticket Header -->
            <div class="ticket-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-dribbble me-2"></i> BADMINTON BOOKPLAY</h3>
                    <p class="small text-light mb-0">Jl. Olahraga Juara No. 88 &bull; Telp: 0812-3456-7890</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold">E-TICKET RESMI</span>
                </div>
            </div>

            <!-- Ticket Body -->
            <div class="p-4 p-sm-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Kode Booking</div>
                        <div class="fs-3 fw-bold font-monospace text-primary">{{ $booking->booking_code }}</div>
                    </div>
                    <div>
                        @if($booking->status === 'confirmed')
                            <div class="stamp-confirmed">LUNAS / VERIFIED</div>
                        @else
                            <span class="badge {{ $booking->status_badge_class }} px-3 py-2 fs-6">
                                {{ $booking->status_label }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted small">Nama Pemesan:</div>
                        <div class="fw-bold fs-6">{{ $booking->customer_display_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">No. WhatsApp / HP:</div>
                        <div class="fw-bold fs-6">{{ $booking->customer_display_phone }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Nama Lapangan:</div>
                        <div class="fw-bold fs-5 text-dark">{{ $booking->court->name }}</div>
                        <span class="badge bg-light text-dark border small">{{ $booking->court->floor_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Tanggal Main:</div>
                        <div class="fw-bold fs-5 text-dark">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Jam Main:</div>
                        <div class="fw-bold fs-5 text-primary">{{ $booking->formatted_time_range }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Durasi Sewa:</div>
                        <div class="fw-bold fs-5 text-dark">{{ $booking->duration }} Jam</div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-4 border d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Metode Pembayaran:</div>
                        <div class="fw-semibold text-dark">{{ $booking->payment ? $booking->payment->payment_method : 'Transfer Bank' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Total Pembayaran:</div>
                        <div class="fs-4 fw-extrabold text-success">{{ $booking->formatted_total_price }}</div>
                    </div>
                </div>

                <!-- QR Code & Scanner Instructions -->
                <div class="d-flex align-items-center gap-4 p-3 border rounded-3 bg-white">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ $booking->booking_code }}" alt="QR Code" class="img-fluid border p-1 rounded" style="width: 100px; height: 100px;">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Pindai di Lokasi GOR</h6>
                        <p class="small text-muted mb-0">
                            Tunjukkan QR code ini atau sebutkan kode booking kepada staf resepsionis saat memasuki lapangan.
                        </p>
                    </div>
                </div>

                <!-- Terms -->
                <div class="mt-4 pt-3 border-top small text-muted">
                    <div class="fw-bold mb-1">Tata Tertib Bermain di GOR:</div>
                    <ol class="ps-3 mb-0" style="font-size: 11px;">
                        <li>Pemain wajib menggunakan sepatu khusus badminton ber-sol karet bersih.</li>
                        <li>Harap hadir 10 menit sebelum jam sewa dimulai.</li>
                        <li>Dilarang merokok, makan berat, dan membawa minuman manis ke atas karpet lapangan.</li>
                    </ol>
                </div>
            </div>

            <!-- Ticket Footer -->
            <div class="bg-light p-3 text-center small text-muted border-top">
                Dicetak pada {{ date('d F Y H:i') }} WIB &bull; Sistem Badminton BookPlay
            </div>
        </div>
    </div>

</body>
</html>
