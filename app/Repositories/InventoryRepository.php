<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Models\Inventory;
use Illuminate\Support\Collection;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function findByProductId(int $productId): ?Inventory
    {
        return Inventory::query()
            ->where('product_id', $productId)
            ->first();
    }

    public function getByProductIds(array $productIds): Collection
    {
        return Inventory::query()
            ->whereIn('product_id', $productIds)
            ->get();
    }

    public function getByProductIdsForUpdate(array $productIds): Collection
    {
        return Inventory::query()
            ->whereIn('product_id', $productIds)
            ->lockForUpdate()
            ->get();
    }

    public function getWithProducts(): Collection
    {
        return Inventory::query()
            ->select(['id', 'product_id', 'quantity', 'last_updated', 'created_at', 'updated_at'])
            ->with(['product:id,sku,name,cost_price,sale_price'])
            ->get();
    }

    public function create(array $data): Inventory
    {
        return Inventory::query()->create($data);
    }

    public function update(Inventory $inventory, array $data): Inventory
    {
        $inventory->fill($data);
        $inventory->save();

        return $inventory;
    }

    public function deleteStale(\DateTimeInterface $threshold): int
    {
        return Inventory::query()
            ->whereNotNull('last_updated')
            ->where('last_updated', '<', $threshold)
            ->delete();
    }
}
