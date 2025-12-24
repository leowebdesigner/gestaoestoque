<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Inventory;
use App\Support\Money;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    private const CACHE_KEY = 'inventory:summary';
    private const CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly InventoryRepositoryInterface $inventoryRepository
    ) {}

    public function addStock(array $payload): Inventory
    {
        return DB::transaction(function () use ($payload): Inventory {
            $product = $this->productRepository->findBySku($payload['sku']);

            if (!$product) {
                throw new ModelNotFoundException('Product not found.');
            }

            if (isset($payload['cost_price'])) {
                if (Money::toCents($payload['cost_price']) > Money::toCents($product->sale_price)) {
                    throw ValidationException::withMessages([
                        'cost_price' => ['Cost price cannot be greater than sale price.'],
                    ]);
                }

                $this->productRepository->update($product, [
                    'cost_price' => $payload['cost_price'],
                ]);
            }

            $now = Carbon::now();
            $existingInventory = $this->inventoryRepository->findByProductId($product->id);

            $inventory = $existingInventory
                ? $this->inventoryRepository->update($existingInventory, [
                    'quantity' => $existingInventory->quantity + $payload['quantity'],
                    'last_updated' => $now,
                ])
                : $this->inventoryRepository->create([
                    'product_id' => $product->id,
                    'quantity' => $payload['quantity'],
                    'last_updated' => $now,
                ]);

            Cache::forget(self::CACHE_KEY);
            $inventory->load('product');

            return $inventory;
        });
    }

    public function getInventorySummary(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $items = $this->inventoryRepository->getWithProducts();
            $totalAmountCents = 0;
            $totalCostCents = 0;

            foreach ($items as $item) {
                $saleCents = Money::toCents($item->product->sale_price);
                $costCents = Money::toCents($item->product->cost_price);
                $qty = (int) $item->quantity;

                $totalAmountCents += $saleCents * $qty;
                $totalCostCents += $costCents * $qty;
            }

            return [
                'items' => $items,
                'total_amount' => Money::formatBrl($totalAmountCents),
                'total_cost' => Money::formatBrl($totalCostCents),
                'total_profit' => Money::formatBrl($totalAmountCents - $totalCostCents),
            ];
        });
    }

    public function cleanupOldStock(int $days): int
    {
        $threshold = Carbon::now()->subDays($days);
        $deleted = $this->inventoryRepository->deleteStale($threshold);

        if ($deleted > 0) {
            Cache::forget(self::CACHE_KEY);
        }

        return $deleted;
    }

}
