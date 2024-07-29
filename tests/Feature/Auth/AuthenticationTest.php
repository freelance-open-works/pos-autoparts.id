<?php

use App\Models\Default\User;

test('login screen can be rendered', function () {
    $response = $this->withHeaders(['Accept' => 'application/json'])->get('/login');

    $response->assertStatus(200);
    $response->assertSee('email');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->post('login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
