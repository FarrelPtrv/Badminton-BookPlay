<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        // Auto expire outdated pending bookings
        Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $query = Booking::with(['court', 'user', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $courts = Court::all();

        return view('admin.bookings.index', compact('bookings', 'courts'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['court', 'user', 'payment.verifier']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function verifyPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:confirm,reject'],
            'rejection_reason' => ['nullable', 'required_if:action,reject', 'string', 'max:500'],
        ], [
            'action.required' => 'Pilih tindakan verifikasi.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pembayaran ditolak.',
        ]);

        if ($validated['action'] === 'confirm') {
            $booking->update([
                'status' => 'confirmed',
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'verified_by' => Auth::id(),
                    'payment_date' => $booking->payment->payment_date ?: Carbon::now(),
                    'rejection_reason' => null,
                ]);
            } else {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'Verifikasi Admin (Manual)',
                    'amount' => $booking->total_price,
                    'verified_by' => Auth::id(),
                    'payment_date' => Carbon::now(),
                ]);
            }

            return back()->with('success', "Pesanan {$booking->booking_code} berhasil dikonfirmasi dan berstatus LUNAS.");
        } else {
            $booking->update([
                'status' => 'cancelled',
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'verified_by' => Auth::id(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);
            }

            return back()->with('success', "Pesanan {$booking->booking_code} ditolak dengan alasan: " . $validated['rejection_reason']);
        }
    }

    public function createWalkin()
    {
        $courts = Court::where('status', 'available')->get();
        return view('admin.bookings.create_walkin', compact('courts'));
    }

    public function storeWalkin(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'court_id' => ['required', 'exists:courts,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1', 'max:6'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Nama pelanggan walk-in wajib diisi.',
            'customer_phone.required' => 'Nomor HP pelanggan wajib diisi.',
            'court_id.required' => 'Pilih lapangan.',
            'booking_date.required' => 'Tanggal main wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'duration.required' => 'Durasi main wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $court = Court::findOrFail($validated['court_id']);

        $startHour = (int) substr($validated['start_time'], 0, 2);
        $duration = (int) $validated['duration'];
        $endHour = $startHour + $duration;

        if ($startHour < 8 || $endHour > 23) {
            return back()->withInput()->with('error', 'Jam operasional GOR adalah 08:00 hingga 23:00 WIB.');
        }

        $startTimeFormatted = sprintf('%02d:00:00', $startHour);
        $endTimeFormatted = sprintf('%02d:00:00', $endHour);
        $bookingDate = $validated['booking_date'];

        try {
            $booking = DB::transaction(function () use ($court, $bookingDate, $startTimeFormatted, $endTimeFormatted, $duration, $validated) {
                // Anti double-booking check
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
                    throw new \Exception("Jadwal pukul {$conflictTime} di {$court->name} sudah terisi. Silakan pilih jam lain.");
                }

                $bookingCode = 'WLK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $totalPrice = $court->price_per_hour * $duration;

                $b = Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => null,
                    'court_id' => $court->id,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTimeFormatted,
                    'end_time' => $endTimeFormatted,
                    'duration' => $duration,
                    'total_price' => $totalPrice,
                    'status' => 'confirmed',
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'notes' => $validated['notes'] ?? 'Manual Walk-in oleh Admin',
                ]);

                Payment::create([
                    'booking_id' => $b->id,
                    'payment_method' => $validated['payment_method'],
                    'account_name' => $validated['customer_name'],
                    'amount' => $totalPrice,
                    'payment_date' => Carbon::now(),
                    'verified_by' => Auth::id(),
                ]);

                return $b;
            });

            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', "Pesanan Walk-in {$booking->booking_code} berhasil dibuat dan otomatis terkonfirmasi LUNAS.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function blockSchedule()
    {
        $courts = Court::all();
        return view('admin.bookings.block_schedule', compact('courts'));
    }

    public function storeBlockSchedule(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1', 'max:15'],
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'court_id.required' => 'Pilih lapangan yang akan diblokir.',
            'booking_date.required' => 'Pilih tanggal.',
            'start_time.required' => 'Pilih jam mulai.',
            'duration.required' => 'Pilih durasi blokir.',
            'reason.required' => 'Isi alasan pemblokiran jadwal (misal: Turnamen / Maintenance).',
        ]);

        $court = Court::findOrFail($validated['court_id']);

        $startHour = (int) substr($validated['start_time'], 0, 2);
        $duration = (int) $validated['duration'];
        $endHour = $startHour + $duration;

        $startTimeFormatted = sprintf('%02d:00:00', $startHour);
        $endTimeFormatted = sprintf('%02d:00:00', $endHour);
        $bookingDate = $validated['booking_date'];

        try {
            DB::transaction(function () use ($court, $bookingDate, $startTimeFormatted, $endTimeFormatted, $duration, $validated) {
                // Conflict check
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
                    throw new \Exception("Terdapat jadwal aktif ({$conflictTime}) pada rentang jam tersebut. Batalkan atau sesuaikan pesanan terlebih dahulu sebelum memblokir jadwal.");
                }

                $bookingCode = 'BLK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => null,
                    'court_id' => $court->id,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTimeFormatted,
                    'end_time' => $endTimeFormatted,
                    'duration' => $duration,
                    'total_price' => 0,
                    'status' => 'confirmed',
                    'customer_name' => '[DIBLOKIR PENGELOLA]',
                    'customer_phone' => '-',
                    'notes' => 'Alasan: ' . $validated['reason'],
                ]);
            });

            return redirect()->route('admin.bookings.index')
                ->with('success', "Jadwal lapangan berhasil diblokir untuk keperluan: {$validated['reason']}.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
