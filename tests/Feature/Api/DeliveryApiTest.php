<?php

use App\Models\Delivery;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);
    $this->driverStaff = User::factory()->create(['role' => 'center_staff', 'name' => 'Driver Rahul']);

    $this->shop = Shop::create([
        'shop_code' => 'SHP-API-DEL-01',
        'name' => 'Metro Dairy Hub',
        'owner_name' => 'Sunil Kumar',
        'phone' => '9876543210',
        'address' => '123 Market Street',
        'status' => true,
    ]);

    $this->product = Product::create([
        'product_code' => 'PRD-API-DEL-MILK',
        'name' => 'Fresh Packaged Milk',
        'category' => 'processed_milk',
        'unit' => 'Litre',
        'unit_price' => 50.00,
        'stock_quantity' => 200.00,
        'status' => true,
    ]);

    $this->order = ShopOrder::create([
        'order_number' => 'ORD-2026-8888',
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'subtotal' => 500.00,
        'discount' => 0.00,
        'total_amount' => 500.00,
        'stock_deducted' => true,
        'created_by' => $this->superAdmin->id,
    ]);

    $this->order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'unit' => $this->product->unit,
        'quantity' => 10,
        'unit_price' => 50.00,
        'line_total' => 500.00,
    ]);
});

test('unauthenticated request to Deliveries API returns 401', function () {
    $response = $this->getJson('/api/deliveries');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $response = $this->postJson('/api/deliveries', []);
    $response->assertStatus(401);

    $response = $this->getJson('/api/deliveries/1');
    $response->assertStatus(401);

    $response = $this->patchJson('/api/deliveries/1/status', []);
    $response->assertStatus(401);

    $response = $this->deleteJson('/api/deliveries/1');
    $response->assertStatus(401);
});

test('authorized roles can list deliveries and collection_staff receives 403', function () {
    foreach ([$this->superAdmin, $this->manager, $this->centerStaff] as $user) {
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/deliveries');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Deliveries retrieved successfully',
            ]);
    }

    Sanctum::actingAs($this->collectionStaff);
    $response = $this->getJson('/api/deliveries');
    $response->assertStatus(403);
});

test('authorized user can create delivery linked to shop order with pre-filled shop recipient details', function () {
    Sanctum::actingAs($this->centerStaff);

    $response = $this->postJson('/api/deliveries', [
        'shop_order_id' => $this->order->id,
        'delivery_date' => '2026-08-13',
        'status' => 'pending',
        'notes' => 'Handle with care',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Delivery created successfully',
            'data' => [
                'shop_order_id' => $this->order->id,
                'shop_id' => $this->shop->id,
                'delivery_address' => $this->shop->address,
                'contact_person' => $this->shop->owner_name,
                'contact_phone' => $this->shop->phone,
                'status' => 'pending',
                'notes' => 'Handle with care',
            ],
        ]);

    $delivery = Delivery::latest()->first();
    expect($delivery->shop_order_id)->toBe($this->order->id)
        ->and($delivery->shop_id)->toBe($this->shop->id);
});

test('creating duplicate active delivery for the same order returns 422', function () {
    Delivery::create([
        'delivery_number' => 'DEL-2026-0001',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/deliveries', [
        'shop_order_id' => $this->order->id,
        'delivery_date' => '2026-08-13',
        'status' => 'pending',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['shop_order_id']);

    expect(Delivery::count())->toBe(1);
});

test('invalid shop_order_id or status validation returns 422', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/deliveries', [
        'shop_order_id' => 99999, // non-existent order
        'delivery_date' => 'invalid-date',
        'status' => 'invalid-status',
        'assigned_to' => 99999, // non-existent user
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['shop_order_id', 'delivery_date', 'status', 'assigned_to']);
});

