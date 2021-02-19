<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Http\Resources\CardResource;
use Illuminate\Support\Facades\Log;

class CardGroup extends Model
{
    use HasFactory;

    protected $table="card_groups";

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cardGroup) {
            $cardGroup->uuid = Str::uuid();
        });

        static::deleting(function ($cardGroup) {
           foreach($cardGroup->cards as $card) {
               $card->delete();
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

    public function cards()
    {
        return $this->hasMany(Card::class, 'group_id');
    }

    public function getCards() {
        $cards = $this->cards;
        $cardData = [];
        $cardIndex = 1;

        foreach($cards as $card) {
            array_push($cardData, new CardResource($card, $cardIndex));
            $cardIndex++;
        }
        return $cardData;
    }

    public function getChildButtons() {
        $cards = $this->getCards();
        if($cards) {
            $cardIds = [];
            foreach($cards as $card) {
                array_push($cardIds, $card->id);
            }
        }
        return Button::where('parent', Card::class)->whereIn('parent_id', $cardIds)->get();
    }
}
