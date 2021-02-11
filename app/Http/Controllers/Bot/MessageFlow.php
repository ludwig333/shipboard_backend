<?php


namespace App\Http\Controllers\Bot;


use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class MessageFlow extends Conversation {
//    private $variableArray = [];
//    private $flow = null;
//
//    public function __construct(Flow $flow)
//    {
//        $this->flow = $flow;
//    }
//
//    public function run()
//    {
//        $messages = $this->flow->messages;
//        foreach($messages as $message) {
//            $contents = $message->contents;
//            foreach($contents as $content) {
//                $this->ask($content->first()->child->body, function (Answer $answer) {
//                    $this->say('Nice to meet you');
//                });
//            }
//        }
//    }
    protected $firstname;

    protected $email;

    public function askFirstname()
    {
        $question = Question::create('Welcome to chat bot. Do you want to continue?')
            ->fallback('Unable to create a new database')
            ->addButtons([
                Button::create('Of course')->value('yes'),
                Button::create('Hell no!')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
                $selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'

                if($selectedValue == 'yes') {
                   $this->nextMessage('you have continued');
                }
            }
        });
    }

    public function nextMessage($messageId)
    {

    }

    public function run()
    {
        // This will be called immediately
        $this->askFirstname();
    }
}
