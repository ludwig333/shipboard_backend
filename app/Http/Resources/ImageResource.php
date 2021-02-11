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
        $image = $this->getImage();
        return [
            'id' => $this->uuid,
            'type' => BuilderContentType::IMAGE,
            'height' => $image ? 180 : 150,
            'imagePreviewUrl' => $image,
            'selectedImage' => 'null'
        ];
    }
}
