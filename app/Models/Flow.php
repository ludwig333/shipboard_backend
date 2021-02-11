<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Flow extends Model
{
    use HasFactory;

    protected $table="flows";
    protected $fillable = [
      'name',
      'bot_id'
    ];

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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function messages() {
        return $this->hasMany(Message::class)->orderBy('id', 'ASC');
    }

    public function bot() {
        return $this->belongsTo(Bot::class);
    }
}
