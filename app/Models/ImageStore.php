<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImageStore extends Model
{
    use HasFactory;

    protected $table="image_stores";

    protected $fillable = [
        'name',
        'path'
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($imageStore) {
            Storage::disk('public')->delete($imageStore->path);
        });
    }
}
