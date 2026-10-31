<?php

namespace Tests\Feature;

use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\ImportError;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_import_page_and_empty_errors(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('import.create'));

        $response->assertStatus(200);
        $response->assertSee('Import Produk dari CSV');
        $response->assertSee('Belum ada baris yang tercatat gagal di-import.');
    }

    public function test_import_job_records_failed_rows(): void
    {
        Storage::fake('local');

        $category = new Category();
        $category->name = 'Minuman';
        $category->save();

        $csvContent = "name,category_id,price\n" .
                      "Kopi Valid,{$category->id},15000\n" .
                      "Kopi Error Kategori,999,10000\n" .
                      "Kopi Error Harga,{$category->id},-1000\n";

        $path = 'imports/test_job.csv';
        Storage::disk('local')->put($path, $csvContent);

        $job = new ProcessProductImport($path);
        $job->handle();

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Valid',
        ]);

        $this->assertDatabaseMissing('products', [
            'name' => 'Kopi Error Kategori',
        ]);

        $this->assertDatabaseCount('import_errors', 2);

        $this->assertDatabaseHas('import_errors', [
            'row_number' => 3,
        ]);

        $this->assertDatabaseHas('import_errors', [
            'row_number' => 4,
        ]);
    }

    public function test_admin_can_clear_import_errors(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        ImportError::create([
            'row_number' => 2,
            'error_message' => 'Error testing',
            'raw_data' => 'sample data',
        ]);

        $response = $this->actingAs($admin)->delete(route('import.errors.clear'));

        $response->assertRedirect();
        $this->assertDatabaseCount('import_errors', 0);
    }
}
