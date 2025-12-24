<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'total_cost' => $this->total_cost,
            'total_profit' => $this->total_profit,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
