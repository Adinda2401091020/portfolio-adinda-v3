<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_returns_active_products(): void
    {
        Product::query()->create([
            'name' => 'Seroja Arabica',
            'price' => 85000,
            'stock' => 20,
            'is_active' => true,
        ]);
        Product::query()->create([
            'name' => 'Hidden coffee',
            'price' => 10000,
            'stock' => 5,
            'is_active' => false,
        ]);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Seroja Arabica')
            ->assertJsonPath('data.0.price', 85000);
    }

    public function test_customer_can_view_an_active_product(): void
    {
        $product = Product::query()->create([
            'name' => 'House Blend',
            'description' => 'Coffee blend',
            'price' => 75000,
            'stock' => 12,
            'is_active' => true,
        ]);

        $this->getJson('/api/products/'.$product->id)
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'House Blend');
    }

    public function test_customer_cannot_view_an_inactive_product(): void
    {
        $product = Product::query()->create([
            'name' => 'Hidden coffee',
            'price' => 10000,
            'stock' => 5,
            'is_active' => false,
        ]);

        $this->getJson('/api/products/'.$product->id)->assertNotFound();
    }
}
