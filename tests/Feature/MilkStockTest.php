<?php

use App\Models\MilkReceiving;
use App\Models\MilkStock;
use App\Models\User;
use App\Models\Village;

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);
    $this->village = Village::create([
        'name' => 'Stock Test Village',
        'code' => 'STK-001',
        'status' => true,
    ]);
});

test('creating milk receiving automatically creates stock in transaction', function () {
    $this->actingAs($this->centerStaff);

    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 500.00,
        'received_quantity' => 495.00,
        'expected_fat' => 4.5,
        'received_fat' => 4.4,
        'expected_snf' => 8.5,
        'received_snf' => 8.5,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
        'notes' => 'Tested batch intake',
    ]);

    $this->assertDatabaseHas('milk_stocks', [
        'milk_receiving_id' => $receiving->id,
        'type' => 'in',
        'quantity' => 495.00,
    ]);

    $this->assertEquals(495.00, MilkStock::getAvailableStock());
});

test('updating milk receiving updates existing stock in without duplicate', function () {
    $this->actingAs($this->centerStaff);

    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'evening',
        'expected_quantity' => 300.00,
        'received_quantity' => 300.00,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    $this->assertEquals(1, MilkStock::where('milk_receiving_id', $receiving->id)->count());
    $this->assertEquals(300.00, MilkStock::getAvailableStock());

    // Update receiving quantity
    $receiving->update(['received_quantity' => 350.00]);

    // Verify NO duplicate created and quantity updated
    $this->assertEquals(1, MilkStock::where('milk_receiving_id', $receiving->id)->count());
    $this->assertEquals(350.00, MilkStock::getAvailableStock());
});

test('deleting milk receiving removes corresponding stock in', function () {
    $this->actingAs($this->centerStaff);

    $receiving = MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 200.00,
        'received_quantity' => 200.00,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    $this->assertEquals(200.00, MilkStock::getAvailableStock());

    $receiving->delete();

    $this->assertDatabaseMissing('milk_stocks', [
        'milk_receiving_id' => $receiving->id,
    ]);

    $this->assertEquals(0.00, MilkStock::getAvailableStock());
});

test('stock out cannot exceed available stock', function () {
    $this->actingAs($this->centerStaff);

    // Add 100 L stock via receiving
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 100.00,
        'received_quantity' => 100.00,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    // Attempt Stock OUT of 150 L
    $response = $this->post(route('milk-stocks.store-out'), [
        'transaction_date' => now()->toDateString(),
        'quantity' => 150.00,
        'source_or_reason' => 'Shop Order #999',
    ]);

    $response->assertSessionHasErrors(['quantity']);
    $this->assertEquals(100.00, MilkStock::getAvailableStock());
});

test('valid stock out reduces available stock', function () {
    $this->actingAs($this->centerStaff);

    // Add 500 L stock
    MilkReceiving::create([
        'village_id' => $this->village->id,
        'receiving_date' => now()->toDateString(),
        'shift' => 'morning',
        'expected_quantity' => 500.00,
        'received_quantity' => 500.00,
        'status' => 'received',
        'verified_by' => $this->centerStaff->id,
    ]);

    // Record valid Stock OUT of 150 L
    $response = $this->post(route('milk-stocks.store-out'), [
        'transaction_date' => now()->toDateString(),
        'quantity' => 150.00,
        'source_or_reason' => 'Shop Order #101 Dispatch',
    ]);

    $response->assertRedirect(route('milk-stocks.index'));
    $this->assertEquals(350.00, MilkStock::getAvailableStock());
});

test('collection staff cannot access or manage stock', function () {
    $this->actingAs($this->collectionStaff);

    $response = $this->get(route('milk-stocks.index'));
    $response->assertStatus(403);

    $response = $this->post(route('milk-stocks.store-out'), [
        'transaction_date' => now()->toDateString(),
        'quantity' => 10.00,
        'source_or_reason' => 'Unauthorized Dispatch',
    ]);
    $response->assertStatus(403);
});
