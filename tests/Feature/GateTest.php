<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class GateTest extends TestCase
{

    public function testGateValid()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where('email', 'doni@localhost')->firstOrFail();
        Auth::login($user);

        $contact = Contact::where('email', 'test@localhost')->firstOrFail();

        self::assertTrue(Gate::allows('get-contact', $contact));
        self::assertTrue(Gate::allows('update-contact', $contact));
        self::assertTrue(Gate::allows('delete-contact', $contact));
    }

    public function testGateInvalid()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where('email', 'eko@localhost')->firstOrFail();
        Auth::login($user);

        $contact = Contact::where('email', 'test@localhost')->firstOrFail();

        self::assertFalse(Gate::allows('get-contact', $contact));
        self::assertFalse(Gate::allows('update-contact', $contact));
        self::assertFalse(Gate::allows('delete-contact', $contact));
    }

    public function testGateMethod()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::where('email', 'doni@localhost')->firstOrFail();
        Auth::login($user);

        $contact = Contact::where('email', 'test@localhost')->firstOrFail();
        self::assertTrue(Gate::any(['get-contact', 'update-contact', 'delete-contact'], $contact));
        self::assertFalse(Gate::none(['get-contact', 'update-contact', 'delete-contact'], $contact));

        $user =  User::where('email', 'eko@localhost')->firstOrFail();
        Auth::login($user);

        self::assertFalse(Gate::any(['get-contact', 'update-contact', 'delete-contact'], $contact));
        self::assertTrue(Gate::none(['get-contact', 'update-contact', 'delete-contact'], $contact));
    }

    public function testGateNonLogin()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where('email', 'doni@localhost')->firstOrFail();
        $contact = Contact::where('email', 'test@localhost')->firstOrFail();

        // JIKA USER TIDAK LOGIN OTOMATIS METHOD ALLOWS MENGEMBALIKAN FALSE
        self::assertFalse(Gate::allows('get-contact', $contact));
        self::assertFalse(Gate::allows('update-contact', $contact));
        self::assertFalse(Gate::allows('delete-contact', $contact));

        $gate = Gate::forUser($user);
        self::assertTrue($gate->allows('get-contact', $contact));
        self::assertTrue($gate->allows('update-contact', $contact));
        self::assertTrue($gate->allows('delete-contact', $contact));

        Auth::logout($user);
        self::assertFalse(Gate::allows('get-contact', $contact));
        self::assertFalse(Gate::allows('update-contact', $contact));
        self::assertFalse(Gate::allows('delete-contact', $contact));
    }

    public function testGateResponse()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where('email', 'doni@localhost')->firstOrFail();
        Auth::login($user);

        $response = Gate::inspect('create-contact');
        self::assertFalse($response->allowed());
        self::assertTrue($response->denied());
        self::assertEquals('You are not admin', $response->message());
    }
}
