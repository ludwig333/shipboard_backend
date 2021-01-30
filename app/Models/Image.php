<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $table="images";

    protected $fillable = [
        'image_store_id'
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

    public function content() {
        return $this->belongsTo(Content::class, 'id', 'content_id');
    }

    public function imageStore()
    {
        return $this->belongsTo(ImageStore::class, 'image_store_id');
    }

    public function getImage()
    {
        if($this->imageStore) {
            $type = pathinfo($this->imageStore->path, PATHINFO_EXTENSION);
            $data = Storage::disk('public')->get($this->imageStore->path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            return $base64;
        }
        return null;
    }
}
