<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'role' => 'super_admin',
    ]);

    $this->manager = User::factory()->create([
        'name' => 'Manager User',
        'email' => 'manager@test.com',
        'role' => 'manager',
    ]);

    $this->centerStaff = User::factory()->create([
        'name' => 'Center Staff',
        'email' => 'centerstaff@test.com',
        'role' => 'center_staff',
    ]);
});

test('super admin can view system settings page', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('settings.index'));

    $response->assertOk();
    $response->assertViewIs('settings.index');
    $response->assertSee('Dhoat Dairy Farm');
});

test('non-super-admin users cannot access or update system settings', function () {
    // Manager GET & PUT
    $responseManagerGet = $this->actingAs($this->manager)->get(route('settings.index'));
    $responseManagerGet->assertForbidden();

    $responseManagerPut = $this->actingAs($this->manager)->put(route('settings.update'), [
        'business_name' => 'Unauthorized Change',
    ]);
    $responseManagerPut->assertForbidden();

    // Center Staff
    $responseStaffGet = $this->actingAs($this->centerStaff)->get(route('settings.index'));
    $responseStaffGet->assertForbidden();
});

test('super admin can update system settings and values persist', function () {
    $payload = [
        'business_name' => 'Punjab Fresh Dairy Hub',
        'business_phone' => '+91 99887 66554',
        'business_email' => 'contact@punjabfreshdairy.com',
        'business_address' => 'GT Road, Milk Processing Center',
        'business_city' => 'Jalandhar',
        'business_state' => 'Punjab',
        'business_pincode' => '144001',
        'business_website' => 'https://punjabfreshdairy.com',
        'business_gst' => '03PBX12345678',
        'business_description' => 'Organic Fresh Milk Supplier',

        'milk_default_unit' => 'Litre',
        'milk_default_currency' => 'INR',
        'milk_currency_symbol' => '₹',
        'milk_base_fat' => '4.8',
        'milk_base_snf' => '8.8',
        'milk_default_shift' => 'evening',

        'order_number_prefix' => 'SO-',
        'delivery_number_prefix' => 'DISP-',
        'default_order_status' => 'confirmed',
        'default_delivery_status' => 'assigned',

        'system_timezone' => 'Asia/Kolkata',
        'system_date_format' => 'd/m/Y',
        'system_pagination_limit' => '25',
        'customer_ordering_enabled' => '1',
        'maintenance_mode_enabled' => '0',
    ];

    $response = $this->actingAs($this->superAdmin)->put(route('settings.update'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify DB persistence
    $this->assertDatabaseHas('settings', [
        'key' => 'business_name',
        'value' => 'Punjab Fresh Dairy Hub',
    ]);

    // Verify global helper retrieval
    expect(setting('business_name'))->toBe('Punjab Fresh Dairy Hub')
        ->and(setting('milk_base_fat'))->toBe('4.8')
        ->and(setting('order_number_prefix'))->toBe('SO-');
});

test('super admin can upload business logo safely', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('dairy_logo.png', 200, 200);

    $payload = [
        'business_name' => 'Dhoat Dairy Farm & Processing Center',
        'business_phone' => '+91 98765 43210',
        'business_email' => 'contact@dhoatdairy.com',
        'business_address' => 'Main Highway, Milk Hub',
        'business_city' => 'Amritsar',
        'business_state' => 'Punjab',
        'business_pincode' => '143001',
        'business_logo' => $logo,

        'milk_default_unit' => 'Litre',
        'milk_default_currency' => 'INR',
        'milk_currency_symbol' => '₹',
        'milk_base_fat' => '4.5',
        'milk_base_snf' => '8.5',
        'milk_default_shift' => 'morning',

        'order_number_prefix' => 'ORD-',
        'delivery_number_prefix' => 'DEL-',
        'default_order_status' => 'pending',
        'default_delivery_status' => 'pending',

        'system_timezone' => 'Asia/Kolkata',
        'system_date_format' => 'Y-m-d',
        'system_pagination_limit' => '15',
        'customer_ordering_enabled' => '1',
        'maintenance_mode_enabled' => '0',
    ];

    $response = $this->actingAs($this->superAdmin)->put(route('settings.update'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $storedLogoPath = Setting::get('business_logo');
    expect($storedLogoPath)->not->toBeNull();

    Storage::disk('public')->assertExists($storedLogoPath);
});

test('settings validation rejects invalid email or oversized file', function () {
    Storage::fake('public');

    $oversizedFile = UploadedFile::fake()->create('huge.pdf', 3000); // PDF file > 2MB

    $response = $this->actingAs($this->superAdmin)->put(route('settings.update'), [
        'business_name' => '',
        'business_email' => 'not-an-email',
        'business_logo' => $oversizedFile,
    ]);

    $response->assertSessionHasErrors(['business_name', 'business_email', 'business_logo']);
});
