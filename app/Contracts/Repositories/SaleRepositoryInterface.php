<?php

namespace App\Contracts\Repositories;

use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function create(array $data): Sale;

    public function findById(int $id): ?Sale;

    public function findWithItems(int $id): ?Sale;

    public function update(Sale $sale, array $data): Sale;

    public function getSalesReport(array $filters, int $perPage = 50): LengthAwarePaginator;
}
