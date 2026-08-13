<?php

use App\Models\MilkStock;
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

    $this->shop = Shop::create([
        'shop_code' => 'SHP-API-ORD-01',
        'name' => 'City Dairy Outlet',
        'owner_name' => 'Vikram Singh',
        'phone' => '9988776655',
        'status' => true,
        'credit_limit' => 50000.00,
    ]);

    $this->curdProduct = Product::create([
        'product_code' => 'PRD-API-CURD',
        'name' => 'Fresh Curd',
        'category' => 'dairy_product',
        'unit' => 'Kg',
        'unit_price' => 100.00,
        'stock_quantity' => 50.00,
        'status' => true,
    ]);

    $this->gheeProduct = Product::create([
        'product_code' => 'PRD-API-GHEE',
        'name' => 'Pure Ghee',
        'category' => 'dairy_product',
        'unit' => 'Kg',
        'unit_price' => 500.00,
        'stock_quantity' => 20.00,
        'status' => true,
    ]);
});

test('unauthenticated request to Shop Orders API returns 401', function () {
    $response = $this->getJson('/api/shop-orders');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $response = $this->postJson('/api/shop-orders', []);
    $response->assertStatus(401);

    $response = $this->getJson('/api/shop-orders/1');
    $response->assertStatus(401);

    $response = $this->patchJson('/api/shop-orders/1/status', []);
    $response->assertStatus(401);

    $response = $this->deleteJson('/api/shop-orders/1');
    $response->assertStatus(401);
});

test('authorized roles can list shop orders and collection_staff gets 403', function () {
    foreach ([$this->superAdmin, $this->manager, $this->centerStaff] as $user) {
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/shop-orders');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Shop orders retrieved successfully',
            ]);
    }

    Sanctum::actingAs($this->collectionStaff);
    $response = $this->getJson('/api/shop-orders');
    $response->assertStatus(403);
});

test('authorized user can create shop order with line totals, subtotal, and total amount calculated server side', function () {
    Sanctum::actingAs($this->centerStaff);

    $response = $this->postJson('/api/shop-orders', [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'pending',
        'discount' => 50.00,
        'notes' => 'API Order Test',
        'items' => [
            [
                'product_id' => $this->curdProduct->id,
                'quantity' => 5,
                'unit_price' => 100.00, // 5 * 100 = 500
            ],
            [
                'product_id' => $this->gheeProduct->id,
                'quantity' => 2,
                'unit_price' => 500.00, // 2 * 500 = 1000
            ],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Shop order created successfully',
            'data' => [
                'shop_id' => $this->shop->id,
                'status' => 'pending',
                'subtotal' => 1500.00,
                'discount' => 50.00,
                'total_amount' => 1450.00,
                'stock_deducted' => false,
                'items' => [
                    [
                        'product_id' => $this->curdProduct->id,
                        'quantity' => 5,
                        'line_total' => 500.00,
                    ],
                    [
                        'product_id' => $this->gheeProduct->id,
                        'quantity' => 2,
                        'line_total' => 1000.00,
                    ],
                ],
            ],
        ]);

    $order = ShopOrder::latest()->first();
    expect($order->subtotal)->toBe(1500.00)
        ->and($order->discount)->toBe(50.00)
        ->and($order->total_amount)->toBe(1450.00)
        ->and($order->items)->toHaveCount(2);
});

test('validation failure on invalid fields returns 422', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shop-orders', [
        'shop_id' => 9999, // non-existent shop
        'order_date' => 'invalid-date',
        'status' => 'invalid-status',
        'items' => [], // empty items
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'The given data was invalid.',
        ])
        ->assertJsonValidationErrors(['shop_id', 'order_date', 'status', 'items']);
});

test('insufficient stock returns 422 business error on stock-deducting order', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shop-orders', [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 9999, 'unit_price' => 100.00],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['items']);

    expect(ShopOrder::count())->toBe(0);
});

test('confirmed order deducts product stock via API', function () {
    $initialStock = $this->curdProduct->stock_quantity;

    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shop-orders', [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 10, 'unit_price' => 100.00],
        ],
    ]);

    $response->assertStatus(201);
    $order = ShopOrder::latest()->first();

    expect($order->status)->toBe('confirmed')
        ->and($order->stock_deducted)->toBeTrue()
        ->and($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 10);
});

