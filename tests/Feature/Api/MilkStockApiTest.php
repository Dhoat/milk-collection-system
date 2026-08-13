<?php

use App\Models\MilkReceiving;
use App\Models\MilkStock;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);

    $this->village = Village::create([
        'name' => 'API Stock Test Village',
        'code' => 'STK-API-001',
        'status' => true,
    ]);
});

test('unauthenticated request to Milk Stock API gets 401', function () {
    $response = $this->getJson('/api/milk-stocks');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $response = $this->getJson('/api/milk-stocks/1');
    $response->assertStatus(401);

    $response = $this->postJson('/api/milk-stocks/out', [
        'transaction_date' => now()->toDateString(),
        'quantity' => 10.0,
        'source_or_reason' => 'Test',
    ]);
    $response->assertStatus(401);
});

test('super_admin, manager, and center_staff can list milk stock transactions via API', function () {
    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 500.0,
        'received_quantity' => 500.0,
        'received_fat' => 4.5,
        'received_snf' => 8.5,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    foreach ([$this->superAdmin, $this->manager, $this->centerStaff] as $user) {
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/milk-stocks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'summary' => [
                    'opening_stock',
                    'today_received',
                    'today_stock_out',
                    'available_stock',
                ],
                'data' => [
                    '*' => [
                        'id',
                        'transaction_date',
                        'type',
                        'item_type',
                        'quantity',
                        'fat',
                        'snf',
                        'milk_receiving_id',
                        'created_by',
                        'source_or_reason',
                        'notes',
                        'milk_receiving' => [
                            'id',
                            'village_id',
                            'receiving_date',
                            'shift',
                            'village' => ['id', 'name', 'code'],
                        ],
                        'creator' => ['id', 'name', 'email'],
                    ],
                ],
                'meta',
                'links',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Milk stock transactions retrieved successfully',
                'summary' => [
                    'today_received' => 500.0,
                    'today_stock_out' => 0.0,
                    'available_stock' => 500.0,
                ],
            ]);
    }
});

test('collection_staff gets 403 when trying to access Milk Stock API', function () {
    Sanctum::actingAs($this->collectionStaff);

    $response = $this->getJson('/api/milk-stocks');
    $response->assertStatus(403);

    $response = $this->postJson('/api/milk-stocks/out', [
        'transaction_date' => now()->toDateString(),
        'quantity' => 10.0,
        'source_or_reason' => 'Unauthorized Out',
    ]);
    $response->assertStatus(403);
});

test('authorized user can view single milk stock record details', function () {
    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'evening',
        'expected_quantity' => 300.0,
        'received_quantity' => 300.0,
        'received_fat' => 4.2,
        'received_snf' => 8.4,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    $stock = MilkStock::where('milk_receiving_id', $receiving->id)->firstOrFail();

    Sanctum::actingAs($this->centerStaff);

    $response = $this->getJson("/api/milk-stocks/{$stock->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Milk stock record retrieved successfully',
            'data' => [
                'id' => $stock->id,
                'type' => 'in',
                'quantity' => 300.0,
                'fat' => 4.2,
                'snf' => 8.4,
                'milk_receiving_id' => $receiving->id,
                'milk_receiving' => [
                    'id' => $receiving->id,
                    'village' => [
                        'id' => $this->village->id,
                        'name' => 'API Stock Test Village',
                    ],
                ],
            ],
        ]);
});

test('milk receiving operations sync stock automatically and API reads same database data', function () {
    Sanctum::actingAs($this->centerStaff);

    // 1. Create receiving
    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 400.0,
        'received_quantity' => 400.0,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    $response = $this->getJson('/api/milk-stocks');
    $response->assertStatus(200)
        ->assertJson([
            'summary' => [
                'available_stock' => 400.0,
            ],
        ]);

    // 2. Update receiving quantity
    $receiving->update(['received_quantity' => 450.0]);

    $response = $this->getJson('/api/milk-stocks');
    $response->assertStatus(200)
        ->assertJson([
            'summary' => [
                'available_stock' => 450.0,
            ],
        ]);
    $this->assertEquals(1, MilkStock::where('milk_receiving_id', $receiving->id)->count());

    // 3. Delete receiving
    $receiving->delete();

    $response = $this->getJson('/api/milk-stocks');
    $response->assertStatus(200)
        ->assertJson([
            'summary' => [
                'available_stock' => 0.0,
            ],
        ]);
});

test('authorized user can record valid stock out transaction via API', function () {
    // Initial Stock IN: 500 L
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 500.0,
        'received_quantity' => 500.0,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    Sanctum::actingAs($this->centerStaff);

    $payload = [
        'transaction_date' => now()->toDateString(),
        'quantity' => 200.0,
        'source_or_reason' => 'Shop Order #501 Dispatch',
        'fat' => 4.0,
        'snf' => 8.5,
        'notes' => 'Dispatched raw milk to shop',
    ];

    $response = $this->postJson('/api/milk-stocks/out', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Stock OUT transaction recorded successfully',
            'data' => [
                'type' => 'out',
                'quantity' => 200.0,
                'source_or_reason' => 'Shop Order #501 Dispatch',
                'fat' => 4.0,
                'snf' => 8.5,
                'created_by' => $this->centerStaff->id,
            ],
        ]);

    $this->assertEquals(300.0, MilkStock::getAvailableStock());

    // Verify stock summary API reflects updated stock out KPI
    $response = $this->getJson('/api/milk-stocks');
    $response->assertStatus(200)
        ->assertJson([
            'summary' => [
                'today_received' => 500.0,
                'today_stock_out' => 200.0,
                'available_stock' => 300.0,
            ],
        ]);
});

test('stock out quantity exceeding available stock fails with 422 error', function () {
    // Initial Stock IN: 100 L
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 100.0,
        'received_quantity' => 100.0,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    Sanctum::actingAs($this->centerStaff);

    $response = $this->postJson('/api/milk-stocks/out', [
        'transaction_date' => now()->toDateString(),
        'quantity' => 150.0,
        'source_or_reason' => 'Excessive Dispatch',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
            'errors' => [
                'quantity' => [
                    'Stock OUT quantity (150.00 L) cannot exceed current available stock (100.00 L).',
                ],
            ],
        ]);

    $this->assertEquals(100.0, MilkStock::getAvailableStock());
});

test('milk stock index filtering by date, type, and search keyword works', function () {
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => '2026-08-10',
        'shift' => 'morning',
        'expected_quantity' => 300.0,
        'received_quantity' => 300.0,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    Sanctum::actingAs($this->centerStaff);

    // Record Stock OUT on 2026-08-11
    MilkStock::create([
        'transaction_date' => '2026-08-11',
        'type' => 'out',
        'item_type' => 'raw_milk',
        'quantity' => 50.0,
        'created_by' => $this->centerStaff->id,
        'source_or_reason' => 'Special Processing Batch',
        'notes' => 'Filtering keyword test',
    ]);

    // Filter by type=out
    $response = $this->getJson('/api/milk-stocks?type=out');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.type', 'out');

    // Filter by search keyword
    $response = $this->getJson('/api/milk-stocks?search=Processing');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.source_or_reason', 'Special Processing Batch');

    // Filter by date
    $response = $this->getJson('/api/milk-stocks?date=2026-08-10');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.type', 'in');
});
