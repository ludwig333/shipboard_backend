<?php


namespace App\Utilities;


use App\Models\Message;
use App\Models\Text;
use App\Models\CardGroup;
use App\Models\Image;
use App\Constants\MessageType;
use Illuminate\Support\Facades\Log;

class BotMaker {
    public function make(Message $message)
    {
        try{
            if($message->type == MessageType::DEFAULT) {
                return $this->getMessageMethods($message);
            }
            else if ($message->type == MessageType::FLOW ) {
                $nextMessage = Message::where('id', $message->next_message_id)->first();
                if($nextMessage) {
                    $className = str_replace("-", "", $nextMessage->uuid);
                    $flow = $nextMessage->flow;
                    $bot = $flow->bot;
                    $botId = $bot->id;
                    $userId = $bot->user->id;
                    return "\t\t\$this->bot->startConversation(new \App\Http\Controllers\Bot\UserBots\U$userId\B$botId\M$className(\$this->platform));";
                }
            }

        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }

    private function getMessageMethods($message) {
        $contents = $message->contents;
        $methods = "";
        $textMaker = new TextMaker();
        $imageMaker = new ImageMaker();
        $cardMaker = new CardMaker();
        $totalContent = $contents->count();
        $count = 1;

        foreach ($contents as $content) {
            if ($content->content_type == Text::class) {
                $text = $content->child;
                if($message->next_message_id != 0 && $totalContent == $count) {
                    $methods = $methods . $textMaker->makeTextQuestion($text, $message->next_message_id);
                } else {
                    $methods = $methods. "\n" .$textMaker->make($text);
                }
            }
            else if ($content->content_type == Image::class) {
                $image = $content->child;
                if($message->next_message_id != 0 && $totalContent == $count) {
                    $methods = $methods . $imageMaker->makeImageQuestion($image, $message->next_message_id);
                } else {
                    $methods = $methods. "\n" .$imageMaker->make($image);
                }
            }
            else if ($content->content_type == CardGroup::class) {
                $cardGroup = $content->child;
                if($message->next_message_id != 0 && $totalContent == $count) {
                    $methods = $methods . $cardMaker->makeCardQuestion($cardGroup, $message->next_message_id);
                } else {
                    $methods = $methods. "\n" .$cardMaker->make($cardGroup);
                }
            }

            $count++;
        }

        return $methods;
    }

}
