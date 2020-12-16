<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flow;

class FlowsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Flow::create([
            'name' => 'test folder',
            'user_id' => 1,
        ]);
    }
}
