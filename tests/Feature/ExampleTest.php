<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the login redirect points to Filament admin login.
     */
    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect('/admin/login');
    }

    /**
     * Test root redirects to Filament admin panel.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    /**
     * Test dashboard route redirects to Filament admin panel.
     */
    public function test_dashboard_route_redirects_to_admin(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/admin');
    }

    /**
     * Test Filament login page returns HTTP 200.
     */
    public function test_filament_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('SIMAS-RS RS UMMI Bogor');
    }

    /**
     * Test non-existent route returns 404 HTTP status.
     */
    public function test_non_existent_route_returns_404(): void
    {
        $response = $this->get('/non-existent-page-url-404');

        $response->assertStatus(404);
    }

    /**
     * Test authenticated user can access Filament admin panel.
     */
    public function test_authenticated_user_can_access_filament_admin_panel(): void
    {
        $role = Role::create([
            'name' => 'admin_filament',
            'label' => 'Administrator Filament',
            'level' => 10,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('SIMAS-RS RS UMMI Bogor');
    }
}
