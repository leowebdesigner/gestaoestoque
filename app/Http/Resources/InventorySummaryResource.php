<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventorySummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'items' => InventoryItemResource::collection($this->resource['items']),
            'total_amount' => $this->resource['total_amount'],
            'total_cost' => $this->resource['total_cost'],
            'total_profit' => $this->resource['total_profit'],
        ];
    }
}
