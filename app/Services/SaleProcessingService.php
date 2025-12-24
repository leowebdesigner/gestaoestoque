<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\SaleItemRepositoryInterface;
use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Enums\SaleStatus;
use App\Events\SaleFinalized;
use App\Exceptions\InsufficientStockException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleProcessingService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly SaleItemRepositoryInterface $saleItemRepository,
        private readonly InventoryRepositoryInterface $inventoryRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function process(int $saleId, array $items): void
    {
        DB::transaction(function () use ($saleId, $items): void {
            $sale = $this->saleRepository->findById($saleId);

            if (!$sale) {
                throw new ModelNotFoundException('Sale not found.');
            }

            $productIds = array_unique(array_column($items, 'product_id'));
            $products = $this->productRepository->getByIds($productIds)->keyBy('id');
            $inventories = $this->inventoryRepository
                ->getByProductIdsForUpdate($productIds)
                ->keyBy('product_id');

            $rows = [];
            $totalAmount = 0.0;
            $totalCost = 0.0;
            $now = Carbon::now();

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (!$product) {
                    throw new ModelNotFoundException('Product not found.');
                }

                $qty = (int) $item['quantity'];
                $inventory = $inventories->get($item['product_id']);

                if (!$inventory || $inventory->quantity < $qty) {
                    throw new InsufficientStockException($product->sku, $qty);
                }

                $unitPrice = (float) $product->sale_price;
                $unitCost = (float) $product->cost_price;

                $totalAmount += $unitPrice * $qty;
                $totalCost += $unitCost * $qty;

                $rows[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                ];

                $this->inventoryRepository->update($inventory, [
                    'quantity' => $inventory->quantity - $qty,
                    'last_updated' => $now,
                ]);
            }

            $this->saleItemRepository->createMany($sale->id, $rows);

            $this->saleRepository->update($sale, [
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
                'status' => SaleStatus::Completed,
            ]);

            event(new SaleFinalized($sale->refresh()));
        });
    }
}
