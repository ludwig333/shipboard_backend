<?php


namespace App\Utilities;


use App\Models\Text;

class TextMaker {
    public function make(Text $text) {
        $body = $text->body;
        $buttons = $text->buttons();
        if($buttons->count() > 0) {
            $textButtons = (new ButtonMaker())->make($buttons);
            $buttonArrays = $textButtons["array"];
            $buttonConditions = $textButtons["conditions"];

            return "\t\t\$question = Question::create('". str_replace(array("\r","\n"),"",nl2br($body))."')\n"
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
            return "\t\t\$this->say('".str_replace(array("\r","\n"),"",nl2br($body))."');\n";
        }
    }
}
