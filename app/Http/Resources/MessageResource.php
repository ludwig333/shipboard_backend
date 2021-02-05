<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Message;

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
        $nextMessage = $this->next_message_id ? Message::find($this->next_message_id)->uuid : null;
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
           'next' => $nextMessage
       ];
    }
}
