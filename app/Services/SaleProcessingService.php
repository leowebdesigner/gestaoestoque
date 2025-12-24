<?php

namespace App\Services;

use App\Contracts\Services\SaleProcessingServiceInterface;
use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\SaleItemRepositoryInterface;
use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Enums\SaleStatus;
use App\Events\SaleFinalized;
use App\Exceptions\InsufficientStockException;
use App\Models\Sale;
use App\Support\Money;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaleProcessingService implements SaleProcessingServiceInterface
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly SaleItemRepositoryInterface $saleItemRepository,
        private readonly InventoryRepositoryInterface $inventoryRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function process(int $saleId, array $items): void
    {
        $sale = $this->saleRepository->findById($saleId);

        if (!$sale) {
            throw new ModelNotFoundException('Sale not found.');
        }

        if ($sale->status !== SaleStatus::Pending) {
            return;
        }

        DB::transaction(function () use ($sale, $items): void {
            $lockedSale = Sale::query()
                ->where('id', $sale->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedSale || $lockedSale->status !== SaleStatus::Pending) {
                return;
            }

            $productIds = array_unique(array_column($items, 'product_id'));
            $products = $this->productRepository->getByIds($productIds)->keyBy('id');
            $inventories = $this->inventoryRepository
                ->getByProductIdsForUpdate($productIds)
                ->keyBy('product_id');

            $rows = [];
            $totalAmountCents = 0;
            $totalCostCents = 0;
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

                $unitPriceCents = Money::toCents($product->sale_price);
                $unitCostCents = Money::toCents($product->cost_price);

                $totalAmountCents += $unitPriceCents * $qty;
                $totalCostCents += $unitCostCents * $qty;

                $rows[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => bcdiv((string) $unitPriceCents, '100', 2),
                    'unit_cost' => bcdiv((string) $unitCostCents, '100', 2),
                ];

                $this->inventoryRepository->update($inventory, [
                    'quantity' => $inventory->quantity - $qty,
                    'last_updated' => $now,
                ]);
            }

            $this->saleItemRepository->createMany($sale->id, $rows);

            Cache::forget('inventory:summary');

            $this->saleRepository->update($sale, [
                'total_amount' => bcdiv((string) $totalAmountCents, '100', 2),
                'total_cost' => bcdiv((string) $totalCostCents, '100', 2),
                'total_profit' => bcdiv((string) ($totalAmountCents - $totalCostCents), '100', 2),
                'status' => SaleStatus::Completed,
            ]);

            event(new SaleFinalized($sale->refresh()));
        });
    }
}
