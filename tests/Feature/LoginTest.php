<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'),[
            'email' => $user->email,
            'password' => 'password' // Factory password
        ])->assertOk();

        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $this->postJson(route('login'),[
            'email' => 'incorrect@mail.com',
            'password' => 'wrongpass'
        ])->assertUnauthorized();
    }
}
