<?php


namespace App\Utilities;


use App\Models\Text;
use App\Models\Message;

class TextMaker {
    public function make(Text $text) {
        $body = str_replace("'", "\'", $text->body);
        $buttons = $text->buttons();
        if($buttons->count() > 0) {
            $textButtons = (new ButtonMaker())->make($buttons);
            $telegramButtonElement = $textButtons["telegram"];
            $messengerButtonElement = $textButtons["messenger"];
            $buttonConditions = $textButtons["conditions"];

            return
                "\t\t\t\$question=null;\n"
                ."\t\tif (\$this->platform === \"telegram\") {\n"
                ."\t\t\t\$question = Question::create('". str_replace(array("\r","\n"),"",nl2br($body))."')\n"
                ."\t\t\t\t->addButtons([\n"
                ."\t\t\t\t\t$telegramButtonElement"
                ."\t\t\t]);\n"
                ."\t\t}\n"
                ."\t\telse if (\$this->platform ===\"facebook\") {\n"
                ."\t\t\t\$question = ButtonTemplate::create('". str_replace(array("\r","\n"),"",nl2br($body))."')\n"
                ."\t\t\t\t$messengerButtonElement\t\t\t\t;\n"
                ."\t\t\t\t}\n\n"
                ."\t\t\$this->ask(\$question, function (Answer \$answer) {\n"
                ."\t\t\t\$platformName = strtolower(\$this->bot->getDriver()->getName());\n"
                ."\t\t\tif(\$answer->isInteractiveMessageReply()) {\n"
                ."\t\t\t\t\$selectedValue = \$answer->getValue();\n"
                ."\t\t\t\t$buttonConditions"
                ."\t\t\t}\n"
                ."\t\t});\n";
        } else {
            return "\t\t\$this->say('$body');\n";
        }
    }

    public function makeTextQuestion(Text $text, $nextMessage) {
        $message = Message::where('id', $nextMessage)->first();
        if($message) {
//            $flow = $message->flow;
//            $bot = $flow->bot;
//            $botId = $bot->id;
//            $userId = $bot->user->id;
            $flowClass = str_replace("-", "", $message->uuid);
            $className = 'M'.$flowClass;

            $body = str_replace("'", "\'", $text->body);
            return "\t\t\$this->ask('$body', function (Answer \$response) {\n"
                ."\t\t\t\$this->bot->startConversation(new $className(\$this->platform));\n"
                ."\t\t});";
        }
    }
}
