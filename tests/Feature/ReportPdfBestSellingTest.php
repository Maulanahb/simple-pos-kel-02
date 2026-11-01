<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPdfBestSellingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_pdf_with_best_selling_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = new Category();
        $category->name = 'Makanan';
        $category->save();

        $product1 = Product::create([
            'name' => 'Nasi Goreng',
            'category_id' => $category->id,
            'price' => 20000,
        ]);

        $product2 = Product::create([
            'name' => 'Mie Goreng',
            'category_id' => $category->id,
            'price' => 15000,
        ]);

        $transaction = Transaction::create([
            'user_id' => $admin->id,
            'total' => 50000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product1->id,
            'qty' => 2,
            'subtotal' => 40000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product2->id,
            'qty' => 1,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export.pdf'));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_admin_can_export_pdf_with_category_filter(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category1 = new Category();
        $category1->name = 'Makanan';
        $category1->save();

        $category2 = new Category();
        $category2->name = 'Minuman';
        $category2->save();

        $product1 = Product::create([
            'name' => 'Es Teh',
            'category_id' => $category2->id,
            'price' => 5000,
        ]);

        $transaction = Transaction::create([
            'user_id' => $admin->id,
            'total' => 10000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product1->id,
            'qty' => 2,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export.pdf', [
            'category_id' => $category2->id,
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
