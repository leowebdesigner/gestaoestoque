<?php

namespace App\Services;

use App\Contracts\Repositories\SaleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function getSalesReport(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->saleRepository->getSalesReport($filters, $perPage);
    }
}
