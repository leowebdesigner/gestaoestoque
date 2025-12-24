<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $sku, int $quantity)
    {
        parent::__construct("Insufficient stock for product {$sku}. Requested: {$quantity}");
    }
}
