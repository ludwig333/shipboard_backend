<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\BuilderContentType;
use Illuminate\Support\Facades\Log;

class TextResource extends JsonResource
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
            'type' => BuilderContentType::TEXT,
            'value' => $this->body,
            'height' => $this->height,
            'buttons' => ButtonResource::collection($this->buttons())
        ];
    }
}
