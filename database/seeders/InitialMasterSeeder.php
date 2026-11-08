<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Departemen Rumah Sakit Awal
        $departmentsData = [
            ['code' => 'IPSRS', 'name' => 'Instalasi Pemeliharaan Sarana Rumah Sakit (IPSRS)', 'requires_movement_approval' => false],
            ['code' => 'ICU', 'name' => 'Intensive Care Unit (ICU)', 'requires_movement_approval' => true],
            ['code' => 'RAD', 'name' => 'Instalasi Radiologi', 'requires_movement_approval' => true],
            ['code' => 'IGD', 'name' => 'Instalasi Gawat Darurat (IGD)', 'requires_movement_approval' => true],
            ['code' => 'FAR', 'name' => 'Instalasi Farmasi', 'requires_movement_approval' => false],
            ['code' => 'RAWAT_INAP', 'name' => 'Instalasi Rawat Inap', 'requires_movement_approval' => true],
        ];

        foreach ($departmentsData as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                [
                    'name' => $dept['name'],
                    'requires_movement_approval' => $dept['requires_movement_approval'],
                ]
            );
        }

        $roles = Role::all()->keyBy('name');
        $departments = Department::all()->keyBy('code');

        // 2. Akun Berdasarkan Seluruh Peran (BRD)
        $accounts = [
            [
                'email' => 'admin@rs-ummi.id',
                'name' => 'Administrator Sistem',
                'phone' => '081234567890',
                'role_name' => 'admin',
                'department_code' => 'IPSRS',
            ],
            [
                'email' => 'petugas.aset@rs-ummi.id',
                'name' => 'Petugas Aset',
                'phone' => '081234567891',
                'role_name' => 'petugas_aset',
                'department_code' => 'IPSRS',
            ],
            [
                'email' => 'teknisi@rs-ummi.id',
                'name' => 'Teknisi IPSRS & Biomedis',
                'phone' => '081234567892',
                'role_name' => 'teknisi',
                'department_code' => 'IPSRS',
            ],
            [
                'email' => 'kepala.unit@rs-ummi.id',
                'name' => 'Kepala Ruangan ICU',
                'phone' => '081234567893',
                'role_name' => 'kepala_unit',
                'department_code' => 'ICU',
            ],
            [
                'email' => 'staf.klinis@rs-ummi.id',
                'name' => 'Staf Perawat ICU',
                'phone' => '081234567894',
                'role_name' => 'staf',
                'department_code' => 'ICU',
            ],
            [
                'email' => 'manajemen@rs-ummi.id',
                'name' => 'Manajemen & Direksi',
                'phone' => '081234567895',
                'role_name' => 'manajemen',
                'department_code' => 'IPSRS',
            ],
        ];

        foreach ($accounts as $account) {
            $role = $roles->get($account['role_name']);
            $dept = $departments->get($account['department_code']);

            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'phone' => $account['phone'],
                    'password' => Hash::make('password'),
                    'role_id' => $role?->id,
                    'department_id' => $dept?->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
