<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Administrator Sistem',
                'permissions' => ['*'],
            ],
            [
                'name' => 'petugas_aset',
                'label' => 'Petugas Aset',
                'permissions' => [
                    'assets.create',
                    'assets.update',
                    'assets.view',
                    'assets.import',
                    'assets.mutate',
                    'stock_opname.manage',
                    'reports.export',
                ],
            ],
            [
                'name' => 'teknisi',
                'label' => 'Teknisi IPSRS / Biomedis',
                'permissions' => [
                    'assets.view',
                    'work_orders.handle',
                    'maintenance.manage',
                    'calibrations.manage',
                    'tickets.report',
                ],
            ],
            [
                'name' => 'kepala_unit',
                'label' => 'Kepala Unit / Ruangan',
                'permissions' => [
                    'assets.view_unit',
                    'tickets.report',
                    'tickets.confirm',
                    'movements.approve',
                    'reports.export_unit',
                ],
            ],
            [
                'name' => 'staf',
                'label' => 'Staf Klinis',
                'permissions' => [
                    'assets.view_unit',
                    'tickets.report',
                    'tickets.confirm_own',
                ],
            ],
            [
                'name' => 'manajemen',
                'label' => 'Manajemen',
                'permissions' => [
                    'assets.view',
                    'dashboard.view',
                    'reports.export',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'label' => $roleData['label'],
                    'permissions' => $roleData['permissions'],
                ]
            );
        }
    }
}
