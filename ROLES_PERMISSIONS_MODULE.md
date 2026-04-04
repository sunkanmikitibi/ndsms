# Roles & Permissions Module

## Overview

Comprehensive Role-Based Access Control (RBAC) module for managing user roles and permissions in NDSMS. Built with Spatie Laravel Permission, Livewire 4, and Flux UI.

## Features

✅ **Full CRUD Operations** for Roles and Permissions
✅ **Granular Permission Management** - Create, edit, assign permissions to roles
✅ **Role Management** - Create, edit, delete, assign permissions
✅ **Permission Grouping** - Organized by resource (users, streets, addresses, etc.)
✅ **User Role Assignment** - Assign roles to users through admin panel
✅ **Pre-configured Roles** - Super-admin, Admin, Registry Officer, Approvals Officer, Field Officer, Citizen
✅ **Database Seeding** - Automatic setup with default roles and permissions
✅ **Access Control** - Super-admin only endpoints with middleware protection

## Architecture

### Models & Database

- **Spatie Permission Models**:
    - `Spatie\Permission\Models\Role` - Role records
    - `Spatie\Permission\Models\Permission` - Permission records
    - Pivot tables: `role_has_permissions`, `role_user`, `model_has_permissions`

- **Key Files**:
    - `database/migrations/2026_04_03_111956_create_permission_tables.php` - Permission schema
    - `database/seeders/RolesAndPermissionsSeeder.php` - Initial data

### Livewire Components

#### Roles Management

**File**: `app/Livewire/Admin/Roles/Index.php`

- **View**: `resources/views/livewire/admin/roles/index.blade.php`
- **Features**:
    - List all roles with pagination
    - Search/filter roles
    - Create new roles
    - Edit existing roles
    - Assign/remove permissions to roles
    - Delete roles (with safeguards for system roles)
    - Display permission count and user assignment count

#### Permissions Management

**File**: `app/Livewire/Admin/Permissions/Index.php`

- **View**: `resources/views/livewire/admin/permissions/index.blade.php`
- **Features**:
    - List all permissions with pagination
    - Search/filter permissions
    - Create new permissions with descriptions
    - Edit existing permissions
    - Delete permissions (with cascade cleanup)
    - Show which roles use each permission
    - Group permissions by resource

### Routes

Protected under `Route::prefix('admin')->middleware(['auth', 'verified', 'role:super-admin'])`

```php
GET  /admin/roles        → Roles Index
GET  /admin/permissions  → Permissions Index
```

Both routes require `super-admin` role.

## Built-in Roles

### 1. Super Admin

- **Description**: Full system access
- **Permissions**: ALL permissions
- **Typical User**: System administrator

### 2. Admin

- **Description**: Operational administration
- **Permissions**:
    - All street/address/approval/payment management
    - View reports
    - Cannot manage users, roles, or settings

### 3. Registry Officer

- **Description**: Street and address record management
- **Permissions**:
    - View/edit streets
    - View/edit addresses
    - View reports

### 4. Approvals Officer

- **Description**: Application review and approval
- **Permissions**:
    - View approvals
    - Approve/reject applications
    - View streets, addresses, payments
    - View reports

### 5. Field Officer

- **Description**: Field data submission
- **Permissions**:
    - Submit street applications
    - Submit address registrations
    - Request address indexing (Paystack)
    - Request street revalidation (Paystack)
    - View streets and reports

### 6. Citizen

- **Description**: Public user role
- **Permissions**:
    - Submit street applications
    - Submit address registrations
    - Request address indexing
    - Request street revalidation

## Built-in Permissions

### User Management

- `view users` - See user list
- `create users` - Create new users
- `edit users` - Modify user details
- `delete users` - Remove users
- `manage users` - Legacy (full user management)

### Role Management

- `view roles` - See role list
- `create roles` - Create new roles
- `edit roles` - Modify roles
- `delete roles` - Remove roles
- `manage roles` - Legacy

### Permission Management

- `view permissions` - See permission list
- `create permissions` - Create new permissions
- `edit permissions` - Modify permissions
- `delete permissions` - Remove permissions

### Street Management

- `view streets` - View street records
- `create streets` - Create new streets
- `edit streets` - Modify street data
- `delete streets` - Remove streets
- `approve streets` - Approve street submissions
- `manage streets` - Legacy

