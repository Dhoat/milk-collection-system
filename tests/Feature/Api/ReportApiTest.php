<?php

use App\Models\Delivery;
use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
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
        'code' => 'VIL-API-RPT-01',
        'name' => 'Green Valley Village',
        'district' => 'Central',
        'state' => 'Punjab',
        'status' => true,
    ]);

    $this->farmer = Farmer::create([
        'farmer_code' => 'FRM-API-RPT-01',
        'village_id' => $this->village->id,
        'name' => 'Gurmeet Singh',
        'mobile' => '9812345678',
        'status' => true,
    ]);

    $this->shop = Shop::create([
        'shop_code' => 'SHP-API-RPT-01',
        'name' => 'Metro Dairy Express',
        'owner_name' => 'Rajesh Sharma',
        'phone' => '9888877777',
        'status' => true,
    ]);

    $this->product = Product::create([
        'product_code' => 'PRD-API-RPT-CURD',
        'name' => 'Fresh Pouch Curd',
        'category' => 'dairy_product',
        'unit' => 'Kg',
        'unit_price' => 80.00,
        'stock_quantity' => 100.00,
        'status' => true,
    ]);

    // Seed Milk Collection for target date 2026-08-11
    MilkCollection::create([
        'farmer_id' => $this->farmer->id,
        'collection_date' => '2026-08-11',
        'shift' => 'morning',
        'milk_quantity' => 50.00,
        'fat' => 4.5,
        'snf' => 8.5,
        'rate' => 40.00,
        'amount' => 2000.00,
    ]);

    // Seed Milk Receiving for 2026-08-11 (automatically triggers Stock IN of 50 Litres)
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => '2026-08-11',
        'shift' => 'morning',
        'expected_quantity' => 50.00,
        'received_quantity' => 50.00,
        'expected_fat' => 4.5,
        'received_fat' => 4.5,
        'expected_snf' => 8.5,
        'received_snf' => 8.5,
        'status' => 'confirmed',
        'verified_by' => $this->centerStaff->id,
    ]);

    // Seed Shop Order for 2026-08-11
    $order = ShopOrder::create([
        'order_number' => 'ORD-API-RPT-001',
        'shop_id' => $this->shop->id,
        'order_date' => '2026-08-11',
        'status' => 'confirmed',
        'subtotal' => 400.00,
        'discount' => 0.00,
        'total_amount' => 400.00,
        'stock_deducted' => true,
        'created_by' => $this->superAdmin->id,
    ]);

    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'unit' => $this->product->unit,
        'quantity' => 5.00,
        'unit_price' => 80.00,
        'line_total' => 400.00,
    ]);

    // Seed Delivery for 2026-08-11
    Delivery::create([
        'delivery_number' => 'DEL-API-RPT-001',
        'shop_order_id' => $order->id,
        'shop_id' => $this->shop->id,
        'delivery_date' => '2026-08-11',
        'status' => 'delivered',
        'delivery_address' => 'Shop Premises',
        'contact_person' => 'Rajesh',
        'contact_phone' => '9888877777',
        'created_by' => $this->superAdmin->id,
        'delivered_at' => now(),
    ]);
});

test('unauthenticated request to Reports API returns 401', function () {
    $response = $this->getJson('/api/reports/daily');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);

    $response = $this->getJson('/api/reports/monthly');
    $response->assertStatus(401);
});

test('only super_admin and manager can access reports while center_staff and collection_staff receive 403', function () {
    foreach ([$this->superAdmin, $this->manager] as $user) {
        Sanctum::actingAs($user);

        $this->getJson('/api/reports/daily')->assertStatus(200);
        $this->getJson('/api/reports/monthly')->assertStatus(200);
    }

    foreach ([$this->centerStaff, $this->collectionStaff] as $user) {
        Sanctum::actingAs($user);

        $this->getJson('/api/reports/daily')->assertStatus(403);
        $this->getJson('/api/reports/monthly')->assertStatus(403);
    }
});

