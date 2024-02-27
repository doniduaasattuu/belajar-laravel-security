<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testAuth()
    {
        $this->seed(UserSeeder::class);

        $login = Auth::attempt([
            'email' => 'doni@localhost',
            'password' => 'rahasia'
        ]);

        self::assertTrue($login);
        $user = Auth::user();
        self::assertNotNull($user);
        self::assertEquals('doni@localhost', $user->email);
        Log::info(json_encode($user, JSON_PRETTY_PRINT));
    }

    public function testGuest()
    {
        $user = Auth::user();
        self::assertNull($user);
    }

    // SESSION
    public function testLogin()
    {
        $this->seed(UserSeeder::class);

        $this->get('/users/login?email=doni@localhost&password=rahasia')
            ->assertRedirect('/users/current');

        $this->get('/users/login?email=wrong&password=wrong')
            ->assertSeeText('Wrong credentials');
    }

    public function testCurrent()
    {
        $this->seed(UserSeeder::class);

        $this->get('/users/current')
            ->assertStatus(302)
            ->assertRedirectToRoute('login');

        $user = User::where('email', 'doni@localhost')->first();
        $this->actingAs($user)
            ->get('/users/current')
            ->assertSeeText('Hello Doni Darmawan');
    }

    public function testTokenGuard()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Accept' => 'application/json',
        ])
            ->assertStatus(401);


        $this
            ->get('/api/users/current', [
                'Accept' => 'application/json',
                'API-Key' => 'secret'
            ])
            ->assertSeeText('Hello Doni Darmawan');
    }

    public function testUserProvider()
    {
        $this->seed(UserSeeder::class);

        $this->get('/simple-api/users/current', [
            'Accept' => 'application/json',
        ])
            ->assertStatus(401);


        $this
            ->get('/simple-api/users/current', [
                'Accept' => 'application/json',
                'API-Key' => 'secret'
            ])
            ->assertSeeText('Hello Khannedy');

        $this
            ->get('/simple-api/users/current')
            ->assertSeeText('Hello Khannedy');
    }
}
