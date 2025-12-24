<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:64'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
