<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventorySummaryResource;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    public function store(InventoryStoreRequest $request, InventoryService $service): InventoryItemResource
    {
        $inventory = $service->addStock($request->validated());
        $inventory->load('product');

        return new InventoryItemResource($inventory);
    }

    public function index(InventoryService $service): InventorySummaryResource
    {
        $summary = $service->getInventorySummary();

        return new InventorySummaryResource($summary);
    }
}
