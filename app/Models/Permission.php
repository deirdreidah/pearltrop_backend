<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory, \App\Traits\Auditable;
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($permission) {
            $permission->name = Str::slug($permission->name);
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
