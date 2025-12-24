<?php

namespace App\Contracts\Services;

interface SaleProcessingServiceInterface
{
    public function process(int $saleId, array $items): void;
}
