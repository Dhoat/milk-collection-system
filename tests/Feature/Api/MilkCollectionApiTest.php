<?php

use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated request to Milk Collection API gets 401', function () {
    $response = $this->getJson('/api/milk-collections');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('super_admin, manager, and collection_staff can list milk collections via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village A', 'code' => 'VIL-A']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-001',
        'name' => 'John Farmer',
        'mobile' => '9876543210',
    ]);

    MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 10.5,
        'fat' => 4.2,
        'snf' => 8.5,
        'rate' => 45.0,
        'amount' => 472.5,
    ]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/milk-collections');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'farmer_id',
                    'collection_date',
                    'shift',
                    'milk_quantity',
                    'fat',
                    'snf',
                    'rate',
                    'amount',
                    'farmer' => [
                        'id',
                        'name',
                        'farmer_code',
                        'village' => ['id', 'name', 'code'],
                    ],
                ],
            ],
            'meta',
            'links',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Milk collections retrieved successfully',
        ]);
});

test('center_staff receives 403 when accessing Milk Collection API', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);
    $token = $centerStaff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/milk-collections');

    $response->assertStatus(403);
});

test('authorized user can view single milk collection entry details', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village B', 'code' => 'VIL-B']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-002',
        'name' => 'Jane Farmer',
        'mobile' => '9876543211',
    ]);

    $collection = MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'evening',
        'milk_quantity' => 15.0,
        'fat' => 4.5,
        'snf' => 8.8,
        'rate' => 50.0,
        'amount' => 750.0,
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/milk-collections/{$collection->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk collection retrieved successfully',
            'data' => [
                'id' => $collection->id,
                'farmer_id' => $farmer->id,
                'collection_date' => '2026-08-12',
                'shift' => 'evening',
                'milk_quantity' => 15.0,
                'rate' => 50.0,
                'amount' => 750.0,
                'farmer' => [
                    'id' => $farmer->id,
                    'name' => 'Jane Farmer',
                    'village' => [
                        'id' => $village->id,
                        'name' => 'Village B',
                    ],
                ],
            ],
        ]);
});

test('authorized user can create a milk collection entry and server calculates total amount correctly', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village C', 'code' => 'VIL-C']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-003',
        'name' => 'Robert Farmer',
        'mobile' => '9876543212',
        'status' => true,
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    $payload = [
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 20.0,
        'fat' => 4.0,
        'snf' => 8.5,
        'rate' => 40.0,
        'notes' => 'Morning collection',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/milk-collections', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Milk collection created successfully',
            'data' => [
                'farmer_id' => $farmer->id,
                'milk_quantity' => 20.0,
                'rate' => 40.0,
                'amount' => 800.0, // 20.0 * 40.0
                'notes' => 'Morning collection',
            ],
        ]);

    $this->assertDatabaseHas('milk_collections', [
        'farmer_id' => $farmer->id,
        'amount' => 800.0,
    ]);
});

test('duplicate collection entry for same farmer, date, and shift is rejected with 422', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village D', 'code' => 'VIL-D']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-004',
        'name' => 'Alice Farmer',
        'mobile' => '9876543213',
        'status' => true,
    ]);

    MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 10.0,
        'rate' => 40.0,
        'amount' => 400.0,
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    // Attempt to store duplicate entry for morning shift
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/milk-collections', [
            'farmer_id' => $farmer->id,
            'collection_date' => '2026-08-12',
            'shift' => 'morning',
            'milk_quantity' => 12.0,
            'rate' => 42.0,
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => [
                'farmer_id' => [
                    'A milk collection entry already exists for this farmer on the selected date and shift.'
                ],
            ],
        ]);
});

test('invalid farmer ID or inactive farmer is rejected on store', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $token = $manager->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/milk-collections', [
            'farmer_id' => 999999,
            'collection_date' => '2026-08-12',
            'shift' => 'morning',
            'milk_quantity' => 10.0,
            'rate' => 40.0,
        ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'message',
            'errors' => ['farmer_id'],
        ]);
});

test('authorized user can update a milk collection entry and amount recalculates', function () {
    $staff = User::factory()->create(['role' => 'collection_staff']);
    $village = Village::create(['name' => 'Village E', 'code' => 'VIL-E']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-005',
        'name' => 'David Farmer',
        'mobile' => '9876543214',
    ]);

    $collection = MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 10.0,
        'rate' => 40.0,
        'amount' => 400.0,
    ]);

    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson("/api/milk-collections/{$collection->id}", [
            'farmer_id' => $farmer->id,
            'collection_date' => '2026-08-12',
            'shift' => 'morning',
            'milk_quantity' => 15.0, // Updated quantity
            'rate' => 45.0,          // Updated rate
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk collection updated successfully',
            'data' => [
                'id' => $collection->id,
                'milk_quantity' => 15.0,
                'rate' => 45.0,
                'amount' => 675.0, // 15.0 * 45.0
            ],
        ]);

    $this->assertDatabaseHas('milk_collections', [
        'id' => $collection->id,
        'amount' => 675.0,
    ]);
});

test('authorized user can delete a milk collection entry', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village F', 'code' => 'VIL-F']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-006',
        'name' => 'Emily Farmer',
        'mobile' => '9876543215',
    ]);

    $collection = MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'evening',
        'milk_quantity' => 10.0,
        'rate' => 40.0,
        'amount' => 400.0,
    ]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson("/api/milk-collections/{$collection->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk collection deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('milk_collections', ['id' => $collection->id]);
});
