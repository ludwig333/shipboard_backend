<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Text extends Model
{
    use HasFactory;
    protected $table="texts";

    protected $fillable = [
        'body',
        'height'
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

        static::deleting(function ($text) {
            foreach($text->buttons() as $button) {
                $button->delete();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function content() {
        return $this->belongsTo(Content::class, 'id', 'content_id');
    }

    public function buttons() {
        return Button::where([
            'parent' => Text::class,
            'parent_id' => $this->id
        ])->get();
    }
}
