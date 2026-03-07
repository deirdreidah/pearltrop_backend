<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourCompany extends Model
{
    /** @use HasFactory<\Database\Factories\TourCompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'address',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
