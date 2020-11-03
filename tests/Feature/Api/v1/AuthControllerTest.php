<?php


namespace Tests\Feature\Api\v1;


use Tests\TestCase;

class AuthControllerTest extends TestCase {

    /**
     * @test
     */
    public function can_register_user() {
        $response = $this->post(route('register'), [
            'fname' => $fname = 'John',
            'lname' => $lname = 'Doe',
            'email' => $email = 'john.doe@email.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123'
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'fname' => $fname,
                'lname' => $lname
            ]
        ])->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => $fname,
            'last_name' => $lname,
            'email' => $email
        ]);
    }

    /**
     * @test
     */
    public function can_login_user()
    {
        $response = $this->post(route('login'), [
            'email' => 'jane.doe@email.com',
            'password' => 'Password@123',
        ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Logged in successfully.',
        ])->assertStatus(200);
    }
}
