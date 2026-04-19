<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions grouped by resource with descriptions
        $permissions = [
            // User Management
            'view users' => 'View list of users',
            'create users' => 'Create new users',
            'edit users' => 'Edit existing users',
            'delete users' => 'Delete users',
            'manage users' => 'Legacy: Full user management',

            // Role Management
            'view roles' => 'View list of roles',
            'create roles' => 'Create new roles',
            'edit roles' => 'Edit existing roles',
            'delete roles' => 'Delete roles',
            'manage roles' => 'Legacy: Full role management',

            // Permission Management
            'view permissions' => 'View list of permissions',
            'create permissions' => 'Create new permissions',
            'edit permissions' => 'Edit existing permissions',
            'delete permissions' => 'Delete permissions',

            // Street Management
            'view streets' => 'View street records',
            'create streets' => 'Create new street records',
            'edit streets' => 'Edit street records',
            'delete streets' => 'Delete street records',
            'manage streets' => 'Legacy: Full street management',
            'approve streets' => 'Approve street applications',

            // Address Management
            'view addresses' => 'View address records',
            'create addresses' => 'Create new address records',
            'edit addresses' => 'Edit address records',
            'delete addresses' => 'Delete address records',
            'manage addresses' => 'Legacy: Full address management',
            'approve addresses' => 'Approve address registrations',

            // Approval Workflow
            'view approvals' => 'View pending approvals',
            'approve applications' => 'Approve applications',
            'reject applications' => 'Reject applications',
            'manage approvals' => 'Legacy: Full approval management',
            'manage street applications' => 'Manage street applications',
            'manage field reports' => 'Manage field reports',

            // Payment Management
            'view payments' => 'View payment records',
            'refund payments' => 'Process payment refunds',
            'manage payments' => 'Legacy: Full payment management',

            // Reports
            'view reports' => 'View reports and analytics',
            'export reports' => 'Export report data',

            // Fee Schedule Management
            'view fee schedules' => 'View fee schedule rates',
            'create fee schedules' => 'Create new fee schedules',
            'edit fee schedules' => 'Edit existing fee schedules',
            'delete fee schedules' => 'Delete fee schedules',
            'manage fee schedules' => 'Full fee schedule management',

            // Settings
            'view settings' => 'View system settings',
            'manage settings' => 'Manage system settings',

            // Field Officer Actions
            'submit street applications' => 'Submit new street applications',
            'submit address registrations' => 'Submit address registrations',
            'perform address indexing' => 'Request Google Maps address indexing',
            'request street revalidation' => 'Request street revalidation',
        ];

        $hasPermissionDescription = Schema::hasColumn('permissions', 'description');

        foreach ($permissions as $name => $description) {
            $attributes = ['name' => $name, 'guard_name' => 'web'];

            $values = [];
            if ($hasPermissionDescription) {
                $values['description'] = $description;
            }

            Permission::firstOrCreate($attributes, $values);
        }

        // Field Officer — can view and register addresses/streets, submit applications
        $hasRoleDescription = Schema::hasColumn('roles', 'description');

        $fieldOfficerAttributes = ['name' => 'field-officer', 'guard_name' => 'web'];
        $fieldOfficerValues = [];
        if ($hasRoleDescription) {
            $fieldOfficerValues['description'] = 'Field officer role. Can submit applications and perform indexing/revalidation requests.';
        }
        $fieldOfficer = Role::firstOrCreate($fieldOfficerAttributes, $fieldOfficerValues);
        $fieldOfficer->syncPermissions([
            'view addresses',
            'view streets',
            'submit street applications',
            'submit address registrations',
            'perform address indexing',
            'request street revalidation',
            'view reports',
            'view fee schedules',
        ]);

        // Registry Officer — can manage addresses and streets
        $registryOfficerAttributes = ['name' => 'registry-officer', 'guard_name' => 'web'];
        $registryOfficerValues = [];
        if ($hasRoleDescription) {
            $registryOfficerValues['description'] = 'Registry officer role. Can manage street and address records.';
        }
        $registryOfficer = Role::firstOrCreate($registryOfficerAttributes, $registryOfficerValues);
        $registryOfficer->syncPermissions([
            'view addresses',
            'edit addresses',
            'view streets',
            'edit streets',
            'view reports',
            'view fee schedules',
        ]);

        // Approvals Officer — can review and manage applications
        $approvalsOfficerAttributes = ['name' => 'approvals-officer', 'guard_name' => 'web'];
        $approvalsOfficerValues = [];
        if ($hasRoleDescription) {
            $approvalsOfficerValues['description'] = 'Approvals officer role. Can review and approve street and address applications.';
        }
        $approvalsOfficer = Role::firstOrCreate($approvalsOfficerAttributes, $approvalsOfficerValues);
        $approvalsOfficer->syncPermissions([
            'view addresses',
            'view streets',
            'view approvals',
            'approve applications',
            'reject applications',
            'manage street applications',
            'manage field reports',
            'view payments',
            'view reports',
            'view fee schedules',
        ]);

        // Admin — all operational permissions
        $adminAttributes = ['name' => 'admin', 'guard_name' => 'web'];
        $adminValues = [];
        if ($hasRoleDescription) {
            $adminValues['description'] = 'Administrative access. Can manage streets, addresses, approvals, and view reports.';
        }
        $admin = Role::firstOrCreate($adminAttributes, $adminValues);
        $admin->syncPermissions(Permission::where('name', 'not like', '%users%')
            ->where('name', 'not like', '%roles%')
            ->where('name', 'not like', '%permissions%')
            ->where('name', 'not like', '%settings%')
            ->where('name', 'not like', 'manage%')
            ->get());

        // Super Admin — all permissions
        $superAdminAttributes = ['name' => 'super-admin', 'guard_name' => 'web'];
        $superAdminValues = [];
        if ($hasRoleDescription) {
            $superAdminValues['description'] = 'Full system access. Can manage users, roles, permissions, and all resources.';
        }
        $superAdmin = Role::firstOrCreate($superAdminAttributes, $superAdminValues);
        $superAdmin->syncPermissions(Permission::all());

        // Citizen role
        $citizenAttributes = ['name' => 'citizen', 'guard_name' => 'web'];
        $citizenValues = [];
        if ($hasRoleDescription) {
            $citizenValues['description'] = 'Citizen role. Can submit applications and perform indexing/revalidation requests.';
        }
        $citizen = Role::firstOrCreate($citizenAttributes, $citizenValues);
        $citizen->syncPermissions([
            'view streets',
            'submit street applications',
            'submit address registrations',
            'perform address indexing',
            'request street revalidation',
            'view fee schedules',
        ]);

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
        $this->command->info('  Available roles: super-admin, admin, approvals-officer, registry-officer, field-officer, citizen');
    }
}
