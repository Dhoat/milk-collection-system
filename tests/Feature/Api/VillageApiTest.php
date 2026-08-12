<?php

use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated request to Village API gets 401', function () {
    $response = $this->getJson('/api/villages');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('super_admin can list villages via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    Village::create(['name' => 'Alpha Village', 'code' => 'VIL-ALP']);
    Village::create(['name' => 'Beta Village', 'code' => 'VIL-BET']);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/villages');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'code', 'address', 'status', 'created_at', 'updated_at'],
            ],
            'meta',
            'links',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Villages retrieved successfully',
        ]);
});

test('manager can list villages via API', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    Village::create(['name' => 'Delta Village', 'code' => 'VIL-DEL']);

    $token = $manager->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/villages');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Villages retrieved successfully',
        ]);
});

test('collection_staff can list villages via API', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    Village::create(['name' => 'Echo Village', 'code' => 'VIL-ECH']);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/villages');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Villages retrieved successfully',
        ]);
});

test('center_staff gets 403 when trying to access Village API', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);

    $token = $centerStaff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/villages');

    $response->assertStatus(403);
});

test('authorized user can view single village details', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $village = Village::create([
        'name' => 'Green Valley',
        'code' => 'GV-01',
    ]);

    $token = $manager->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/villages/{$village->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Village retrieved successfully',
            'data' => [
                'id' => $village->id,
                'name' => 'Green Valley',
                'code' => 'GV-01',
            ],
        ]);
});

test('super_admin and manager can create a village via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $token = $admin->createToken('test-token')->plainTextToken;

    $payload = [
        'name' => 'Sunshine Village',
        'code' => 'SUN-01',
        'address' => '123 Main Road',
        'status' => true,
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/villages', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Village created successfully',
            'data' => [
                'name' => 'Sunshine Village',
                'code' => 'SUN-01',
                'address' => '123 Main Road',
                'status' => true,
            ],
        ]);

    $this->assertDatabaseHas('villages', ['code' => 'SUN-01']);
});

test('collection_staff cannot create a village (respects VillagePolicy)', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $token = $staff->createToken('test-token')->plainTextToken;

    $payload = [
        'name' => 'Staff Village',
        'code' => 'STF-01',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/villages', $payload);

    $response->assertStatus(403);
});

test('village creation fails with 422 on invalid data or duplicate code', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    Village::create(['name' => 'Existing Village', 'code' => 'EXISTING-01']);

    $token = $manager->createToken('test-token')->plainTextToken;

    // Test missing required fields
    $responseMissing = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/villages', []);

    $responseMissing->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => ['name', 'code'],
        ]);

    // Test duplicate code validation
    $responseDuplicate = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/villages', [
            'name' => 'New Village',
            'code' => 'EXISTING-01',
        ]);

    $responseDuplicate->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => [
                'code' => ['The village code has already been taken.'],
            ],
        ]);
});

test('authorized user can update a village', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $village = Village::create(['name' => 'Old Name', 'code' => 'OLD-01']);

    $token = $manager->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson("/api/villages/{$village->id}", [
            'name' => 'Updated Name',
            'code' => 'OLD-01',
            'address' => 'New Address',
            'status' => true,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Village updated successfully',
            'data' => [
                'id' => $village->id,
                'name' => 'Updated Name',
                'code' => 'OLD-01',
            ],
        ]);

    $this->assertDatabaseHas('villages', ['id' => $village->id, 'name' => 'Updated Name']);
});

test('collection_staff cannot update a village (respects VillagePolicy)', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village X', 'code' => 'VIL-X']);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson("/api/villages/{$village->id}", [
            'name' => 'Unauthorized Update',
            'code' => $village->code,
        ]);

    $response->assertStatus(403);
});

test('authorized user can delete a village', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Delete Village', 'code' => 'VIL-DEL']);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson("/api/villages/{$village->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Village deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('villages', ['id' => $village->id]);
});
