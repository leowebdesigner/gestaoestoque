<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Http\Resources\SaleResource;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $service
    ) {}

    public function store(SaleStoreRequest $request): JsonResponse
    {
        $sale = $this->service->createSale($request->validated());

        return $this->accepted([
            'id' => $sale->id,
            'status' => $sale->status,
        ]);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $sale = $this->service->getSaleById($id);

        if (!$sale) {
            throw new ModelNotFoundException('Sale not found.');
        }

        $resource = (new SaleResource($sale))->toArray($request);

        return $this->success($resource);
    }
}
