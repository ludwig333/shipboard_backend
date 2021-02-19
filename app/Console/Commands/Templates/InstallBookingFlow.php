<?php

namespace App\Console\Commands\Templates;

use Illuminate\Console\Command;
use App\Models\Flow;
use App\Models\User;
use App\Models\Bot;
use App\Models\Message;
use App\Models\Text;
use App\Models\Content;
use App\Models\Button;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallBookingFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:booking-template {bot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the booking flow to given user on given bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Installing booking templates');
            $bot = Bot::findOrFail($this->argument('bot'));
            Log::info($bot->name);
            DB::beginTransaction();

            $flow = Flow::create([
                'name' => 'Booking Template '.time(),
                'bot_id' => $bot->id
            ]);
            $welcomeMessage = $this->getStartMessage($flow);
            $endMessage = $this->endMessage($flow);
            $bookedInformation = $this->bookedInfo($flow);
            $confirmBooking = $this->confirmBooking($flow, $bookedInformation, $endMessage);
            $askForInstructions = $this->askForInstruction($flow, $confirmBooking);
            $askForDropAddress = $this->askForDropAddress($flow, $askForInstructions);
            $askForPickUpAddress = $this->askforPickUpAddress($flow, $askForDropAddress);
            $askForPickUpTime = $this->askForPickUpTime($flow, $askForPickUpAddress);
            $askForPickUpDate = $this->askForPickUpDate($flow, $askForPickUpTime);
            $askForPhoneNumber = $this->askPhoneNumberMessage($flow, $askForPickUpDate);
            //Add confirm to welcome message
            $this->updateWelcomeMessage($welcomeMessage, $endMessage, $askForPhoneNumber);

            DB::commit();
            $this->info("Operation Completed");
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            $this->error($exception->getMessage());
        }
    }

    private function getStartMessage($flow) {
        $startMessage = Message::create([
            'name' => 'Welcome Message',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 500,
            'position_y' => -100,
        ]);

        $text = Text::create([
            'body' => 'Hey, nice to meet you!!',
            'height' => 29
        ]);
        Content::create([
            'message_id' => $startMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);
        $text = Text::create([
            'body' => 'I am here to book a taxi for you.',
            'height' => 29
        ]);
        Content::create([
            'message_id' => $startMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 2
        ]);
        return $startMessage;
    }

    private function updateWelcomeMessage($startMessage, $endMessage, $askPhoneNumberMessage) {
        $text = Text::create([
            'body' => 'Would you like to book a taxi now.',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $startMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 3
        ]);
        $buttonYes = Button::create([
            'name' => 'Yes',
            'type' => 'default',
            'parent' => Text::class,
            'parent_id' => $text->id,
            'leads_to_message' => $askPhoneNumberMessage->id
        ]);

        $buttonNo = Button::create([
            'name' => 'No',
            'type' => 'default',
            'parent' => Text::class,
            'parent_id' => $text->id,
            'leads_to_message' => $endMessage->id
        ]);
    }

    private function askPhoneNumberMessage($flow, $askDateMessage) {
        $askPhoneNumber = Message::create([
            'name' => 'Welcome Message',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 900,
            'position_y' => -100,
            'next_message_id' => $askDateMessage->id
        ]);

        $text = Text::create([
            'body' => 'Ok let\'s do it',
            'height' => 29
        ]);
        Content::create([
            'message_id' => $askPhoneNumber->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        $text = Text::create([
            'body' => 'What is your phone number?',
            'height' => 29
        ]);
        Content::create([
            'message_id' => $askPhoneNumber->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 2
        ]);
        return $askPhoneNumber;
    }

    private function askForPickUpDate($flow, $askTimeMessage) {
        $askDate = Message::create([
            'name' => 'Date',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 1350,
            'position_y' => -100,
            'next_message_id' => $askTimeMessage->id
        ]);

        $text = Text::create([
            'body' => 'When would you like us to pick up?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askDate->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        $text = Text::create([
            'body' => 'On which date?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askDate->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 2
        ]);
        return $askDate;
    }

    private function askForPickUpTime($flow, $pickUpAddressMessage){
        $askTime = Message::create([
            'name' => 'Time',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 1800,
            'position_y' => -100,
            'next_message_id' => $pickUpAddressMessage->id
        ]);

        $text = Text::create([
            'body' => 'At what time?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askTime->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        return $askTime;
    }

    private function askForPickUpAddress($flow, $dropAddressMessage){
        $asForPickAddress = Message::create([
            'name' => 'Pickup Address',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 1800,
            'position_y' => 320,
            'next_message_id' => $dropAddressMessage->id
        ]);

        $text = Text::create([
            'body' => 'Where do you want us to pick you up from?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $asForPickAddress->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        $text = Text::create([
            'body' => 'Provide your pickup address',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $asForPickAddress->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 2
        ]);
        return $asForPickAddress;
    }

    private function askForDropAddress($flow, $instructionMessage) {
        $askDropAddress = Message::create([
            'name' => 'Drop Address',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 1350,
            'position_y' => 320,
            'next_message_id' => $instructionMessage->id
        ]);

        $text = Text::create([
            'body' => 'Where do you want us to drop you?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askDropAddress->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        $text = Text::create([
            'body' => 'Provide your drop address',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askDropAddress->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 2
        ]);
        return $askDropAddress;
    }

    private function askForInstruction($flow, $confirmationMessage){
        $askForInstruction = Message::create([
            'name' => 'Special Instructions',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 900,
            'position_y' => 320,
            'next_message_id' => $confirmationMessage->id
        ]);

        $text = Text::create([
            'body' => 'Any special instructions for us?',
            'height' => 29
        ]);

        Content::create([
            'message_id' => $askForInstruction->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);
        return $askForInstruction;
    }

    private function confirmBooking($flow, $confirmedMessage, $endMessage){
        $bookInfoMessage = Message::create([
            'name' => 'Confirmation',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 900,
            'position_y' => 630,
        ]);

        $text = Text::create([
            'body' => 'Do you want to confirm your booking?',
            'height' => 46
        ]);

        Content::create([
            'message_id' => $bookInfoMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);

        Button::create([
            'name' => 'Yes',
            'type' => 'default',
            'parent' => Text::class,
            'parent_id' => $text->id,
            'leads_to_message' => $confirmedMessage->id
        ]);

        Button::create([
            'name' => 'No',
            'type' => 'default',
            'parent' => Text::class,
            'parent_id' => $text->id,
            'leads_to_message' => $endMessage->id
        ]);

        return $bookInfoMessage;
    }

    private function bookedInfo($flow){
        $bookedMessage = Message::create([
            'name' => 'Confirmed',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 1600,
            'position_y' => 700,
        ]);

        $text = Text::create([
            'body' => 'Your booking has been accepted. Meet you with our taxi',
            'height' => 46
        ]);

        Content::create([
            'message_id' => $bookedMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);
        return $bookedMessage;
    }

    private function endMessage($flow) {
        $endMessage = Message::create([
            'name' => 'Bye',
            'type' => 'default',
            'flow_id' =>  $flow->id,
            'position_x' => 450,
            'position_y' => 450,
        ]);

        $text = Text::create([
            'body' => 'Thank you! \n See you if you need taxi',
            'height' => 46
        ]);

        Content::create([
            'message_id' => $endMessage->id,
            'content_type' => Text::class,
            'content_id' => $text->id,
            'index' => 1
        ]);
        return $endMessage;
    }

}
