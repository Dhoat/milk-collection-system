<?php

use App\Models\Shop;
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
        'name' => 'API Shop Test Village',
        'code' => 'SHP-API-001',
        'status' => true,
    ]);
});

test('unauthenticated request to Shops API gets 401', function () {
    $response = $this->getJson('/api/shops');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $response = $this->postJson('/api/shops', []);
    $response->assertStatus(401);

    $response = $this->getJson('/api/shops/1');
    $response->assertStatus(401);

    $response = $this->putJson('/api/shops/1', []);
    $response->assertStatus(401);

    $response = $this->deleteJson('/api/shops/1');
    $response->assertStatus(401);
});

test('super_admin, manager, and center_staff can list shops via API', function () {
    Shop::create([
        'shop_code' => 'SHP-LIST-01',
        'name' => 'List Test Shop',
        'owner_name' => 'List Owner',
        'phone' => '9876543201',
        'village_id' => $this->village->id,
        'status' => true,
    ]);

    foreach ([$this->superAdmin, $this->manager, $this->centerStaff] as $user) {
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/shops');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'shop_code',
                        'name',
                        'owner_name',
                        'phone',
                        'email',
                        'village_id',
                        'area',
                        'address',
                        'status',
                        'credit_limit',
                        'notes',
                        'village' => ['id', 'name', 'code'],
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta',
                'links',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Shops retrieved successfully',
            ]);
    }
});

test('collection_staff gets 403 when trying to access Shops API', function () {
    Sanctum::actingAs($this->collectionStaff);

    $response = $this->getJson('/api/shops');
    $response->assertStatus(403);

    $response = $this->postJson('/api/shops', [
        'shop_code' => 'SHP-UNAUTH',
        'name' => 'Forbidden Shop',
        'owner_name' => 'Forbidden Owner',
        'phone' => '9876543200',
        'status' => true,
    ]);
    $response->assertStatus(403);
});

test('super_admin and manager can create a shop via API', function () {
    foreach ([$this->superAdmin, $this->manager] as $index => $user) {
        Sanctum::actingAs($user);

        $code = "SHP-CREATE-0{$index}";
        $payload = [
            'shop_code' => $code,
            'name' => "Created Shop {$index}",
            'owner_name' => "Owner {$index}",
            'phone' => "987654321{$index}",
            'email' => "shop{$index}@example.com",
            'village_id' => $this->village->id,
            'area' => 'Central Market',
            'address' => '456 Commercial Street',
            'status' => true,
            'credit_limit' => 20000.00,
            'notes' => 'Frequent customer',
        ];

        $response = $this->postJson('/api/shops', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Shop created successfully',
                'data' => [
                    'shop_code' => $code,
                    'name' => "Created Shop {$index}",
                    'owner_name' => "Owner {$index}",
                    'phone' => "987654321{$index}",
                    'status' => true,
                    'credit_limit' => 20000.00,
                    'village' => [
                        'id' => $this->village->id,
                        'name' => 'API Shop Test Village',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('shops', [
            'shop_code' => $code,
            'name' => "Created Shop {$index}",
        ]);
    }
});

test('center_staff gets 403 when trying to create a shop', function () {
    Sanctum::actingAs($this->centerStaff);

    $response = $this->postJson('/api/shops', [
        'shop_code' => 'SHP-STAFF-DENIED',
        'name' => 'Staff Shop',
        'owner_name' => 'Staff Owner',
        'phone' => '9876543222',
        'status' => true,
    ]);

    $response->assertStatus(403);
});

test('validation errors return 422 response with error details', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shops', [
        'shop_code' => '',
        'name' => '',
        'owner_name' => '',
        'phone' => '',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
        ])
        ->assertJsonValidationErrors(['shop_code', 'name', 'owner_name', 'phone']);
});

