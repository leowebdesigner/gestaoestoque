<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_amount' => Money::formatBrl($this->total_amount),
            'total_cost' => Money::formatBrl($this->total_cost),
            'total_profit' => Money::formatBrl($this->total_profit),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'items' => $this->whenLoaded('items', function () {
                return SaleItemResource::collection($this->items);
            }),
        ];
    }
}
