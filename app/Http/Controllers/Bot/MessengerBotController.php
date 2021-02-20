<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Bot;
use Illuminate\Http\Request;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Facebook\FacebookDriver;
use App\Models\MessengerConfiguration;
use Illuminate\Support\Facades\Log;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\BotMan;


class MessengerBotController extends Controller
{
    public function reply($id, Request $request)
    {
        Log::info("facebook");
        $myBot = Bot::where('uuid', $id)->first();
        $config = MessengerConfiguration::where('bot_id', $myBot->id)->first();

        if($request->get('hub_mode') == 'subscribe') {
            if($request->get('hub_verify_token') == $config->verification_code) {
                return $request->get('hub_challenge');
            }
        }
        if($config && $config->connect_status == 1) {
            // Load the driver(s) you want to use
            DriverManager::loadDriver(FacebookDriver::class);
            $config = [
                'facebook' => [
                    'token' => $config->access_token,
                    'app_secret' => $config->app_secret,
                    'verification'=> $config->verification_code,
                ]
            ];
            // Load the driver(s) you want to use
            DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
            // Create an instance
            $botman = BotManFactory::create($config, new LaravelCache());

            $botman->hears('._(Hi|Hello|Start)._', function(Botman $bot) use($myBot) {
                $firstFlow = $myBot->flows->first();
                if ($firstFlow) {
                    $firstMessage = $firstFlow->messages->first();
                    if($firstMessage) {
                        $userId = $myBot->user->id;
                        $botId = $myBot->id;
                        $flowClass = str_replace("-", "", $firstMessage->uuid);
                        $className = 'App\Http\Controllers\Bot\UserBots\U'.$userId.'\B'.$botId.'\M'.$flowClass;
                        $bot->startConversation(new $className);
                    }
                }
            });

            // Start listening
            $botman->listen();
        }
    }
}
