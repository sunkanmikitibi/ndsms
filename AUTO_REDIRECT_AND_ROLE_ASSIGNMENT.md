# Automatic Role-Based Redirect & Auto-Role Assignment

## Overview

The NDSMS system now automatically handles user redirection after login based on their assigned role and automatically assigns the field-officer role to new user registrations.

---

## What Changed

### 1. Fortify Configuration Update

**File**: `config/fortify.php`

**Changed**: Home redirect path from `/admin` to `/dashboard`

```php
// BEFORE
'home' => '/admin',

// AFTER
'home' => '/dashboard',
```

**Why**: This ensures ALL users after login are first routed through the `HomeRedirectController` which intelligently redirects based on role, rather than defaulting everyone to `/admin`.

### 2. CreateNewUser Action Update

**File**: `app/Actions/Fortify/CreateNewUser.php`

**Added**: Automatic role assignment for new registrations

```php
// After user creation
$user = User::create([...]);

// NEW: Automatically assign field-officer role
$user->assignRole('field-officer');

return $user;
```

**Why**: New users registering via the standard `/register` page now automatically get the `field-officer` role, immediately giving them access to the citizen/field officer portal at `/portal`.

### 3. HomeRedirectController Enhancement

**File**: `app/Http/Controllers/HomeRedirectController.php`

The controller now clearly documents the role-based routing logic with comments for clarity.

---

## How It Works

### Login & Redirect Flow

```
User logs in
    ↓
Fortify authenticates user
    ↓
User redirected to /dashboard
    ↓
HomeRedirectController invoked
    ↓
Checks user's roles:
    ├─ Has admin role? → Redirect to /admin/dashboard
    │  (super-admin, admin, approvals-officer, registry-officer)
    │
    └─ No admin role? → Redirect to /portal/dashboard
       (field-officer, citizen, etc.)
```

### Registration & Role Assignment Flow

```
User registers via /register page
    ↓
Fortify validates input
    ↓
CreateNewUser action creates user
    ↓
field-officer role automatically assigned
    ↓
User redirected to /dashboard
    ↓
HomeRedirectController sees field-officer role
    ↓
Redirects to /portal/dashboard
    ↓
User sees portal interface
```

---

## User Experience

### Scenario 1: Super Admin User

1. Clicks login
2. Enters credentials
3. → Automatically redirected to `/admin/dashboard`
4. Sees admin sidebar with all sections

### Scenario 2: Approvals Officer User

1. Clicks login
2. Enters credentials
3. → Automatically redirected to `/admin/dashboard`
4. Sees admin sidebar with staff functions

### Scenario 3: Field Officer / Citizen User

1. Clicks login
2. Enters credentials
3. → Automatically redirected to `/portal/dashboard`
4. Sees portal with street registration forms

### Scenario 4: New User Registration

1. Clicks register
2. Fills in name, email, password
3. → Registration completes
4. **Automatically assigned `field-officer` role**
5. → Redirected to `/portal/dashboard`
6. Immediately enabled to:
    - Register streets
    - Register addresses
    - Submit field reports
    - Access all citizen/field officer features

---

## Role-Based Access Matrix

| User Role         | After Login Redirects To | Can Access                                                                               |
| ----------------- | ------------------------ | ---------------------------------------------------------------------------------------- |
| super-admin       | `/admin/dashboard`       | Entire admin panel, all sections                                                         |
| admin             | `/admin/dashboard`       | All admin functions except user/role/permission management                               |
| approvals-officer | `/admin/dashboard`       | Approvals, street applications, field reports                                            |
| registry-officer  | `/admin/dashboard`       | Address/street registry                                                                  |
| field-officer     | `/portal/dashboard`      | Street/address registration, field reports (automatically assigned to new registrations) |
| citizen           | `/portal/dashboard`      | Street/address registration                                                              |

---

## Implementation Details

### What Happens During Registration

1. **Validation**: Standard form validation (name, email, password)
2. **Creation**: User record created in database
3. **Role Assignment**: `field-officer` role automatically assigned via `$user->assignRole('field-officer')`
4. **Redirect**: User redirected to `/dashboard` → Home Controller → `/portal/dashboard`
5. **Authentication**: User is already authenticated and can start using portal

