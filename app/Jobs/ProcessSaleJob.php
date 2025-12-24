<?php

namespace App\Jobs;

use App\Contracts\Repositories\SaleRepositoryInterface;
use App\Contracts\Services\SaleProcessingServiceInterface;
use App\Enums\SaleStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct(
        public readonly int $saleId,
        public readonly array $items
    ) {}

    public function handle(SaleProcessingServiceInterface $service): void
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
                'failure_reason' => mb_substr($exception->getMessage(), 0, 1000),
            ]);
        }

        Log::error('Sale processing failed', [
            'sale_id' => $this->saleId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
