<?php

namespace Tests\Unit;

use App\Jobs\ProcessSaleJob;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sale_dispatches_job(): void
    {
        Bus::fake();

        $service = app(SaleService::class);
        $sale = $service->createSale([
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ]);

        Bus::assertDispatched(ProcessSaleJob::class, function (ProcessSaleJob $job) use ($sale): bool {
            return $job->saleId === $sale->id;
        });
    }
}
