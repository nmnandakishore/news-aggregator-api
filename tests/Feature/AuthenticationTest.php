<?php

it('Register a new user', function () {
    $name = fake()->name;
    $email = fake()->email;
    $password = fake()->password;

//    \App\Models\User::where('email', $email)->delete();
    \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->delete();

    $response = $this->postJson('/api/user/register', [
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "password_confirmation" => $password
    ]);

    $response->assertStatus(201);
    $response->assertValid();
    $response->assertJsonFragment(['message' => 'User registered']);
});

it('Register an existing user', function () {
    $user =\App\Models\User::first();
    $password = fake()->password;

    $response = $this->postJson('/api/user/register', [
        "name" => $user->name,
        "email" => $user->email,
        "password" => $password,
        "password_confirmation" => $password
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['error' => 'Unprocessable']);
    $response->assertJsonPath('message.email', ['The email has already been taken.']);

});

it('Logs a user in', function () {
    $name = fake()->name;
    $email = fake()->email;
    $password = fake()->password;
    \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->delete();

    $this->postJson('/api/user/register', [
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "password_confirmation" => $password
    ]);

    $response = $this->postJson('/api/user/login', [
        "email" => $email,
        "password" => $password,
    ]);

    $response->assertStatus(200);
    $response->assertValid();
    $response->assertJsonStructure([
        'message',
        'data' => ['api_token']
    ]);

});

it('Logs a user in with wrong password', function () {
    $name = fake()->name;
    $email = fake()->email;
    $password = fake()->password;
    \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->delete();

    $this->postJson('/api/user/register', [
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "password_confirmation" => $password
    ]);

    $response = $this->postJson('/api/user/login', [
        "email" => $email,
        "password" => \Illuminate\Support\Str::random(),
    ]);

    $response->assertStatus(401);
//    $response->assertJsonStructure([
//        'message',
//        'data' => ['api_token']
//    ]);

});
