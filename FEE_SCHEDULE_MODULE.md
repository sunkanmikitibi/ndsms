# Fee Schedule Module

## Overview

Comprehensive fee management system for NDSMS enabling admins to set service fees and users to view pricing. Built with Livewire 4, Flux UI, and Laravel's elegant query builder patterns.

## Features

✅ **Complete CRUD Operations** for service fees
✅ **Multiple Payment Services** - Support for 6+ service types with individual pricing
✅ **Time-based Pricing** - Effective date ranges for fee validity
✅ **Dynamic Portal Display** - Users see only active fees in real-time
✅ **Admin Dashboard** - Centralized fee management and monitoring
✅ **Status Management** - Active/Inactive/Archived fee states
✅ **Bulk Pricing** - Calculate discounts for bulk operations
✅ **Expedited Processing** - Surcharge support for faster processing
✅ **Fee Service Layer** - Reusable pricing logic for integrations
✅ **Database Seeding** - Pre-configured default fees

## Architecture

### Database Schema

**fee_schedules table**:

```sql
CREATE TABLE fee_schedules (
    id BIGINT PRIMARY KEY,
    service_type VARCHAR(50) INDEXED,
    service_name VARCHAR(255),
    description TEXT NULL,
    base_amount DECIMAL(12,2),
    currency VARCHAR(3) DEFAULT 'NGN',
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    effective_from TIMESTAMP NULL,
    effective_to TIMESTAMP NULL,
    metadata JSON NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

### Models

**FeeSchedule Model** (`app/Models/FeeSchedule.php`):

- Handles all fee-related database operations
- 6 pre-defined service types (address_registration, street_registration, address_indexing, street_revalidation, qr_code_generation, certificate_generation)
- Query scopes for filtering and finding active fees
- Methods for formatted display and validation

### Service Layer

**FeeService** (`app/Services/FeeService.php`):

- Central service for all fee-related operations
- Methods: `getActiveFee()`, `getFeeAmount()`, `hasActiveFee()`, `calculateDiscount()`, `applyBulkDiscount()`, `getExpeditedCost()`
- Utility methods: Icon mapping, color mapping for UI display
- Validation and reporting: `validateAllServicesHaveFees()`, `getUpcomingFeeChanges()`, `getExpiredFees()`

### Livewire Components

#### Admin Fee Management

**File**: `app/Livewire/Admin/Fees/Index.php`

- **View**: `resources/views/livewire/admin/fees/index.blade.php`
- **Features**:
    - List all fees with pagination (15 per page)
    - Search by service name or type
    - Filter by status (Active, Inactive, Archived, All)
    - Create new fees with modal form
    - Edit existing fees with pre-populated data
    - Toggle fee status (Active ↔ Inactive)
    - Delete fees with confirmation
    - Formatted currency display with proper symbols

**Public Methods**:

- `openCreate()` - Open create fee modal
- `openEdit(int $id)` - Open edit fee modal
- `closeModal()` - Close modal and reset form
- `save()` - Create or update fee schedule
- `confirmDelete(int $id)` - Show delete confirmation
- `deleteFee()` - Delete and cascade cleanup
- `toggleStatus(int $id)` - Switch fee between active/inactive

**Public Properties**:

- `$search` - Service name/type search (live debounce 300ms)
- `$filterStatus` - Filter by status (active/inactive/archived/all)
- `$showModal` - Modal visibility toggle
- `$showDeleteModal` - Delete confirmation toggle
- `$editId` - Current editing fee ID
- `$serviceType` - Form: service type
- `$serviceName` - Form: service name
- `$description` - Form: fee description
- `$baseAmount` - Form: fee amount
- `$currency` - Form: currency (NGN, USD, EUR)
- `$status` - Form: fee status
- `$effectiveFrom` - Form: effective from date
- `$effectiveTo` - Form: effective to date
- `$deleteId` - Fee ID pending deletion

#### Portal Fee Display

**File**: `app/Livewire/Portal/FeeSchedule.php`

- **View**: `resources/views/livewire/portal/fee-schedule.blade.php`
- **Features**:
    - Display all active fees grouped by service type
    - Shows service descriptions and icons
    - Highlights inactive fees if applicable
    - Formatted currency display (₦, $, €)
    - Empty state if no fees are configured
    - Responsive grid layout

**Public Properties**:

- `$fees` - Active fee schedules (computed property)
- `$groupedFees` - Fees grouped by service type (computed property)

### Routes

**Admin Route** (requires `can:manage settings` or `role:super-admin`):

```
GET /admin/fee-schedules → Fees Index
```

**Portal Route** (public, no protection):

```
GET /portal/fee-schedule → Fee Schedule Display
```

## Built-in Service Types

```php
SERVICE_TYPES = [
    'address_registration' => 'Address Registration',
    'street_registration' => 'Street Registration',
    'address_indexing' => 'Address Indexing (Google Maps)',
    'street_revalidation' => 'Street Revalidation',
    'qr_code_generation' => 'QR Code Generation',
    'certificate_generation' => 'Certificate Generation',
]
```

## Default Fees (from Seeder)

| Service Type           | Service Name                     | Default Amount | Currency |
| ---------------------- | -------------------------------- | -------------- | -------- |
| address_registration   | Address Registration             | ₦2,000.00      | NGN      |
| street_registration    | Street Registration/Naming       | ₦5,000.00      | NGN      |
| address_indexing       | Address Indexing (Google Maps)   | ₦3,000.00      | NGN      |
| street_revalidation    | Street Revalidation              | ₦2,500.00      | NGN      |
| qr_code_generation     | QR Code Generation               | ₦1,500.00      | NGN      |
| certificate_generation | Address Verification Certificate | ₦1,000.00      | NGN      |

## Usage

### 1. Initialize Fees

Run the seeder to create default fees:

```bash
# Run all seeders (includes fees)
php artisan db:seed

