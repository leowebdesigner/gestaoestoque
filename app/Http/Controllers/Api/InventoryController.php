<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventorySummaryResource;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $service
    ) {}

    public function store(InventoryStoreRequest $request): JsonResponse
    {
        $inventory = $this->service->addStock($request->validated());

        $resource = (new InventoryItemResource($inventory))->toArray($request);

        return $this->created($resource);
    }

    public function index(Request $request): JsonResponse
    {
        $summary = $this->service->getInventorySummary();

        $resource = (new InventorySummaryResource($summary))->toArray($request);

        return $this->success($resource);
    }
}
