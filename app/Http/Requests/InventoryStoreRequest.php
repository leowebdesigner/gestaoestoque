<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $costPrice = $this->input('cost_price');

            if ($costPrice === null) {
                return;
            }

            $product = Product::query()->where('sku', $this->input('sku'))->first();

            if (!$product) {
                return;
            }

            if (Money::toCents($costPrice) > Money::toCents($product->sale_price)) {
                $validator->errors()->add('cost_price', 'Cost price cannot be greater than sale price.');
            }
        });
    }
}
