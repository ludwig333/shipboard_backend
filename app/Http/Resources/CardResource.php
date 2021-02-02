<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class CardResource extends JsonResource
{
    private $cardIndex;

    public function __construct($resource, $cardIndex = null) {
        parent::__construct($resource);
        $this->cardIndex = $cardIndex;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       return [
           'id' => $this->uuid,
           'active' => ($this->cardIndex == 1) ? true : false,
           'imagePreviewUrl' => $this->getImage(),
           'selectedImage' => null,
           'heading' => $this->title,
           'body' => $this->body,
           'height' => 283
       ];
    }
}
