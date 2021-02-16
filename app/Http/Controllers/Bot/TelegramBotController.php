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
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\BotMan;

class TelegramBotController extends Controller
{
    public function reply($id) {
        try {
            $myBot = Bot::where('uuid', $id)->first();
            $config = TelegramConfiguration::where('bot_id', $myBot->id)->first();
            if($config && $config->connect_status == 1) {
                $config = [
                    'telegram' => [
                        'token' => $config->access_token
                    ]
                ];
                // Load the driver(s) you want to use
                DriverManager::loadDriver(TelegramDriver::class);
                // Create an instance
                $botman = BotManFactory::create($config, new LaravelCache());

                $botman->hears('start', function($bot) use($myBot) {
                    $firstFlow = $myBot->flows->first();
                    if ($firstFlow) {
                        $firstMessage = $firstFlow->messages->first();
                        if($firstMessage) {
                            $userId = $myBot->user->id;
                            $botId = $myBot->id;
                            $flowClass = str_replace("-", "", $firstMessage->uuid);
                            $className = 'App\Http\Controllers\Bot\UserBots\U'.$userId.'\B'.$botId.'\M'.$flowClass;
                            $bot->startConversation(new $className("telegram"));
                        }
                    }
                });

                // Start listening
                $botman->listen();
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
