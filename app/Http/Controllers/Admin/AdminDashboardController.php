<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Auto expire outdated pending bookings
        Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $today = Carbon::today()->format('Y-m-d');
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Stats
        $todayRevenue = Booking::where('status', 'confirmed')
            ->where('booking_date', $today)
            ->sum('total_price');

        $monthRevenue = Booking::where('status', 'confirmed')
            ->whereYear('booking_date', $thisYear)
            ->whereMonth('booking_date', $thisMonth)
            ->sum('total_price');

        $activeBookingsCount = Booking::whereIn('status', ['waiting_verification', 'pending'])
            ->orWhere(function ($q) use ($today) {
                $q->where('status', 'confirmed')->where('booking_date', '>=', $today);
            })
            ->count();

        $totalCustomers = User::where('role', 'customer')->count();
        $totalCourts = Court::count();

        // Needs immediate action: waiting verification
        $waitingVerifications = Booking::with(['court', 'user', 'payment'])
            ->where('status', 'waiting_verification')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Recent bookings
        $recentBookings = Booking::with(['court', 'user', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        // Courts overview
        $courts = Court::withCount(['bookings' => function ($q) use ($today) {
            $q->where('booking_date', $today)->whereIn('status', ['confirmed', 'waiting_verification']);
        }])->get();

        return view('admin.dashboard', compact(
            'todayRevenue',
            'monthRevenue',
            'activeBookingsCount',
            'totalCustomers',
            'totalCourts',
            'waitingVerifications',
            'recentBookings',
            'courts'
        ));
    }
}
