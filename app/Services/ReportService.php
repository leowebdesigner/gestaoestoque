<?php

namespace App\Services;

use App\Contracts\Repositories\SaleRepositoryInterface;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function getSalesReport(array $filters): Collection
    {
        return $this->saleRepository->getSalesReport($filters);
    }
}
