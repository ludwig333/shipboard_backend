<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Bot;
use Illuminate\Http\Request;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Facebook\FacebookDriver;
use App\Models\MessengerConfiguration;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use App\Http\Controllers\Bot\UserBots\U1\B1\F1\M97817734920942ce84959c26dc939e56;
use Illuminate\Support\Facades\Log;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Http\Controllers\Bot\UserBots\U1\B1\F1\Mb8c5859db5a24207ab39dfd30922811d;
use App\Http\Controllers\Bot\UserBots\U1\B2\F2\M131c1204abb34ec08b015ea7833440f1;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class MessengerBotController extends Controller
{
    public function reply($id, Request $request)
    {
        $myBot = Bot::where('uuid', $id)->first();
        $config = MessengerConfiguration::where('bot_id', $myBot->id)->first();
        if($request->get('hub_mode') == 'subscribe') {
            if($request->get('hub_verify_token') == $config->verification_code) {
                return $request->get('hub_challenge');
            }
        }
        if($myBot->connect_status == 1) {
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

            $botman->hears('start', function($bot) use($myBot) {
                $firstFlow = $myBot->flows->first();
                if ($firstFlow) {
                    $firstMessage = $firstFlow->messages->first();
                    if($firstMessage) {
                        $userId = $myBot->user->id;
                        $botId = $myBot->id;
                        $flowId = $firstMessage->id;
                        $flowClass = str_replace("-", "", $firstMessage->uuid);
                        $className = 'App\Http\Controllers\Bot\UserBots\U'.$userId.'\B'.$botId.'\F'.$flowId.'\M'.$flowClass;
                        $bot->startConversation(new $className("facebook"));
                    }
                }
            });

            // Start listening
            $botman->listen();
        }
    }
}
