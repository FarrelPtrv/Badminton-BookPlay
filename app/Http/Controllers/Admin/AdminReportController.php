<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $courtId = $request->input('court_id', 'all');

        $query = Booking::with(['court', 'user', 'payment'])
            ->whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($courtId !== 'all') {
            $query->where('court_id', $courtId);
        }

        $bookings = (clone $query)->orderBy('booking_date', 'asc')->orderBy('start_time', 'asc')->get();

        // Metrics
        $totalRevenue = (clone $query)->where('status', 'confirmed')->sum('total_price');
        $totalBookings = $bookings->count();
        $confirmedCount = $bookings->where('status', 'confirmed')->count();
        $waitingCount = $bookings->where('status', 'waiting_verification')->count();
        $cancelledCount = $bookings->where('status', 'cancelled')->count();

        // Breakdown by court
        $courts = Court::all();

        return view('admin.reports.index', compact(
            'bookings',
            'startDate',
            'endDate',
            'status',
            'courtId',
            'totalRevenue',
            'totalBookings',
            'confirmedCount',
            'waitingCount',
            'cancelledCount',
            'courts'
        ));
    }

    public function print(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $courtId = $request->input('court_id', 'all');

        $query = Booking::with(['court', 'user', 'payment'])
            ->whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($courtId !== 'all') {
            $query->where('court_id', $courtId);
        }

        $bookings = $query->orderBy('booking_date', 'asc')->orderBy('start_time', 'asc')->get();
        $totalRevenue = $bookings->where('status', 'confirmed')->sum('total_price');

        return view('admin.reports.print', compact('bookings', 'startDate', 'endDate', 'status', 'totalRevenue'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $courtId = $request->input('court_id', 'all');

        $query = Booking::with(['court', 'user', 'payment'])
            ->whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($courtId !== 'all') {
            $query->where('court_id', $courtId);
        }

        $bookings = $query->orderBy('booking_date', 'asc')->get();
        $filename = "laporan_booking_{$startDate}_sampai_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Kode Booking',
                'Tanggal Main',
                'Jam Main',
                'Durasi (Jam)',
                'Nama Lapangan',
                'Nama Pelanggan',
                'No. HP',
                'Total Harga (Rp)',
                'Metode Pembayaran',
                'Status Pesanan',
                'Waktu Dibuat',
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_code,
                    $b->booking_date->format('Y-m-d'),
                    $b->formatted_time_range,
                    $b->duration,
                    $b->court ? $b->court->name : '-',
                    $b->customer_display_name,
                    $b->customer_display_phone,
                    $b->total_price,
                    $b->payment ? $b->payment->payment_method : '-',
                    $b->status_label,
                    $b->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
