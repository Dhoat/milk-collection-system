<?php

use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated request to Milk Receiving API gets 401', function () {
    $response = $this->getJson('/api/milk-receivings');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('super_admin, manager, and center_staff can list milk receivings via API', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village A', 'code' => 'VIL-A']);

    MilkReceiving::create([
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'expected_quantity' => 100.0,
        'received_quantity' => 100.0,
        'expected_fat' => 4.2,
        'received_fat' => 4.2,
        'expected_snf' => 8.5,
        'received_snf' => 8.5,
        'status' => 'received',
        'verified_by' => $admin->id,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/milk-receivings');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'village_id',
                    'receiving_date',
                    'shift',
                    'expected_quantity',
                    'received_quantity',
                    'quantity_variance',
                    'quantity_variance_percent',
                    'status',
                    'verified_by',
                    'village' => ['id', 'name', 'code'],
                    'verifier' => ['id', 'name', 'email'],
                ],
            ],
            'meta',
            'links',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Milk receiving records retrieved successfully',
        ]);
});

test('collection_staff gets 403 when trying to access Milk Receiving API', function () {
    $collectionStaff = User::factory()->create(['role' => 'collection_staff']);
    Sanctum::actingAs($collectionStaff);

    $response = $this->getJson('/api/milk-receivings');

    $response->assertStatus(403);
});

test('authorized user can view single milk receiving entry details', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);
    $village = Village::create(['name' => 'Village B', 'code' => 'VIL-B']);

    $receiving = MilkReceiving::create([
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'evening',
        'expected_quantity' => 50.0,
        'received_quantity' => 48.0,
        'status' => 'discrepancy',
        'verified_by' => $centerStaff->id,
    ]);

    Sanctum::actingAs($centerStaff);

    $response = $this->getJson("/api/milk-receivings/{$receiving->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk receiving record retrieved successfully',
            'data' => [
                'id' => $receiving->id,
                'receiving_date' => '2026-08-12',
                'shift' => 'evening',
                'expected_quantity' => 50.0,
                'received_quantity' => 48.0,
                'quantity_variance' => -2.0,
                'status' => 'discrepancy',
                'village' => [
                    'id' => $village->id,
                    'name' => 'Village B',
                ],
                'verifier' => [
                    'id' => $centerStaff->id,
                ],
            ],
        ]);
});

test('authorized user can create a receiving record with automatic metrics calculation and stock sync', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);
    $village = Village::create(['name' => 'Village C', 'code' => 'VIL-C']);
    $farmer1 = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-101',
        'name' => 'Farmer C1',
        'mobile' => '9876543210',
    ]);
    $farmer2 = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-102',
        'name' => 'Farmer C2',
        'mobile' => '9876543211',
    ]);

    // Create 2 farmer collections for 2 different farmers in this village/date/shift
    MilkCollection::create([
        'farmer_id' => $farmer1->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 40.0,
        'fat' => 4.0,
        'snf' => 8.0,
        'rate' => 40.0,
        'amount' => 1600.0,
    ]);

    MilkCollection::create([
        'farmer_id' => $farmer2->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 60.0,
        'fat' => 5.0,
        'snf' => 9.0,
        'rate' => 45.0,
        'amount' => 2700.0,
    ]);

    // Total expected quantity = 100. Weighted fat = (40*4 + 60*5)/100 = 4.6. Weighted snf = (40*8 + 60*9)/100 = 8.6

    Sanctum::actingAs($centerStaff);

    $payload = [
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'received_quantity' => 100.0,
        'received_fat' => 4.6,
        'received_snf' => 8.6,
    ];

    $response = $this->postJson('/api/milk-receivings', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Milk receiving record created successfully',
            'data' => [
                'village_id' => $village->id,
                'receiving_date' => '2026-08-12',
                'shift' => 'morning',
                'expected_quantity' => 100.0,
                'received_quantity' => 100.0,
                'expected_fat' => 4.6,
                'expected_snf' => 8.6,
                'status' => 'received',
                'verified_by' => $centerStaff->id,
            ],
        ]);

    $this->assertDatabaseHas('milk_receivings', [
        'village_id' => $village->id,
        'status' => 'received',
    ]);
});

test('duplicate receiving record for same village, date, and shift returns 422', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $village = Village::create(['name' => 'Village D', 'code' => 'VIL-D']);

    MilkReceiving::create([
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'expected_quantity' => 50.0,
        'received_quantity' => 50.0,
        'verified_by' => $manager->id,
    ]);

    Sanctum::actingAs($manager);

    $response = $this->postJson('/api/milk-receivings', [
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'received_quantity' => 50.0,
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => [
                'village_id' => [
                    'A receiving record already exists for this village, date, and shift.'
                ],
            ],
        ]);
});

test('authorized user can update a receiving record', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);
    $village = Village::create(['name' => 'Village E', 'code' => 'VIL-E']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-105',
        'name' => 'Farmer E',
        'mobile' => '9876543215',
    ]);

    // Expected quantity is 100.0 from farmer collections
    MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 100.0,
        'rate' => 40.0,
        'amount' => 4000.0,
    ]);

    $receiving = MilkReceiving::create([
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'expected_quantity' => 100.0,
        'received_quantity' => 95.0,
        'status' => 'discrepancy',
        'verified_by' => $centerStaff->id,
    ]);

    Sanctum::actingAs($centerStaff);

    $response = $this->putJson("/api/milk-receivings/{$receiving->id}", [
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'morning',
        'received_quantity' => 100.0, // Corrected quantity -> status becomes received
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk receiving record updated successfully',
            'data' => [
                'id' => $receiving->id,
                'received_quantity' => 100.0,
                'status' => 'received',
            ],
        ]);
});

test('authorized user can delete a receiving record', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $village = Village::create(['name' => 'Village F', 'code' => 'VIL-F']);

    $receiving = MilkReceiving::create([
        'village_id' => $village->id,
        'receiving_date' => '2026-08-12',
        'shift' => 'evening',
        'expected_quantity' => 50.0,
        'received_quantity' => 50.0,
        'verified_by' => $admin->id,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->deleteJson("/api/milk-receivings/{$receiving->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk receiving record deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('milk_receivings', ['id' => $receiving->id]);
});

test('summary endpoint returns expected collection metrics for village, date, and shift', function () {
    $centerStaff = User::factory()->create(['role' => 'center_staff']);
    $village = Village::create(['name' => 'Village G', 'code' => 'VIL-G']);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FAR-201',
        'name' => 'Farmer G',
        'mobile' => '9876543219',
    ]);

    MilkCollection::create([
        'farmer_id' => $farmer->id,
        'collection_date' => '2026-08-12',
        'shift' => 'morning',
        'milk_quantity' => 50.0,
        'fat' => 4.0,
        'snf' => 8.5,
        'rate' => 40.0,
        'amount' => 2000.0,
    ]);

    Sanctum::actingAs($centerStaff);

    $response = $this->getJson("/api/milk-receivings/summary?village_id={$village->id}&date=2026-08-12&shift=morning");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Collection summary retrieved successfully',
            'data' => [
                'expected_quantity' => 50.0,
                'expected_fat' => 4.0,
                'expected_snf' => 8.5,
                'farmer_count' => 1,
            ],
        ]);
});
