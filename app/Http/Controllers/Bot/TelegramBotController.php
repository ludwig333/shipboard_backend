<?php

namespace App\Http\Controllers\Bot;

use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Models\TelegramConfiguration;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\BotMan\Cache\LaravelCache;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function reply($id) {
        try {
            $bot = Bot::where('uuid', $id)->first();
            $config = TelegramConfiguration::where('bot_id', $bot->id)->first();
            $config = [
                'telegram' => [
                    'token' => $config->access_token
                ]
            ];
            // Load the driver(s) you want to use
            DriverManager::loadDriver(TelegramDriver::class);
            // Create an instance
            $botman = BotManFactory::create($config, new LaravelCache());

            $botman->hears('start', function($bot) {
                $firstFlow = $bot->flows->first();
                if ($firstFlow) {
                    $firstMessage = $firstFlow->messages->first();
                    if($firstMessage) {
                        $className = "M" . str_replace("-", "", $firstMessage->uuid);
                        $bot->startConversation(new $className("facebook"));
                    }
                }
            });

            // Start listening
            $botman->listen();
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
