<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'account_name',
        'amount',
        'proof_of_payment',
        'payment_date',
        'verified_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount' => 'integer',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofUrlAttribute(): ?string
    {
        if ($this->proof_of_payment) {
            if (file_exists(public_path($this->proof_of_payment))) {
                return asset($this->proof_of_payment);
            }
            if (file_exists(public_path('uploads/' . $this->proof_of_payment))) {
                return asset('uploads/' . $this->proof_of_payment);
            }
            if (file_exists(public_path('storage/' . $this->proof_of_payment))) {
                return asset('storage/' . $this->proof_of_payment);
            }
            return asset($this->proof_of_payment);
        }
        return null;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }
}
