<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_inventory_entry(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::query()->create([
            'sku' => 'SKU-TEST-1',
            'name' => 'Produto Teste',
            'description' => 'Desc',
            'cost_price' => 10.00,
            'sale_price' => 15.00,
        ]);

        $response = $this->postJson('/api/inventory', [
            'sku' => $product->sku,
            'quantity' => 5,
            'cost_price' => 11.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'product' => ['id', 'sku', 'name', 'cost_price', 'sale_price'],
                    'quantity',
                    'last_updated',
                ],
            ]);
    }

    public function test_inventory_index_returns_summary(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::query()->create([
            'sku' => 'SKU-TEST-2',
            'name' => 'Produto Teste 2',
            'description' => null,
            'cost_price' => 20.00,
            'sale_price' => 30.00,
        ]);

        $this->postJson('/api/inventory', [
            'sku' => $product->sku,
            'quantity' => 3,
        ]);

        $response = $this->getJson('/api/inventory');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items' => [
                        [
                            'id',
                            'product' => ['id', 'sku', 'name', 'cost_price', 'sale_price'],
                            'quantity',
                            'last_updated',
                        ],
                    ],
                    'total_amount',
                    'total_cost',
                    'total_profit',
                ],
            ]);
    }
}
