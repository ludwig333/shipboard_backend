<?php

namespace App\Http\Controllers\Web\Back\App\Platforms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bot\Bot;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\BotMan;
use App\Models\Bot\BotCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Bot\FacebookConfiguration;
use BotMan\Drivers\Facebook\FacebookDriver;
use App\Models\MessengerConfiguration;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;

class MessengerBotController extends Controller
{
    public function __invoke($id, Request $request)
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
        $botman->hears('(.*)', function (BotMan $bot) use($botId) {
            $bot->reply(ButtonTemplate::create('Do you want to know more about BotMan?')
                ->addButton(ElementButton::create('Tell me more')
                    ->type('postback')
                    ->payload('tellmemore')
                )
                ->addButton(ElementButton::create('Show me the docs')
                    ->url('http://botman.io/')
                )
            );
        });

        // Start listening
        $botman->listen();
    }
}
