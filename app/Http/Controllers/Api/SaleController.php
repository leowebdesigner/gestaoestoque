<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Resources\SaleResource;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $service
    ) {}

    public function store(SaleStoreRequest $request): JsonResponse
    {
        $sale = $this->service->createSale($request->validated());

        return (new SaleResource($sale))
            ->response()
            ->setStatusCode(202);
    }

    public function show(int $id): SaleResource
    {
        $sale = $this->service->getSaleById($id);

        if (!$sale) {
            throw new ModelNotFoundException('Sale not found.');
        }

        return new SaleResource($sale);
    }
}
