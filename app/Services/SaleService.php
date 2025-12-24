<?php

namespace App\Services;

use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Enums\SaleStatus;
use App\Jobs\ProcessSaleJob;
use App\Models\Sale;

class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function createSale(array $payload): Sale
    {
        $sale = $this->saleRepository->create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => SaleStatus::Pending,
        ]);

        ProcessSaleJob::dispatch($sale->id, $payload['items']);

        return $sale;
    }

    public function getSaleById(int $id): ?Sale
    {
        return $this->saleRepository->findWithItems($id);
    }
}
