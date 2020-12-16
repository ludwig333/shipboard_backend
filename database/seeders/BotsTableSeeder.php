<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bot;

class BotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bot::create([
            'name' => 'hello bot',
            'user_id' => 1
        ]);
    }
}
