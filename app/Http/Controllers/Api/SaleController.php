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
    public function store(SaleStoreRequest $request, SaleService $service): JsonResponse
    {
        $sale = $service->createSale($request->validated());

        return (new SaleResource($sale))
            ->response()
            ->setStatusCode(202);
    }

    public function show(int $id, SaleService $service): SaleResource
    {
        $sale = $service->getSaleById($id);

        if (!$sale) {
            throw new ModelNotFoundException('Sale not found.');
        }

        return new SaleResource($sale);
    }
}
