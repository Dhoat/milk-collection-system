<?php

use App\Models\Shop;
use App\Models\User;
use App\Models\Village;

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);
    $this->village = Village::create([
        'name' => 'Shop Test Village',
        'code' => 'SHP-VIL-01',
        'status' => true,
    ]);
});

test('guests are redirected to login when accessing shops', function () {
    $response = $this->get(route('shops.index'));
    $response->assertRedirect('/login');

    $response = $this->get(route('shops.create'));
    $response->assertRedirect('/login');
});

test('super admin and manager can view shops directory', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('shops.index'));
    $response->assertOk();
    $response->assertViewIs('shops.index');

    $response = $this->actingAs($this->manager)->get(route('shops.index'));
    $response->assertOk();
});

test('center staff can view shops directory but collection staff is forbidden', function () {
    $response = $this->actingAs($this->centerStaff)->get(route('shops.index'));
    $response->assertOk();

    $response = $this->actingAs($this->collectionStaff)->get(route('shops.index'));
    $response->assertStatus(403);
});

test('authorized user can create a shop', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('shops.store'), [
        'shop_code' => 'SHP-101',
        'name' => 'Sunrise Dairy Store',
        'owner_name' => 'Ramesh Sharma',
        'phone' => '9876543210',
        'email' => 'sunrise@example.com',
        'village_id' => $this->village->id,
        'area' => 'Market Square',
        'address' => '123 Main Road',
        'status' => '1',
        'credit_limit' => 50000.00,
        'notes' => 'Daily raw milk purchaser',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('shops.index'));

    $this->assertDatabaseHas('shops', [
        'shop_code' => 'SHP-101',
        'name' => 'Sunrise Dairy Store',
        'owner_name' => 'Ramesh Sharma',
        'phone' => '9876543210',
        'village_id' => $this->village->id,
        'status' => true,
        'credit_limit' => 50000.00,
    ]);
});

test('shop code must be unique', function () {
    Shop::create([
        'shop_code' => 'SHP-DUP',
        'name' => 'First Shop',
        'owner_name' => 'Owner 1',
        'phone' => '9876543211',
    ]);

    $response = $this->actingAs($this->superAdmin)->post(route('shops.store'), [
        'shop_code' => 'SHP-DUP',
        'name' => 'Second Shop',
        'owner_name' => 'Owner 2',
        'phone' => '9876543212',
        'status' => '1',
    ]);

    $response->assertSessionHasErrors(['shop_code']);
});

test('validation rules are enforced when registering a shop', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('shops.store'), [
        'shop_code' => '',
        'name' => '',
        'owner_name' => '',
        'phone' => '',
    ]);

    $response->assertSessionHasErrors(['shop_code', 'name', 'owner_name', 'phone']);
});

test('authorized user can view shop details', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-SHOW',
        'name' => 'Show Test Shop',
        'owner_name' => 'Show Owner',
        'phone' => '9876543213',
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('shops.show', $shop));
    $response->assertOk();
    $response->assertViewIs('shops.show');
});

test('authorized user can update shop', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-OLD',
        'name' => 'Old Shop Name',
        'owner_name' => 'Old Owner',
        'phone' => '9876543214',
        'status' => true,
    ]);

    $response = $this->actingAs($this->superAdmin)->put(route('shops.update', $shop), [
        'shop_code' => 'SHP-NEW',
        'name' => 'New Shop Name',
        'owner_name' => 'New Owner',
        'phone' => '9876543299',
        'status' => '1',
        'credit_limit' => 25000.00,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('shops.index'));

    $this->assertDatabaseHas('shops', [
        'id' => $shop->id,
        'shop_code' => 'SHP-NEW',
        'name' => 'New Shop Name',
        'owner_name' => 'New Owner',
        'phone' => '9876543299',
        'credit_limit' => 25000.00,
    ]);
});

test('authorized user can toggle shop active status', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-TOG',
        'name' => 'Toggle Shop',
        'owner_name' => 'Toggle Owner',
        'phone' => '9876543215',
        'status' => true,
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('shops.toggle-status', $shop));
    $response->assertRedirect();

    $this->assertDatabaseHas('shops', [
        'id' => $shop->id,
        'status' => false,
    ]);
});

test('authorized user can delete a shop', function () {
    $shop = Shop::create([
        'shop_code' => 'SHP-DEL',
        'name' => 'Delete Shop',
        'owner_name' => 'Del Owner',
        'phone' => '9876543216',
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('shops.destroy', $shop));
    $response->assertRedirect(route('shops.index'));

    $this->assertDatabaseMissing('shops', [
        'id' => $shop->id,
    ]);
});

test('search and status filters work correctly', function () {
    $shop1 = Shop::create([
        'shop_code' => 'SHP-ALPHA',
        'name' => 'Alpha Bakery',
        'owner_name' => 'Ahmad',
        'phone' => '9000000001',
        'status' => true,
    ]);

    $shop2 = Shop::create([
        'shop_code' => 'SHP-BETA',
        'name' => 'Beta Sweets',
        'owner_name' => 'Bablu',
        'phone' => '9000000002',
        'status' => false,
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('shops.index', ['search' => 'Alpha']));
    $response->assertOk();
    $response->assertSee('Alpha Bakery');
    $response->assertDontSee('Beta Sweets');

    $response = $this->actingAs($this->superAdmin)->get(route('shops.index', ['status' => '0']));
    $response->assertOk();
    $response->assertSee('Beta Sweets');
    $response->assertDontSee('Alpha Bakery');
});
