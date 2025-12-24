<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
                $this->productRepository->update($product, [
                    'cost_price' => $payload['cost_price'],
                ]);
            }

            $inventory = $this->inventoryRepository->findByProductId($product->id);
            $now = Carbon::now();

            if ($inventory) {
                $updated = $this->inventoryRepository->update($inventory, [
                    'quantity' => $inventory->quantity + $payload['quantity'],
                    'last_updated' => $now,
                ]);

                Cache::forget(self::CACHE_KEY);

                return $updated;
            }

            $created = $this->inventoryRepository->create([
                'product_id' => $product->id,
                'quantity' => $payload['quantity'],
                'last_updated' => $now,
            ]);

            Cache::forget(self::CACHE_KEY);

            return $created;
        });
    }

    public function getInventorySummary(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $items = $this->inventoryRepository->getWithProducts();
            $totalAmount = 0.0;
            $totalCost = 0.0;

            foreach ($items as $item) {
                $totalAmount += $item->quantity * (float) $item->product->sale_price;
                $totalCost += $item->quantity * (float) $item->product->cost_price;
            }

            return [
                'items' => $items,
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
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
