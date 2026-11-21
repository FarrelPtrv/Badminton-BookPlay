<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gorbadminton.com'],
            [
                'name' => 'Admin GOR Badminton',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Sample Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'phone' => '089876543210',
                'role' => 'customer',
                'password' => Hash::make('password'),
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'ahmad@gmail.com'],
            [
                'name' => 'Ahmad Fauzi',
                'phone' => '081398765432',
                'role' => 'customer',
                'password' => Hash::make('password'),
            ]
        );

        // 3. Create Courts
        $courtsData = [
            [
                'name' => 'Lapangan 1 (Karpet Vinyl BWF)',
                'floor_type' => 'Karpet Vinyl',
                'description' => 'Lapangan karpet vinyl hijau standar turnamen BWF. Dilengkapi pencahayaan LED sorot anti silau dan ventilasi udara nyaman.',
                'price_per_hour' => 50000,
                'image' => null,
                'status' => 'available',
            ],
            [
                'name' => 'Lapangan 2 (Karpet Vinyl BWF)',
                'floor_type' => 'Karpet Vinyl',
                'description' => 'Karpet vinyl premium dengan grip prima, cocok untuk latihan rutin, sparing, maupun turnamen.',
                'price_per_hour' => 50000,
                'image' => null,
                'status' => 'available',
            ],
            [
                'name' => 'Lapangan 3 (Lantai Kayu Parquet)',
                'floor_type' => 'Kayu Parquet',
                'description' => 'Lantai kayu parquet interlocking elastis yang meredam benturan lutut dan tumit secara optimal.',
                'price_per_hour' => 60000,
                'image' => null,
                'status' => 'available',
            ],
            [
                'name' => 'Lapangan 4 (Semen Halus)',
                'floor_type' => 'Semen Halus',
                'description' => 'Lantai semen acian halus dengan cat khusus lapangan badminton, tarif bersahabat untuk komunitas.',
                'price_per_hour' => 35000,
                'image' => null,
                'status' => 'available',
            ],
        ];

        $courts = [];
        foreach ($courtsData as $data) {
            $courts[] = Court::firstOrCreate(['name' => $data['name']], $data);
        }

        // 4. Sample Bookings for today
        $today = Carbon::today()->format('Y-m-d');
        $todayCode = date('Ymd');

        // Confirmed Booking on Court 1 (18:00 - 20:00)
        $code1 = 'BDM-' . $todayCode . '-001';
        $booking1 = Booking::firstOrCreate(
            ['booking_code' => $code1],
            [
                'user_id' => $customer->id,
                'court_id' => $courts[0]->id,
                'booking_date' => $today,
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'duration' => 2,
                'total_price' => 100000,
                'status' => 'confirmed',
                'notes' => 'Sparing komunitas bulutangkis',
                'expires_at' => Carbon::now()->addMinutes(30),
            ]
        );

        Payment::firstOrCreate(
            ['booking_id' => $booking1->id],
            [
                'payment_method' => 'Transfer BCA',
                'account_name' => 'Budi Santoso',
                'amount' => 100000,
                'proof_of_payment' => null,
                'payment_date' => Carbon::now()->subHours(2),
                'verified_by' => $admin->id,
            ]
        );

        // Waiting Verification Booking on Court 1 (20:00 - 22:00)
        $code2 = 'BDM-' . $todayCode . '-002';
        $booking2 = Booking::firstOrCreate(
            ['booking_code' => $code2],
            [
                'user_id' => $customer2->id,
                'court_id' => $courts[0]->id,
                'booking_date' => $today,
                'start_time' => '20:00:00',
                'end_time' => '22:00:00',
                'duration' => 2,
                'total_price' => 100000,
                'status' => 'waiting_verification',
                'notes' => 'Mohon diverifikasi segera min',
                'expires_at' => Carbon::now()->addMinutes(25),
            ]
        );

        Payment::firstOrCreate(
            ['booking_id' => $booking2->id],
            [
                'payment_method' => 'QRIS',
                'account_name' => 'Ahmad Fauzi',
                'amount' => 100000,
                'proof_of_payment' => null,
                'payment_date' => Carbon::now()->subMinutes(15),
                'verified_by' => null,
            ]
        );

        // Pending Booking on Court 2 (19:00 - 20:00)
        $code3 = 'BDM-' . $todayCode . '-003';
        Booking::firstOrCreate(
            ['booking_code' => $code3],
            [
                'user_id' => $customer->id,
                'court_id' => $courts[1]->id,
                'booking_date' => $today,
                'start_time' => '19:00:00',
                'end_time' => '20:00:00',
                'duration' => 1,
                'total_price' => 50000,
                'status' => 'pending',
                'notes' => 'Menunggu transfer bank',
                'expires_at' => Carbon::now()->addMinutes(20),
            ]
        );
    }
}
