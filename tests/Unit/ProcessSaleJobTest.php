<?php

namespace Tests\Unit;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\SaleItemRepositoryInterface;
use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Events\SaleFinalized;
use App\Jobs\ProcessSaleJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessSaleJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_updates_stock_and_finalizes_sale(): void
    {
        $product = Product::query()->create([
            'sku' => 'SKU-JOB-1',
            'name' => 'Produto Job',
            'description' => null,
            'cost_price' => 10.00,
            'sale_price' => 25.00,
        ]);

        $inventory = Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 4,
            'last_updated' => now(),
        ]);

        $sale = Sale::query()->create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        Event::fake([SaleFinalized::class]);

        $job = new ProcessSaleJob($sale->id, [
            ['product_id' => $product->id, 'quantity' => 2],
        ]);

        $job->handle(
            app(SaleRepositoryInterface::class),
            app(SaleItemRepositoryInterface::class),
            app(InventoryRepositoryInterface::class),
            app(ProductRepositoryInterface::class)
        );

        $inventory->refresh();
        $sale->refresh();

        $this->assertSame(2, $inventory->quantity);
        $this->assertSame('completed', $sale->status);
        $this->assertSame('50.00', $sale->total_amount);
        $this->assertSame('20.00', $sale->total_cost);

        Event::assertDispatched(SaleFinalized::class);
    }
}
