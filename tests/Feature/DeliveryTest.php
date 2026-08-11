<?php

use App\Models\Delivery;
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
    $this->driverStaff = User::factory()->create(['role' => 'center_staff', 'name' => 'Driver Rahul']);

    $this->shop = Shop::create([
        'shop_code' => 'SHP-DEL-01',
        'name' => 'Metro Dairy Hub',
        'owner_name' => 'Sunil Kumar',
        'phone' => '9876543210',
        'address' => '123 Market Street',
        'status' => true,
    ]);

    $this->product = Product::create([
        'product_code' => 'PRD-DEL-MILK',
        'name' => 'Fresh Packaged Milk',
        'category' => 'processed_milk',
        'unit' => 'Litre',
        'unit_price' => 50.00,
        'stock_quantity' => 200.00,
        'status' => true,
    ]);

    // Create confirmed shop order (stock is deducted here)
    $this->order = ShopOrder::create([
        'order_number' => 'ORD-2026-9999',
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
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

test('guests are redirected to login when accessing deliveries', function () {
    $response = $this->get(route('deliveries.index'));
    $response->assertRedirect('/login');

    $response = $this->get(route('deliveries.create'));
    $response->assertRedirect('/login');
});

test('center staff can view and create deliveries but collection staff is forbidden', function () {
    $response = $this->actingAs($this->centerStaff)->get(route('deliveries.index'));
    $response->assertOk();

    $response = $this->actingAs($this->collectionStaff)->get(route('deliveries.index'));
    $response->assertStatus(403);
});

test('creating delivery links to shop order and pre-fills shop recipient data', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('deliveries.store'), [
        'shop_order_id' => $this->order->id,
        'delivery_date' => '2026-08-11',
        'status' => 'pending',
        'notes' => 'Handle with care',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('deliveries.index'));

    $delivery = Delivery::latest()->first();

    expect($delivery->shop_order_id)->toBe($this->order->id)
        ->and($delivery->shop_id)->toBe($this->shop->id)
        ->and($delivery->delivery_address)->toBe($this->shop->address)
        ->and($delivery->contact_person)->toBe($this->shop->owner_name)
        ->and($delivery->contact_phone)->toBe($this->shop->phone)
        ->and($delivery->status)->toBe('pending');
});

test('prevent creating duplicate active delivery for the same order', function () {
    // First delivery
    Delivery::create([
        'delivery_number' => 'DEL-2026-0001',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    // Second delivery attempt
    $response = $this->actingAs($this->superAdmin)->post(route('deliveries.store'), [
        'shop_order_id' => $this->order->id,
        'delivery_date' => '2026-08-11',
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors(['shop_order_id']);
    expect(Delivery::count())->toBe(1);
});

test('assigning staff to delivery updates status and assigned_to', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0002',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'pending',
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('deliveries.update-status', $delivery), [
        'status' => 'assigned',
        'assigned_to' => $this->driverStaff->id,
    ]);

    $response->assertSessionHasNoErrors();
    expect($delivery->fresh()->status)->toBe('assigned')
        ->and($delivery->fresh()->assigned_to)->toBe($this->driverStaff->id);
});

test('transitioning to out_for_delivery sets dispatched_at and syncs order status', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0003',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'assigned',
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('deliveries.update-status', $delivery), [
        'status' => 'out_for_delivery',
    ]);

    $response->assertSessionHasNoErrors();
    expect($delivery->fresh()->dispatched_at)->not->toBeNull()
        ->and($this->order->fresh()->status)->toBe('dispatched');
});

test('transitioning to delivered sets delivered_at and syncs order status', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0004',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'out_for_delivery',
        'dispatched_at' => now(),
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('deliveries.update-status', $delivery), [
        'status' => 'delivered',
    ]);

    $response->assertSessionHasNoErrors();
    expect($delivery->fresh()->delivered_at)->not->toBeNull()
        ->and($this->order->fresh()->status)->toBe('delivered');
});

test('invalid status transition from delivered back to pending fails', function () {
    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0005',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'delivered',
        'delivered_at' => now(),
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('deliveries.update-status', $delivery), [
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors(['status']);
    expect($delivery->fresh()->status)->toBe('delivered');
});

test('delivery status change does not deduct product stock again', function () {
    $initialStock = $this->product->stock_quantity; // 200

    $delivery = Delivery::create([
        'delivery_number' => 'DEL-2026-0006',
        'shop_order_id' => $this->order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'out_for_delivery',
        'dispatched_at' => now(),
        'assigned_to' => $this->driverStaff->id,
        'delivery_address' => $this->shop->address,
        'contact_person' => $this->shop->owner_name,
        'contact_phone' => $this->shop->phone,
        'created_by' => $this->superAdmin->id,
    ]);

    // Mark as delivered
    $this->actingAs($this->superAdmin)->patch(route('deliveries.update-status', $delivery), [
        'status' => 'delivered',
    ]);

    expect($this->product->fresh()->stock_quantity)->toBe($initialStock);
});