# Or run just the fees seeder
php artisan db:seed --class=FeeScheduleSeeder
```

### 2. Manage Fees (Admin Panel)

**Access**: `/admin/fee-schedules` (Admin or Super-admin only)

**Create a New Fee**:

1. Click "New Fee Schedule" button
2. Select service type from dropdown
3. Enter service name and optional description
4. Set fee amount in NGN (or preferred currency)
5. Set status (Active/Inactive/Archived)
6. (Optional) Set effective date range
7. Click "Save Fee Schedule"

**Edit a Fee**:

1. Click "Edit" on the fee
2. Modify any field
3. Click "Save Fee Schedule"

**Activate/Deactivate a Fee**:

1. Click play/pause icon to toggle between Active and Inactive
2. Fee status changes immediately

**Delete a Fee**:

1. Click trash icon on the fee
2. Confirm deletion
3. Fee is permanently removed from system

### 3. Access Fees Programmatically

**Inject FeeService**:

```php
use App\Services\FeeService;

class MyController {
    public function __construct(private FeeService $feeService) {}

    public function show() {
        $fee = $this->feeService->getActiveFee('address_registration');
        $amount = $this->feeService->getFeeAmount('address_registration');
        $formatted = $this->feeService->getFormattedAmount('address_indexing');
    }
}
```

**Direct Model Usage**:

```php
use App\Models\FeeSchedule;

// Get active fee for service
$fee = FeeSchedule::getActiveFeeFor('address_registration');

// Get fee amount
$amount = FeeSchedule::getFeeAmount('street_registration');

// Check if service has active fee
$hasFee = FeeSchedule::hasActiveFee('address_indexing');

// Get all active fees
$allFees = FeeSchedule::active()->get();

// Get active fees by type
$revalidationFee = FeeSchedule::byServiceType('street_revalidation')->active()->first();
```

### 4. Price Calculations

**Get Formatted Amount**:

```php
$service = app(FeeService::class);
$displayPrice = $service->getFormattedAmount('address_registration');
// Output: ₦2,000.00
```

**Apply Bulk Discount** (e.g., 20 addresses = 5% off):

```php
$service = app(FeeService::class);
$bulkPrice = $service->applyBulkDiscount('address_registration', 20);
// Output: 1900 (5% discount)
```

**Apply Expedited Processing** (e.g., 50% surcharge):

```php
$service = app(FeeService::class);
$expeditedPrice = $service->getExpeditedCost('address_indexing', 50);
// Output: 4500 (50% surcharge on ₦3,000)
```

### 5. Integrate with Payment System

**In Payment Controller**:

```php
use App\Services\FeeService;

