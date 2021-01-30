<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageStore extends Model
{
    use HasFactory;

    protected $table="image_stores";

    protected $fillable = [
        'name',
        'path'
    ];
}
