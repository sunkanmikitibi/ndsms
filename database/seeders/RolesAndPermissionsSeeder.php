<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions grouped by resource
        $permissions = [
            // Address management
            'view addresses',
            'manage addresses',
            // Street management
            'view streets',
            'manage streets',
            // Approvals
            'view approvals',
            'manage approvals',
            // Payments
            'view payments',
            'manage payments',
            // Reports
            'view reports',
            // Users (super-admin only)
            'view users',
            'manage users',
            // Roles (super-admin only)
            'view roles',
            'manage roles',
            // Settings
            'view settings',
            'manage settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Field Officer — can view and register addresses/streets, submit applications
        $fieldOfficer = Role::firstOrCreate(['name' => 'field-officer', 'guard_name' => 'web']);
        $fieldOfficer->syncPermissions([
            'view addresses',
            'view streets',
        ]);

        // Registry Officer — can manage addresses and streets
        $registryOfficer = Role::firstOrCreate(['name' => 'registry-officer', 'guard_name' => 'web']);
        $registryOfficer->syncPermissions([
            'view addresses',
            'manage addresses',
            'view streets',
            'manage streets',
            'view reports',
        ]);

        // Approvals Officer — can review and manage applications
        $approvalsOfficer = Role::firstOrCreate(['name' => 'approvals-officer', 'guard_name' => 'web']);
        $approvalsOfficer->syncPermissions([
            'view addresses',
            'view streets',
            'view approvals',
            'manage approvals',
            'view payments',
            'view reports',
        ]);

        // Admin — all operational permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('name', 'not like', '%users%')
            ->where('name', 'not like', '%roles%')
            ->where('name', 'not like', '%settings%')
            ->get());

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Create default super-admin user (if not exists)
        $user = User::firstOrCreate(
            ['email' => 'superadmin@ndsms.gov.ng'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('Admin@1234'),
            ]
        );
        $user->assignRole('super-admin');

        $this->command->info('✓ Roles, permissions, and super-admin user seeded successfully.');
        $this->command->info('  Email: superadmin@ndsms.gov.ng | Password: Admin@1234');
    }
}