$fee = app(FeeService::class)->getActiveFee('address_indexing');

if (!$fee) {
    return response()->json(['error' => 'Service fee not configured'], 422);
}

$amount = $fee->base_amount;
// Use $amount for Paystack payment initialization
```

**In Livewire Component**:

```php
use App\Models\FeeSchedule;

class RegisterAddressIndexing extends Component {
    public function getFeeProperty() {
        return FeeSchedule::getActiveFeeFor('address_indexing');
    }

    public function submit() {
        $fee = $this->fee;
        if (!$fee) {
            $this->dispatch('error', 'Service not available for payment');
            return;
        }
        // Proceed with payment using $fee->base_amount
    }
}
```

## Fee Validation

### Check All Services Have Fees

```php
$service = app(FeeService::class);
$missing = $service->validateAllServicesHaveFees();

if (!empty($missing)) {
    foreach ($missing as $type => $name) {
        echo "Missing fee for: $name ($type)";
    }
}
```

### Get Upcoming Fee Changes

```php
$service = app(FeeService::class);
$upcoming = $service->getUpcomingFeeChanges();
// Returns fees with effective_from date in the future
```

### Get Fee History

```php
$service = app(FeeService::class);
$history = $service->getFeeHistory('address_registration', limit: 10);
// Returns last 10 fee records for service type
```

## Livewire Features

### Admin Component Validation

```php
protected $rules = [
    'serviceType'   => 'required|string|max:50',
    'serviceName'   => 'required|string|max:255',
    'description'   => 'nullable|string|max:500',
    'baseAmount'    => 'required|numeric|min:0.01|max:999999.99',
    'currency'      => 'required|string|max:3',
    'status'        => 'required|in:active,inactive,archived',
    'effectiveFrom' => 'nullable|date_format:Y-m-d H:i',
    'effectiveTo'   => 'nullable|date_format:Y-m-d H:i|after_or_equal:effectiveFrom',
];
```

### Portal Component Query Optimization

- Uses eager loading with `with()` where applicable
- Groups fees by service type in memory for display
- Caches computed properties
- Minimal database queries per request

## UI Components

**Admin Fee Management**:

- Modal-based CRUD with form validation
- Pagination (15 items per page)
- Live search with 300ms debounce
- Status filter dropdown
- Toggle buttons for Active/Inactive
- Icon indicators for service types
- Formatted currency with proper symbols
- Delete confirmation dialogs
- Toast notifications for actions
- Empty states with helpful messages

**Portal Fee Display**:

- Responsive grid layout
- Grouped by service type
- Service icons and descriptions
- Status indicators for inactive fees
- Formatted amounts in local currency
- Empty state if no fees configured

## Security

1. **Admin-Only Access**: Fee management requires `manage settings` permission or `super-admin` role
2. **Validation**: All inputs validated before database operations
3. **Authorization**: Routes protected by middleware and Livewire authorization
4. **Data Integrity**: Cascade cleanup prevents orphaned relationships

## Performance Considerations

- **Database Indexes**: `service_type` and `status` columns indexed
- **Query Optimization**: Uses scopes and eager loading
- **Pagination**: 15 items per page to reduce memory usage
- **Caching**: Computed properties leverage Livewire's caching
- **Search Debouncing**: 300ms debounce reduces database hits

## Common Scenarios

### Display Fee in User Form

```php
<div class="fee-info">
    @php
        $fee = \App\Models\FeeSchedule::getActiveFeeFor('address_registration');
    @endphp

    @if($fee)
        <p>Fee: {{ $fee->getFormattedAmount() }}</p>
        @if($fee->description)
            <p class="description">{{ $fee->description }}</p>
        @endif
    @else
        <p>Fee not configured</p>
    @endif
</div>
```

### Calculate Total Cost with Tax

```php
$service = app(\App\Services\FeeService::class);
$fee = $service->getActiveFee('address_indexing');
$baseAmount = $fee->base_amount;
$taxRate = 0.025; // 2.5% tax
$totalCost = $baseAmount * (1 + $taxRate);
```

### Fee Schedule Report

```php
$service = app(\App\Services\FeeService::class);
$allServices = $service->validateAllServicesHaveFees();

