<?php

namespace App\Jobs;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\SaleItemRepositoryInterface;
use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Events\SaleFinalized;
use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $saleId,
        public readonly array $items
    ) {}

    public function handle(
        SaleRepositoryInterface $saleRepository,
        SaleItemRepositoryInterface $saleItemRepository,
        InventoryRepositoryInterface $inventoryRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        DB::transaction(function () use (
            $saleRepository,
            $saleItemRepository,
            $inventoryRepository,
            $productRepository
        ): void {
            $sale = $saleRepository->findById($this->saleId);

            if (!$sale) {
                throw new ModelNotFoundException('Sale not found.');
            }

            $productIds = array_unique(array_column($this->items, 'product_id'));
            $products = $productRepository->getByIds($productIds)->keyBy('id');
            $inventories = $inventoryRepository->getByProductIdsForUpdate($productIds)->keyBy('product_id');

            $totalAmount = 0.0;
            $totalCost = 0.0;
            $rows = [];
            $now = Carbon::now();

            foreach ($this->items as $item) {
                $product = $products->get($item['product_id']);

                if (!$product) {
                    throw new ModelNotFoundException('Product not found.');
                }

                $inventory = $inventories->get($item['product_id']);

                if (!$inventory || $inventory->quantity < $item['quantity']) {
                    throw new ModelNotFoundException('Insufficient stock.');
                }

                $unitPrice = (float) $product->sale_price;
                $unitCost = (float) $product->cost_price;
                $qty = (int) $item['quantity'];

                $totalAmount += $unitPrice * $qty;
                $totalCost += $unitCost * $qty;

                $rows[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                ];

                $inventoryRepository->update($inventory, [
                    'quantity' => $inventory->quantity - $qty,
                    'last_updated' => $now,
                ]);
            }

            $saleItemRepository->createMany($sale->id, $rows);

            $saleRepository->update($sale, [
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
                'status' => 'completed',
            ]);

            event(new SaleFinalized($sale->refresh()));
        });
    }

    public function failed(\Throwable $exception): void
    {
        $sale = Sale::query()->find($this->saleId);

        if ($sale) {
            $sale->update([
                'status' => 'failed',
            ]);
        }

        Log::error('Sale processing failed', [
            'sale_id' => $this->saleId,
            'error' => $exception->getMessage(),
        ]);
    }
}
