<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $courts = Court::where('status', 'available')->get();
        $selectedCourtId = $request->input('court_id');
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $selectedHour = $request->input('hour');

        $selectedCourt = null;
        if ($selectedCourtId) {
            $selectedCourt = Court::find($selectedCourtId);
        }

        return view('bookings.create', compact('courts', 'selectedCourt', 'selectedCourtId', 'selectedDate', 'selectedHour'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1', 'max:6'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'court_id.required' => 'Silakan pilih lapangan.',
            'court_id.exists' => 'Lapangan tidak ditemukan.',
            'booking_date.required' => 'Tanggal main wajib diisi.',
            'booking_date.after_or_equal' => 'Tanggal main tidak boleh di masa lampau.',
            'start_time.required' => 'Jam mulai wajib dipilih.',
            'duration.required' => 'Durasi main wajib dipilih.',
            'duration.min' => 'Durasi minimal 1 jam.',
            'duration.max' => 'Durasi maksimal 6 jam per pemesanan.',
        ]);

        $court = Court::findOrFail($validated['court_id']);

        if ($court->status !== 'available') {
            return back()->withInput()->with('error', 'Lapangan ini sedang dalam masa pemeliharaan (maintenance) dan tidak dapat dibooking.');
        }

        $startHour = (int) substr($validated['start_time'], 0, 2);
        $duration = (int) $validated['duration'];
        $endHour = $startHour + $duration;

        if ($startHour < 8 || $endHour > 23) {
            return back()->withInput()->with('error', 'Jam operasional GOR adalah 08:00 hingga 23:00 WIB. Pilihan jam Anda melebihi jam operasional.');
        }

        $startTimeFormatted = sprintf('%02d:00:00', $startHour);
        $endTimeFormatted = sprintf('%02d:00:00', $endHour);
        $bookingDate = $validated['booking_date'];

        // Strict Anti Double-Booking Check inside Transaction with lock
        try {
            $booking = DB::transaction(function () use ($court, $bookingDate, $startTimeFormatted, $endTimeFormatted, $duration, $validated) {
                // 1. Release expired pending bookings first
                Booking::where('status', 'pending')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<', Carbon::now())
                    ->update(['status' => 'cancelled']);

                // 2. Overlap check with row locking
                $conflict = Booking::where('court_id', $court->id)
                    ->where('booking_date', $bookingDate)
                    ->whereIn('status', ['pending', 'waiting_verification', 'confirmed'])
                    ->where(function ($query) use ($startTimeFormatted, $endTimeFormatted) {
                        $query->where('start_time', '<', $endTimeFormatted)
                              ->where('end_time', '>', $startTimeFormatted);
                    })
                    ->lockForUpdate()
                    ->first();

                if ($conflict) {
                    $conflictTime = substr($conflict->start_time, 0, 5) . ' - ' . substr($conflict->end_time, 0, 5);
                    throw new \Exception("Maaf, jadwal pada pukul {$conflictTime} di {$court->name} sudah dipesan orang lain. Silakan pilih jam atau lapangan lain.");
                }

                // 3. Generate unique booking code
                $todayPrefix = date('Ymd');
                $randomSuffix = strtoupper(Str::random(4));
                $bookingCode = 'BDM-' . $todayPrefix . '-' . $randomSuffix;

                $totalPrice = $court->price_per_hour * $duration;

                return Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => Auth::id(),
                    'court_id' => $court->id,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTimeFormatted,
                    'end_time' => $endTimeFormatted,
                    'duration' => $duration,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                    'expires_at' => Carbon::now()->addMinutes(30), // 30 minutes to complete payment
                ]);
            });

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran dalam batas waktu 30 menit.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        // Auto expire outdated pending bookings
        Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $bookings = Booking::with(['court', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && $booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk melihat pesanan ini.');
        }

        // Auto expire if needed
        if ($booking->isExpired()) {
            $booking->update(['status' => 'cancelled']);
        }

        $booking->load(['court', 'payment', 'user']);

        return view('bookings.show', compact('booking'));
    }

    public function uploadPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($booking->status === 'confirmed') {
            return back()->with('info', 'Pesanan ini sudah terkonfirmasi lunas.');
        }

        if ($booking->isExpired()) {
            $booking->update(['status' => 'cancelled']);
            return back()->with('error', 'Waktu pembayaran telah habis. Pesanan ini sudah dibatalkan otomatis.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
            'account_name' => ['required', 'string', 'max:255'],
            'proof_of_payment' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'payment_method.required' => 'Pilih metode pembayaran yang digunakan.',
            'account_name.required' => 'Nama pemilik rekening / pengirim wajib diisi.',
            'proof_of_payment.required' => 'Unggah foto bukti transfer.',
            'proof_of_payment.image' => 'File bukti bayar harus berupa gambar.',
            'proof_of_payment.mimes' => 'Format file yang diperbolehkan: JPG, PNG, WEBP.',
            'proof_of_payment.max' => 'Ukuran file bukti bayar maksimal 2 MB.',
        ]);

        $file = $request->file('proof_of_payment');
        $filename = 'proof_' . $booking->booking_code . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Save to public/uploads/proofs
        $destinationPath = public_path('uploads/proofs');
        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $filename);
        $savedPath = 'uploads/proofs/' . $filename;

        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method' => $validated['payment_method'],
                'account_name' => $validated['account_name'],
                'amount' => $booking->total_price,
                'proof_of_payment' => $savedPath,
                'payment_date' => Carbon::now(),
                'rejection_reason' => null,
            ]
        );

        $booking->update([
            'status' => 'waiting_verification',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Pengelola GOR akan segera memverifikasi pesanan Anda.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pesanan yang sudah dalam proses atau lunas tidak dapat dibatalkan secara mandiri. Silakan hubungi admin GOR.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function printTicket(Booking $booking)
    {
        if (! Auth::user()->isAdmin() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['court', 'payment', 'user']);

        return view('bookings.ticket', compact('booking'));
    }
}