test('assigning staff to delivery updates status to assigned', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0002',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->patchJson("/api/deliveries/{$delivery->id}/status", [
        'status' => 'assigned',
        'assigned_to' => $this->driverStaff->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $delivery->id,
                'status' => 'assigned',
                'assigned_to' => $this->driverStaff->id,
            ],
        ]);

    expect($delivery->fresh()->status)->toBe('assigned')
        ->and($delivery->fresh()->assigned_to)->toBe($this->driverStaff->id);
});

test('transitioning status to out_for_delivery sets dispatched_at and syncs order status to dispatched', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0003',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'assigned',
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->patchJson("/api/deliveries/{$delivery->id}/status", [
        'status' => 'out_for_delivery',
    ]);

    $response->assertStatus(200);

    expect($delivery->fresh()->dispatched_at)->not->toBeNull()
        ->and($this->order->fresh()->status)->toBe('dispatched');
});

test('transitioning status to delivered sets delivered_at and syncs order status to delivered', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0004',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'out_for_delivery',
        'dispatched_at' => now(),
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->patchJson("/api/deliveries/{$delivery->id}/status", [
        'status' => 'delivered',
    ]);

    $response->assertStatus(200);

    expect($delivery->fresh()->delivered_at)->not->toBeNull()
        ->and($this->order->fresh()->status)->toBe('delivered');
});

test('invalid status transition from delivered back to pending returns 422', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0005',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'delivered',
        'delivered_at' => now(),
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    $response = $this->patchJson("/api/deliveries/{$delivery->id}/status", [
        'status' => 'pending',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);

    expect($delivery->fresh()->status)->toBe('delivered');
});

test('super_admin and manager can delete delivery and center_staff is forbidden from deleting', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0006',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-13',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    // center_staff forbidden from deleting
    Sanctum::actingAs($this->centerStaff);
    $response = $this->deleteJson("/api/deliveries/{$delivery->id}");
    $response->assertStatus(403);

    // super_admin allowed to delete
    Sanctum::actingAs($this->superAdmin);
    $response = $this->deleteJson("/api/deliveries/{$delivery->id}");
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Delivery deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('deliveries', ['id' => $delivery->id]);
});

test('filtering deliveries by search, status, shop_id, assigned_to, and date works via API', function () {
    $shop2 = Shop::create([
        'shop_code' => 'SHP-API-DEL-02',
        'name' => 'Royal Bakery Hub',
        'owner_name' => 'Ramesh',
        'phone' => '9876543211',
        'address' => '456 Bakery Lane',
        'status' => true,
    ]);

    $order2 = ShopOrder::create([
        'order_number' => 'ORD-2026-7777',
        'shop_id' => $shop2->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'subtotal' => 300.00,
        'total_amount' => 300.00,
        'created_by' => $this->superAdmin->id,
    ]);

    $del1 = Delivery::create([
        'delivery_number' => 'DEL-2026-0010',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-10',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    $del2 = Delivery::create([
        'delivery_number' => 'DEL-2026-0011',
        'shop_order_id' => $order2->id,
        'shop_id' => $shop2->id,
        'delivery_date' => '2026-08-12',
        'status' => 'assigned',
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $shop2->address,
        'contact_person' => $shop2->owner_name,
        'contact_phone' => $shop2->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    // Search filter
    $response = $this->getJson('/api/deliveries?search=DEL-2026-0010');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.delivery_number', 'DEL-2026-0010');

    // Status filter
    $response = $this->getJson('/api/deliveries?status=assigned');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.delivery_number', 'DEL-2026-0011');

    // Shop filter
    $response = $this->getJson("/api/deliveries?shop_id={$shop2->id}");
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.delivery_number', 'DEL-2026-0011');

    // Assigned staff filter
    $response = $this->getJson("/api/deliveries?assigned_to={$this->driverStaff->id}");
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.delivery_number', 'DEL-2026-0011');

    // Date filter
    $response = $this->getJson('/api/deliveries?date=2026-08-10');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.delivery_number', 'DEL-2026-0010');
});
