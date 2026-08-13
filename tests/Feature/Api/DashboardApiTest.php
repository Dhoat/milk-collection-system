<?php

use App\Models\Farmer;
use App\Models\MilkCollection;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->manager = User::factory()->create(['role' => 'manager']);
    $this->centerStaff = User::factory()->create(['role' => 'center_staff']);
    $this->collectionStaff = User::factory()->create(['role' => 'collection_staff']);

    $this->village = Village::create([
        'code' => 'VIL-DSH-01',
        'name' => 'Sunrise Village',
        'district' => 'Central',
        'state' => 'Punjab',
        'status' => true,
    ]);

    $this->farmer = Farmer::create([
        'farmer_code' => 'FRM-DSH-01',
        'village_id' => $this->village->id,
        'name' => 'Manjit Singh',
        'mobile' => '9812300000',
        'status' => true,
    ]);

    $this->todayString = Carbon::today()->toDateString();

    MilkCollection::create([
        'farmer_id' => $this->farmer->id,
        'collection_date' => $this->todayString,
        'shift' => 'morning',
        'milk_quantity' => 40.00,
        'fat' => 4.5,
        'snf' => 8.5,
        'rate' => 40.00,
        'amount' => 1600.00,
    ]);

    MilkCollection::create([
        'farmer_id' => $this->farmer->id,
        'collection_date' => $this->todayString,
        'shift' => 'evening',
        'milk_quantity' => 30.00,
        'fat' => 4.5,
        'snf' => 8.5,
        'rate' => 40.00,
        'amount' => 1200.00,
    ]);
});

test('unauthenticated request to Dashboard API returns 401', function () {
    $response = $this->getJson('/api/dashboard');
    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

test('all authenticated user roles can access dashboard API matching web behavior', function () {
    foreach ([$this->superAdmin, $this->manager, $this->centerStaff, $this->collectionStaff] as $user) {
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
            ]);
    }
});

test('dashboard API returns exact calculated KPIs and metrics matching web dashboard', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson('/api/dashboard');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => [
                'today' => $this->todayString,
                'kpis' => [
                    'total_farmers' => 1,
                    'active_farmers' => 1,
                    'total_villages' => 1,
                    'active_villages' => 1,
                    'today_quantity' => 70,
                    'today_amount' => 2800,
                ],
                'todayOverview' => [
                    'morning' => [
                        'quantity' => 40,
                        'farmers' => 1,
                    ],
                    'evening' => [
                        'quantity' => 30,
                        'farmers' => 1,
                    ],
                    'total' => [
                        'quantity' => 70,
                        'farmers' => 1,
                    ],
                ],
            ],
        ]);

    expect($response->json('data.collectionTrend'))->toHaveCount(7)
        ->and($response->json('data.recentCollections'))->toHaveCount(2)
        ->and($response->json('data.villagePerformance.0.name'))->toBe('Sunrise Village')
        ->and((float) $response->json('data.villagePerformance.0.today_quantity'))->toBe(70.0);
});

test('dashboard API supports optional date parameter and defaults to today when omitted', function () {
    Sanctum::actingAs($this->superAdmin);

    // Omitted date parameter (defaults to today)
    $responseToday = $this->getJson('/api/dashboard');
    $responseToday->assertStatus(200)
        ->assertJsonPath('data.today', $this->todayString)
        ->assertJsonPath('data.kpis.today_quantity', 70);

    // Passed historical date parameter with no collections
    $responseHistorical = $this->getJson('/api/dashboard?date=2020-01-01');
    $responseHistorical->assertStatus(200)
        ->assertJsonPath('data.today', '2020-01-01')
        ->assertJsonPath('data.kpis.today_quantity', 0);
});

test('invalid date parameter format returns 422 validation error', function () {
    Sanctum::actingAs($this->superAdmin);

    $response = $this->getJson('/api/dashboard?date=not-a-valid-date');
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date']);
});

test('dashboard API does not modify database state', function () {
    Sanctum::actingAs($this->superAdmin);

    $farmersBefore = Farmer::count();
    $villagesBefore = Village::count();
    $collectionsBefore = MilkCollection::count();

    $this->getJson('/api/dashboard');

    expect(Farmer::count())->toBe($farmersBefore)
        ->and(Village::count())->toBe($villagesBefore)
        ->and(MilkCollection::count())->toBe($collectionsBefore);
});

test('web dashboard controller still functions identically via web routes', function () {
    $response = $this->actingAs($this->superAdmin)->get('/dashboard');

    $response->assertOk()
        ->assertViewIs('dashboard')
        ->assertViewHasAll([
            'today',
            'kpis',
            'todayOverview',
            'collectionTrend',
            'trendMax',
            'recentCollections',
            'villagePerformance',
            'recentActivity',
        ]);
});
