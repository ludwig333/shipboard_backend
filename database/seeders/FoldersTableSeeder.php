<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Folder;

class FoldersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Folder::create([
            'name' => 'Main folder',
            'flow_id' => 1
        ]);

        Folder::create([
            'name' => 'Sub Folder',
            'flow_id' => 1,
            'parent_folder_id' => 1
        ]);

        Folder::create([
            'name' => 'Sub Folder',
            'flow_id' => 1,
            'parent_folder_id' => 2
        ]);
    }
}
