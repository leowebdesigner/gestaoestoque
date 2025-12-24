<?php

namespace Tests\Unit;

use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_stock_updates_quantity_and_clears_cache(): void
    {
        $product = Product::query()->create([
            'sku' => 'SKU-SVC-1',
            'name' => 'Produto Service',
            'description' => null,
            'cost_price' => 10.00,
            'sale_price' => 15.00,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'last_updated' => now(),
        ]);

        Cache::put('inventory:summary', ['cached' => true], 60);

        $service = app(InventoryService::class);
        $service->addStock([
            'sku' => $product->sku,
            'quantity' => 3,
        ]);

        $this->assertNull(Cache::get('inventory:summary'));

        $inventory = Inventory::query()->where('product_id', $product->id)->first();
        $this->assertSame(5, $inventory->quantity);
    }
}
