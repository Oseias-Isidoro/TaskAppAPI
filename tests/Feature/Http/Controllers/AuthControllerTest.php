<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider userRegisterRequests
     */
    public function test_user_register_validation($data)
    {
        User::factory()->create([
            'email' => 'usertest2@gmail.com'
        ]);

        $response = $this->post('api/register', $data);

        $response->assertStatus(422);
        $this->assertDatabaseCount(User::class, 1);
    }

    public function test_register_user()
    {
        $response = $this->post('api/register', [
            'name' => 'user',
            'email' => 'usertest@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseHas(User::class, [
            'name' => 'user',
            'email' => 'usertest@gmail.com',
        ]);
    }

    public static function userRegisterRequests(): array
    {
        return [
            'without name' => [
                ['email' => 'usertest@gmail.com', 'password' => 'password']
            ],
            'without email' => [
                ['name' => 'user', 'password' => 'password']
            ],
            'without password' => [
                ['name' => 'user', 'email' => 'usertest@gmail.com']
            ],
            'with email that exist' => [
                ['name' => 'user', 'email' => 'usertest2@gmail.com', 'password' => 'password']
            ]
        ];
    }

    public function test_user_login()
    {
        User::factory()->create([
            'email' => 'usertest@gmail.com',
            'password' => 'password'
        ]);

        $response = $this->post('api/login', [
            'email' => 'usertest@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }


    /**
     * @dataProvider userLoginRequests
     */
    public function test_user_login_validation($data)
    {
        User::factory()->create([
            'email' => 'usertest@gmail.com',
            'password' => 'password'
        ]);

        $response = $this->post('api/register', $data);

        $response->assertStatus(422);
    }

    public static function userLoginRequests(): array
    {
        return [
            'without email' => [
                ['password' => 'password']
            ],
            'without password' => [
                ['email' => 'usertest@gmail.com']
            ],
            'without data' => [
                []
            ]
        ];
    }
}
