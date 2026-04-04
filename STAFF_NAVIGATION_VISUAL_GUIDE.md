# Staff Navigation - Visual Implementation Guide

## 🎯 What You've Gained

A complete staff management system for approvals and field operations with:

- ✅ Street Applications management interface
- ✅ Field Reports review system
- ✅ Permission-based access control
- ✅ Pagination & search capabilities
- ✅ Status workflow management
- ✅ Admin notes for audit trail

---

## 📋 Pre-Implementation Checklist

Before running commands, ensure you have:

```
✓ Database connection working
✓ Laravel environment configured
✓ Spatie Permission installed (already in project)
✓ Livewire installed and configured
```

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Run the Migration

```bash
php artisan migrate
```

**What it does**: Adds `title`, `description`, `location` columns to field_reports table

**Output**:

```
Migrating: 2026_04_04_120000_add_fields_to_field_reports_table
Migrated:  2026_04_04_120000_add_fields_to_field_reports_table (xxx ms)
```

### Step 2: Seed the Database

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

**What it does**:

- Creates new permissions for managing applications and reports
- Updates the approvals-officer role with new permissions
- Maintains existing role definitions

**Expected output**:

```
Seeding: Database\Seeders\RolesAndPermissionsSeeder
✓ Roles, permissions, and super-admin user seeded successfully.
```

### Step 3: Clear Cache (Recommended)

```bash
php artisan cache:clear
php artisan config:clear
```

---

## 🔐 Permissions Breakdown

### New Permissions Created

| Permission                   | Description                       |
| ---------------------------- | --------------------------------- |
| `manage street applications` | Manage street application records |
| `manage field reports`       | Manage field report records       |

### Who Gets These Permissions?

| Role              | Has Access? | Notes                         |
| ----------------- | ----------- | ----------------------------- |
| super-admin       | ✅ Yes      | All permissions               |
| admin             | ✅ Yes      | All operational permissions   |
| approvals-officer | ✅ Yes      | Newly added via seeder update |
| registry-officer  | ❌ No       | Can only view, not manage     |
| field-officer     | ❌ No       | Can only submit, not manage   |
| citizen           | ❌ No       | Can only submit applications  |

---

## 📍 Navigation Location

After implementation, the admin sidebar will look like:

```
┌─────────────────────────────┐
│  NDSMS Admin Portal         │
├─────────────────────────────┤
│ ■ Dashboard                 │
├─────────────────────────────┤
│ 📍 ADDRESS REGISTRY         │
│   □ Addresses              │
│   □ Streets                │
├─────────────────────────────┤
│ 📋 WORKFLOW                 │
│   □ Approvals              │
│   ✨ □ Street Applications │ ← NEW
│   ✨ □ Field Reports       │ ← NEW
├─────────────────────────────┤
│ 💰 FINANCIAL                │
│   □ Payments               │
│   □ Fee Schedules          │
├─────────────────────────────┤
│ 📊 ANALYTICS                │
│   □ Reports                │
│   □ Ward Map               │
├─────────────────────────────┤
│ ⚙️ ADMINISTRATION (Super)   │
│   □ Users                  │
│   □ Roles                  │
│   □ Permissions            │
│   □ Settings               │
└─────────────────────────────┘
```

---

## 👥 How to Assign Roles to Users

### Option 1: Using Laravel Tinker

```bash
php artisan tinker
```

Then run:

```php
$user = User::find(2); // Replace 2 with user ID
$user->assignRole('approvals-officer');
```

### Option 2: Using Roles Admin Interface

1. Log in as super-admin
2. Navigate to **Administration → Users**
3. Edit the user
4. Select role: **approvals-officer**
5. Save changes

### Option 3: Using Database

```sql
-- Insert role assignment
INSERT INTO model_has_roles (model_id, role_id, model_type)
VALUES (USER_ID, ROLE_ID, 'App\\Models\\User');

-- ROLE_ID for approvals-officer: Usually 3 or 4 (check roles table)
```

---

## 🧪 Testing the Implementation

### Test 1: Verify Permissions Exist

```bash
php artisan tinker
```

```php
$permissions = \Spatie\Permission\Models\Permission::all();
$permissions->pluck('name');
```

