<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Flow;
use App\Models\MessengerConfiguration;
use App\Constants\PlatformType;
use App\Models\TelegramConfiguration;
use App\Models\SlackConfiguration;
use App\Utilities\BotMaker;

class PublishFlowsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:flow {flow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the flow into classes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $flow = Flow::where('uuid', $this->argument('flow'))->firstOrFail();
            if ($flow) {
                $bot= $flow->bot;
                $user = $bot->user;
                $messages = $flow->messages;
                $this->publishBotClasses($user, $bot, $flow, $messages);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->error($exception->getMessage());
            $this->error($exception->getLine());
        }

    }

    private function publishBotClasses($user, $bot, $flow, $messages) {
        $userId = $user->id;
        $botId = $bot->id;
        $flowFolder = $this->getPath(app_path().'/Http/Controllers/Bot/UserBots/U'.$userId.'/B'.$botId);
        $nameSpace =  "namespace App\Http\Controllers\Bot\UserBots\\U".$userId."\\B".$botId.";\n\n";

        foreach($messages as $message) {
            $className = "M" . str_replace("-", "", $message->uuid);
            //Create a message class file
            $messageClassFile = fopen($flowFolder.'/'.$className.'.php', 'w')
            or die("Unable to open class!");

            $botLogic = (new BotMaker())->make($message);
            $contents =
                "<?php\n"
                .$nameSpace
                ."use BotMan\BotMan\Messages\Conversations\Conversation;\n"
                ."use BotMan\BotMan\Messages\Incoming\Answer;\n"
                ."use BotMan\BotMan\Messages\Outgoing\Question;\n"
                ."use BotMan\BotMan\Messages\Outgoing\Actions\Button;\n"
                ."use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;\n"
                ."use BotMan\Drivers\Facebook\Extensions\ElementButton;\n"
                ."use BotMan\Drivers\Facebook\Extensions\GenericTemplate;\n"
                ."use BotMan\Drivers\Facebook\Extensions\Element;\n"
                ."use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;\n"
                ."use BotMan\BotMan\Messages\Attachments\Image;\n"

                ."class $className extends Conversation {\n"
                ."\tpublic function run() {\n"
                .$botLogic
                ."\t}\n"
                ."}\n\n";
            fwrite($messageClassFile, trim($contents));

            fclose($messageClassFile);
        }
    }

    private function getPlatform($connection) {
        if ($connection == MessengerConfiguration::class) {
            return PlatformType::MESSENGER;
        } else if ($connection == TelegramConfiguration::class) {
            return PlatformType::TELEGRAM;
        } else if ($connection == SlackConfiguration::class) {
            return PlatformType::SLACK;
        }
    }

    private function getPath($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }

}
