<?php


namespace App\Utilities;


use App\Models\Message;
use App\Models\Text;
use App\Models\CardGroup;
use App\Models\Image;

class BotMaker {
    public function make(Message $message)
    {
        try{
            $contents = $message->contents;
            $methods = "\n";
            $textMaker = new TextMaker();
            $imageMaker = new ImageMaker();
            $cardMaker = new CardMaker();

            foreach ($contents as $content) {
                if ($content->content_type == Text::class) {
                    $text = $content->child;
                    $methods = $methods . $textMaker->make($text);
                }
                else if ($content->content_type == Image::class) {
                    $image = $content->child;
                    $methods = $methods . $imageMaker->make($image);
                }
                else if ($content->content_type == CardGroup::class) {
                    $cardGroup = $content->child;
                    $methods = $methods . $cardMaker->make($cardGroup);
                }
            }
            return $methods;
        } catch (\Exception $exception) {
            $this->error($exception->getLine());
            $this->error($exception->getFile());
            $this->error($exception->getMessage());
        }
    }
}
