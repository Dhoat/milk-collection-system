<?php

use App\Models\User;
use App\Models\Village;
use App\Models\Farmer;

test('guests are redirected to login when accessing farmers', function () {
    $response = $this->get('/farmers');
    $response->assertRedirect('/login');

    $response = $this->get('/farmers/create');
    $response->assertRedirect('/login');

    $response = $this->post('/farmers', []);
    $response->assertRedirect('/login');
});

test('authenticated user can view farmers list', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/farmers');

    $response->assertOk();
    $response->assertViewIs('farmers.index');
});

test('authenticated user can view register farmer form', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/farmers/create');

    $response->assertOk();
    $response->assertViewIs('farmers.create');
});

test('authenticated user can register a farmer', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Active Village',
        'code' => 'VIL-ACT',
        'status' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/farmers', [
            'village_id' => $village->id,
            'farmer_code' => 'FMR-001',
            'name' => 'John Doe',
            'father_name' => 'Senior Doe',
            'mobile' => '9876543210',
            'alternate_mobile' => '9876543211',
            'address' => 'Farm 1',
            'gender' => 'male',
            'joining_date' => '2026-08-04',
            'bank_name' => 'Example Bank',
            'account_number' => '1234567890',
            'ifsc_code' => 'EXAMP000123',
            'status' => '1',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/farmers');

    $this->assertDatabaseHas('farmers', [
        'village_id' => $village->id,
        'farmer_code' => 'FMR-001',
        'name' => 'John Doe',
        'mobile' => '9876543210',
        'status' => true,
    ]);
});

test('farmer validation rules are enforced', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Test Village',
        'code' => 'VIL-TEST',
        'status' => true,
    ]);

    // Missing required fields
    $response = $this
        ->actingAs($user)
        ->post('/farmers', [
            'name' => '',
            'farmer_code' => '',
            'mobile' => '',
        ]);

    $response->assertSessionHasErrors(['name', 'farmer_code', 'mobile', 'village_id']);

    // Invalid mobile length
    $response = $this
        ->actingAs($user)
        ->post('/farmers', [
            'village_id' => $village->id,
            'farmer_code' => 'FMR-VAL',
            'name' => 'John Doe',
            'mobile' => '123', // too short
        ]);

    $response->assertSessionHasErrors(['mobile']);

    // Duplicate farmer code
    $existingFarmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-DUP',
        'name' => 'Existing Farmer',
        'mobile' => '9876543210',
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/farmers', [
            'village_id' => $village->id,
            'farmer_code' => 'FMR-DUP',
            'name' => 'New Farmer',
            'mobile' => '9876543211',
        ]);

    $response->assertSessionHasErrors(['farmer_code']);
});

test('authenticated user can view farmer profile', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Village Show',
        'code' => 'VIL-SHOW',
    ]);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-SHOW',
        'name' => 'John Show',
        'mobile' => '9876543210',
    ]);

    $response = $this
        ->actingAs($user)
        ->get("/farmers/{$farmer->id}");

    $response->assertOk();
    $response->assertViewIs('farmers.show');
});

test('authenticated user can edit farmer profile', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Village Edit',
        'code' => 'VIL-EDIT',
    ]);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-OLD',
        'name' => 'John Old',
        'mobile' => '9876543210',
    ]);

    $response = $this
        ->actingAs($user)
        ->put("/farmers/{$farmer->id}", [
            'village_id' => $village->id,
            'farmer_code' => 'FMR-NEW',
            'name' => 'John New',
            'mobile' => '9876543212',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/farmers');

    $this->assertDatabaseHas('farmers', [
        'id' => $farmer->id,
        'farmer_code' => 'FMR-NEW',
        'name' => 'John New',
        'mobile' => '9876543212',
    ]);
});

test('authenticated user can delete farmer', function () {
    $user = User::factory()->create();
    $village = Village::create([
        'name' => 'Village Del',
        'code' => 'VIL-DEL',
    ]);
    $farmer = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-DEL',
        'name' => 'John Delete',
        'mobile' => '9876543210',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete("/farmers/{$farmer->id}");

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/farmers');

    $this->assertDatabaseMissing('farmers', [
        'id' => $farmer->id,
    ]);
});

test('village can load its related farmers', function () {
    $village = Village::create([
        'name' => 'Rel Village',
        'code' => 'VIL-REL',
    ]);
    
    $farmer1 = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-R1',
        'name' => 'Farmer 1',
        'mobile' => '9876543210',
    ]);

    $farmer2 = Farmer::create([
        'village_id' => $village->id,
        'farmer_code' => 'FMR-R2',
        'name' => 'Farmer 2',
        'mobile' => '9876543211',
    ]);

    $this->assertCount(2, $village->farmers);
    $this->assertTrue($village->farmers->contains($farmer1));
    $this->assertTrue($village->farmers->contains($farmer2));
});
