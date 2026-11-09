<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Borrower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_dashboard_summary(): void
    {
        Asset::factory()->count(3)->create(['status' => 'available']);
        Asset::factory()->count(2)->create(['status' => 'borrowed']);
        Borrower::factory()->count(2)->create();

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'summary' => [
                             'total_assets',
                             'available_assets',
                             'borrowed_assets',
                             'maintenance_assets',
                             'total_borrowers',
                             'total_loans',
                             'active_loans',
                             'returned_loans',
                             'overdue_loans',
                         ],
                         'assets_by_category',
                         'recent_loans',
                     ],
                 ]);
    }
}

