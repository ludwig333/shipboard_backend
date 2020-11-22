<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->seed([
            'UsersTableSeeder',
            'FlowsTableSeeder',
            'FoldersTableSeeder'
        ]);

        $this->user = User::first();
    }
}