**Should include**: `manage street applications`, `manage field reports`

### Test 2: Check Role Assignments

```php
$role = Role::where('name', 'approvals-officer')->first();
$role->permissions->pluck('name');
```

**Should include new permissions in the list**

### Test 3: Test User Access

```php
$user = User::find(2);
$user->assignRole('approvals-officer');
$user->can('view approvals'); // Should return true
```

### Test 4: Manual UI Testing

1. Create a test user with approvals-officer role
2. Log in as that user
3. Navigate to admin dashboard
4. Check sidebar for new navigation items
5. Click on "Street Applications" - should see list
6. Click on "Field Reports" - should see list
7. Try to view/edit/delete items

---

## 📊 Data Flow for Street Applications

```
┌─────────────────────┐
│  Street Application │
│  (Portal/Citizen)   │
└──────────┬──────────┘
           │ Submits
           ▼
┌─────────────────────────────────┐
│ StreetApplication Table         │
│ - street_name                   │
│ - ward                          │
│ - status: pending               │
│ - user_id                       │
└──────────┬──────────────────────┘
           │ Staff reviews via
           │ Admin Interface
           ▼
┌──────────────────────────────┐
│ Approvals Officer Views:      │
│ - Street Applications list    │
│ - Search by name/ward/applicant
│ - Filter by status           │
│ - Update status to approved  │
│ - Add admin notes            │
└──────────────────────────────┘
           │ Status changed
           ▼
┌────────────────────────┐
│ citizen/user sees      │
│ application approved!  │
│ Can now proceed       │
│ with payment          │
└────────────────────────┘
```

---

## 📝 Street Applications Management

### List View Features

```
Search Box: ──────────────────────
Status Filter: [All ▼] [Pending] [Approved] [Rejected]

┌──────────────────────────────────────────────────────────┐
│ Street Applications                                      │
├──────────────────────────────────────────────────────────┤
│ Street Name  │ Applicant    │ Ward   │ Status │ Actions │
├──────────────────────────────────────────────────────────┤
│ Aba Road     │ John Doe     │ Ward 1 │ ⏳     │ View Del│
├──────────────────────────────────────────────────────────┤
│ Church St.   │ Jane Smith   │ Ward 2 │ ✓      │ View Del│
├──────────────────────────────────────────────────────────┤
│ Market Ln.   │ Bob Johnson  │ Ward 3 │ ✗      │ View Del│
└──────────────────────────────────────────────────────────┘

Showing 1-15 of 47 applications [< 1 2 3 >]
```

### Detail Modal

```
┌─────────────────────────────────────┐
│ Review Application              [X] │
├─────────────────────────────────────┤
│ Street Name: Aba Road               │
│ Ward: Ward 1                        │
│ Applicant: John Doe (+234801234567) │
│ Description: A major commercial...  │
│                                     │
│ Status: [Pending ▼] (Pending,      │
│         Approved, Rejected)         │
│                                     │
│ Admin Note:                         │
│ ┌─────────────────────────────────┐ │
│ │Verify street location first...  │ │
│ └─────────────────────────────────┘ │
│                                     │
│ [Cancel] [Update Status] │
└─────────────────────────────────────┘
```

---

## 📋 Field Reports Management

### List View Features

```
Search Box: ──────────────────────
Status Filter: [All ▼] [Pending] [Reviewed] [Closed]

┌──────────────────────────────────────────────────────────┐
│ Field Reports                                            │
├──────────────────────────────────────────────────────────┤
│ Report Title │ Location  │ Reporter   │ Status │ Actions│
├──────────────────────────────────────────────────────────┤
│ Broken Drain │ Market St │ Officer A  │ ⏳     │ View Del
├──────────────────────────────────────────────────────────┤
│ Pothole      │ Main Rd   │ Officer B  │ ✓      │ View Del
├──────────────────────────────────────────────────────────┤
│ Flood Risk   │ Park Ave  │ Officer C  │ ⏳     │ View Del
└──────────────────────────────────────────────────────────┘

Showing 1-15 of 32 reports [< 1 2 3 >]
```

### Detail Modal

