<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestInfoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_info_page(): void
    {
        $response = $this->get('/info');

        $response->assertStatus(200);
        $response->assertSee('Informasi Sistem - Simple POS');
        $response->assertSee('Halaman Publik / Tamu');
    }

    public function test_guest_can_access_informasi_alias(): void
    {
        $response = $this->get('/informasi');

        $response->assertRedirect('/info');
    }

    public function test_authenticated_user_is_redirected_to_pos_when_accessing_info_page(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/info');

        $response->assertRedirect('/pos');
    }

    public function test_authenticated_admin_is_redirected_to_pos_when_accessing_info_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/info');

        $response->assertRedirect('/pos');
    }

    public function test_authenticated_user_is_redirected_to_pos_when_accessing_login_page(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/pos');
    }

    public function test_unauthenticated_user_accessing_pos_is_redirected_to_login(): void
    {
        $response = $this->get('/pos');

        $response->assertRedirect('/login');
    }
}
