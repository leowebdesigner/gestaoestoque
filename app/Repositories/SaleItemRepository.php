<?php

namespace App\Repositories;

use App\Contracts\Repositories\SaleItemRepositoryInterface;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;

class SaleItemRepository implements SaleItemRepositoryInterface
{
    public function createMany(int $saleId, array $items): void
    {
        $now = Carbon::now();
        $rows = [];

        foreach ($items as $item) {
            $rows[] = [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'unit_cost' => $item['unit_cost'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        SaleItem::query()->insert($rows);
    }
}
