<?php

use App\Models\User;
use App\Models\Village;

test('guests are redirected to login when accessing villages', function () {
    $response = $this->get('/villages');
    $response->assertRedirect('/login');

    $response = $this->get('/villages/create');
    $response->assertRedirect('/login');

    $response = $this->post('/villages', []);
    $response->assertRedirect('/login');
});

test('authenticated user can view villages list', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/villages');

    $response->assertOk();
    $response->assertViewIs('villages.index');
});

test('authenticated user can view add village form', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/villages/create');

    $response->assertOk();
    $response->assertViewIs('villages.create');
});

test('authenticated user can store village', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/villages', [
            'name' => 'Village A',
            'code' => 'VIL-001',
            'address' => '123 Main St',
            'status' => '1',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/villages');

    $this->assertDatabaseHas('villages', [
        'name' => 'Village A',
        'code' => 'VIL-001',
        'address' => '123 Main St',
        'status' => true,
    ]);
});

test('village validation rules are enforced', function () {
    $user = User::factory()->create();

    // Required fields missing
    $response = $this
        ->actingAs($user)
        ->post('/villages', [
            'name' => '',
            'code' => '',
        ]);

    $response->assertSessionHasErrors(['name', 'code']);

    // Duplicate village code validation
    $village = Village::create([
        'name' => 'Existing Village',
        'code' => 'VIL-DUP',
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/villages', [
            'name' => 'New Village',
            'code' => 'VIL-DUP',
        ]);

    $response->assertSessionHasErrors(['code']);
});

test('authenticated user can view village details', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Village Show',
        'code' => 'VIL-SHOW',
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/villages/{$village->id}");

    $response->assertOk();
    $response->assertViewIs('villages.show');
});

test('authenticated user can edit village', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Old Name',
        'code' => 'VIL-OLD',
    ]);

    $response = $this
        ->actingAs($user)
        ->put("/villages/{$village->id}", [
            'name' => 'New Name',
            'code' => 'VIL-NEW',
            'address' => 'Updated Address',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/villages');

    $this->assertDatabaseHas('villages', [
        'id' => $village->id,
        'name' => 'New Name',
        'code' => 'VIL-NEW',
        'address' => 'Updated Address',
    ]);
});

test('authenticated user can delete village', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Delete Me',
        'code' => 'VIL-DEL',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete("/villages/{$village->id}");

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/villages');

    $this->assertDatabaseMissing('villages', [
        'id' => $village->id,
    ]);
});
