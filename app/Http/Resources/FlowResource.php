<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\Bot;

class FlowResource extends JsonResource {
    private $detail;

    public function __construct($resource, $detail=false)
    {
        parent::__construct($resource);
        $this->detail = $detail;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->uuid,
            'name' => $this->name,
            'message_count' => count($this->messages)
        ];

        if($this->detail) {
            $data['bot'] = Bot::find($this->bot_id)->uuid;
        }

        return $data;
    }
}