test('daily report endpoint returns accurate metrics matching web report service', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson('/api/reports/daily?date=2026-08-11');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Daily report retrieved successfully',
            'data' => [
                'date' => '2026-08-11',
                'collection' => [
                    'farmers_count' => 1,
                    'total_litres' => 50,
                    'avg_per_farmer' => 50,
                    'total_amount' => 2000,
                    'avg_fat' => 4.5,
                    'avg_snf' => 8.5,
                ],
                'center' => [
                    'total_received' => 50,
                    'records_count' => 1,
                    'diff_litres' => 0,
                ],
                'stock' => [
                    'in' => 50,
                ],
                'orders' => [
                    'total_count' => 1,
                    'total_value' => 400,
                ],
                'deliveries' => [
                    'total_count' => 1,
                    'by_status' => [
                        'delivered' => 1,
                    ],
                ],
                'products' => [
                    'total_units' => 5,
                ],
                'financial' => [
                    'total_sales' => 400,
                    'milk_expense' => 2000,
                    'net_balance' => -1600,
                ],
            ],
        ]);
});

test('monthly report endpoint returns accurate aggregated metrics matching web report service', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson('/api/reports/monthly?month=8&year=2026');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Monthly report retrieved successfully',
            'data' => [
                'month' => 8,
                'year' => 2026,
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-31',
                'days_in_month' => 31,
                'collection' => [
                    'total_litres' => 50,
                    'farmers_count' => 1,
                    'total_amount' => 2000,
                ],
                'orders' => [
                    'total_count' => 1,
                    'total_value' => 400,
                ],
                'financial' => [
                    'total_sales' => 400,
                    'milk_expense' => 2000,
                    'net_balance' => -1600,
                ],
            ],
        ]);
});

test('filters work correctly on daily and monthly reports', function () {
    Sanctum::actingAs($this->superAdmin);

    // Filter by matching village_id
    $response = $this->getJson("/api/reports/daily?date=2026-08-11&village_id={$this->village->id}");
    $response->assertStatus(200)
        ->assertJsonPath('data.collection.total_litres', 50);

    // Filter by matching shop_id
    $response = $this->getJson("/api/reports/daily?date=2026-08-11&shop_id={$this->shop->id}");
    $response->assertStatus(200)
        ->assertJsonPath('data.orders.total_value', 400);

    // Filter by matching product_id
    $response = $this->getJson("/api/reports/monthly?month=8&year=2026&product_id={$this->product->id}");
    $response->assertStatus(200)
        ->assertJsonPath('data.products.total_units', 5);
});

test('validation errors on invalid date, month, year, or foreign key filters return 422', function () {
    Sanctum::actingAs($this->superAdmin);

    // Invalid date
    $response = $this->getJson('/api/reports/daily?date=invalid-date');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date']);

    // Invalid month and year
    $response = $this->getJson('/api/reports/monthly?month=13&year=1800');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['month', 'year']);

    // Invalid village_id
    $response = $this->getJson('/api/reports/daily?village_id=99999');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['village_id']);
});

test('empty period returns zeroed reports safely without errors', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson('/api/reports/daily?date=2020-01-01');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'date' => '2020-01-01',
                'collection' => [
                    'farmers_count' => 0,
                    'total_litres' => 0,
                    'total_amount' => 0,
                ],
                'orders' => [
                    'total_count' => 0,
                    'total_value' => 0,
                ],
                'financial' => [
                    'net_balance' => 0,
                ],
            ],
        ]);
});

test('reports endpoints do not modify database state', function () {
    Sanctum::actingAs($this->superAdmin);

    $collectionsBefore = MilkCollection::count();
    $receivingsBefore = MilkReceiving::count();
    $ordersBefore = ShopOrder::count();

    $this->getJson('/api/reports/daily?date=2026-08-11');
    $this->getJson('/api/reports/monthly?month=8&year=2026');

    expect(MilkCollection::count())->toBe($collectionsBefore)
        ->and(MilkReceiving::count())->toBe($receivingsBefore)
        ->and(ShopOrder::count())->toBe($ordersBefore);
});