### Permissions Granted to field-officer

Via the seeders, field-officer has these permissions:

- `view addresses` - View address records
- `view streets` - View street records
- `submit street applications` - Submit new applications
- `submit address registrations` - Register addresses
- `perform address indexing` - Request Google Maps indexing
- `request street revalidation` - Request revalidation

---

## Configuration Files Modified

### 1. config/fortify.php

```php
// Line ~71
'home' => '/dashboard',  // ← Changed from '/admin'
```

### 2. app/Actions/Fortify/CreateNewUser.php

```php
// After line 29 (in create method)
$user->assignRole('field-officer');  // ← Added
```

### 3. app/Http/Controllers/HomeRedirectController.php

```php
// Updated comments and logic for clarity
```

---

## How to Test

### Test 1: Login Redirect for Admin User

1. **Setup**: Ensure you have a super-admin or admin user (exists in seeder)
2. **Login**: Go to /login and login with admin credentials:
    - Email: `superadmin@ndsms.gov.ng`
    - Password: `Admin@1234`
3. **Expected**: Redirects to `/admin/dashboard`
4. **Verify**: See admin sidebar

### Test 2: Login Redirect for Field Officer User

1. **Setup**: Create a test user and manually assign field-officer role:
    ```bash
    php artisan tinker
    $user = User::find(ID);
    $user->assignRole('field-officer');
    ```
2. **Login**: Go to /login and login with this user's credentials
3. **Expected**: Redirects to `/portal/dashboard`
4. **Verify**: See portal interface with registration forms

### Test 3: New User Registration

1. **Navigate**: Go to `/register` page
2. **Fill Form**: Enter:
    - Name: Test User
    - Email: testuser@example.com
    - Password: TestPass123!
3. **Submit**: Click register
4. **Expected**:
    - Redirects to `/portal/dashboard` (auto-redirected)
    - User is already authenticated
5. **Verify**:
    - See portal interface
    - Can immediately start using features
6. **Backend Check**:
    ```bash
    php artisan tinker
    $user = User::where('email', 'testuser@example.com')->first();
    $user->roles()->pluck('name');  // Should output ['field-officer']
    ```

### Test 4: Browser Session Test

1. **Clear Session**: (Optional) Clear browser cookies/cache
2. **Go to Dashboard**: Navigate directly to `/dashboard`
    - **For admin user**: Should redirect to `/admin/dashboard`
    - **For field officer user**: Should redirect to `/portal/dashboard`
3. **Go to Admin**: Navigate directly to `/admin`
    - **For admin user**: Should work fine
    - **For field officer user**: Should show 403 Forbidden

---

## Troubleshooting

### Issue: Login redirects to `/admin` instead of based on role

**Solution 1**: Check Fortify config

```bash
grep -n "'home'" config/fortify.php
# Should show: 'home' => '/dashboard',
```

**Solution 2**: Clear config cache

```bash
php artisan config:clear
php artisan cache:clear
```

**Solution 3**: Verify routes

```bash
php artisan route:list | grep dashboard
# Should show /dashboard route pointing to HomeRedirectController
```

### Issue: New users not getting field-officer role

**Solution 1**: Check CreateNewUser file

```bash
grep -A 5 "assignRole" app/Actions/Fortify/CreateNewUser.php
```

**Solution 2**: Verify field-officer role exists

```bash
php artisan tinker
Role::where('name', 'field-officer')->first();
# Should return the role object
```

**Solution 3**: Check for role assignment errors in logs

```bash
tail -f storage/logs/laravel.log
```

### Issue: Users redirecting to wrong dashboard

**Solution 1**: Check user's roles

```bash
php artisan tinker
$user = User::find(ID);
$user->roles()->pluck('name');  // Check assigned roles
```

**Solution 2**: Verify role hierarchy

```bash
php artisan tinker
Role::all()->pluck('name');  # Should include: super-admin, admin, approvals-officer, registry-officer, field-officer, citizen
```

**Solution 3**: Test HomeRedirectController directly

```bash
php artisan tinker
$user = User::find(ID);
auth()->login($user);
app('HomeRedirectController')();  # Should return proper redirect
```

---

## Security Considerations

### 1. Role Assignment Timing

