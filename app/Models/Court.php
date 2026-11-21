<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'floor_type',
        'description',
        'price_per_hour',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'integer',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_hour, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
            if (file_exists(public_path('uploads/' . $this->image))) {
                return asset('uploads/' . $this->image);
            }
            if (file_exists(public_path('storage/' . $this->image))) {
                return asset('storage/' . $this->image);
            }
            return asset($this->image);
        }
        return 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=800&q=80';
    }
}
