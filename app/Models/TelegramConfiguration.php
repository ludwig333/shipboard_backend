<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramConfiguration extends Model
{
    use HasFactory;
    protected $table = "telegram_configurations";
    protected $guarded = [];

    public function connections()
    {
        return $this->morphMany(BotConnection::class, 'connectable');
    }
}
