<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->longText('access_token');
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
        Schema::dropIfExists('telegram_configurations');
    }
}
