<?php

namespace Tests\Feature;

use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_assets(): void
    {
        Asset::factory()->create(['name' => 'Laptop Dell XPS']);

        $response = $this->getJson('/api/assets');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);
    }

    public function test_can_create_asset_with_valid_data(): void
    {
        $payload = [
            'asset_code'    => 'AST-TEST-01',
            'name'          => 'Smart TV 55 Inch',
            'category'      => 'Elektronik',
            'status'        => 'available',
            'condition'     => 'Bagus',
            'description'   => 'TV display meeting room',
            'purchase_date' => '2024-01-01',
        ];

        $response = $this->postJson('/api/assets', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'asset_code' => 'AST-TEST-01',
                         'name' => 'Smart TV 55 Inch',
                     ],
                 ]);

        $this->assertDatabaseHas('assets', ['asset_code' => 'AST-TEST-01']);
    }

    public function test_cannot_create_asset_with_duplicate_code(): void
    {
        Asset::create([
            'asset_code' => 'DUP-001',
            'name'       => 'Item 1',
            'category'   => 'Office',
            'status'     => 'available',
        ]);

        $response = $this->postJson('/api/assets', [
            'asset_code' => 'DUP-001',
            'name'       => 'Item 2',
            'category'   => 'Office',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['asset_code']);
    }

    public function test_can_update_asset(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-UPD',
            'name'       => 'Original Name',
            'category'   => 'Office',
            'status'     => 'available',
        ]);

        $response = $this->putJson("/api/assets/{$asset->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Name',
                     ],
                 ]);

        $this->assertDatabaseHas('assets', ['id' => $asset->id, 'name' => 'Updated Name']);
    }

    public function test_cannot_delete_borrowed_asset(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST-BORROWED',
            'name'       => 'In Use Laptop',
            'category'   => 'Elektronik',
            'status'     => 'borrowed',
        ]);

        $response = $this->deleteJson("/api/assets/{$asset->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);

        $this->assertDatabaseHas('assets', ['id' => $asset->id]);
    }
}

