<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;

class TelegramServices {

    public function register($token, $bot_id, $remove = false)
    {
        try {
            $url = 'https://api.telegram.org/bot'
                .$token
                .'/setWebhook';

            if (! $remove) {
                $url .= '?url='.env("TARGET_URL").'/api/v1/telegram/'.$bot_id;
            }

            $output = json_decode(file_get_contents($url));

            if ($output->ok == true && $output->result == true) {
                Log::info($remove
                    ? 'Your bot Telegram\'s webhook has been removed!'
                    : 'Your bot is now set up with Telegram\'s webhook!'
                );
            }

            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
    }
}
