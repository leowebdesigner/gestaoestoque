<?php

namespace App\Services;

use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Models\Sale;

class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function createSale(array $payload): Sale
    {
        return $this->saleRepository->create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);
    }

    public function getSaleById(int $id): ?Sale
    {
        return $this->saleRepository->findWithItems($id);
    }
}
