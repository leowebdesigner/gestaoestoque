<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $items = $this->input('items', []);

            if (!is_array($items) || $items === []) {
                return;
            }

            $productIds = array_unique(array_column($items, 'product_id'));
            $inventories = Inventory::query()
                ->whereIn('product_id', $productIds)
                ->get(['product_id', 'quantity'])
                ->keyBy('product_id');

            foreach ($items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);

                if (!$productId || $qty < 1) {
                    continue;
                }

                $inventory = $inventories->get($productId);

                if (!$inventory || $inventory->quantity < $qty) {
                    $validator->errors()->add(
                        "items.$index.quantity",
                        'Insufficient stock for requested quantity.'
                    );
                }
            }
        });
    }
}
