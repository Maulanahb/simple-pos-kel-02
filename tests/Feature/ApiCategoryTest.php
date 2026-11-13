<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_categories(): void
    {
        $this->getJson('/api/categories')->assertUnauthorized();
    }

    public function test_token_without_ability_is_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['products:read']);

        $this->getJson('/api/categories')->assertForbidden();
    }

    public function test_authenticated_user_with_ability_can_list_categories(): void
    {
        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);
        Product::create(['category_id' => $minuman->id, 'name' => 'Kopi', 'price' => 15000, 'stock' => 10]);
        Product::create(['category_id' => $minuman->id, 'name' => 'Teh', 'price' => 10000, 'stock' => 5]);

        Sanctum::actingAs(User::factory()->create(), ['categories:read']);

        $response = $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'products_count'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $makanan->id)
            ->assertJsonPath('data.0.name', 'Makanan')
            ->assertJsonPath('data.0.products_count', 0)
            ->assertJsonPath('data.1.id', $minuman->id)
            ->assertJsonPath('data.1.name', 'Minuman')
            ->assertJsonPath('data.1.products_count', 2);

        $this->assertArrayNotHasKey('created_at', $response->json('data.0'));
        $this->assertArrayNotHasKey('updated_at', $response->json('data.0'));
    }

    public function test_login_token_can_access_categories(): void
    {
        User::factory()->create([
            'email' => 'kasir@pos.test',
            'password' => 'password',
        ]);
        Category::create(['name' => 'Snack']);

        $token = $this->postJson('/api/login', [
            'email' => 'kasir@pos.test',
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token)
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Snack');
    }

    public function test_categories_support_filter_sort_and_pagination_validation(): void
    {
        $snack = Category::create(['name' => 'Snack']);
        $minuman = Category::create(['name' => 'Minuman']);
        Category::create(['name' => 'Alat Tulis']);

        Product::create(['category_id' => $minuman->id, 'name' => 'Kopi', 'price' => 15000, 'stock' => 10]);
        Product::create(['category_id' => $minuman->id, 'name' => 'Teh', 'price' => 10000, 'stock' => 5]);
        Product::create(['category_id' => $snack->id, 'name' => 'Roti', 'price' => 8000, 'stock' => 3]);

        Sanctum::actingAs(User::factory()->create(), ['categories:read']);

        $this->getJson('/api/categories?q=Minum')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Minuman');

        $this->getJson('/api/categories?sort=-products_count&per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('data.0.name', 'Minuman')
            ->assertJsonPath('data.1.name', 'Snack');

        $this->getJson('/api/categories?per_page=500')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');

        $this->getJson('/api/categories?sort=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort');
    }
}

