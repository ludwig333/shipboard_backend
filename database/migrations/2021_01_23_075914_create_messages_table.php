<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\MessageType;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->bigInteger('flow_id')->unsigned()->index();
            $table->bigInteger('next_message_id')->default(0);
            $table->double('position_x');
            $table->double('position_y');
            $table->integer('is_start')->default(0);
            $table->string("type")->default(MessageType::DEFAULT);
            $table->timestamps();

            $table->foreign('flow_id')
                ->references('id')
                ->on('flows')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
