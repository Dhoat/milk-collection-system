<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can log in successfully via API with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'admin@milkcenter.com',
        'password' => bcrypt('password123'),
        'role' => 'super_admin',
        'status' => true,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'admin@milkcenter.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'status',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type',
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => 'admin@milkcenter.com',
                    'role' => 'super_admin',
                ],
                'token_type' => 'Bearer',
            ],
        ]);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'admin@milkcenter.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'admin@milkcenter.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
});

test('login validation fails when required fields are missing', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'email',
                'password',
            ],
        ])
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
        ]);
});

test('inactive user cannot log in', function () {
    User::factory()->create([
        'email' => 'inactive@milkcenter.com',
        'password' => bcrypt('password123'),
        'status' => false,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'inactive@milkcenter.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'message' => 'Your account is inactive. Please contact system administrator.',
        ]);
});

test('authenticated user can fetch their profile via GET /api/me', function () {
    $user = User::factory()->create([
        'role' => 'manager',
        'status' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => 'manager',
                ],
            ],
        ]);
});

test('unauthenticated request to GET /api/me returns 401 unauthenticated', function () {
    $response = $this->getJson('/api/me');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('authenticated user can log out and revoke their token via POST /api/logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);

    // Ensure the token has been revoked
    expect($user->tokens()->count())->toBe(0);

    // Clear cached auth user instance to test token check on next request
    auth()->forgetUser();

    // Verify token can no longer be used
    $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/me');

    $meResponse->assertStatus(401);
});
