<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Auto expire outdated pending bookings
        Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $selectedCourtId = $request->input('court_id');

        $courts = Court::all();

        // If no specific court is chosen, pick the first available one or first in list
        $selectedCourt = null;
        if ($selectedCourtId) {
            $selectedCourt = Court::find($selectedCourtId);
        }
        if (! $selectedCourt && $courts->isNotEmpty()) {
            $selectedCourt = $courts->firstWhere('status', 'available') ?? $courts->first();
            $selectedCourtId = $selectedCourt->id;
        }

        $scheduleData = $this->buildScheduleData($selectedCourt, $selectedDate);

        // Also build a full matrix for all courts for the selected date
        $allCourtsMatrix = [];
        foreach ($courts as $court) {
            $allCourtsMatrix[$court->id] = [
                'court' => $court,
                'slots' => $this->buildCourtSlots($court, $selectedDate),
            ];
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'court' => $selectedCourt,
                'date' => $selectedDate,
                'slots' => $scheduleData,
                'allCourts' => $allCourtsMatrix,
            ]);
        }

        return view('schedule.index', [
            'courts' => $courts,
            'selectedCourt' => $selectedCourt,
            'selectedDate' => $selectedDate,
            'scheduleData' => $scheduleData,
            'allCourtsMatrix' => $allCourtsMatrix,
        ]);
    }

    private function buildCourtSlots(?Court $court, string $date): array
    {
        if (! $court) {
            return [];
        }

        $startHour = 8;
        $endHour = 23;
        $slots = [];

        $bookings = Booking::where('court_id', $court->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'waiting_verification', 'confirmed'])
            ->get();

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slotStart = sprintf('%02d:00:00', $hour);
            $slotEnd = sprintf('%02d:00:00', $hour + 1);
            $label = sprintf('%02d:00 - %02d:00', $hour, $hour + 1);

            if ($court->status === 'maintenance') {
                $status = 'maintenance';
                $booking = null;
            } else {
                $matchingBooking = $bookings->first(function ($b) use ($slotStart, $slotEnd) {
                    return $b->start_time < $slotEnd && $b->end_time > $slotStart;
                });

                if ($matchingBooking) {
                    if ($matchingBooking->status === 'confirmed') {
                        $status = 'booked';
                    } else { // pending or waiting_verification
                        $status = 'waiting';
                    }
                    $booking = $matchingBooking;
                } else {
                    $status = 'available';
                    $booking = null;
                }
            }

            $slots[] = [
                'hour' => $hour,
                'time_start' => sprintf('%02d:00', $hour),
                'time_end' => sprintf('%02d:00', $hour + 1),
                'label' => $label,
                'status' => $status,
                'booking_code' => $booking ? $booking->booking_code : null,
            ];
        }

        return $slots;
    }

    private function buildScheduleData(?Court $court, string $date): array
    {
        return $this->buildCourtSlots($court, $date);
    }
}
