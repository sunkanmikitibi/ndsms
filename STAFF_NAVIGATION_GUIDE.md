# Staff Navigation and Access Control

## Overview

This document describes the staff navigation section added to the NDSMS admin panel, enabling staff members with appropriate roles and permissions to access street applications and field reports management interfaces.

## New Staff Navigations Added

### 1. Street Applications Management

- **Route**: `/admin/street-applications`
- **Component**: `App\Livewire\Admin\StreetApplications\Index`
- **View**: `resources/views/livewire/admin/street-applications/index.blade.php`
- **Permission Required**: `view approvals` or `manage approvals`
- **Features**:
    - List all street applications with search and filter
    - View application details (street name, ward, applicant info, description)
    - Update application status (pending, approved, rejected)
    - Add admin notes to applications
    - Delete applications
    - Pagination with 15 items per page
    - Badge showing pending count

### 2. Field Reports Management

- **Route**: `/admin/field-reports`
- **Component**: `App\Livewire\Admin\FieldReports\Index`
- **View**: `resources/views/livewire/admin/field-reports/index.blade.php`
- **Permission Required**: `view approvals` or `manage approvals`
- **Features**:
    - List all field reports with search and filter
    - View report details (title, location, reporter info, description)
    - Update report status (pending, reviewed, closed)
    - Add admin notes to reports
    - Delete reports
    - Pagination with 15 items per page

## Roles and Permissions

### New Permissions Added

- `manage street applications` - Manage street application records
- `manage field reports` - Manage field report records

### Roles with Access

#### Approvals Officer

The `approvals-officer` role now includes permissions to:

- View and manage street applications
- View and manage field reports
- View approvals and other application workflows

#### Admin

The `admin` role inherits all operational permissions and can access these sections.

#### Super Admin

The `super-admin` role has full system access and can manage all staff functions.

## Database Changes

### New Columns Added to FieldReport Table

A migration has been created to add the following columns to the `field_reports` table:

- `title` (string, nullable) - Report title
- `description` (string, nullable) - Report description
- `location` (string, nullable) - Report location

**Migration File**: `database/migrations/2026_04_04_120000_add_fields_to_field_reports_table.php`

### Updated Models

- `App\Models\FieldReport` - Updated fillable array to include new columns
- `App\Models\StreetApplication` - Already has necessary columns

## Navigation Structure

### Workflow Section

The existing "Workflow" section in the admin sidebar now includes:

1. **Approvals** - View main approvals/workflow
2. **Street Applications** - Manage street registration applications
3. **Field Reports** - Manage field officer reports

## Usage

### For Staff with Approvals Officer Role

1. Navigate to Admin Dashboard
2. In the sidebar under "Workflow" section, click:
    - "Street Applications" to manage street registration apps
    - "Field Reports" to manage field reports

### Typical Workflow

1. **View Applications/Reports**: See list of all pending/approved/rejected items
2. **Search & Filter**: Use search and status filters to find specific items
3. **Review**: Click "View" to open details modal
4. **Update Status**: Change status and add admin notes
5. **Save**: Click "Update Status" to save changes
6. **Delete**: Remove applications/reports if needed

## Feature Details

### Street Applications Interface

- **Search Fields**: Street name, ward, applicant name
- **Status Options**: Pending, Approved, Rejected
- **Display Fields**:
    - Street Name (with description preview)
    - Applicant Name (with phone number)
    - Ward
    - Type (street type)
    - Status (color-coded badge)
    - Submission Date
    - Actions (View, Delete)

### Field Reports Interface

- **Search Fields**: Report title, description, location
- **Status Options**: Pending, Reviewed, Closed
- **Display Fields**:
    - Report Title (with description preview)
    - Location
    - Reporter Name (with phone number)
    - Status (color-coded badge)
    - Submission Date
    - Actions (View, Delete)

## Technical Implementation

### Component Structure

Both components follow the same pattern:

- `WithPagination` trait for efficient pagination
- Livewire data binding with reactive filters
- Modal-based detail views and confirmations
- Form validation before updates
- Error handling with user notifications

### Security

- Route middleware requires `canany:view approvals,manage approvals` permission
- Component mount checks user authorization
- All updates are protected by permission checks
- Admin notes functionality for audit trail

### Event Dispatching

Components dispatch 'notify' events for user feedback:

- Success notifications on update/delete
- Error notifications on exceptions
- Toast-style notifications in the UI

## Routes File

Routes have been added to `routes/web.php`:

```php
// Staff Functions (Street Applications & Field Reports)
Route::middleware('canany:view approvals,manage approvals')->group(function () {
    Route::get('/street-applications', StreetApplicationsIndex::class)->name('street-applications.index');
    Route::get('/field-reports', FieldReportsIndex::class)->name('field-reports.index');
});
```

## Seeder Updates

The `RolesAndPermissionsSeeder` has been updated to:

1. Create new permissions for managing applications and reports
2. Assign these permissions to the Approvals Officer role
3. Maintain backward compatibility with existing role definitions

## Next Steps

To use these staff navigation items:

1. Run database migrations: `php artisan migrate`
2. Run seeders (if permissions need updating): `php artisan db:seed --class=RolesAndPermissionsSeeder`
3. Assign users to the `approvals-officer` role to grant access
4. Users will see navigation items in sidebar based on their permissions

## Customization

### Adding More Staff Roles

To create additional staff roles with similar access:

1. Create new role in seeder
2. Assign needed permissions
3. Add conditional navigation items in `admin.blade.php` template

### Extending Functionality

- Add more fields to Street Applications (e.g., contact info, coordinates)
- Implement approval workflow automation
- Add reporting/analytics for staff metrics
- Create role-based filtering for regional staff

## Support

For issues or questions about staff navigation:

- Check permissions are correctly seeded
- Verify user has appropriate role assigned
- Clear application cache: `php artisan cache:clear`
- Check Livewire components are properly loaded
