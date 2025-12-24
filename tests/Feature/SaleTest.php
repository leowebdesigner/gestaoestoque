<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sale_dispatches_processing(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::query()->create([
            'sku' => 'SKU-SALE-1',
            'name' => 'Produto Venda',
            'description' => null,
            'cost_price' => 10.00,
            'sale_price' => 20.00,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'last_updated' => now(),
        ]);

        $response = $this->postJson('/api/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'status'],
            ]);

        $saleId = $response->json('data.id');
        $sale = \App\Models\Sale::query()->findOrFail($saleId);

        $this->assertSame('completed', $sale->status);
        $this->assertSame('40.00', $sale->total_amount);
        $this->assertSame('20.00', $sale->total_cost);
    }

    public function test_show_sale_returns_items(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::query()->create([
            'sku' => 'SKU-SALE-2',
            'name' => 'Produto Venda 2',
            'description' => null,
            'cost_price' => 5.00,
            'sale_price' => 12.00,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'last_updated' => now(),
        ]);

        $saleResponse = $this->postJson('/api/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $saleId = $saleResponse->json('data.id');
        $response = $this->getJson("/api/sales/{$saleId}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'status',
                    'items' => [
                        [
                            'product' => ['id', 'sku', 'name'],
                            'quantity',
                            'unit_price',
                            'unit_cost',
                        ],
                    ],
                ],
            ]);
    }
}
