<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string $description
 * @property string $image_path
 * @property string $category
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Accommodation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
