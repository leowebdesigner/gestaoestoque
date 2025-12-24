<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_filters_by_sku(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::query()->create([
            'sku' => 'SKU-REP-1',
            'name' => 'Produto Report',
            'description' => null,
            'cost_price' => 15.00,
            'sale_price' => 25.00,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'last_updated' => now(),
        ]);

        $this->postJson('/api/sales', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->getJson('/api/reports/sales?product_sku=SKU-REP-1');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'links',
                    'meta',
                ],
            ]);
    }
}
