<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventorySummaryResource;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $service
    ) {}

    public function store(InventoryStoreRequest $request): InventoryItemResource
    {
        $inventory = $this->service->addStock($request->validated());
        $inventory->load('product');

        return new InventoryItemResource($inventory);
    }

    public function index(): InventorySummaryResource
    {
        $summary = $this->service->getInventorySummary();

        return new InventorySummaryResource($summary);
    }
}