test('updating order status to cancelled reverses product stock deduction', function () {
    $initialStock = $this->curdProduct->stock_quantity;

    Sanctum::actingAs($this->superAdmin);

    $this->postJson('/api/shop-orders', [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 8, 'unit_price' => 100.00],
        ],
    ]);

    $order = ShopOrder::latest()->first();
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 8);

    // Cancel order via API
    $response = $this->patchJson("/api/shop-orders/{$order->id}/status", [
        'status' => 'cancelled',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'status' => 'cancelled',
                'stock_deducted' => false,
            ],
        ]);

    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock);
});

test('raw milk product order integrates directly with MilkStock ledger via API', function () {
    MilkStock::create([
        'transaction_date' => '2026-08-13',
        'type' => 'in',
        'item_type' => 'raw_milk',
        'quantity' => 100.00,
        'source_or_reason' => 'Milk Stock Intake',
        'created_by' => $this->superAdmin->id,
    ]);

    $rawMilkProduct = Product::create([
        'product_code' => 'PRD-RAW-API',
        'name' => 'Raw Milk Product',
        'category' => 'raw_milk',
        'unit' => 'Litre',
        'unit_price' => 60.00,
        'stock_quantity' => 0.00,
        'status' => true,
    ]);

    $initialAvailable = MilkStock::getAvailableStock(); // 100.00

    Sanctum::actingAs($this->superAdmin);

    $response = $this->postJson('/api/shop-orders', [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $rawMilkProduct->id, 'quantity' => 30.00, 'unit_price' => 60.00],
        ],
    ]);

    $response->assertStatus(201);
    expect(MilkStock::getAvailableStock())->toBe($initialAvailable - 30.00);
});

test('super_admin and manager can delete shop order and center_staff is forbidden from deleting', function () {
    $order = ShopOrder::create([
        'order_number' => 'ORD-2026-9999',
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-13',
        'status' => 'pending',
        'subtotal' => 500.00,
        'total_amount' => 500.00,
        'stock_deducted' => false,
        'created_by' => $this->superAdmin->id,
    ]);

    // center_staff forbidden from deleting
    Sanctum::actingAs($this->centerStaff);
    $response = $this->deleteJson("/api/shop-orders/{$order->id}");
    $response->assertStatus(403);

    // super_admin allowed to delete
    Sanctum::actingAs($this->superAdmin);
    $response = $this->deleteJson("/api/shop-orders/{$order->id}");
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Shop order deleted successfully',
            'data' => null,
        ]);

    $this->assertDatabaseMissing('shop_orders', ['id' => $order->id]);
});

test('filtering shop orders by search, status, shop_id, and date works via API', function () {
    $shop2 = Shop::create([
        'shop_code' => 'SHP-API-ORD-02',
        'name' => 'Royal Bakery Outlet',
        'owner_name' => 'Ramesh',
        'phone' => '9988776644',
        'status' => true,
    ]);

    $order1 = ShopOrder::create([
        'order_number' => 'ORD-2026-0001',
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-10',
        'status' => 'pending',
        'subtotal' => 100.00,
        'total_amount' => 100.00,
        'created_by' => $this->superAdmin->id,
    ]);

    $order2 = ShopOrder::create([
        'order_number' => 'ORD-2026-0002',
        'shop_id' => $shop2->id,
        'order_date' => '2026-08-12',
        'status' => 'confirmed',
        'subtotal' => 200.00,
        'total_amount' => 200.00,
        'created_by' => $this->superAdmin->id,
    ]);

    Sanctum::actingAs($this->superAdmin);

    // Search filter by order number
    $response = $this->getJson('/api/shop-orders?search=ORD-2026-0001');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.order_number', 'ORD-2026-0001');

    // Status filter
    $response = $this->getJson('/api/shop-orders?status=confirmed');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.order_number', 'ORD-2026-0002');

    // Shop filter
    $response = $this->getJson("/api/shop-orders?shop_id={$shop2->id}");
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.order_number', 'ORD-2026-0002');

    // Date filter
    $response = $this->getJson('/api/shop-orders?date=2026-08-10');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.order_number', 'ORD-2026-0001');
});
