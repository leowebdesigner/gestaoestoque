<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'SKU-1001',
                'name' => 'Notebook Pro 14',
                'description' => 'Notebook com 16GB RAM e SSD 512GB.',
                'cost_price' => 4200.00,
                'sale_price' => 5899.90,
            ],
            [
                'sku' => 'SKU-1002',
                'name' => 'Monitor UltraWide 34',
                'description' => 'Monitor 34 polegadas, 144Hz.',
                'cost_price' => 1550.00,
                'sale_price' => 2299.90,
            ],
            [
                'sku' => 'SKU-1003',
                'name' => 'Teclado Mecânico RGB',
                'description' => 'Switches táteis e iluminação RGB.',
                'cost_price' => 230.00,
                'sale_price' => 399.90,
            ],
            [
                'sku' => 'SKU-1004',
                'name' => 'Mouse Gamer 16000 DPI',
                'description' => 'Sensor óptico de alta precisão.',
                'cost_price' => 120.00,
                'sale_price' => 219.90,
            ],
            [
                'sku' => 'SKU-1005',
                'name' => 'Headset Noise Cancelling',
                'description' => 'Cancelamento de ruído ativo.',
                'cost_price' => 310.00,
                'sale_price' => 499.90,
            ],
        ];

        Product::query()->upsert($products, ['sku']);
    }
}
