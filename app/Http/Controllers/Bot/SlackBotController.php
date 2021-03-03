<?php

namespace App\Http\Controllers\Bot;


use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;
use App\Models\SlackConfiguration;
use App\Models\Bot;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\BotMan;

class SlackBotController extends Controller
{
    public function __invoke($id, Request $request)
    {

        if($request->get('challenge')) {
            return [
                'challenge' => $request->get('challenge')
            ];
        }

        $myBot = Bot::where('uuid', $id)->first();
        $connection = SlackConfiguration::where('bot_id', $myBot->id)->first();
        $config = [
            'slack' => [
                'token' => $connection->access_token
            ]
        ];

        if($connection && $connection->connect_status == 1)
        {
            // Load the driver(s) you want to use
            DriverManager::loadDriver(\BotMan\Drivers\Slack\SlackDriver::class);
            // Create an instance
            $botman = BotManFactory::create($config);


            $botman->hears('.*(Hi|Hello|Start).*', function (Botman $bot) use ($myBot) {
                $firstFlow = $myBot->flows->first();
                if ($firstFlow)
                {
                    $firstMessage = $firstFlow->messages->first();
                    if ($firstMessage)
                    {
                        $userId = $myBot->user->id;
                        $botId = $myBot->id;
                        $flowClass = str_replace("-", "", $firstMessage->uuid);
                        $className = 'App\Http\Controllers\Bot\UserBots\U' . $userId . '\B' . $botId . '\M' . $flowClass;
                        $bot->startConversation(new $className);
                    }
                }
            });

            // Start listening
            $botman->listen();
        }
    }

}
