<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessengerConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messenger_configurations', function (Blueprint $table) {
            $table->id();
            $table->longText('access_token');
            $table->longText('app_secret')->nullable();
            $table->text('verification_code');
            $table->tinyInteger('connect_status')->default(0);
            $table->foreignId('bot_id')->constrained('bots')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messenger_configurations');
    }
}
