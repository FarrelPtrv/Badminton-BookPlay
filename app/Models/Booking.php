<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'court_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration',
        'total_price',
        'status',
        'customer_name',
        'customer_phone',
        'notes',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'expires_at' => 'datetime',
            'duration' => 'integer',
            'total_price' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->customer_name ?: 'Walk-in Customer';
    }

    public function getCustomerDisplayPhoneAttribute(): string
    {
        if ($this->user && $this->user->phone) {
            return $this->user->phone;
        }
        return $this->customer_phone ?: '-';
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi',
            'confirmed' => 'Terkonfirmasi (Lunas)',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'waiting_verification' => 'bg-info text-dark',
            'confirmed' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        $start = substr($this->start_time, 0, 5);
        $end = substr($this->end_time, 0, 5);
        return "{$start} - {$end} WIB";
    }

    public function isExpired(): bool
    {
        if ($this->status === 'pending' && $this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return true;
        }
        return false;
    }
}