### Address Management

- `view addresses` - View address records
- `create addresses` - Create new addresses
- `edit addresses` - Modify addresses
- `delete addresses` - Remove addresses
- `approve addresses` - Approve address registrations
- `manage addresses` - Legacy

### Approval Workflow

- `view approvals` - See pending approvals
- `approve applications` - Approve submissions
- `reject applications` - Reject submissions
- `manage approvals` - Legacy

### Payments & Reports

- `view payments` - View payment records
- `refund payments` - Process refunds
- `manage payments` - Legacy payment management
- `view reports` - Access analytics
- `export reports` - Export report data

### System

- `view settings` - View system settings
- `manage settings` - Configure system settings

## Usage

### 1. Initialize Roles & Permissions

Run the seeder to create default roles and permissions:

```bash
# Run all seeders (includes roles)
php artisan db:seed

# Or run just the roles seeder
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This creates:

- 50+ permissions organized by resource
- 6 pre-configured roles
- Super-admin user account

### 2. Manage Roles (Admin Panel)

**Access**: `/admin/roles` (Super-admin only)

**Create a New Role**:

1. Click "New Role" button
2. Enter role name (e.g., "moderator")
3. Select permissions to assign
4. Click "Save Role"

**Edit a Role**:

1. Click "Edit" on the role
2. Modify name or permissions
3. Click "Save Role"

**Delete a Role**:

1. Click trash icon on the role
2. Confirm deletion
3. Role and its permissions are removed from all users

### 3. Manage Permissions (Admin Panel)

**Access**: `/admin/permissions` (Super-admin only)

**Create a New Permission**:

1. Click "New Permission" button
2. Enter permission name (format: `action resource`, e.g., "view dashboard")
3. Add optional description
4. Click "Save Permission"

**Edit a Permission**:

1. Click "Edit" on the permission
2. Modify name or description
3. Click "Save Permission"

**Delete a Permission**:

1. Click trash icon on the permission
2. Confirm deletion
3. Permission is removed from all roles

### 4. Assign Roles to Users

Edit user through Users admin panel:

```php
$user->assignRole('field-officer');
$user->assignRole(['admin', 'moderator']);
```

### 5. Check Permissions in Code

```php
// Check if user has permission
if ($user->can('view addresses')) {
    // Show addresses
}

// Check if user has role
if ($user->hasRole('admin')) {
    // Admin-only action
}

// Check multiple roles
if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // Administrative access
}

// Check multiple permissions
if ($user->hasAllPermissions(['view users', 'edit users'])) {
    // Show user management
}
```

### 6. Protect Routes with Permissions

```php
// Only users with permission
Route::middleware('can:view addresses')->get('/addresses', ...);

// Only users with role
Route::middleware('role:admin')->get('/admin', ...);

// Multiple roles
Route::middleware('role:admin|super-admin')->get('/dashboard', ...);

// Authorization policy
if (Gate::allows('manage-roles')) {
    // Can manage roles
}
```

## Livewire Features

### Roles Index Component

**Public Methods**:

- `openCreate()` - Open create role modal
- `openEdit(int $id)` - Open edit role modal
- `save()` - Save/update role with permissions
- `confirmDelete(int $id)` - Show delete confirmation
- `deleteRole()` - Delete role and cleanup relationships
- `search()` - Trigger search pagination reset

**Public Properties**:

- `$search` - Role name search query (live debounce)
- `$showModal` - Toggle modal visibility
- `$showDeleteModal` - Toggle delete confirmation
- `$editId` - Current editing role ID
- `$roleName` - Form input: role name
- `$selectedPermissions` - Form input: array of selected permission IDs
- `$deleteId` - Role ID pending deletion

### Permissions Index Component

**Public Methods**:

- `openCreate()` - Open create permission modal
- `openEdit(int $id)` - Open edit permission modal
- `save()` - Save/update permission
- `confirmDelete(int $id)` - Show delete confirmation
- `deletePermission()` - Delete permission with cascade cleanup
- `search()` - Trigger search pagination reset

**Public Properties**:

- `$search` - Permission search query (live debounce)
- `$showModal` - Toggle modal visibility
- `$showDeleteModal` - Toggle delete confirmation
- `$editId` - Current editing permission ID
- `$permissionName` - Form input: permission name
- `$permissionDescription` - Form input: optional description
- `$deleteId` - Permission ID pending deletion

## UI Components

Both components use Flux UI and Tailwind CSS with:

- Modal overlays for CRUD forms
- Pagination (15 items per page)
- Live search with debouncing (300ms)
- Permission grouping by resource
- Status badges and action buttons
- Empty states with helpful messages
- Toast notifications for actions
- Icons from FontAwesome

## Validation

### Roles

- **Name**: Required, 3-255 characters, unique
- **Permissions**: Optional array

### Permissions

- **Name**: Required, max 100 characters
- **Description**: Optional, max 500 characters

## Database Relationships

```
Roles (1) ←→ (M) Permissions [through role_has_permissions]
Users (M) ←→ (M) Roles [through model_has_roles]
Users (M) ←→ (M) Permissions [through model_has_permissions]
```

## Testing

Run tests for permission functionality:

```bash
# Run all permission tests
php artisan test --filter=Permission

