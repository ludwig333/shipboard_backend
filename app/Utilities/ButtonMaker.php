<?php


namespace App\Utilities;


use App\Models\Message;
use App\Constants\ButtonType;

class ButtonMaker {
   public function make($buttons){
        $telegramButton = "";
        $messengerButton = "";
        $conditions = "";
        $count = 1;
        foreach($buttons as $button) {
            $buttonName = $button->name;
            $buttonId = "BV".str_replace("-", "", $button->uuid);
            $next = $button->leads_to_message;
            $message = Message::where('id', $next)->first();
            if($message) {
                $messageUUID = "M".str_replace("-", "", $message->uuid);
                $telegramButton = $telegramButton.$this->telegramButtonElement($buttonName, $buttonId, $button->type, $next);
                $messengerButton = $messengerButton.$this->messengerButtonElement($buttonName, $buttonId, $button->type, $next);

                if($count == 1) {
                    $conditions = $conditions
                        ."if(\$selectedValue == '$buttonId') {\n"
                        ."\t\t\t\t\t\$this->bot->startConversation(new $messageUUID(\$platformName));"
                        ."}\n";
                } else  {
                    $conditions = $conditions
                        ."\t\t\t\telse if(\$selectedValue == '$buttonId') {\n"
                        ."\t\t\t\t\t\$this->bot->startConversation(new $messageUUID(\$platformName));"
                        ."}\n";
                }
                $count++;
            }
        }

        return [
            'telegram' => $telegramButton,
            'messenger' => $messengerButton,
            'conditions' => $conditions
        ];
    }

    private function messengerButtonElement($buttonName, $buttonId, $type, $next) {
       if($type == ButtonType::DEFAULT) {
           return
               "\t\t\t\t->addButton(ElementButton::create('$buttonName')\n"
               ."\t\t\t\t\t->type('postback')\n"
               ."\t\t\t\t\t->payload('$buttonId')\n"
               ."\t\t\t\t)\n";
       }
       else if ($type == ButtonType::URL) {
           return
               "->addButton(ElementButton::create($buttonName)\n"
               ."\t->url($next)\n"
               .")";
       }
    }



    private function telegramButtonElement($buttonName, $buttonId, $type, $next) {
        if($type == ButtonType::DEFAULT) {
            return
                "Button::create('$buttonName')->value('$buttonId'),\n";
        }
        else if ($type == ButtonType::URL) {
            return
                "\tButton::create('$buttonName')->url('$next')->value('go'),";
        }
    }
}
