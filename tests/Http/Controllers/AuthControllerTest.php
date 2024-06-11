<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful user registration.
     *
     * @return void
     */
    public function testSuccessfulUserRegistration()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully created user!',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test user registration with invalid input.
     *
     * @return void
     */
    public function testUserRegistrationWithInvalidInput()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Provide proper details',
            ]);
    }

    /**
     * Test user registration with exception.
     *
     * @return void
     */
    public function testUserRegistrationWithException()
    {
        // Mock an exception
        $this->mock(User::class, function ($mock) {
            $mock->shouldReceive('save')->andThrow(new \Exception('Something went wrong'));
        });

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Something went wrong',
            ]);
    }
}
