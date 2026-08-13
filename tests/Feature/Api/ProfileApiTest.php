<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Profile User',
        'email' => 'profileuser@example.com',
        'role' => 'manager',
        'status' => true,
    ]);

    $this->otherUser = User::factory()->create([
        'name' => 'Other User',
        'email' => 'otheruser@example.com',
        'role' => 'collection_staff',
        'status' => true,
    ]);
});

test('unauthenticated request to Profile API returns 401', function () {
    $response = $this->getJson('/api/profile');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $this->putJson('/api/profile', ['name' => 'New Name', 'email' => 'new@example.com'])->assertStatus(401);
    $this->patchJson('/api/profile/password', ['current_password' => 'password', 'password' => 'new-password'])->assertStatus(401);
});

test('authenticated user can view their own profile', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/profile');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $this->user->id,
                    'name' => 'Profile User',
                    'email' => 'profileuser@example.com',
                    'role' => 'manager',
                    'status' => true,
                ],
            ],
        ]);

    // Ensure sensitive attributes are never exposed
    $responseData = $response->json('data.user');
    expect($responseData)->not->toHaveKey('password')
        ->and($responseData)->not->toHaveKey('remember_token')
        ->and($responseData)->not->toHaveKey('tokens');
});

test('authenticated user can update their own profile information', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson('/api/profile', [
        'name' => 'Updated Profile Name',
        'email' => 'updatedprofile@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'name' => 'Updated Profile Name',
                    'email' => 'updatedprofile@example.com',
                ],
            ],
        ]);

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Profile Name')
        ->and($this->user->email)->toBe('updatedprofile@example.com')
        ->and($this->user->email_verified_at)->toBeNull();
});

test('profile update keeps email_verified_at unchanged if email is not modified', function () {
    $this->user->email_verified_at = now();
    $this->user->save();

    Sanctum::actingAs($this->user);

    $response = $this->putJson('/api/profile', [
        'name' => 'Updated Name Only',
        'email' => $this->user->email,
    ]);

    $response->assertStatus(200);
    expect($this->user->refresh()->email_verified_at)->not->toBeNull();
});

test('profile update validation fails on missing or invalid email format', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson('/api/profile', [
        'name' => '',
        'email' => 'invalid-email-string',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

test('profile update validation fails when attempting to use an existing email of another user', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson('/api/profile', [
        'name' => 'Profile User',
        'email' => $this->otherUser->email,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('role and status cannot be manipulated through profile update', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson('/api/profile', [
        'name' => 'Profile User',
        'email' => 'profileuser@example.com',
        'role' => 'super_admin',
        'status' => false,
    ]);

    $response->assertStatus(200);

    $this->user->refresh();
    expect($this->user->role)->toBe('manager')
        ->and($this->user->status)->toBeTrue();
});

test('user can update password with valid current password and password confirmation', function () {
    Sanctum::actingAs($this->user);

    $response = $this->patchJson('/api/profile/password', [
        'current_password' => 'password',
        'password' => 'new-secret-password-123',
        'password_confirmation' => 'new-secret-password-123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);

    expect(Hash::check('new-secret-password-123', $this->user->refresh()->password))->toBeTrue();
});

test('password update fails when incorrect current password is provided', function () {
    Sanctum::actingAs($this->user);

    $response = $this->patchJson('/api/profile/password', [
        'current_password' => 'wrong-current-password',
        'password' => 'new-secret-password-123',
        'password_confirmation' => 'new-secret-password-123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['current_password']);
});

test('password update fails when confirmation password does not match', function () {
    Sanctum::actingAs($this->user);

    $response = $this->patchJson('/api/profile/password', [
        'current_password' => 'password',
        'password' => 'new-secret-password-123',
        'password_confirmation' => 'mismatched-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
