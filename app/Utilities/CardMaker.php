<?php


namespace App\Utilities;


use App\Models\CardGroup;

class CardMaker {
    public function make(CardGroup $cardGroup) {
        $cardString = "\t\t";
        $cardString = $cardString.
            "if ( \$this->platform == \"telegram\") {\n"
            .$this->getTelegramCardElement($cardGroup)
            ."\t\t}\n"
            ."\t\telse if ( \$this->platform == \"facebook\") {\n"
            .$this->getMessengerCardElement($cardGroup)
            ."\t\t}\n"
        ;

        return $cardString;

    }

    private function getMessengerCardElement($cardGroup) {
        $cardsElement = "\t\t";
        $cards = $cardGroup->cards;
        foreach($cards as $card) {
            $cardsElement = $cardsElement . $this->getSingleCardElement($card, "facebook");
        }
        return "\t\t\t\$this->say(GenericTemplate::create()\n"
                ."\t\t\t\t->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)\n"
                ."\t\t\t\t->addElements([\n"
                   .$cardsElement
                ."\t\t\t\t])\n"
                ."\t\t\t);\n";
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
        if ( $type == "telegram") {
            return
                "\t\t\t// Create attachment\n"
                ."\t\t\t\$attachment = new Image('$imageUrl');\n"
                ."\t\t\t// Build message object\n"
                ."\t\t\t\$message = OutgoingMessage::create('$text')\n"
                ."\t\t\t\t->withAttachment(\$attachment);\n\n"
                ."\t\t\t// Reply message object\n"
                ."\t\t\t\$this->bot->reply(\$message);\n";
        }
        else if ($type == "facebook") {
            return
                "\t\t\tElement::create('$card->title')\n"
                ."\t\t\t\t\t\t->subtitle('$card->body')\n"
                ."\t\t\t\t\t\t->image('$imageUrl')\n"
                ."\t\t\t\t\t\t->addButton(ElementButton::create('visit')\n"
                ."\t\t\t\t\t\t->url('http://botman.io')\n"
                ."\t\t\t\t\t)\n"
                ."\t\t\t\t\t->addButton(ElementButton::create('tell me more')\n"
                ."\t\t\t\t\t\t->payload('tellmemore')\n"
                ."\t\t\t\t\t\t->type('postback')\n"
                ."\t\t\t\t\t),\n";
        }
    }
}
