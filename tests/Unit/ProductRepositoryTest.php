<?php

namespace Tests\Unit;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_sku(): void
    {
        $product = Product::query()->create([
            'sku' => 'SKU-REP-TEST',
            'name' => 'Produto Repo',
            'description' => null,
            'cost_price' => 10.00,
            'sale_price' => 12.00,
        ]);

        $repository = app(ProductRepositoryInterface::class);
        $found = $repository->findBySku('SKU-REP-TEST');

        $this->assertNotNull($found);
        $this->assertSame($product->id, $found->id);
    }
}
