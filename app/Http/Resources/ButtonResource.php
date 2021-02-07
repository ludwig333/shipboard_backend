<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Message;

class ButtonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $message = Message::where('id', $this->leads_to_message)->first();
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'next' => $message ? $message->uuid : null
        ];
    }
}
