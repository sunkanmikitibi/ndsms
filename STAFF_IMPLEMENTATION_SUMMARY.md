# Staff Navigation Implementation - Quick Reference

## What Was Added

### New Livewire Components

1. **StreetApplications Index** (`app/Livewire/Admin/StreetApplications/Index.php`)
    - CRUD operations for street applications
    - Search, filter, and pagination
    - Status update with admin notes

2. **FieldReports Index** (`app/Livewire/Admin/FieldReports/Index.php`)
    - CRUD operations for field reports
    - Search, filter, and pagination
    - Status update with admin notes

### New Views

1. `resources/views/livewire/admin/street-applications/index.blade.php`
2. `resources/views/livewire/admin/field-reports/index.blade.php`

### New Routes

- `/admin/street-applications` → Street Applications management
- `/admin/field-reports` → Field Reports management

### Updated Files

1. **Navigation** - `resources/views/components/layouts/admin.blade.php`
    - Added Street Applications navigation item
    - Added Field Reports navigation item
    - Both in "Workflow" section under appropriate permission checks

2. **Routes** - `routes/web.php`
    - Added imports for new components
    - Added route group for staff functions

3. **Database Migrations** - `database/migrations/2026_04_04_120000_add_fields_to_field_reports_table.php`
    - Added `title`, `description`, `location` columns to field_reports table

4. **Models** - `app/Models/FieldReport.php`
    - Updated fillable array with new columns

5. **Seeder** - `database/seeders/RolesAndPermissionsSeeder.php`
    - Added `manage street applications` permission
    - Added `manage field reports` permission
    - Updated approvals-officer role with new permissions

## Implementation Steps

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will add the new columns to the field_reports table.

### Step 2: Seed Database

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This will create the new permissions and update role assignments.

### Step 3: Verify Installation

1. Log in to admin panel as a user with approvals-officer role
2. In sidebar under "Workflow" section, you should see:
    - Approvals (existing)
    - Street Applications (new)
    - Field Reports (new)

### Step 4: Grant Access

Ensure users who need access have one of these roles:

- `approvals-officer` - Recommended for staff
- `admin` - Full operational access
- `super-admin` - Full system access

## Permission Structure

### Who Can See & Access These Functions?

**In Sidebar Navigation:**

- Users with `view approvals` OR `manage approvals` permission
- Currently: approvals-officer, admin, super-admin roles

**Via Direct URL:**

- Requires `view approvals` permission
- Will be blocked if user lacks permission

## Component Features

### Street Applications Component

- **Columns**: Street Name, Applicant, Ward, Type, Status, Submitted, Actions
- **Search**: Street name, ward, applicant name
- **Filter**: By status (all, pending, approved, rejected)
- **Actions**: View details, Update status, Delete
- **Modal Features**: View full details, change status, add admin notes

### Field Reports Component

- **Columns**: Report Title, Location, Reporter, Status, Submitted, Actions
- **Search**: Report title, description, location
- **Filter**: By status (all, pending, reviewed, closed)
- **Actions**: View details, Update status, Delete
- **Modal Features**: View full details, change status, add admin notes

## Data Models

### StreetApplication

- `street_name` - Name of the street
- `ward` - Ward location
- `type` - Application type
- `description` - Detailed description
- `status` - Current status (pending, approved, rejected)
- `admin_note` - Notes from admin reviewer
- `reviewed_at` - Timestamp of review
- `coordinates` - Street start/end coordinates (optional)

### FieldReport

- `title` - Report title (new)
- `description` - Report description (new)
- `location` - Report location (new)
- `type` - Report type
- `data` - JSON data storage
- `status` - Current status (pending, reviewed, closed)
- `admin_note` - Notes from admin reviewer
- `reviewed_at` - Timestamp of review

## Sidebar Structure After Implementation

```
Main
  └─ Dashboard

Address Registry
  ├─ Addresses
  └─ Streets

Workflow  ← New items in this section
  ├─ Approvals
  ├─ Street Applications (NEW - requires view approvals permission)
  └─ Field Reports (NEW - requires view approvals permission)

Financial
  ├─ Payments
  └─ Fee Schedules

Analytics
  ├─ Reports
  └─ Ward Map

Administration (super-admin only)
  ├─ Users
  ├─ Roles
  ├─ Permissions
  └─ Settings
```

## Testing Checklist

- [ ] Migrations run successfully: `php artisan migrate`
- [ ] Seeder runs without errors: `php artisan db:seed --class=RolesAndPermissionsSeeder`
- [ ] Log in as approvals-officer user
- [ ] Verify navigation items appear in sidebar
- [ ] Click Street Applications - should see list view
- [ ] Click Field Reports - should see list view
- [ ] Test search functionality
- [ ] Test filter by status
- [ ] Test viewing details (clicking View button)
- [ ] Test updating status of an item
- [ ] Test adding admin notes
- [ ] Test pagination
- [ ] Test delete confirmation
- [ ] Log in as regular user without permissions - items should be hidden

## Common Issues & Solutions

### Issue: Staff functions not visible in sidebar

**Solution**:

- Check user has `view approvals` permission
- Run seeder again: `php artisan db:seed --class=RolesAndPermissionsSeeder`
- Clear cache: `php artisan cache:clear`

### Issue: Routes return 403 Forbidden

**Solution**:

- Verify user has approvals-officer role
- Check permissions are synced in seeder
- Confirm migrations have run

### Issue: Field Reports showing empty values

**Solution**:

- Run migration to add new columns
- Check that reports have data in the fields
- Verify model fillable array includes new columns

### Issue: Livewire components not rendering

**Solution**:

- Run: `php artisan livewire:publish`
- Clear cache: `php artisan cache:clear config:clear`
- Check console for JavaScript errors

## Next Steps / Enhancements

1. **Bulk Actions**: Add batch approve/reject functionality
2. **Workflow Automation**: Auto-transition statuses based on conditions
3. **Notifications**: Email staff when applications need review
4. **Advanced Filters**: Filter by date range, user, etc.
5. **Export**: Export applications/reports to CSV/Excel
6. **History**: Track all status changes and notes
7. **Assign To**: Assign review tasks to specific staff
8. **SLA Tracking**: Monitor time-to-review metrics
9. **Comments**: Multi-user comment thread on each item
10. **Document Attachments**: Support For photos/documents in reports

## File Locations Reference

| Component                     | Location                                                                      |
| ----------------------------- | ----------------------------------------------------------------------------- |
| Street Applications Component | `app/Livewire/Admin/StreetApplications/Index.php`                             |
| Field Reports Component       | `app/Livewire/Admin/FieldReports/Index.php`                                   |
| Street Applications View      | `resources/views/livewire/admin/street-applications/index.blade.php`          |
| Field Reports View            | `resources/views/livewire/admin/field-reports/index.blade.php`                |
| Admin Layout                  | `resources/views/components/layouts/admin.blade.php`                          |
| Routes                        | `routes/web.php`                                                              |
| Seeder                        | `database/seeders/RolesAndPermissionsSeeder.php`                              |
| Migration                     | `database/migrations/2026_04_04_120000_add_fields_to_field_reports_table.php` |
| Models                        | `app/Models/StreetApplication.php`, `app/Models/FieldReport.php`              |

## Summary

✅ Created two new Livewire admin components for managing street applications and field reports
✅ Added navigation items in admin sidebar under Workflow section
✅ Configured proper permission-based access control
✅ Updated database with migration to add fields to field_reports table
✅ Updated roles and permissions seeder with new permissions
✅ Comprehensive documentation provided

Staff members with the **approvals-officer** role can now access street applications and field reports management directly from the admin sidebar sidebar.
