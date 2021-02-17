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

    /**
     * The "booted" method of the model.
     *
     * This model events deletes the child of content when content is deleting
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($content) {
            $content->child->delete();
            foreach($content->child->buttons() as $button) {
                $button->delete();
            }
        });
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function child() {
        return $this->belongsTo($this->content_type, 'content_id');
    }
}
