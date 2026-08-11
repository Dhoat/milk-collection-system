<?php

use App\Models\MilkStock;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);

    $this->shop = Shop::create([
        'shop_code' => 'SHP-ORD-01',
        'name' => 'City Dairy Outlet',
        'owner_name' => 'Vikram Singh',
        'phone' => '9988776655',
        'status' => true,
        'credit_limit' => 50000.00,
    ]);

    $this->curdProduct = Product::create([
        'product_code' => 'PRD-TEST-CURD',
        'name' => 'Test Fresh Curd',
        'category' => 'dairy_product',
        'unit' => 'Kg',
        'unit_price' => 100.00,
        'stock_quantity' => 50.00,
        'status' => true,
    ]);

    $this->gheeProduct = Product::create([
        'product_code' => 'PRD-TEST-GHEE',
        'name' => 'Test Pure Ghee',
        'category' => 'dairy_product',
        'unit' => 'Kg',
        'unit_price' => 500.00,
        'stock_quantity' => 20.00,
        'status' => true,
    ]);
});

test('guests are redirected to login when accessing shop orders', function () {
    $response = $this->get(route('shop-orders.index'));
    $response->assertRedirect('/login');

    $response = $this->get(route('shop-orders.create'));
    $response->assertRedirect('/login');
});

test('center staff can view and create shop orders but collection staff is forbidden', function () {
    $response = $this->actingAs($this->centerStaff)->get(route('shop-orders.index'));
    $response->assertOk();

    $response = $this->actingAs($this->collectionStaff)->get(route('shop-orders.index'));
    $response->assertStatus(403);
});

test('order creation calculates line totals, subtotal, and total amount on server side', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'pending',
        'discount' => 50.00,
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

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('shop-orders.index'));

    $order = ShopOrder::latest()->first();

    expect($order->subtotal)->toBe(1500.00)
        ->and($order->discount)->toBe(50.00)
        ->and($order->total_amount)->toBe(1450.00)
        ->and($order->items)->toHaveCount(2)
        ->and($order->stock_deducted)->toBeFalse();
});

test('pending order does not deduct product stock', function () {
    $initialCurdStock = $this->curdProduct->stock_quantity;

    $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'pending',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 10, 'unit_price' => 100.00],
        ],
    ]);

    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialCurdStock);
});

test('confirmed order deducts product stock', function () {
    $initialStock = $this->curdProduct->stock_quantity;

    $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 10, 'unit_price' => 100.00],
        ],
    ]);

    $order = ShopOrder::latest()->first();
    expect($order->status)->toBe('confirmed')
        ->and($order->stock_deducted)->toBeTrue()
        ->and($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 10);
});

test('stock validation fails if requested quantity exceeds available stock', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 9999, 'unit_price' => 100.00],
        ],
    ]);

    $response->assertSessionHasErrors(['items']);
    expect(ShopOrder::count())->toBe(0);
});

test('transitioning order status multiple times prevents duplicate stock deduction', function () {
    $initialStock = $this->curdProduct->stock_quantity;

    $response = $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 5, 'unit_price' => 100.00],
        ],
    ]);

    $order = ShopOrder::latest()->first();
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 5);

    // Transition confirmed -> preparing
    $this->actingAs($this->superAdmin)->patch(route('shop-orders.update-status', $order), ['status' => 'preparing']);
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 5);

    // Transition preparing -> dispatched
    $this->actingAs($this->superAdmin)->patch(route('shop-orders.update-status', $order), ['status' => 'dispatched']);
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 5);

    // Transition dispatched -> delivered
    $this->actingAs($this->superAdmin)->patch(route('shop-orders.update-status', $order), ['status' => 'delivered']);
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 5);
});

test('cancelling a confirmed order reverses stock deduction', function () {
    $initialStock = $this->curdProduct->stock_quantity;

    $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $this->curdProduct->id, 'quantity' => 8, 'unit_price' => 100.00],
        ],
    ]);

    $order = ShopOrder::latest()->first();
    expect($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock - 8);

    // Cancel order
    $this->actingAs($this->superAdmin)->patch(route('shop-orders.update-status', $order), ['status' => 'cancelled']);

    expect($order->fresh()->status)->toBe('cancelled')
        ->and($order->fresh()->stock_deducted)->toBeFalse()
        ->and($this->curdProduct->fresh()->stock_quantity)->toBe($initialStock);
});

test('raw milk product order integrates directly with MilkStock ledger', function () {
    // Create stock IN for raw milk
    MilkStock::create([
        'transaction_date' => '2026-08-11',
        'type' => 'in',
        'item_type' => 'raw_milk',
        'quantity' => 100.00,
        'source_or_reason' => 'Milk Stock Intake',
        'created_by' => $this->superAdmin->id,
    ]);

    $rawMilkProduct = Product::create([
        'product_code' => 'PRD-RAW-TEST',
        'name' => 'Raw Milk Product',
        'category' => 'raw_milk',
        'unit' => 'Litre',
        'unit_price' => 60.00,
        'stock_quantity' => 0.00,
        'status' => true,
    ]);

    $initialAvailableMilk = MilkStock::getAvailableStock(); // 100.00

    $this->actingAs($this->superAdmin)->post(route('shop-orders.store'), [
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'items' => [
            ['product_id' => $rawMilkProduct->id, 'quantity' => 25.00, 'unit_price' => 60.00],
        ],
    ]);

    expect(MilkStock::getAvailableStock())->toBe($initialAvailableMilk - 25.00);
});