foreach (\App\Models\FeeSchedule::SERVICE_TYPES as $type => $name) {
    $fee = $service->getActiveFee($type);
    echo $name . ' ('. ($fee ? $fee->getFormattedAmount() : 'NOT SET') .')';
}
```

### Create Fee with Future Effective Date

```php
use App\Models\FeeSchedule;

FeeSchedule::create([
    'service_type' => 'address_registration',
    'service_name' => 'Address Registration',
    'base_amount' => 2500.00,
    'currency' => 'NGN',
    'status' => 'active',
    'effective_from' => now()->addMonths(1),
    'effective_to' => null,
]);
```

### Archive Old Fees

```php
$service = app(\App\Services\FeeService::class);
$expired = $service->getExpiredFees();

foreach ($expired as $fee) {
    $fee->update(['status' => 'archived']);
}
```

## Troubleshooting

### Fees Not Showing in Portal

1. Check if fees are marked as `active` in admin panel
2. Verify `effective_from` date is in the past (or null)
3. Verify `effective_to` date is in the future (or null)
4. Clear browser cache and refresh
5. Check database: `SELECT COUNT(*) FROM fee_schedules WHERE status = 'active'`

### Seeder Not Running

1. Verify migration ran: `php artisan migrate:status`
2. Check if DatabaseSeeder calls FeeScheduleSeeder
3. Clear cache: `php artisan cache:clear`
4. Rerun: `php artisan db:seed`

### Fee Service Not Found

1. `use App\Services\FeeService;` in class
2. Or: `app(FeeService::class)->methodName()`
3. Or inject via constructor: `public function __construct(FeeService $feeService) {}`

### Currency Not Displaying Correctly

1. Check fee currency is correct in admin (NGN, USD, EUR)
2. Verify FeeService::getFormattedAmount() includes proper logic for currency symbol
3. Update model's getFormattedAmount() method if custom symbols needed

## Future Enhancements

- [ ] Bulk fee updates
- [ ] Discount code/coupon system
- [ ] Fee change approval workflow
- [ ] Audit log for fee modifications
- [ ] Fee analytics and reporting
- [ ] Subscription-based pricing
- [ ] Multi-currency support with exchange rates
- [ ] Fee waiver/exemption system
- [ ] Payment plan support
- [ ] Regional fee variations

## File Summary

| File                                                                   | Purpose                           |
| ---------------------------------------------------------------------- | --------------------------------- |
| `app/Models/FeeSchedule.php`                                           | Fee model with scopes and helpers |
| `app/Services/FeeService.php`                                          | Centralized fee service logic     |
| `app/Livewire/Admin/Fees/Index.php`                                    | Admin fee management component    |
| `resources/views/livewire/admin/fees/index.blade.php`                  | Admin fee management view         |
| `app/Livewire/Portal/FeeSchedule.php`                                  | Portal fee display component      |
| `resources/views/livewire/portal/fee-schedule.blade.php`               | Portal fee display view           |
| `database/migrations/2026_04_04_000006_create_fee_schedules_table.php` | Fee table migration               |
| `database/seeders/FeeScheduleSeeder.php`                               | Fee seeder with defaults          |
| `routes/web.php`                                                       | Route definitions                 |

## Integration Points

### With Payment System

- Use `FeeService::getFeeAmount()` when initializing Paystack transactions
- Store service fee details in Payment model metadata

### With Address Indexing

- Get fee via `FeeSchedule::getFeeAmount('address_indexing')`
- Display in form before payment initiation
- Apply to payment record

### With Street Revalidation

- Get fee via `FeeSchedule::getFeeAmount('street_revalidation')`
- Show in modal before user proceeds
- Track in payment polymorphic relationship

## Support & Documentation

For issues, enhancements, or questions:

1. Check default fees in FeeScheduleSeeder
2. Verify admin can access /admin/fee-schedules
3. Test with `php artisan tinker` and FeeService::class
4. Review FeeSchedule model for available methods
