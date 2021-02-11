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

class MessengerBotController extends Controller
{
    public function reply($id, Request $request)
    {
        $bot = Bot::where('uuid', $id)->first();
        $botId = $bot->id;
        $config = MessengerConfiguration::where('bot_id', $bot->id)->first();
        if($request->get('hub_mode') == 'subscribe') {
            if($request->get('hub_verify_token') == $config->verification_code) {
                return $request->get('hub_challenge');
            }
        }
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
        $botman = BotManFactory::create($config);

        //Applying middleware
//        $botman->middleware->received(new ReceivedMiddleware($id));

        // Give the bot something to listen for.
        $botman->hears('start', function($bot) {
            $bot->startConversation(new M97817734920942ce84959c26dc939e56);
        });

        // Start listening
        $botman->listen();
    }
}
