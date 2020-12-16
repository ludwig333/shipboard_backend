<?php


namespace Tests\Feature\Api\v1;


use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\Folder;

class FolderControllerTest extends TestCase
{
    /** @test */
    public function can_create_folder()
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
    public function can_edit_folder()
    {
        Passport::actingAs($this->user);

        $response = $this->patch(route('folders.update', [ 'folder' => 1 ]), [
            'name' => $name ='Updated name'
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Folder updated successfully.',
            'data' => [
                'name' => $name
            ]
        ])->assertStatus(202);

    }

    /** @test */
    public function can_delete_folder()
    {
        Passport::actingAs($this->user);

        $folder = Folder::where('id', 1)->first();

        $response = $this->delete(route('folders.destroy', [ 'folder' => $folder->id ]));

        $response->assertStatus(204);

    }

    public function can_list_sub_folder()
    {
        Passport::actingAs($this->user);

        $folder = Folder::where('id', 1)->first();

        $response = $this->get(route('folders.sub-folders', [ 'folder' => $folder->id]));

        $response->assertStatus(200);

    }
}
