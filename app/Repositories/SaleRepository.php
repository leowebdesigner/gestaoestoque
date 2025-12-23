<?php

namespace App\Repositories;

use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Models\Sale;
use Illuminate\Support\Collection;

class SaleRepository implements SaleRepositoryInterface
{
    public function create(array $data): Sale
    {
        return Sale::query()->create($data);
    }

    public function findById(int $id): ?Sale
    {
        return Sale::query()->find($id);
    }

    public function findWithItems(int $id): ?Sale
    {
        return Sale::query()
            ->with(['items.product'])
            ->find($id);
    }

    public function update(Sale $sale, array $data): Sale
    {
        $sale->fill($data);
        $sale->save();

        return $sale;
    }

    public function getSalesReport(array $filters): Collection
    {
        $query = Sale::query()
            ->select([
                'sales.id',
                'sales.total_amount',
                'sales.total_cost',
                'sales.total_profit',
                'sales.status',
                'sales.created_at',
            ])
            ->with(['items.product:id,sku,name']);

        if (!empty($filters['start_date'])) {
            $query->whereDate('sales.created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('sales.created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['product_sku'])) {
            $query->whereHas('items.product', function ($builder) use ($filters): void {
                $builder->where('sku', $filters['product_sku']);
            });
        }

        return $query->get();
    }
}
