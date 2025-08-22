<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_products_list_when_products_exist(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/products?per_page=100');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data')
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'price',
                            'stock',
                            'sku',
                            'status',
                            'category_id',
                            'brand_id',
                            'uuid',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
    }
}
