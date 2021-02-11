<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerConfiguration extends Model
{
    use HasFactory;
    protected $table = "messenger_configurations";
    protected $guarded = [];


    public function connections()
    {
        return $this->morphMany(BotConnection::class, 'connectable');
    }
}
