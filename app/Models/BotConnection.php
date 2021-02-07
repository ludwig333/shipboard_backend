<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotConnection extends Model
{
    use HasFactory;
    protected $table = "bot_connections";
    protected $guarded = [];

    public function bot()
    {
        return $this->belongsTo(Bot::class, 'id', 'bot_id');
    }
    public function connectable()
    {
        return $this->morphTo();
    }
}
