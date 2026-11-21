<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_guest_can_access_landing_page_and_schedule()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Badminton BookPlay');

        $responseSchedule = $this->get('/schedule');
        $responseSchedule->assertStatus(200);
        $responseSchedule->assertSee('Indikator Status');
    }

    public function test_anti_double_booking_prevents_overlapping_reservation()
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);

        $court = Court::create([
            'name' => 'Lapangan Test Anti-Overlap',
            'floor_type' => 'Karpet Vinyl',
            'price_per_hour' => 50000,
            'status' => 'available',
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        // Initial booking: 18:00 - 20:00
        $this->actingAs($customer)->post('/bookings', [
            'court_id' => $court->id,
            'booking_date' => $date,
            'start_time' => '18:00',
            'duration' => 2,
        ])->assertSessionHasNoErrors();

        // Overlapping attempt: 19:00 - 21:00 (overlaps with 18:00-20:00)
        $anotherCustomer = User::factory()->create(['role' => 'customer']);
        $responseConflict = $this->actingAs($anotherCustomer)->post('/bookings', [
            'court_id' => $court->id,
            'booking_date' => $date,
            'start_time' => '19:00',
            'duration' => 2,
        ]);

        $responseConflict->assertSessionHas('error');

        // Non-overlapping attempt: 20:00 - 21:00 (adjacent, not overlapping) should succeed!
        $responseAdjacent = $this->actingAs($anotherCustomer)->post('/bookings', [
            'court_id' => $court->id,
            'booking_date' => $date,
            'start_time' => '20:00',
            'duration' => 1,
        ]);

        $responseAdjacent->assertSessionHasNoErrors();
    }

    public function test_customer_can_upload_payment_proof()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $court = Court::first();

        $booking = Booking::create([
            'booking_code' => 'TEST-' . uniqid(),
            'user_id' => $customer->id,
            'court_id' => $court->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'duration' => 2,
            'total_price' => 100000,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $response = $this->actingAs($customer)->post("/my-bookings/{$booking->id}/payment", [
            'payment_method' => 'Transfer BCA',
            'account_name' => 'Budi Santoso',
            'proof_of_payment' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('waiting_verification', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->payment);
    }

    public function test_admin_can_verify_and_confirm_booking()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $court = Court::first();

        $booking = Booking::create([
            'booking_code' => 'TEST-VERIFY-' . uniqid(),
            'user_id' => $customer->id,
            'court_id' => $court->id,
            'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'duration' => 2,
            'total_price' => 100000,
            'status' => 'waiting_verification',
        ]);

        $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/verify", [
            'action' => 'confirm',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    public function test_customer_cannot_access_admin_dashboard()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_create_walkin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $court = Court::first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $walkinDate = Carbon::today()->addDays(rand(10, 200))->format('Y-m-d');
        $customerName = 'Pak Joko Walkin ' . uniqid();

        $responseWalkin = $this->actingAs($admin)->post('/admin/bookings/walkin', [
            'customer_name' => $customerName,
            'customer_phone' => '081234567890',
            'court_id' => $court->id,
            'booking_date' => $walkinDate,
            'start_time' => '16:00',
            'duration' => 2,
            'payment_method' => 'Tunai / Cash (Walk-in)',
        ]);

        $responseWalkin->assertSessionHas('success');
        $this->assertDatabaseHas('bookings', [
            'customer_name' => $customerName,
            'status' => 'confirmed',
        ]);
    }
}
