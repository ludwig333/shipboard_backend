<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\BuilderContentType;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
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
            'type' => BuilderContentType::IMAGE,
            'height' => 150,
            'imagePreviewUrl' => $this->getImage(),
            'selectedImage' => 'null'
        ];
    }
}