test('duplicate shop code is rejected with 422', function () {
    Shop::create([
        'shop_code' => 'SHP-UNIQUE-01',
        'name' => 'Existing Shop',
        'owner_name' => 'Owner 1',
        'phone' => '9876543200',
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shops', [
        'shop_code' => 'SHP-UNIQUE-01',
        'name' => 'Duplicate Shop',
        'owner_name' => 'Owner 2',
        'phone' => '9876543201',
        'status' => true,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['shop_code']);
});

test('authorized user can view single shop details', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-VIEW-01',
        'name' => 'View Single Shop',
        'owner_name' => 'Single Owner',
        'phone' => '9876543205',
        'village_id' => $this->village->id,
        'status' => true,
    ]);

    Sanctum::actingAs($this->centerStaff);

    $response = $this->getJson("/api/shops/{$shop->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Shop retrieved successfully',
            'data' => [
                'id' => $shop->id,
                'shop_code' => 'SHP-VIEW-01',
                'name' => 'View Single Shop',
                'village' => [
                    'id' => $this->village->id,
                    'name' => 'API Shop Test Village',
                ],
            ],
        ]);
});

test('super_admin and manager can update shop details', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-UPDATE-01',
        'name' => 'Old Shop Name',
        'owner_name' => 'Old Owner',
        'phone' => '9876543210',
        'status' => true,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->putJson("/api/shops/{$shop->id}", [
        'shop_code' => 'SHP-UPDATE-01', // Same shop_code allowed on update
        'name' => 'Updated Shop Name',
        'owner_name' => 'Updated Owner',
        'phone' => '9876543999',
        'status' => true,
        'credit_limit' => 35000.00,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Shop updated successfully',
            'data' => [
                'id' => $shop->id,
                'name' => 'Updated Shop Name',
                'owner_name' => 'Updated Owner',
                'phone' => '9876543999',
                'credit_limit' => 35000.00,
            ],
        ]);

    $this->assertDatabaseHas('shops', [
        'id' => $shop->id,
        'name' => 'Updated Shop Name',
    ]);
});

test('center_staff gets 403 when trying to update a shop', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-STAFF-UPD',
        'name' => 'Staff Update Test',
        'owner_name' => 'Owner',
        'phone' => '9876543211',
    ]);

    Sanctum::actingAs($this->centerStaff);

    $response = $this->putJson("/api/shops/{$shop->id}", [
        'shop_code' => 'SHP-STAFF-UPD',
        'name' => 'Attempted Update',
        'owner_name' => 'Owner',
        'phone' => '9876543211',
        'status' => true,
    ]);

    $response->assertStatus(403);
});

test('authorized user can toggle shop status via API', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-TOGGLE',
        'name' => 'Toggle Status Shop',
        'owner_name' => 'Owner',
        'phone' => '9876543212',
        'status' => true,
    ]);

    Sanctum::actingAs($this->manager);

    $response = $this->patchJson("/api/shops/{$shop->id}/toggle-status");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Shop status updated successfully',
            'data' => [
                'id' => $shop->id,
                'status' => false,
            ],
        ]);

    $this->assertDatabaseHas('shops', [
        'id' => $shop->id,
        'status' => false,
    ]);
});

test('super_admin and manager can delete a shop', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-DELETE-01',
        'name' => 'Delete Test Shop',
        'owner_name' => 'Delete Owner',
        'phone' => '9876543213',
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->deleteJson("/api/shops/{$shop->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Shop deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('shops', [
        'id' => $shop->id,
    ]);
});

test('center_staff gets 403 when trying to delete a shop', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-STAFF-DEL',
        'name' => 'Staff Delete Test',
        'owner_name' => 'Owner',
        'phone' => '9876543214',
    ]);

    Sanctum::actingAs($this->centerStaff);

    $response = $this->deleteJson("/api/shops/{$shop->id}");

    $response->assertStatus(403);
});

test('shops index filtering by search, status, and village_id works', function () {
    $village2 = Village::create(['name' => 'Village 2', 'code' => 'VIL-02']);

    Shop::create([
        'shop_code' => 'SHP-ALPHA',
        'name' => 'Alpha Bakery',
        'owner_name' => 'Ahmad',
        'phone' => '9000000001',
        'village_id' => $this->village->id,
        'status' => true,
    ]);

    Shop::create([
        'shop_code' => 'SHP-BETA',
        'name' => 'Beta Sweets',
        'owner_name' => 'Bablu',
        'phone' => '9000000002',
        'village_id' => $village2->id,
        'status' => false,
    ]);

    Sanctum::actingAs($this->superAdmin);

    // Search filter
    $response = $this->getJson('/api/shops?search=Alpha');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Alpha Bakery');

    // Status filter
    $response = $this->getJson('/api/shops?status=0');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Beta Sweets');

    // Village filter
    $response = $this->getJson("/api/shops?village_id={$village2->id}");
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Beta Sweets');
});
