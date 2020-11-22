<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Flow extends Model
{
    use HasFactory;

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bot) {
            $bot->uuid = Str::uuid();
        });
    }
}
