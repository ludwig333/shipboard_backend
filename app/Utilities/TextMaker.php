<?php


namespace App\Utilities;


use App\Models\Text;

class TextMaker {
    public function make(Text $text) {
        $body = $text->body;
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
            return "\t\t\$this->say('".str_replace(array("\r","\n"),"",nl2br($body))."');\n";
        }
    }
}
