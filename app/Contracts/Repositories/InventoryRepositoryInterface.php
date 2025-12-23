<?php

namespace App\Contracts\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;

interface InventoryRepositoryInterface
{
    public function findByProductId(int $productId): ?Inventory;

    public function getByProductIds(array $productIds): Collection;

    public function getByProductIdsForUpdate(array $productIds): Collection;

    public function getWithProducts(): Collection;

    public function create(array $data): Inventory;

    public function update(Inventory $inventory, array $data): Inventory;
}