- Role is assigned immediately after user creation
- User is then authenticated automatically by Fortify
- Zero-gap access - user is ready to use system immediately

### 2. Route Protection

- Admin routes have middleware:
    ```php
    Route::middleware(['auth', 'verified'])->prefix('admin')->group(...)
    ```
- Portal routes also protected:
    ```php
    Route::middleware(['auth', 'verified'])->prefix('portal')->group(...)
    ```

### 3. Permission Verification

- HomeRedirectController uses `hasAnyRole()` from Spatie package
- Checks against known admin roles
- Defaults to portal for any other role (safe fallback)

---

## Database View

### Users Table (No changes needed)

```
id | name | email | password | email_verified_at | created_at | updated_at
```

### Model Has Roles Table (auto-managed by Spatie)

```
model_id | role_id | model_type
```

When a user registers, a row is added:

```
NEW_USER_ID | FIELD_OFFICER_ROLE_ID | 'App\\Models\\User'
```

---

## Code Flow Diagram

```
┌─────────────────────────────────────────────────┐
│ User navigates to /login                        │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ Fortify authentication middleware processes     │
│ credentials from login form                     │
└───────────────┬─────────────────────────────────┘
                │
                ├─ Valid? Continue
                └─ Invalid? Show login error
                │
                ▼
┌─────────────────────────────────────────────────┐
│ Fortify redirects to config('fortify.home')     │
│ Which is now /dashboard (not /admin)            │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ /dashboard route invokes                        │
│ HomeRedirectController::__invoke()              │
└───────────────┬─────────────────────────────────┘
                │
                ▼
        ┌───────────────────────┐
        │ Check user roles      │
        └───────┬───────────────┘
                │
        ┌───────┴────────┐
        │                │
        ▼                ▼
    Has admin role?  No admin role?
        │                │
        ▼                ▼
    /admin/dash      /portal/dash


┌─────────────────────────────────────────────────┐
│ User navigates to /register                     │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ Fortify register view shown                     │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ User fills form & submits                       │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ CreateNewUser::create() action executed         │
└───────────────┬─────────────────────────────────┘
                │
        ┌───────┴───────────────┐
        │                       │
        ▼                       ▼
    Create user          Assign field-officer role
    in database           to user
        │                       │
        └───────┬───────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ Fortify auto-authenticates new user             │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ Redirects to /dashboard (via Fortify config)    │
└───────────────┬─────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────┐
│ HomeRedirectController invoked                  │
│ Sees field-officer role                         │
│ Redirects to /portal/dashboard                  │
└───────────────────────────────────────────────────┘
```

---

## Best Practices

### 1. For Admin User Creation

When creating admin users manually, use seeders or admin panel:

```bash
php artisan tinker
$user = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('pass')]);
$user->assignRole('admin');  # Explicitly assign admin role
```

### 2. For Field Officer/Citizen Users

Registration form handles this automatically:

- Just register normally
- No manual role assignment needed
- They'll always get field-officer role

### 3. For Testing

```bash
# Create test users with different roles
php artisan tinker
$admin = User::create(['name' => 'Admin Test', 'email' => 'admin-test@test.com', 'password' => bcrypt('Test123!')]);
$admin->assignRole('admin');

$officer = User::create(['name' => 'Officer Test', 'email' => 'officer-test@test.com', 'password' => bcrypt('Test123!')]);
$officer->assignRole('field-officer');
```

---

## Summary

✅ **Auto-Redirect**: Users are automatically redirected to appropriate dashboard after login
✅ **Auto-Role Assignment**: New registrations auto-assigned field-officer role
✅ **Admin Routes**: Accessible to super-admin, admin, approvals-officer, registry-officer
✅ **Portal Routes**: Accessible to field-officer, citizen roles
✅ **Seamless UX**: Users don't need to manually select roles or navigate
✅ **Secure**: Role-based access control maintained throughout system

**After these changes:**

- 🎯 Super-admin user logs in → goes to `/admin/dashboard`
- 🎯 Field officer logs in → goes to `/portal/dashboard`
- 🎯 New user registers → automatically gets field-officer role and goes to `/portal/dashboard`
- 🎯 No manual intervention needed for role assignment
