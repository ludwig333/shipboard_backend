<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $table="contents";

    protected $fillable = [
        'message_id',
        'content_type',
        'content_id',
        'index'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function child() {
        return $this->belongsTo($this->content_type, 'content_id');
    }
}
