<?php


namespace App\Utilities;


use App\Models\Image;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;

class ImageMaker {
    public function make(Image $image) {
        $url = $image->getImageUrl();
        $imageUrl = $url ? $url: "https://botman.io/img/logo.png";
        $imageString = "\t\t";
        $imageString = $imageString.
            "if ( \$this->platform == \"telegram\") {\n"
                .$this->getTelegramImageElement($imageUrl)
            ."\t\t}\n"
            ."\t\telse if ( \$this->platform == \"facebook\") {\n"
                .$this->getMessengerImageElement($imageUrl)
            ."\t\t}\n"
        ;

        return $imageString;
    }

    private function getMessengerImageElement($imageUrl) {
        return
            "\t\t\t\$this->bot->reply(GenericTemplate::create()\n"
            ."\t\t\t\t->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)\n"
            ."\t\t\t\t->addElements([\n"
            ."\t\t\t\t\tElement::create()\n"
            ."\t\t\t\t\t\t->image('$imageUrl')\n"
            ."\t\t\t\t])\n"
            ."\t\t\t);\n";
    }

    private function getTelegramImageElement($imageUrl) {
         return "\t\t\t// Create attachment\n"
            ."\t\t\t\$attachment = new Image('$imageUrl');\n"
            ."\t\t\t// Build message object\n"
            ."\t\t\t\$message = OutgoingMessage::create('')\n"
            ."\t\t\t\t->withAttachment(\$attachment);\n\n"
            ."\t\t\t// Reply message object\n"
            ."\t\t\t\$this->bot->reply(\$message);\n";
    }
}
