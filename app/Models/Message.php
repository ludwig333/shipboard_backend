<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TextResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\CardResource;

class Message extends Model
{
    use HasFactory;
    protected $table="messages";

    protected $fillable = [
        'name',
        'flow_id',
        'position_x',
        'position_y'
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function contents() {
        return $this->hasMany(Content::class)->orderBy('index');
    }

    public function getContents() {
        $contentData= [];
        $contents = $this->contents;
        foreach($contents as $content) {
            if($content->content_type == Text::class) {
                array_push($contentData, new TextResource($content->child));
            } else if ($content->content_type == Image::class) {
                array_push($contentData, new ImageResource($content->child));
            } else if ($content->content_type == Card::class) {
                array_push($contentData, new CardResource($content->child));
            }
        }
        return $contentData;
    }
}
