# Super Admin Registration Implementation

## Overview

A comprehensive super admin registration system has been implemented with extended user fields for both regular users and super administrator accounts.

## What Was Implemented

### 1. **Extended User Model**

**File:** `app/Models/User.php`

Added fillable fields:

- `phone` - Phone number
- `address` - Street address
- `town` - Town/City
- `organization` - Organization name
- `position` - Job position/title
- `department` - Department name
- `state` - State/Province
- `country` - Country
- `avatar` - Profile avatar URL
- `is_super_admin` - Boolean flag for super admin users

### 2. **Database Migration**

**File:** `database/migrations/2024_01_01_000001_add_extended_fields_to_users_table.php`

Adds all extended fields to the `users` table with:

- Nullable fields for optional data
- Indexes on `email` and `phone` for faster lookups
- `is_super_admin` boolean flag defaulting to `false`

**To run the migration:**

```bash
php artisan migrate
```

### 3. **Extended Validation Rules**

**File:** `app/Concerns/ProfileValidationRules.php`

New validation methods:

- `extendedProfileRules()` - For regular users (phone/town optional)
- `superAdminRegistrationRules()` - For super admins (phone/town/org/position required)

### 4. **Super Admin Registration Page**

#### Livewire Component

**File:** `app/Livewire/Auth/RegisterSuperAdmin.php`

Features:

- Livewire-based reactive form component
- Real-time validation
- Automatically assigns `super-admin` role to new users
- Redirects to dashboard after successful registration

#### View

**File:** `resources/views/livewire/auth/register-super-admin.blade.php`

Includes form fields for:

- Full Name (required)
- Email Address (required)
- Phone Number (required)
- Organization (required)
- Position/Title (required)
- Address (optional)
- Town/City (required)
- State/Province (optional)
- Country (optional)
- Department (optional)
- Password (required)
- Confirm Password (required)

**Access URL:** `http://your-app/auth/register-super-admin`

### 5. **Super Admin Creation Action**

**File:** `app/Actions/Fortify/CreateSuperAdminUser.php`

Handles:

- Validation of all required super admin fields
- User creation with all extended fields
- Automatic assignment of `super-admin` role
- Sets `is_super_admin` flag to `true`

### 6. **Updated Regular Registration**

**File:** `resources/views/livewire/auth/register.blade.php`

Enhanced regular registration with optional fields:

- Phone number (optional)
- Town/City (optional)

**File:** `app/Actions/Fortify/CreateNewUser.php`

Updated to handle optional extended fields and assign them to regular users.

### 7. **Routes**

**File:** `routes/web.php`

New route added:

```php
Route::get('/auth/register-super-admin', RegisterSuperAdmin::class)->name('register-super-admin');
```

## Usage

### Register a Super Admin

1. Navigate to: `http://your-app/auth/register-super-admin`
2. Fill in all required fields (marked with asterisks):
    - Full Name
    - Email Address
    - Phone Number
    - Organization
    - Position/Title
    - Town/City
    - Password
3. Click "Create Super Admin Account"
4. User is automatically logged in and redirected to dashboard
5. User is assigned the `super-admin` role

### Register a Regular User (Unchanged)

1. Navigate to: `http://your-app/register` (Fortify default)
2. Fill in basic fields (name, email, password)
3. Optionally add phone number and town
4. Regular user is assigned the `field-officer` role automatically

## Database Fields Structure

```sql
-- New columns added to users table
phone VARCHAR(20) - NULL
address VARCHAR(255) - NULL
town VARCHAR(100) - NULL
organization VARCHAR(255) - NULL
position VARCHAR(100) - NULL
department VARCHAR(100) - NULL
state VARCHAR(100) - NULL
country VARCHAR(100) - NULL
avatar VARCHAR(255) - NULL
is_super_admin BOOLEAN - DEFAULT FALSE
```

## Validation Rules

### Regular User Registration

- Phone: optional, must be valid phone format (regex: `/^[0-9\-\+\(\)\s]+$/`)
- Town: optional
- Other fields: standard email validation

### Super Admin Registration

- Phone: required, must be valid phone format
- Organization: required
- Position: required
- Town: required
- Other fields: standard validation

## Notes

1. **Migration**: You must run `php artisan migrate` to add the new columns to the users table
2. **Roles**: Ensure the `super-admin` role exists in your roles table before registering super admin users
3. **Security**: Consider adding middleware/authentication to restrict super admin registration if needed
4. **Authorization**: Routes are currently public, add auth middleware if you want to restrict access
5. **Optional Fields**: Address, State, Country, Department, and Avatar are optional for all users

## Future Enhancements

Consider implementing:

- Avatar upload functionality
- Two-factor authentication for super admin accounts
- Email verification before super admin account activation
- Admin approval workflow for super admin registration
- Activity logging for super admin creation
- Super admin registration access token/code requirement

## Files Modified

1. `app/Models/User.php` - Updated fillable fields
2. `app/Concerns/ProfileValidationRules.php` - Added validation methods
3. `app/Actions/Fortify/CreateNewUser.php` - Updated to handle extended fields
4. `resources/views/livewire/auth/register.blade.php` - Added optional fields
5. `routes/web.php` - Added import and route

## Files Created

1. `database/migrations/2024_01_01_000001_add_extended_fields_to_users_table.php`
2. `app/Livewire/Auth/RegisterSuperAdmin.php`
3. `app/Actions/Fortify/CreateSuperAdminUser.php`
4. `resources/views/livewire/auth/register-super-admin.blade.php`
