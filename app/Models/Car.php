<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $brand
 * @property string $model
 * @property int $year
 * @property string $description
 * @property string $image
 * @property float $price_per_day
 * @property bool $is_available
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Car extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
        'price_per_day' => 'decimal:2',
    ];

    public function carBookings(): HasMany
    {
        return $this->hasMany(CarBooking::class);
    }
}
