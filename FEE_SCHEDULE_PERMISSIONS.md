# Fee Schedule Permissions Implementation

## Overview

The NDSMS fee schedule module now has granular permission controls:

- **Super-admin users**: Full manage access (create, edit, delete, toggle status)
- **All other users**: View-only access to fee schedules

---

## What Changed

### 1. New Permissions Added

**File**: `database/seeders/RolesAndPermissionsSeeder.php`

Five new granular fee schedule permissions:

```php
'view fee schedules' => 'View fee schedule rates',
'create fee schedules' => 'Create new fee schedules',
'edit fee schedules' => 'Edit existing fee schedules',
'delete fee schedules' => 'Delete fee schedules',
'manage fee schedules' => 'Full fee schedule management',
```

### 2. Permissions Assigned by Role

| Role              | view | create | edit | delete | manage |
| ----------------- | ---- | ------ | ---- | ------ | ------ |
| super-admin       | ✅   | ✅     | ✅   | ✅     | ✅     |
| admin             | ✅   | -      | -    | -      | -      |
| approvals-officer | ✅   | -      | -    | -      | -      |
| registry-officer  | ✅   | -      | -    | -      | -      |
| field-officer     | ✅   | -      | -    | -      | -      |
| citizen           | ✅   | -      | -    | -      | -      |

**Key Points:**

- All roles can VIEW fee schedules
- Only super-admin can MANAGE (create/edit/delete) fee schedules
- `manage fee schedules` is automatically assigned to super-admin (gets all permissions)
- `view fee schedules` is assigned to all roles for transparency

### 3. Route Protection Updated

**File**: `routes/web.php`

Changed from:

```php
Route::middleware('can:manage settings|role:super-admin')->group(function () {
```

To:

```php
Route::middleware('can:view fee schedules')->group(function () {
```

**Impact**: Anyone with `view fee schedules` permission can access `/admin/fee-schedules`

### 4. Component Permission Checks

**File**: `app/Livewire/Admin/Fees/Index.php`

Added permission checks to all management operations:

- `mount()` - Requires user exists (can view)
- `openCreate()` - Requires `manage fee schedules` permission
- `openEdit()` - Requires `manage fee schedules` permission
- `save()` - Requires `manage fee schedules` permission
- `confirmDelete()` - Requires `manage fee schedules` permission
- `deleteFee()` - Requires `manage fee schedules` permission (double-check)

Each unauthorized attempt shows error message:

```
"You do not have permission to [action] fee schedules."
```

### 5. View Template Conditional Buttons

**File**: `resources/views/livewire/admin/fees/index.blade.php`

**"New Fee Schedule" button**:

```blade
@canany(['manage fee schedules', 'role:super-admin'])
    <button wire:click="openCreate">Create</button>
@endcanany
```

**Action buttons (Edit, Delete, Toggle)**:

```blade
@canany(['manage fee schedules', 'role:super-admin'])
    <!-- Management buttons -->
@else
    <span>View only</span>
@endcanany
```

---

## User Experience

### View-Only User (e.g., Field Officer)

1. Login → redirected to `/portal/dashboard`
2. Can view portal fees via `/portal/fee-schedule` component
3. **Cannot access admin fee management** (lacks `view fee schedules`)

### Field Officer with View Permission

1. Login → redirected to `/portal/dashboard`
2. Can view fees in admin panel at `/admin/fee-schedules` (if manually navigated)
3. Sees table of fees
4. Sees "View only" next to each fee
5. Cannot click "New Fee Schedule" button (hidden)
6. Cannot click Edit, Delete, or Toggle buttons (hidden)

### Super-Admin User

1. Login → redirected to `/admin/dashboard`
2. Navigate to Fee Schedules in sidebar (under Financial section)
3. Sees "New Fee Schedule" button (visible)
4. Can create, edit, delete, and toggle status
5. Full management capabilities

---

## Permission Logic Flow