```
┌──────────────────────────────────────────┐
│ Review Field Report                  [X] │
├──────────────────────────────────────────┤
│ Title: Broken Drain                      │
│ Location: Market Street                  │
│ Reporter: Officer A (+234802233333)      │
│ Description: Water drain at market...    │
│                                          │
│ Status: [Pending ▼] (Pending,           │
│         Reviewed, Closed)                │
│                                          │
│ Admin Note:                              │
│ ┌──────────────────────────────────────┐ │
│ │Send maintenance team to assess...    │ │
│ └──────────────────────────────────────┘ │
│                                          │
│ [Cancel] [Update Status]                 │
└──────────────────────────────────────────┘
```

---

## 🔄 Workflow States

### Street Applications States

```
┌────────────┐
│  PENDING   │ ← Initial state when submitted
└─────┬──────┘
      │ Staff reviews
      ▼
   ┌──────────┐
   │APPROVED │ → User can proceed with payment
   └──────────┘
      ▲
      │ Or
      ▼
   ┌──────────┐
   │REJECTED  │ → User sees rejection reason in admin notes
   └──────────┘
```

### Field Reports States

```
┌────────────┐
│  PENDING   │ ← Waiting for review
└─────┬──────┘
      │ Staff reviews
      ▼
   ┌──────────┐
   │ REVIEWED │ → Reviewed and actioned
   └──────────┘
      ▲
      │ Or
      ▼
   ┌──────────┐
   │  CLOSED  │ → Case/issue handled
   └──────────┘
```

---

## 🛠️ Troubleshooting

### Problem: Navigation items not showing

**Solution 1**: Check permissions

```bash
php artisan tinker
$user = auth()->user();
$user->getPermissionsViaRoles()->pluck('name');
```

**Solution 2**: Verify role assignment

```bash
php artisan tinker
$user = User::find(ID);
$user->roles()->pluck('name');
```

**Solution 3**: Run seeder again

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan cache:clear
```

### Problem: Modal not opening

**Solution**: Check browser console for JavaScript errors

- Press F12 in browser
- Look for red errors in Console tab
- Check Livewire loading properly

### Problem: Form not submitting

**Solution**: Check form validation
**In Livewire component**: Validation rules are defined in `$rules` property

- Verify: status field is set
- Ensure: admin note is under 500 characters

---

## 📚 Files Reference Summary

| What                    | File Path                                                                     |
| ----------------------- | ----------------------------------------------------------------------------- |
| Street Appl. Component  | `app/Livewire/Admin/StreetApplications/Index.php`                             |
| Field Reports Component | `app/Livewire/Admin/FieldReports/Index.php`                                   |
| Street Appl. View       | `resources/views/livewire/admin/street-applications/index.blade.php`          |
| Field Reports View      | `resources/views/livewire/admin/field-reports/index.blade.php`                |
| Navigation Template     | `resources/views/components/layouts/admin.blade.php`                          |
| Routes Config           | `routes/web.php`                                                              |
| Permissions Seeder      | `database/seeders/RolesAndPermissionsSeeder.php`                              |
| Database Migration      | `database/migrations/2026_04_04_120000_add_fields_to_field_reports_table.php` |
| FieldReport Model       | `app/Models/FieldReport.php`                                                  |
| StreetApplication Model | `app/Models/StreetApplication.php`                                            |

---

## ✨ You're All Set!

After running the 3 setup steps, staff members with the **approvals-officer** role will see:

✅ **Street Applications** tab - to review and manage street applications  
✅ **Field Reports** tab - to review and manage field reports  
✅ Full search and filtering capabilities  
✅ Status management and admin notes functionality  
✅ Clean, user-friendly interface with modals

**Your NDSMS admin system now has complete staff navigation for approvals management!**

---

## 📞 Support Tips

1. **Clear everything if things feel stuck:**

    ```bash
    php artisan optimize:clear
    php artisan cache:clear
    php artisan config:clear
    ```

2. **Check database with:**

    ```bash
    php artisan tinker
    # List permissions
    Permission::all()
    # List roles
    Role::all()
    # Check a user
    User::find(2)->with('roles')->first()
    ```

3. **tail logs for errors:**
    ```bash
    tail -f storage/logs/laravel.log
    ```
