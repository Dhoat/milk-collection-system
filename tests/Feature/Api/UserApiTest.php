<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'name' => 'Primary Super Admin',
        'email' => 'superadmin@dairy.com',
        'role' => 'super_admin',
        'status' => true,
    ]);

    $this->manager = User::factory()->create([
        'name' => 'Dairy Manager',
        'email' => 'manager@dairy.com',
        'role' => 'manager',
        'status' => true,
    ]);

    $this->centerStaff = User::factory()->create([
        'name' => 'Center Staff Member',
        'email' => 'centerstaff@dairy.com',
        'role' => 'center_staff',
        'status' => true,
    ]);

    $this->collectionStaff = User::factory()->create([
        'name' => 'Collection Field Officer',
        'email' => 'collectionstaff@dairy.com',
        'role' => 'collection_staff',
        'status' => true,
    ]);
});

test('unauthenticated request to Users API returns 401', function () {
    $response = $this->getJson('/api/users');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('non-super-admin users receive 403 when accessing Users API', function () {
    foreach ([$this->manager, $this->centerStaff, $this->collectionStaff] as $user) {
        Sanctum::actingAs($user);

        $this->getJson('/api/users')->assertStatus(403);
        $this->getJson("/api/users/{$this->centerStaff->id}")->assertStatus(403);
        $this->postJson('/api/users', [])->assertStatus(403);
        $this->putJson("/api/users/{$this->centerStaff->id}", [])->assertStatus(403);
        $this->patchJson("/api/users/{$this->centerStaff->id}/toggle-status")->assertStatus(403);
        $this->deleteJson("/api/users/{$this->centerStaff->id}")->assertStatus(403);
    }
});

test('super admin can list paginated users with filters matching web logic', function () {
    Sanctum::actingAs($this->superAdmin);

    // List all
    $response = $this->getJson('/api/users');
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Users retrieved successfully',
        ])
        ->assertJsonPath('meta.total', 4);

    // Filter by role
    $responseRole = $this->getJson('/api/users?role=manager');
    $responseRole->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.email', 'manager@dairy.com');

    // Filter by search
    $responseSearch = $this->getJson('/api/users?search=Collection');
    $responseSearch->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.name', 'Collection Field Officer');
});

test('super admin can view single user details via UserResource', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson("/api/users/{$this->manager->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => [
                'id' => $this->manager->id,
                'name' => 'Dairy Manager',
                'email' => 'manager@dairy.com',
                'role' => 'manager',
                'status' => true,
            ],
        ]);

    $responseData = $response->json('data');
    expect($responseData)->not->toHaveKey('password')
        ->and($responseData)->not->toHaveKey('remember_token');
});

test('super admin can create a new user with hashed password', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/users', [
        'name' => 'New Center Supervisor',
        'email' => 'supervisor@dairy.com',
        'password' => 'securepass123',
        'password_confirmation' => 'securepass123',
        'role' => 'center_staff',
        'status' => true,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'User account created successfully',
            'data' => [
                'name' => 'New Center Supervisor',
                'email' => 'supervisor@dairy.com',
                'role' => 'center_staff',
                'status' => true,
            ],
        ]);

    $user = User::where('email', 'supervisor@dairy.com')->first();
    expect($user)->not->toBeNull()
        ->and(Hash::check('securepass123', $user->password))->toBeTrue();
});

test('user creation validates duplicate email and required fields', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/users', [
        'name' => '',
        'email' => 'manager@dairy.com', // Duplicate
        'password' => 'short',
        'password_confirmation' => 'mismatch',
        'role' => 'invalid_role',
        'status' => 'not-bool',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'role', 'status']);
});

test('super admin can update existing user details and role', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->putJson("/api/users/{$this->collectionStaff->id}", [
        'name' => 'Senior Collection Officer',
        'email' => 'senior.collection@dairy.com',
        'role' => 'manager',
        'status' => true,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'User account updated successfully',
            'data' => [
                'id' => $this->collectionStaff->id,
                'name' => 'Senior Collection Officer',
                'email' => 'senior.collection@dairy.com',
                'role' => 'manager',
            ],
        ]);

    $this->collectionStaff->refresh();
    expect($this->collectionStaff->name)->toBe('Senior Collection Officer')
        ->and($this->collectionStaff->role)->toBe('manager');
});

test('updating user without providing password preserves existing password', function () {
    Sanctum::actingAs($this->superAdmin);

    $oldPasswordHash = $this->centerStaff->password;

    $response = $this->putJson("/api/users/{$this->centerStaff->id}", [
        'name' => 'Center Staff Updated Name',
        'email' => $this->centerStaff->email,
        'role' => $this->centerStaff->role,
        'status' => true,
    ]);

    $response->assertStatus(200);

    expect($this->centerStaff->refresh()->password)->toBe($oldPasswordHash);
});

test('super admin can toggle user status active and inactive', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->patchJson("/api/users/{$this->centerStaff->id}/toggle-status");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->centerStaff->id,
                'status' => false,
            ],
        ]);

    expect($this->centerStaff->refresh()->status)->toBeFalse();
});

test('super admin cannot deactivate or delete their own logged-in account', function () {
    Sanctum::actingAs($this->superAdmin);

    // Deactivation self check
    $responseToggle = $this->patchJson("/api/users/{$this->superAdmin->id}/toggle-status");
    $responseToggle->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'You cannot deactivate your own logged-in account.',
        ]);

    // Deletion self check (UserPolicy blocks self deletion with 403 or controller check 422)
    $responseDelete = $this->deleteJson("/api/users/{$this->superAdmin->id}");
    expect(in_array($responseDelete->status(), [403, 422]))->toBeTrue();
});

test('system prevents demoting or deactivating the last active super admin account', function () {
    Sanctum::actingAs($this->superAdmin);

    // Demote last active super admin via update
    $responseDemote = $this->putJson("/api/users/{$this->superAdmin->id}", [
        'name' => 'Primary Super Admin',
        'email' => 'superadmin@dairy.com',
        'role' => 'manager',
        'status' => true,
    ]);
    $responseDemote->assertStatus(422)
        ->assertJsonValidationErrors(['role']);

    // Deactivate last active super admin via update
    $responseDeactivate = $this->putJson("/api/users/{$this->superAdmin->id}", [
        'name' => 'Primary Super Admin',
        'email' => 'superadmin@dairy.com',
        'role' => 'super_admin',
        'status' => false,
    ]);
    $responseDeactivate->assertStatus(422);
});

test('super admin can delete non-self user account', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->deleteJson("/api/users/{$this->collectionStaff->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'User account deleted successfully',
        ]);

    $this->assertDatabaseMissing('users', ['id' => $this->collectionStaff->id]);
});
