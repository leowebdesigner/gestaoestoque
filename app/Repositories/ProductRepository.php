<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::query()->where('sku', $sku)->first();
    }

    public function getByIds(array $ids): Collection
    {
        return Product::query()
            ->whereIn('id', $ids)
            ->get();
    }

    public function getBySkus(array $skus): Collection
    {
        return Product::query()
            ->whereIn('sku', $skus)
            ->get();
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product;
    }
}