### Accessing Fee Schedules Page

```
User navigates to /admin/fee-schedules
    ↓
Route checks: middleware('can:view fee schedules')
    ├─ Has permission? → Allow page load
    └─ No permission? → 403 Forbidden
    ↓
Component mount() runs authorization
    ├─ User exists? → Continue
    └─ Not authenticated? → Redirect to login
```

### Creating/Editing/Deleting

```
User clicks "New Fee Schedule" / "Edit" / "Delete"
    ↓
View checks:
    ├─ Has 'manage fee schedules'? → Show button
    └─ No permission? → Hide button (shows "View only")
    ↓
If button clicked, component method runs
    ↓
Method checks: auth()->user()->hasPermissionTo('manage fee schedules')
    ├─ Has permission? → Allow operation
    └─ No permission? → Show error toast, prevent action
```

---

## Configuration Summary

### Permissions Defined (5 total)

```
view fee schedules       → All roles
create fee schedules     → super-admin only
edit fee schedules       → super-admin only
delete fee schedules     → super-admin only
manage fee schedules     → super-admin only
```

### Role Assignments

```
super-admin      → All 5 permissions
admin            → view fee schedules
approvals-officer → view fee schedules
registry-officer → view fee schedules
field-officer    → view fee schedules
citizen          → view fee schedules
```

### Route Protection

```
/admin/fee-schedules → Accessible to users with 'view fee schedules'
```

---

## Setup Instructions

### Step 1: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 2: Run Seeder

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This will:

- Create 5 new permissions (view, create, edit, delete, manage)
- Assign view permission to all roles
- Assign manage permission to super-admin
- Update all roles with new permission assignments

### Step 3: Verify Setup

```bash
php artisan tinker
# Check permissions exist
Permission::where('name', 'like', '%fee%')->get();
# Should show 5 permissions

# Check super-admin has manage permission
$superAdmin = Role::where('name', 'super-admin')->first();
$superAdmin->hasPermissionTo('manage fee schedules'); # Should return true

# Check field-officer has view permission
$fieldOfficer = Role::where('name', 'field-officer')->first();
$fieldOfficer->hasPermissionTo('view fee schedules'); # Should return true
$fieldOfficer->hasPermissionTo('manage fee schedules'); # Should return false
```

---

## Testing

### Test 1: Super-Admin Can Manage

1. Log in as super-admin
2. Navigate to `/admin/fee-schedules`
3. Verify:
    - ✅ Can see list of fees
    - ✅ "New Fee Schedule" button visible
    - ✅ "Edit" and "Delete" buttons visible
    - ✅ "Toggle" button visible
    - ✅ Can create new fee
    - ✅ Can edit existing fee
    - ✅ Can delete fee
    - ✅ Can toggle status (active/inactive)

### Test 2: Admin Can Only View

1. Create test admin user:

    ```bash
    php artisan tinker
    $user = User::create(['name' => 'Test Admin', 'email' => 'admin@test.com', 'password' => bcrypt('Test123!')]);
    $user->assignRole('admin');
    exit
    ```

2. Log in as test admin
3. Navigate to `/admin/fee-schedules`
4. Verify:
    - ✅ Can see list of fees
    - ✅ "New Fee Schedule" button NOT visible
    - ✅ "Edit", "Delete", "Toggle" buttons NOT visible
    - ✅ Shows "View only" text
    - ❌ Cannot create new fee
    - ❌ Cannot edit fee
    - ❌ Cannot delete fee

### Test 3: Field Officer With View Permission

1. Create test field officer:

    ```bash
    php artisan tinker
    $user = User::create(['name' => 'Test Officer', 'email' => 'officer@test.com', 'password' => bcrypt('Test123!')]);
    $user->assignRole('field-officer');
    exit
    ```

