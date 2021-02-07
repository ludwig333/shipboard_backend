<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\ButtonType;

class AddColumnsInButtonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buttons', function (Blueprint $table) {
            $table->string('name');
            $table->uuid('uuid')->unique();
            $table->string('type')->default(ButtonType::DEFAULT);
            $table->string('parent');
            $table->string('parent_id');
            $table->string('leads_to_message')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buttons', function (Blueprint $table) {
            $table->dropColumn(['name', 'uuid', 'type', 'parent_id', 'parent', 'leads_to_message']);
        });
    }
}
