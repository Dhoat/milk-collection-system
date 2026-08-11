<?php

use App\Models\Delivery;
use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\MilkReceiving;
use App\Models\MilkStock;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);

    $this->village = Village::create([
        'code' => 'VIL-RPT-01',
        'name' => 'Green Valley Village',
        'district' => 'Central',
        'state' => 'Punjab',
        'status' => true,
    ]);

    $this->farmer = Farmer::create([
        'farmer_code' => 'FRM-RPT-01',
        'village_id' => $this->village->id,
        'name' => 'Gurmeet Singh',
        'mobile' => '9812345678',
        'status' => true,
    ]);

    $this->shop = Shop::create([
        'shop_code' => 'SHP-RPT-01',
        'name' => 'Metro Dairy Express',
        'owner_name' => 'Rajesh Sharma',
        'phone' => '9888877777',
        'status' => true,
    ]);

    $this->product = Product::create([
        'product_code' => 'PRD-RPT-CURD',
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
        'order_number' => 'ORD-RPT-001',
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
        'delivery_number' => 'DEL-RPT-001',
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

test('guests are redirected to login when accessing daily and monthly reports', function () {
    $response = $this->get(route('reports.daily'));
    $response->assertRedirect('/login');

    $response = $this->get(route('reports.monthly'));
    $response->assertRedirect('/login');
});

test('only super_admin and manager can access daily and monthly reports', function () {
    // Super admin & Manager OK
    $this->actingAs($this->superAdmin)->get(route('reports.daily'))->assertOk();
    $this->actingAs($this->manager)->get(route('reports.daily'))->assertOk();

    $this->actingAs($this->superAdmin)->get(route('reports.monthly'))->assertOk();
    $this->actingAs($this->manager)->get(route('reports.monthly'))->assertOk();

    // Center staff & Collection staff Forbidden (403)
    $this->actingAs($this->centerStaff)->get(route('reports.daily'))->assertStatus(403);
    $this->actingAs($this->collectionStaff)->get(route('reports.daily'))->assertStatus(403);

    $this->actingAs($this->centerStaff)->get(route('reports.monthly'))->assertStatus(403);
    $this->actingAs($this->collectionStaff)->get(route('reports.monthly'))->assertStatus(403);
});

test('daily report correctly aggregates collection, receiving, stock, order, and delivery metrics', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('reports.daily', ['date' => '2026-08-11']));

    $response->assertOk();
    $data = $response->viewData('reportData');

    expect($data['date'])->toBe('2026-08-11')
        ->and($data['collection']['total_litres'])->toEqual(50.00)
        ->and($data['collection']['farmers_count'])->toBe(1)
        ->and($data['collection']['total_amount'])->toEqual(2000.00)
        ->and($data['center']['total_received'])->toEqual(50.00)
        ->and($data['stock']['in'])->toEqual(50.00)
        ->and($data['orders']['total_value'])->toEqual(400.00)
        ->and($data['deliveries']['by_status']['delivered'])->toBe(1)
        ->and($data['products']['total_units'])->toEqual(5.00)
        ->and($data['financial']['net_balance'])->toEqual(-1600.00);
});

test('monthly report correctly aggregates monthly totals across date range', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('reports.monthly', ['month' => 8, 'year' => 2026]));

    $response->assertOk();
    $data = $response->viewData('reportData');

    expect($data['month'])->toBe(8)
        ->and($data['year'])->toBe(2026)
        ->and($data['collection']['total_litres'])->toEqual(50.00)
        ->and($data['collection']['total_amount'])->toEqual(2000.00)
        ->and($data['orders']['total_value'])->toEqual(400.00)
        ->and(count($data['orders']['top_shops']))->toBe(1)
        ->and(count($data['products']['items']))->toBe(1);
});
