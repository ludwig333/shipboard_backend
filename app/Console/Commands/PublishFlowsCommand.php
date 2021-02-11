<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Flow;
use App\Constants\BuilderContentType;
use App\Models\Text;
use App\Models\Message;
use App\Models\MessengerConfiguration;
use App\Constants\PlatformType;
use App\Models\TelegramConfiguration;
use App\Models\SlackConfiguration;

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
                $configurations = $bot->configurations;
                $this->publishBotClasses($user, $bot, $flow, $messages);
//                foreach($configurations as $configuration) {
//                    $connection = $configuration->connectable_type;
//                    $platform = $this->getPlatform($connection);
//                    $this->publishBotClasses($user, $bot, $flow, $messages, $platform);
//                }
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
        $flowId = $flow->id;
        $flowFolder = $this->getPath(app_path().'/Http/Controllers/Bot/UserBots/U'.$userId.'/B'.$botId.'/F'.$flowId);
        $nameSpace =  "namespace App\Http\Controllers\Bot\UserBots\\U".$userId."\\B".$botId."\\F".$flowId.";\n\n";

        foreach($messages as $message) {
            $className = "M" . str_replace("-", "", $message->uuid);
            //Create a message class file
            $messageClassFile = fopen($flowFolder.'/'.$className.'.php', 'w')
            or die("Unable to open class!");

            $botLogic = $this->getBotLogic($message);
            $contents =
                "<?php\n"
                .$nameSpace
                ."use BotMan\BotMan\Messages\Conversations\Conversation;\n"
                ."use BotMan\BotMan\Messages\Incoming\Answer;\n"
                ."use BotMan\BotMan\Messages\Outgoing\Question;\n"
                ."use BotMan\BotMan\Messages\Outgoing\Actions\Button;\n\n"

                ."class $className extends Conversation {\n"
                ."\tpublic function run() {\n"
                .$botLogic
                ."\t}\n"
                ."}";
            fwrite($messageClassFile, trim($contents));

            fclose($messageClassFile);
        }
    }

    private function getBotLogic($message) {
       try {
           $contents = $message->contents;
           $methods = "\n";
           foreach($contents as $content) {
               if($content->content_type == Text::class) {
                   $text = $content->child;
                   $body = $text->body;
                   $buttons = $text->buttons();
                   if($buttons->count() > 0) {
                       $textButtons = $this->getButtons($buttons);
                       $buttonArrays = $textButtons["array"];
                       $buttonConditions = $textButtons["conditions"];
                       $methods = $methods
                        ."\t\t\$question = Question::create('". str_replace(array("\r","\n"),"",nl2br($body))."')\n"
                        ."\t\t\t->addButtons(["
                        ."\t\t\t\t$buttonArrays"
                        ."]);\n"
                        ."\t\t\$this->ask(\$question, function (Answer \$answer) {\n"
                        ."\t\t\tif(\$answer->isInteractiveMessageReply()) {\n"
                        ."\t\t\t\t\$selectedValue = \$answer->getValue();\n"
                        ."\t\t\t\t$buttonConditions"
                        ."\t\t\t}\n"
                        ."\t\t});\n";
                   } else {
                       $methods = $methods . "\t\t\$this->say('".str_replace(array("\r","\n"),"",nl2br($body))."');\n";
                   }
               }
           }
           return $methods;
       } catch (\Exception $exception) {
           dd($exception->getMessage());
       }
    }

    public function getButtons($buttons) {

        $buttonText = "";
        $conditions = "";
        $count = 1;
        foreach($buttons as $button) {
            $buttonName = $button->name;
            $buttonId = "BV".str_replace("-", "", $button->uuid);
            $message = Message::where('id', $button->leads_to_message)->first();
            $messageUUID = "M".str_replace("-", "", $message->uuid);
            $buttonText = $buttonText."\tButton::create('".$buttonName."')->value('".$buttonId."'),";
            if($count == 1) {
                $conditions = $conditions
                    ."if(\$selectedValue == '$buttonId') {\n"
                    ."\t\t\t\t\t\$this->bot->startConversation(new $messageUUID);"
                    ."}\n";
            } else  {
                $conditions = $conditions
                    ."\t\t\t\telse if(\$selectedValue == '$buttonId') {\n"
                    ."\t\t\t\t\t\$this->bot->startConversation(new $messageUUID);"
                    ."}\n";
            }
            $count++;
        }
        return [
            'array' => $buttonText,
            'conditions' => $conditions
        ];
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
