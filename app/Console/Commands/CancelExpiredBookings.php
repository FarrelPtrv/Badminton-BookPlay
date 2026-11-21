<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan pending yang telah melewati batas waktu pembayaran';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'cancelled']);

        $this->info("Berhasil membatalkan {$count} pesanan pending yang telah kadaluarsa.");

        return Command::SUCCESS;
    }
}
