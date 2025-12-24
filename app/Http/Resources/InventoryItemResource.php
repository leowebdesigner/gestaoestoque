<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $this->product->name,
                'cost_price' => $this->product->cost_price,
                'sale_price' => $this->product->sale_price,
            ],
            'quantity' => $this->quantity,
            'last_updated' => $this->last_updated,
        ];
    }
}
