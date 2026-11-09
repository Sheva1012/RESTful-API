<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Borrower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_borrow_available_asset(): void
    {
        $asset = Asset::factory()->create(['status' => 'available']);
        $borrower = Borrower::factory()->create();

        $payload = [
            'asset_id'             => $asset->id,
            'borrower_id'          => $borrower->id,
            'borrow_date'          => '2026-11-09',
            'expected_return_date' => '2026-11-12',
            'purpose'              => 'Audit kualitas sarang burung walet ke sentra budidaya',
        ];

        $response = $this->postJson('/api/loans', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'active',
                         'asset_id' => $asset->id,
                     ],
                 ]);

        // Asset status must change to borrowed
        $this->assertEquals('borrowed', $asset->fresh()->status);
        $this->assertDatabaseHas('asset_loans', [
            'asset_id'    => $asset->id,
            'borrower_id' => $borrower->id,
            'status'      => 'active',
        ]);
    }

    public function test_cannot_borrow_already_borrowed_asset(): void
    {
        $asset = Asset::factory()->create(['status' => 'borrowed']);
        $borrower = Borrower::factory()->create();

        $payload = [
            'asset_id'             => $asset->id,
            'borrower_id'          => $borrower->id,
            'borrow_date'          => '2026-11-09',
            'expected_return_date' => '2026-11-12',
            'purpose'              => 'Penggunaan mendesak',
        ];

        $response = $this->postJson('/api/loans', $payload);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_can_return_borrowed_asset(): void
    {
        $asset = Asset::factory()->create(['status' => 'borrowed']);
        $borrower = Borrower::factory()->create();

        $loan = AssetLoan::create([
            'loan_code'            => 'LOAN-TEST-001',
            'asset_id'             => $asset->id,
            'borrower_id'          => $borrower->id,
            'borrow_date'          => '2026-11-01',
            'expected_return_date' => '2026-11-05',
            'status'               => 'active',
            'purpose'              => 'Meeting',
        ]);

        $response = $this->postJson("/api/loans/{$loan->id}/return", [
            'actual_return_date' => '2026-11-05',
            'notes'              => 'Barang dalam kondisi sangat baik',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'status' => 'returned',
                     ],
                 ]);

        // Asset status must revert to available
        $this->assertEquals('available', $asset->fresh()->status);
        $this->assertEquals('returned', $loan->fresh()->status);
    }

    public function test_cannot_return_already_returned_asset(): void
    {
        $asset = Asset::factory()->create(['status' => 'available']);
        $borrower = Borrower::factory()->create();

        $loan = AssetLoan::create([
            'loan_code'            => 'LOAN-TEST-002',
            'asset_id'             => $asset->id,
            'borrower_id'          => $borrower->id,
            'borrow_date'          => '2026-11-01',
            'expected_return_date' => '2026-11-05',
            'actual_return_date'   => '2026-11-05',
            'status'               => 'returned',
            'purpose'              => 'Meeting',
        ]);

        $response = $this->postJson("/api/loans/{$loan->id}/return");

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }
}

