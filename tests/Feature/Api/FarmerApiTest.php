<?php

use App\Models\Farmer;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated request to Farmer API gets 401', function () {
    $response = $this->getJson('/api/farmers');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('super_admin can list farmers via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village A', 'code' => 'VIL-A']);

    Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-001',
        'name' => 'John Doe',
        'mobile' => '9876543210',
    ]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/farmers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'village_id',
                    'farmer_code',
                    'name',
                    'mobile',
                    'village' => ['id', 'name', 'code'],
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta',
            'links',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Farmers retrieved successfully',
        ]);
});

test('manager can list farmers via API', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $village = Village::create(['name' => 'Village B', 'code' => 'VIL-B']);

    Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-002',
        'name' => 'Jane Smith',
        'mobile' => '9876543211',
    ]);

    $token = $manager->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/farmers');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Farmers retrieved successfully',
        ]);
});

test('collection_staff can list farmers via API', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village C', 'code' => 'VIL-C']);

    Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-003',
        'name' => 'Robert Johnson',
        'mobile' => '9876543212',
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/farmers');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Farmers retrieved successfully',
        ]);
});

test('center_staff gets 403 when trying to access Farmer API', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);

    $token = $centerStaff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/farmers');

    $response->assertStatus(403);
});

test('authorized user can view single farmer details including village relation', function () {
    $collectionStaff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Green Valley', 'code' => 'GV-01']);

    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-004',
        'name' => 'Alice Williams',
        'mobile' => '9876543213',
    ]);

    $token = $collectionStaff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/farmers/{$farmer->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Farmer retrieved successfully',
            'data' => [
                'id' => $farmer->id,
                'farmer_code' => 'FAR-004',
                'name' => 'Alice Williams',
                'village' => [
                    'id' => $village->id,
                    'name' => 'Green Valley',
                    'code' => 'GV-01',
                ],
            ],
        ]);
});

test('authorized user can create a farmer via API', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Sunrise Village', 'code' => 'SUN-01']);

    $token = $staff->createToken('test-token')->plainTextToken;

    $payload = [
        'village_id' => $village->id,
        'farmer_code' => 'FAR-005',
        'name' => 'Michael Brown',
        'father_name' => 'David Brown',
        'mobile' => '9876543214',
        'address' => 'House 42, Sunrise Village',
        'gender' => 'male',
        'status' => true,
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/farmers', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Farmer created successfully',
            'data' => [
                'farmer_code' => 'FAR-005',
                'name' => 'Michael Brown',
                'village' => [
                    'id' => $village->id,
                    'name' => 'Sunrise Village',
                ],
            ],
        ]);

    $this->assertDatabaseHas('farmers', ['farmer_code' => 'FAR-005']);
});

test('farmer creation fails with 422 on invalid data or invalid village relationship', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $token = $manager->createToken('test-token')->plainTextToken;

    // Test missing required fields
    $responseMissing = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/farmers', []);

    $responseMissing->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => ['village_id', 'farmer_code', 'name', 'mobile'],
        ]);

    // Test non-existing village ID
    $responseInvalidVillage = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/farmers', [
            'village_id' => 999999,
            'farmer_code' => 'FAR-999',
            'name' => 'Invalid Farmer',
            'mobile' => '9876543215',
        ]);

    $responseInvalidVillage->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => [
                'village_id' => ['The selected village does not exist.'],
            ],
        ]);
});

test('authorized user can update a farmer via API', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village1 = Village::create(['name' => 'Village One', 'code' => 'VIL-1']);
    $village2 = Village::create(['name' => 'Village Two', 'code' => 'VIL-2']);

    $farmer = Farmer::create([
        'village_id' => $village1->id,
        'farmer_code' => 'FAR-006',
        'name' => 'Old Name',
        'mobile' => '9876543216',
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson("/api/farmers/{$farmer->id}", [
            'village_id' => $village2->id,
            'farmer_code' => 'FAR-006',
            'name' => 'Updated Farmer Name',
            'mobile' => '9876543216',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Farmer updated successfully',
            'data' => [
                'id' => $farmer->id,
                'name' => 'Updated Farmer Name',
                'village_id' => $village2->id,
                'village' => [
                    'id' => $village2->id,
                    'name' => 'Village Two',
                ],
            ],
        ]);

    $this->assertDatabaseHas('farmers', [
        'id' => $farmer->id,
        'name' => 'Updated Farmer Name',
        'village_id' => $village2->id,
    ]);
});

test('authorized user can delete a farmer via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village Del', 'code' => 'VIL-D']);

    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-DEL',
        'name' => 'Delete Me',
        'mobile' => '9876543217',
    ]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson("/api/farmers/{$farmer->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Farmer deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('farmers', ['id' => $farmer->id]);
});
