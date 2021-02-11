<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\MessengerConfiguration;
use App\Models\TelegramConfiguration;
use App\Models\SlackConfiguration;
use App\Constants\PlatformType;

class BotConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ( $this->connectable_type == MessengerConfiguration::class) {
            return [
                'platform' => PlatformType::MESSENGER,
                'access_token' => $this->connectable->access_token,
                'app_secret' => $this->connectable->app_secret,
                'verification_code' => $this->connectable->verification_code,
                'connect_status' => $this->connectable->connect_status,
                'callback_url' => env('TARGET_URL')."/messenger/".$this->bot->uuid
            ];
        } else if ( $this->connectable_type == TelegramConfiguration::class) {
            return [
                'platform' => PlatformType::TELEGRAM,
                'username' => $this->connectable->username,
                'access_token' => $this->connectable->access_token,
                'connect_status' => $this->connectable->connect_status
            ];
        } else if ( $this->connectable_type == SlackConfiguration::class) {
            return [
                'platform' => PlatformType::SLACK,
                'access_token' => $this->connectable->access_token,
                'connect_status' => $this->connectable->connect_status,
                'callback_url' => env('TARGET_URL')."/messenger/".$this->bot->uuid
            ];
        }
    }
}
