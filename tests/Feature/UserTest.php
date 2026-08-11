<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'role' => 'super_admin',
        'status' => true,
    ]);

    $this->manager = User::factory()->create([
        'name' => 'Manager User',
        'email' => 'manager@test.com',
        'role' => 'manager',
        'status' => true,
    ]);

    $this->centerStaff = User::factory()->create([
        'name' => 'Center Staff',
        'email' => 'centerstaff@test.com',
        'role' => 'center_staff',
        'status' => true,
    ]);

    $this->collectionStaff = User::factory()->create([
        'name' => 'Collection Staff',
        'email' => 'collectionstaff@test.com',
        'role' => 'collection_staff',
        'status' => true,
    ]);
});

test('super admin can view user directory', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('users.index'));

    $response->assertOk();
    $response->assertViewIs('users.index');
    $response->assertSee('superadmin@test.com');
    $response->assertSee('manager@test.com');
});

test('non-super-admin users cannot access user management', function () {
    // Manager
    $responseManager = $this->actingAs($this->manager)->get(route('users.index'));
    $responseManager->assertForbidden();

    // Center Staff
    $responseCenter = $this->actingAs($this->centerStaff)->get(route('users.index'));
    $responseCenter->assertForbidden();

    // Collection Staff
    $responseCollection = $this->actingAs($this->collectionStaff)->get(route('users.index'));
    $responseCollection->assertForbidden();
});

test('super admin can create a new user account', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
        'name' => 'New Staff Member',
        'email' => 'newstaff@dairy.com',
        'role' => 'center_staff',
        'status' => 1,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'newstaff@dairy.com',
        'name' => 'New Staff Member',
        'role' => 'center_staff',
        'status' => true,
    ]);
});

test('super admin can update existing user and change role', function () {
    $response = $this->actingAs($this->superAdmin)->put(route('users.update', $this->collectionStaff), [
        'name' => 'Promoted Collection Staff',
        'email' => 'promoted@test.com',
        'role' => 'manager',
        'status' => 1,
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->collectionStaff->id,
        'name' => 'Promoted Collection Staff',
        'email' => 'promoted@test.com',
        'role' => 'manager',
    ]);
});

test('super admin can toggle user active status', function () {
    $response = $this->actingAs($this->superAdmin)->patch(route('users.toggle-status', $this->centerStaff));

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $this->centerStaff->id,
        'status' => false,
    ]);
});

test('user creation validates unique email and required fields', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('users.store'), [
        'name' => '',
        'email' => 'manager@test.com', // Duplicate email
        'role' => 'invalid_role',
        'password' => '123', // Too short
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'role', 'password']);
});

test('system prevents deleting or demoting the last remaining super admin account', function () {
    // Attempt to delete last super admin (blocked by UserPolicy)
    $deleteResponse = $this->actingAs($this->superAdmin)->delete(route('users.destroy', $this->superAdmin));
    $deleteResponse->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);

    // Attempt to demote last super admin
    $demoteResponse = $this->actingAs($this->superAdmin)->put(route('users.update', $this->superAdmin), [
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'role' => 'manager', // Demoting
        'status' => 1,
    ]);
    $demoteResponse->assertSessionHasErrors(['role']);
    $this->assertDatabaseHas('users', [
        'id' => $this->superAdmin->id,
        'role' => 'super_admin',
    ]);
});
