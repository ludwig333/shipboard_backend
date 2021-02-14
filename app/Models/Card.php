<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    use HasFactory;

    protected $table="cards";

    protected $fillable = [
        'title',
        'body',
        'group_id',
        'image_store_id',
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

        static::creating(function ($card) {
            $card->uuid = Str::uuid();
        });

        static::deleting(function ($card) {
            //Delete image store if it exists
            if($card->imageStore) {
                $card->imageStore->delete();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function cardGroup()
    {
        return $this->belongsTo(Card::class, 'group_id');
    }

    public function buttons() {
        return Button::where([
            'parent' => Card::class,
            'parent_id' => $this->id
        ])->get();
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
