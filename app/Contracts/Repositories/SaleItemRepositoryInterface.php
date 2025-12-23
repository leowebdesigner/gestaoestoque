<?php

namespace App\Contracts\Repositories;

interface SaleItemRepositoryInterface
{
    public function createMany(int $saleId, array $items): void;
}
