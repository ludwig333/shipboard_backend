<?php


namespace Tests\Feature\Api\v1;


use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\Folder;
use App\Models\Bot;

class BotControllerTest extends TestCase
{
    /** @test */
    public function can_list_bots()
    {
        Passport::actingAs($this->user);

        $response = $this->get(route('bots.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_bot()
    {
        Passport::actingAs($this->user);

        $response = $this->post(route('bots.store'), [
            'name' => $bot_name = 'Test Bot',
            'user_id' => $this->user->id,
        ]);

        $response->assertStatus(201);

    }

    /** @test */
    public function can_edit_bot()
    {
        Passport::actingAs($this->user);
        $bot = Bot::first();

        $response = $this->patch(route('bots.update', [ 'bot' => $bot->uuid ]), [
            'name' => $bot_name ='Update Bot'
        ]);

        $response->assertStatus(202);

    }

    /** @test */
    public function can_delete_bot()
    {
        Passport::actingAs($this->user);

        $bot = Bot::first();

        $response = $this->delete(route('bots.destroy', [ 'bot' => $bot->uuid ]));

        $response->assertStatus(204);

    }

    /** @test */
    public function can_get_bot_detail()
    {
        Passport::actingAs($this->user);
        $bot = Bot::first();

        $response = $this->get(route('bots.show', ['bot' => $bot->uuid]));

        $response->assertStatus(200);
    }
}
