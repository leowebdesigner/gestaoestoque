<?php

namespace App\Jobs;

use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Enums\SaleStatus;
use App\Services\SaleProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $saleId,
        public readonly array $items
    ) {}

    public function handle(SaleProcessingService $service): void
    {
        $service->process($this->saleId, $this->items);
    }

    public function failed(\Throwable $exception): void
    {
        $saleRepository = app()->make(SaleRepositoryInterface::class);
        $sale = $saleRepository->findById($this->saleId);

        if ($sale) {
            $saleRepository->update($sale, [
                'status' => SaleStatus::Failed,
            ]);
        }

        Log::error('Sale processing failed', [
            'sale_id' => $this->saleId,
            'error' => $exception->getMessage(),
        ]);
    }
}
