<?php


namespace App\Utilities;


use App\Models\Message;
use App\Models\Text;

class ButtonMaker {
   public function make($buttons){
        $buttonText = "";
        $conditions = "";
        $count = 1;
        foreach($buttons as $button) {
            $buttonName = $button->name;
            $buttonId = "BV".str_replace("-", "", $button->uuid);
            $message = Message::where('id', $button->leads_to_message)->first();
            if($message) {
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
        }

        return [
            'array' => $buttonText,
            'conditions' => $conditions
        ];
    }
}
