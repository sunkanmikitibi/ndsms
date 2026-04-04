<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions by resource
        $permissions = [
            // User Management
            'view users' => 'View list of users',
            'create users' => 'Create new users',
            'edit users' => 'Edit existing users',
            'delete users' => 'Delete users',

            // Role Management
            'view roles' => 'View list of roles',
            'create roles' => 'Create new roles',
            'edit roles' => 'Edit existing roles',
            'delete roles' => 'Delete roles',

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
            'approve streets' => 'Approve street applications',

            // Address Management
            'view addresses' => 'View address records',
            'create addresses' => 'Create new address records',
            'edit addresses' => 'Edit address records',
            'delete addresses' => 'Delete address records',
            'approve addresses' => 'Approve address registrations',

            // Approval Workflow
            'view approvals' => 'View pending approvals',
            'approve applications' => 'Approve applications',
            'reject applications' => 'Reject applications',

            // Payment Management
            'view payments' => 'View payment records',
            'refund payments' => 'Process payment refunds',

            // Reports
            'view reports' => 'View reports and analytics',
            'export reports' => 'Export report data',

            // Settings
            'view settings' => 'View system settings',
            'manage settings' => 'Manage system settings',

            // Field Officer Actions
            'submit street applications' => 'Submit new street applications',
            'submit address registrations' => 'Submit address registrations',
            'perform address indexing' => 'Request Google Maps address indexing',
            'request street revalidation' => 'Request street revalidation',
        ];

        // Create permissions
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Define roles and their permissions
        $roles = [
            'super-admin' => [
                // Super admin has all permissions
            ],
            'admin' => [
                'view streets',
                'create streets',
                'edit streets',
                'delete streets',
                'view addresses',
                'create addresses',
                'edit addresses',
                'delete addresses',
                'view approvals',
                'approve applications',
                'reject applications',
                'view payments',
                'view reports',
                'export reports',
                'view settings',
            ],
            'field-officer' => [
                'view streets',
                'submit street applications',
                'submit address registrations',
                'perform address indexing',
                'request street revalidation',
                'view reports',
            ],
            'citizen' => [
                'view streets',
                'submit street applications',
                'submit address registrations',
                'perform address indexing',
                'request street revalidation',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['description' => $this->getRoleDescription($roleName)]
            );

            // Sync permissions (all permissions for super-admin, specific ones for others)
            if ($roleName === 'super-admin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions(
                    Permission::whereIn('name', $permissionNames)->get()
                );
            }
        }
    }

    /**
     * Get description for role
     */
    private function getRoleDescription(string $roleName): string
    {
        return match ($roleName) {
            'super-admin' => 'Full system access. Can manage users, roles, permissions, and all resources.',
            'admin' => 'Administrative access. Can manage streets, addresses, approvals, and view reports.',
            'field-officer' => 'Field officer role. Can submit applications and perform indexing/revalidation requests.',
            'citizen' => 'Citizen role. Can submit applications and perform indexing/revalidation requests.',
            default => "The {$roleName} role",
        };
    }
}
