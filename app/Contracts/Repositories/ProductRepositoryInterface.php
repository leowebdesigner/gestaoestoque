<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySku(string $sku): ?Product;

    public function getByIds(array $ids): Collection;

    public function getBySkus(array $skus): Collection;

    public function update(Product $product, array $data): Product;
}
