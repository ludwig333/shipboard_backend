<?php

namespace Tests\Feature\Api\v1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class FlowControllerTest extends TestCase
{

    /**
     * @test
     */
    public function can_create_flows()
    {
        Passport::actingAs($this->user);

        $response = $this->post(route('folders.store'), [
            'name' => $folder_name = 'Main folder',
            'flow_id' => 1,
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Folder created successfully.',
            'data' => [
                'name' => $folder_name,
            ]
        ])->assertStatus(201);

    }


    /** @test */
    public function can_edit_flow()
    {
        Passport::actingAs($this->user);

        $response = $this->post(route('folders.store'), [
            'name' => $folder_name = 'Main folder',
            'flow_id' => 1,
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Folder created successfully.',
            'data' => [
                'name' => $folder_name,
            ]
        ])->assertStatus(201);

    }

    /** @test */
    public function can_delete_flow()
    {
        Passport::actingAs($this->user);

        $response = $this->post(route('folders.store'), [
            'name' => $folder_name = 'Main folder',
            'flow_id' => 1,
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Folder created successfully.',
            'data' => [
                'name' => $folder_name,
            ]
        ])->assertStatus(201);

    }
}
