<?php

namespace Tests\Feature;

use App\Models\MilkReceiving;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillagePaymentStatementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Village $village1;
    private Village $village2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->village1 = Village::create([
            'name' => 'Village Alpha',
            'code' => 'V-001',
            'status' => true,
        ]);

        $this->village2 = Village::create([
            'name' => 'Village Beta',
            'code' => 'V-002',
            'status' => true,
        ]);
    }

    public function test_guest_cannot_access_village_payment_statement(): void
    {
        $response = $this->get(route('village-payment-statement.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_statement_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('village-payment-statement.index'));
        $response->assertStatus(200);
        $response->assertSee('Village Payment Statement');
        $response->assertSee('Village Alpha');
        $response->assertSee('Village Beta');
    }

    public function test_date_validation_requires_end_date_after_or_equal_start_date(): void
    {
        $response = $this->actingAs($this->admin)->get(route('village-payment-statement.index', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-01',
        ]));

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_statement_calculates_payment_running_balance_and_summary_accurately(): void
    {
        // Record 1: 01-08-2026 Morning, 20.00 L, Fat 7.70, SNF 32.80
        // Price = 40 + (7.70 * 1.5) + (32.80 * 0.8) = 40 + 11.55 + 26.24 = 77.79
        // Payment = 20 * 77.79 = 1555.80
        MilkReceiving::create([
            'village_id' => $this->village1->id,
            'receiving_date' => '2026-08-01',
            'shift' => 'morning',
            'expected_quantity' => 20.00,
            'received_quantity' => 20.00,
            'expected_fat' => 7.70,
            'received_fat' => 7.70,
            'expected_snf' => 32.80,
            'received_snf' => 32.80,
            'status' => 'confirmed',
            'verified_by' => $this->admin->id,
        ]);

        // Record 2: 01-08-2026 Evening, 30.00 L, Fat 7.70, SNF 32.80
        // Price = 77.79, Payment = 30 * 77.79 = 2333.70
        // Running Balance = 1555.80 + 2333.70 = 3889.50
        MilkReceiving::create([
            'village_id' => $this->village1->id,
            'receiving_date' => '2026-08-01',
            'shift' => 'evening',
            'expected_quantity' => 30.00,
            'received_quantity' => 30.00,
            'expected_fat' => 7.70,
            'received_fat' => 7.70,
            'expected_snf' => 32.80,
            'received_snf' => 32.80,
            'status' => 'confirmed',
            'verified_by' => $this->admin->id,
        ]);

        // Record for Village Beta (should be excluded by village filter)
        MilkReceiving::create([
            'village_id' => $this->village2->id,
            'receiving_date' => '2026-08-01',
            'shift' => 'morning',
            'expected_quantity' => 100.00,
            'received_quantity' => 100.00,
            'expected_fat' => 5.00,
            'received_fat' => 5.00,
            'expected_snf' => 8.50,
            'received_snf' => 8.50,
            'status' => 'confirmed',
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('village-payment-statement.index', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Village Statement:');
        $response->assertSee('Village Alpha');

        // Check API JSON response
        $apiResponse = $this->actingAs($this->admin)->getJson(route('api.village-payment-statement', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
        ]));

        $apiResponse->assertStatus(200);
        $json = $apiResponse->json();

        $this->assertEquals('Village Alpha', $json['village']['name']);
        $this->assertCount(2, $json['entries']);
        $this->assertEquals(50.00, $json['summary']['total_milk']);
        $this->assertEquals(7.70, $json['summary']['average_fat']);
        $this->assertEquals(32.80, $json['summary']['average_snf']);
        $this->assertEquals(3889.50, $json['summary']['total_payment']);
        $this->assertEquals(3889.50, $json['summary']['closing_balance']);

        // Check running balance on second entry
        $this->assertEquals(1555.80, $json['entries'][0]['running_balance']);
        $this->assertEquals(3889.50, $json['entries'][1]['running_balance']);
    }

    public function test_shows_empty_state_when_no_records_exist(): void
    {
        $response = $this->actingAs($this->admin)->get(route('village-payment-statement.index', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('No milk receiving records found for the selected village and date range.');
    }

    public function test_print_and_pdf_routes_work(): void
    {
        MilkReceiving::create([
            'village_id' => $this->village1->id,
            'receiving_date' => '2026-08-01',
            'shift' => 'morning',
            'expected_quantity' => 20.00,
            'received_quantity' => 20.00,
            'expected_fat' => 7.70,
            'received_fat' => 7.70,
            'expected_snf' => 32.80,
            'received_snf' => 32.80,
            'status' => 'confirmed',
            'verified_by' => $this->admin->id,
        ]);

        $printResponse = $this->actingAs($this->admin)->get(route('village-payment-statement.print', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
        ]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('VILLAGE MILK PAYMENT STATEMENT');

        $pdfResponse = $this->actingAs($this->admin)->get(route('village-payment-statement.pdf', [
            'village_id' => $this->village1->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
        ]));
        $pdfResponse->assertStatus(200);
    }
}
