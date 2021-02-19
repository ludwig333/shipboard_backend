<?php


namespace App\Utilities;


use App\Models\CardGroup;
use App\Models\Message;

class CardMaker {
    public function make(CardGroup $cardGroup) {
        $cardString = "\t\t";
        $cardString = $cardString.
            "if ( \$platform == \"telegram\" || \$platform == \"slack\") {\n"
            .$this->getTelegramCardElement($cardGroup)
            ."\t\t}\n"
            ."\t\telse if ( \$platform == \"facebook\") {\n"
            .$this->getMessengerCardElement($cardGroup)
            ."\t\t}\n"
        ;

        return $cardString;

    }

    public function makeCardQuestion($cardGroup, $nextMessage) {
        $message = Message::where('id', $nextMessage)->first();
        if($message) {
            $flowClass = str_replace("-", "", $message->uuid);
            $className = 'M'.$flowClass;

            $cardString = "\t\t";
            $cardString = $cardString.
                "if ( \$platform == \"telegram\" || \$platform == \"slack\") {\n"
                .$this->getTelegramCardElement($cardGroup)
                ."\t\t}\n"
                ."\t\telse if ( \$platform == \"facebook\") {\n"
                .$this->getMessengerCardElement($cardGroup, $className)
                ."\t\t}\n"
            ;

            return $cardString;
        }
    }

    private function getMessengerCardElement($cardGroup, $nextClass=null) {
        $cardsElement = "\t\t";
        $cards = $cardGroup->cards;
        foreach($cards as $card) {
            $cardsElement = $cardsElement . $this->getSingleCardElement($card, "facebook");
        }
        $buttons = $cardGroup->getChildButtons();
        $count = 1;
        $conditions = "";

        foreach($buttons as $button) {
            $buttonId = "BV".str_replace("-", "", $button->uuid);
            $next = $button->leads_to_message;
            $message = Message::where('id', $next)->first();
            if($message) {
                $messageUUID = "M".str_replace("-", "", $message->uuid);
                if ($count == 1){
                    $conditions = $conditions
                        . "if(\$selectedValue == '$buttonId') {\n"
                        . "\t\t\t\t\t\$this->bot->startConversation(new $messageUUID(\$platformName));"
                        . "}\n";
                } else {
                    $conditions = $conditions
                        . "\t\t\t\telse if(\$selectedValue == '$buttonId') {\n"
                        . "\t\t\t\t\t\$this->bot->startConversation(new $messageUUID(\$platformName));"
                        . "}\n";
                }
            }
            $count++;
        }
        //If its the last card so for next step
        $nextMessageString = "";
        if($nextClass) {
            $nextMessageString = " \$this->bot->startConversation(new $nextClass(\$this->platform));";
        }
        return  "\t\t\t\$question = GenericTemplate::create()\n"
                ."\t\t\t\t->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)\n"
                ."\t\t\t\t->addElements([\n"
                   .$cardsElement
                ."\t\t\t\t]);\n"

        ."\$this->ask(\$question, function (Answer \$answer) {\n"
            ."if(\$answer->isInteractiveMessageReply()) {\n"
                ."\$selectedValue = \$answer->getValue();\n"
            ."$conditions"
            ."} else {\n"
            ."$nextMessageString}"
        ."});\n";
    }

    private function getTelegramCardElement($cardGroup) {
        $cardsElement = "\t\t";
        $cards = $cardGroup->cards;
        foreach($cards as $card) {
            $cardsElement = $cardsElement . $this->getSingleCardElement($card, "telegram");
        }
        return $cardsElement;
    }

    private function getSingleCardElement($card, $type) {
        $url = $card->getImageUrl();
        $imageUrl = $url ? $url : "https://botman.io/img/logo.png";
        $text = $card->title . '<br>' . $card->body;
        $buttons = $card->buttons();
        if ( $type == "telegram" || $type == "slack") {
            $cardString =  "\t\t\t// Create attachment\n"
                ."\t\t\t\$attachment = new Image('$imageUrl');\n"
                ."\t\t\t// Build message object\n"
                ."\t\t\t\$message = OutgoingMessage::create('$text')\n"
                ."\t\t\t\t->withAttachment(\$attachment);\n\n"
                ."\t\t\t// Reply message object\n"
                ."\t\t\t\$this->bot->reply(\$message);\n";

            if($buttons->count() > 0) {
                $textButtons = (new ButtonMaker())->make($buttons);
                $telegramButtonElement = $textButtons["telegram"];
                $buttonConditions = $textButtons["conditions"];
                $cardString = $cardString.
                "\t\t\t\$question = Question::create(\"----------------------------------------------------------\")\n"
                    ."\t\t\t\t->addButtons(["
                       ."\t\t\t$telegramButtonElement"
                ."\t\t\t]);\n"
                ."\t\t\$this->ask(\$question, function (Answer \$answer) {\n"
                ."\t\t\tif(\$answer->isInteractiveMessageReply()) {\n"
                ."\t\t\t\t\$selectedValue = \$answer->getValue();\n"
                ."\t\t\t\t$buttonConditions"
                ."\t\t\t}\n"
                 ."\t\t\t});\n";
            }

            return $cardString;
        }
        else if ($type == "facebook") {
            $cardString =
                "\t\t\tElement::create('$card->title')\n"
                ."\t\t\t\t\t\t->subtitle('$card->body')\n"
                ."\t\t\t\t\t\t->image('$imageUrl')\n";
            if($buttons->count() > 0)
            {
                $textButtons = (new ButtonMaker())->make($buttons);
                $messengerButtonElement = $textButtons["messenger"];
                $cardString = $cardString.$messengerButtonElement;
            }


            return $cardString.",";
        }
    }
}
