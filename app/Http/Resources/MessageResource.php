<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
           'name' => $this->name,
           'position' => [
               'x' => $this->position_x,
               'y' => $this->position_y
           ],
           'children' => $this->getContents(),
           'isHover' => false,
           'isSelected' => false,
           'height' => 200,
       ];
    }
}
