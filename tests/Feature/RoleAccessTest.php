<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InitialMasterSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(InitialMasterSeeder::class);
    }

    public function test_all_six_role_accounts_are_properly_seeded(): void
    {
        $expectedRoles = [
            'admin@rs-ummi.id' => 'admin',
            'petugas.aset@rs-ummi.id' => 'petugas_aset',
            'teknisi@rs-ummi.id' => 'teknisi',
            'kepala.unit@rs-ummi.id' => 'kepala_unit',
            'staf.klinis@rs-ummi.id' => 'staf',
            'manajemen@rs-ummi.id' => 'manajemen',
        ];

        foreach ($expectedRoles as $email => $roleName) {
            $user = User::where('email', $email)->first();
            $this->assertNotNull($user, "Pengguna dengan email {$email} harus terdaftar.");
            $this->assertTrue($user->is_active, "Pengguna {$email} harus aktif.");
            $this->assertEquals($roleName, $user->role?->name, "Peran untuk {$email} harus {$roleName}.");
        }
    }

    public function test_all_six_roles_can_access_filament_admin_panel(): void
    {
        $emails = [
            'admin@rs-ummi.id',
            'petugas.aset@rs-ummi.id',
            'teknisi@rs-ummi.id',
            'kepala.unit@rs-ummi.id',
            'staf.klinis@rs-ummi.id',
            'manajemen@rs-ummi.id',
        ];

        foreach ($emails as $email) {
            $this->flushSession();
            $user = User::where('email', $email)->firstOrFail();
            $response = $this->actingAs($user)->get('/admin');
            $response->assertSuccessful();
        }
    }

    public function test_admin_has_full_access_to_user_management(): void
    {
        $admin = User::where('email', 'admin@rs-ummi.id')->firstOrFail();

        $this->actingAs($admin)->get('/admin/users')->assertSuccessful();
        $this->actingAs($admin)->get('/admin/users/create')->assertSuccessful();
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $nonAdminEmails = [
            'petugas.aset@rs-ummi.id',
            'teknisi@rs-ummi.id',
            'kepala.unit@rs-ummi.id',
            'staf.klinis@rs-ummi.id',
            'manajemen@rs-ummi.id',
        ];

        foreach ($nonAdminEmails as $email) {
            $this->flushSession();
            $user = User::where('email', $email)->firstOrFail();
            $this->actingAs($user)->get('/admin/users')->assertForbidden();
            $this->actingAs($user)->get('/admin/users/create')->assertForbidden();
        }
    }

    public function test_petugas_aset_can_manage_assets_and_stock_opnames_but_not_users_or_tickets(): void
    {
        $petugasAset = User::where('email', 'petugas.aset@rs-ummi.id')->firstOrFail();

        $this->actingAs($petugasAset)->get('/admin/assets')->assertSuccessful();
        $this->actingAs($petugasAset)->get('/admin/assets/create')->assertSuccessful();
        $this->actingAs($petugasAset)->get('/admin/stock-opnames')->assertSuccessful();
        $this->actingAs($petugasAset)->get('/admin/asset-movements')->assertSuccessful();
        $this->actingAs($petugasAset)->get('/admin/users')->assertForbidden();
        $this->actingAs($petugasAset)->get('/admin/work-orders')->assertForbidden();
    }

    public function test_teknisi_can_handle_work_orders_and_maintenance_only(): void
    {
        $teknisi = User::where('email', 'teknisi@rs-ummi.id')->firstOrFail();

        $this->actingAs($teknisi)->get('/admin')->assertSuccessful();
        $this->actingAs($teknisi)->get('/admin/work-orders')->assertSuccessful();
        $this->actingAs($teknisi)->get('/admin/maintenance-schedules')->assertSuccessful();
        $this->actingAs($teknisi)->get('/admin/calibrations')->assertSuccessful();
        $this->actingAs($teknisi)->get('/admin/assets')->assertForbidden();
        $this->actingAs($teknisi)->get('/admin/assets/create')->assertForbidden();
        $this->actingAs($teknisi)->get('/admin/stock-opnames')->assertForbidden();
        $this->actingAs($teknisi)->get('/admin/categories')->assertForbidden();
    }

    public function test_kepala_unit_can_manage_movements_and_tickets_but_not_maintenance_or_opname(): void
    {
        $kepalaUnit = User::where('email', 'kepala.unit@rs-ummi.id')->firstOrFail();

        $this->actingAs($kepalaUnit)->get('/admin/assets')->assertSuccessful();
        $this->actingAs($kepalaUnit)->get('/admin/asset-movements')->assertSuccessful();
        $this->actingAs($kepalaUnit)->get('/admin/work-orders')->assertSuccessful();
        $this->actingAs($kepalaUnit)->get('/admin/assets/create')->assertForbidden();
        $this->actingAs($kepalaUnit)->get('/admin/maintenance-schedules')->assertForbidden();
        $this->actingAs($kepalaUnit)->get('/admin/calibrations')->assertForbidden();
        $this->actingAs($kepalaUnit)->get('/admin/stock-opnames')->assertForbidden();
    }

    public function test_staf_klinis_has_focused_access_to_report_tickets_only(): void
    {
        $staf = User::where('email', 'staf.klinis@rs-ummi.id')->firstOrFail();

        $this->actingAs($staf)->get('/admin')->assertSuccessful();
        $this->actingAs($staf)->get('/admin/work-orders')->assertSuccessful();
        $this->actingAs($staf)->get('/admin/work-orders/create')->assertSuccessful();
        $this->actingAs($staf)->get('/admin/assets')->assertForbidden();
        $this->actingAs($staf)->get('/admin/assets/create')->assertForbidden();
        $this->actingAs($staf)->get('/admin/asset-movements')->assertForbidden();
        $this->actingAs($staf)->get('/admin/stock-opnames')->assertForbidden();
        $this->actingAs($staf)->get('/admin/calibrations')->assertForbidden();
    }

    public function test_manajemen_has_read_only_access_to_assets_and_stock_opname(): void
    {
        $manajemen = User::where('email', 'manajemen@rs-ummi.id')->firstOrFail();

        $this->actingAs($manajemen)->get('/admin')->assertSuccessful();
        $this->actingAs($manajemen)->get('/admin/assets')->assertSuccessful();
        $this->actingAs($manajemen)->get('/admin/stock-opnames')->assertSuccessful();
        $this->actingAs($manajemen)->get('/admin/assets/create')->assertForbidden();
        $this->actingAs($manajemen)->get('/admin/stock-opnames/create')->assertForbidden();
    }
}