# Run all role tests
php artisan test --filter=Role
```

## Security Considerations

1. **Super-admin Protection**: Super-admin role cannot be deleted or modified via truncation
2. **Permission Cascade**: Deleting permissions removes them from all roles
3. **Role Cascade**: Deleting roles removes role assignment from all users
4. **Middleware Protection**: All admin routes require `super-admin` role
5. **Authorization**: Check permissions with `$user->can()` before showing sensitive data
6. **Audit Trail**: Consider adding logging for role/permission changes

## Common Scenarios

### Add Permission to Existing Role

```php
$role = Role::where('name', 'admin')->first();
$role->givePermissionTo('delete users');
```

### Remove Permission from Role

```php
$role = Role::where('name', 'field-officer')->first();
$role->revokePermissionFrom('edit streets');
```

### Clone Role with Permissions

```php
$original = Role::where('name', 'admin')->with('permissions')->first();
$clone = Role::create(['name' => 'admin-copy', 'guard_name' => 'web']);
$clone->syncPermissions($original->permissions);
```

### Get All Users with Specific Role

```php
$fieldOfficers = User::role('field-officer')->get();
```

### Sync User Roles

```php
// Replace all user roles
$user->syncRoles(['field-officer', 'citizen']);

// Add role without removing others
$user->assignRole('moderator');

// Remove specific role
$user->removeRole('moderator');
```

## Troubleshooting

### Permissions not showing in UI

- Clear cache: `php artisan cache:clear`
- Clear permission cache: `php artisan permission:cache-reset`
- Verify middleware: Check if routes have proper middleware

### Role not assigned to user

- Verify role exists: `Role::where('name', 'admin')->exists()`
- Check guard_name matches: Should be 'web'
- Verify user model uses HasRoles trait

### Seeder not running

- Check if RolesAndPermissionsSeeder is called in DatabaseSeeder
- Verify migration ran: `php artisan migrate:status`
- Clear cache before seeding: `php artisan cache:clear`

## Performance

- **Database Queries**: Uses eager loading with `with('permissions')` and `with('roles')`
- **Pagination**: 15 items per page to reduce memory usage
- **Caching**: Leverages Spatie's permission caching system
- **Debouncing**: Search debounced 300ms to reduce database hits

## Future Enhancements

- [ ] Bulk role assignment
- [ ] Permission templates
- [ ] Role versioning/history
- [ ] Audit logging for role changes
- [ ] Custom permission validation
- [ ] Role duplication from UI
- [ ] Permission inheritance hierarchies
- [ ] Time-limited role assignments

## File Summary

| File                                                         | Purpose                    |
| ------------------------------------------------------------ | -------------------------- |
| `app/Livewire/Admin/Roles/Index.php`                         | Roles CRUD component       |
| `app/Livewire/Admin/Permissions/Index.php`                   | Permissions CRUD component |
| `resources/views/livewire/admin/roles/index.blade.php`       | Roles view                 |
| `resources/views/livewire/admin/permissions/index.blade.php` | Permissions view           |
| `database/seeders/RolesAndPermissionsSeeder.php`             | Initial data seeder        |
| `routes/web.php`                                             | Route definitions          |

## Support

For issues, questions, or enhancements:

1. Check existing permissions in database
2. Verify middleware on protected routes
3. Clear cache with `php artisan cache:clear`
4. Review Spatie Laravel Permission documentation
