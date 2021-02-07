<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Button extends Model
{
    use HasFactory;
    protected $table = 'buttons';
    protected $fillable = [
      'name', 'type', 'parent', 'parent_id'
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($text) {
            $text->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function parent() {
        return $this->belongsTo($this->parent_type, 'parent_id');
    }
}
