<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Seroja Arabica', 'price' => 85000, 'stock' => 20],
            ['name' => 'Seroja Robusta', 'price' => 65000, 'stock' => 15],
            ['name' => 'House Blend', 'price' => 75000, 'stock' => 12],
        ] as $attributes) {
            Product::query()->updateOrCreate(
                ['name' => $attributes['name']],
                [
                    ...$attributes,
                    'description' => $attributes['name'].' dari Seroja Coffee.',
                    'is_active' => true,
                ],
            );
        }
    }
}