2. Log in as test field officer
3. Manually navigate to `/admin/fee-schedules`
4. Verify:
    - ✅ Can see list of fees (has view permission)
    - ✅ "New Fee Schedule" button NOT visible
    - ✅ Management buttons NOT visible
    - ✅ Shows "View only" text

### Test 4: Permission Enforcement

1. Log in as admin user
2. Open browser console (F12)
3. Try to call:
    ```javascript
    // This should show error notification
    Livewire.find("component-id").call("openEdit", 1);
    ```
4. Verify:
    - ✅ Shows error: "You do not have permission to edit fee schedules"
    - ✅ Modal does not open

---

## Database Schema (No Changes)

The permissions are stored in the standard Spatie Permission tables:

```
permissions table:
- id
- name (e.g., 'view fee schedules')
- guard_name ('web')
- created_at
- updated_at

role_has_permissions table:
- permission_id
- role_id
```

Example data after seeding:

```
Permissions (5 new rows):
1. view fee schedules
2. create fee schedules
3. edit fee schedules
4. delete fee schedules
5. manage fee schedules

Role Has Permissions (5+ new rows):
- super-admin: ALL 5 permissions
- admin: permission 1 (view)
- approvals-officer: permission 1 (view)
- registry-officer: permission 1 (view)
- field-officer: permission 1 (view)
- citizen: permission 1 (view)
```

---

## Troubleshooting

### Issue: Users can still edit fees they shouldn't

**Solution:**

1. Verify permissions seeded: `php artisan db:seed --class=RolesAndPermissionsSeeder`
2. Clear cache: `php artisan cache:clear`
3. Check user role: `User::find(ID)->roles->pluck('name')`

### Issue: Super-admin sees "You do not have permission" error

**Solution:**

1. Verify super-admin has manage permission:
    ```bash
    php artisan tinker
    Role::where('name', 'super-admin')->first()->hasPermissionTo('manage fee schedules')
    ```
2. If false, re-run seeder:
    ```bash
    php artisan db:seed --class=RolesAndPermissionsSeeder
    ```

### Issue: View-only users can't see fee schedules

**Solution:**

1. Verify user role has view permission:
    ```bash
    php artisan tinker
    $user = User::find(ID);
    $user->roles->first()->hasPermissionTo('view fee schedules')
    ```
2. Re-run seeder if needed

### Issue: Buttons appear but don't work

**Solution:**

1. Check browser console for JavaScript errors
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Verify permission check in component:
    ```bash
    php artisan tinker
    auth()->loginUsingId(USER_ID);
    auth()->user()->hasPermissionTo('manage fee schedules')
    ```

---

## Files Modified (4 total)

| File                                                  | Changes                                            |
| ----------------------------------------------------- | -------------------------------------------------- |
| `database/seeders/RolesAndPermissionsSeeder.php`      | Added 5 fee permissions, assigned to roles         |
| `routes/web.php`                                      | Updated middleware to use `can:view fee schedules` |
| `app/Livewire/Admin/Fees/Index.php`                   | Added permission checks to all management methods  |
| `resources/views/livewire/admin/fees/index.blade.php` | Added conditional visibility for buttons           |

---

## Best Practices

### For Super-Admins

- Only super-admin should have `manage fee schedules` permission
- Consider delegating fee management to a dedicated admin user if needed
- Regular audits of fee changes

### For Other Admins

- View-only access maintains transparency
- Can monitor fee rates without accidental changes
- Can provide feedback to super-admin for changes

### For Users

- All users can see current fees (transparency)
- Encourages informed decision-making
- No security risk from viewing

---

## Summary

✅ **Super-admin**: Full management access (create, edit, delete)
✅ **All roles**: View-only access to fees
✅ **Route protected**: Checked at route level
✅ **Component protected**: Double-checked in component methods
✅ **UI protected**: Conditional button rendering
✅ **User feedback**: Error messages for unauthorized actions
✅ **Database intact**: No schema changes needed

**The fee schedule module now has proper role-based access control with permissions assigned appropriately to each role.**
