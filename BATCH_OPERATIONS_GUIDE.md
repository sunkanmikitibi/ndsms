# Batch Operations Implementation Guide

## Overview

The Batch Operations Service allows administrators to perform bulk actions on multiple requests simultaneously, improving efficiency when managing large volumes of approvals, rejections, or status updates.

## Features

✅ **Bulk Approve Operations**

- Approve multiple street numbering plate requests
- Approve multiple address indexing requests
- Approve multiple street applications

✅ **Bulk Reject Operations**

- Reject multiple requests with reason
- Add rejection notes
- Automatic SMS notifications via observers

✅ **Bulk Status Updates**

- Update production status for projects
- Track status change history
- Comprehensive error reporting

✅ **Transaction Safety**

- All-or-nothing approach with database transactions
- Rollback on errors
- Detailed success/failure reporting

✅ **Audit Trail**

- Logging of all batch operations
- Metadata storage
- Result export to CSV

---

## Service Methods

### StreetNumberingPlateRequest Batch Operations

#### Bulk Approve

```php
$service = app(\App\Services\BatchOperationService::class);

$results = $service->bulkApproveStreetNumberingPlates(
    ids: [1, 2, 3, 4, 5],
    notes: 'Batch approved - all documents verified'
);

// Returns:
[
    'success' => [
        ['id' => 1, 'reference' => 'PLATE-ABC123'],
        ['id' => 2, 'reference' => 'PLATE-DEF456'],
    ],
    'failed' => [],
    'total' => 5,
]
```

#### Bulk Reject

```php
$results = $service->bulkRejectStreetNumberingPlates(
    ids: [1, 2, 3],
    rejectionReason: 'Incorrect street name provided'
);

// Returns similar structure with success/failed arrays
```

#### Bulk Status Update

```php
$results = $service->bulkUpdateProductionStatus(
    ids: [1, 2, 3, 4, 5],
    newStatus: 'ready' // or 'in_production', 'delivered', 'installed'
);
```

### AddressIndexingRequest Batch Operations

#### Bulk Approve

```php
$results = $service->bulkApproveAddressIndexing(
    ids: [1, 2, 3],
    notes: 'All addresses verified and approved'
);
```

#### Bulk Reject

```php
$results = $service->bulkRejectAddressIndexing(
    ids: [1, 2],
    rejectionReason: 'Address coordinates outside service area'
);
```

### StreetApplication Batch Operations

#### Bulk Approve

```php
$results = $service->bulkApproveStreetApplications(
    ids: [1, 2, 3, 4],
    notes: 'Street registry updated'
);
```

#### Bulk Reject

```php
$results = $service->bulkRejectStreetApplications(
    ids: [1, 2],
    rejectionReason: 'Street already registered'
);
```

---

## Result Structure

All batch operations return a standardized result array:

```php
[
    'success' => [
        [
            'id' => 1,
            'reference' => 'PLATE-ABC123',  // or 'address' or 'street'
        ],
        // ... more successful operations
    ],
    'failed' => [
        [
            'id' => 5,
            'error' => 'Record already approved',
        ],
        // ... more failed operations
    ],
    'total' => 5,
    'status_changed_to' => 'ready',  // Only for bulk update operations
]
```

---

## Usage Examples

### Basic Usage in Controller

```php
namespace App\Http\Controllers;

use App\Services\BatchOperationService;

class AdminController extends Controller
{
    public function batchApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        $notes = $request->input('notes', '');

        $service = app(BatchOperationService::class);
        $results = $service->bulkApproveStreetNumberingPlates($ids, $notes);

        return response()->json([
            'message' => "Approved {$results['success']|count()} of {$results['total']} requests",
            'results' => $results,
        ]);
    }
}
```

### In Livewire Component

```php
namespace App\Livewire\Admin;

use App\Services\BatchOperationService;
use Livewire\Component;

class BatchApprovalPanel extends Component
{
    public array $selectedIds = [];
    public string $approvalNotes = '';

    public function approve()
    {
        $service = app(BatchOperationService::class);

        $results = $service->bulkApproveStreetNumberingPlates(
            $this->selectedIds,
            $this->approvalNotes
        );

        if (!empty($results['success'])) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Approved {$results['success']|count()} requests",
            ]);
        }

        if (!empty($results['failed'])) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Failed to approve {$results['failed']|count()} requests",
            ]);
        }

        $this->selectedIds = [];
        $this->approvalNotes = '';
    }
}
```

---

## Export Results

Export batch operation results to CSV for reporting:

```php
$service = app(BatchOperationService::class);
$results = $service->bulkApproveStreetNumberingPlates($ids);

$csv = $service->exportResultsToCsv($results, 'batch_approval_2026_04_07');

// CSV Output:
// ID,Reference/Address/Street,Status
// 1,PLATE-ABC123,Success
// 2,PLATE-DEF456,Success
// 3,,Failed: Record already approved
```

---

## Database Transactions

All batch operations use database transactions for data consistency:

```php
// If any operation fails within the transaction,
// the entire batch is rolled back
try {
    $results = $service->bulkApproveStreetNumberingPlates($ids);
    // All operations succeeded
} catch (\Exception $e) {
    // All changes rolled back on exception
    Log::error("Batch operation failed: " . $e->getMessage());
}
```

---

## Performance Considerations

### Recommended Batch Sizes

- **Small batches**: 1-10 records (< 100ms)
- **Medium batches**: 10-50 records (100-500ms)
- **Large batches**: 50-200 records (500ms-2s)
- **Very large batches**: 200+ records (consider chunking)

### Chunking Large Operations

```php
$allIds = [1, 2, 3, ..., 10000];
$chunkSize = 100;
$allResults = ['success' => [], 'failed' => []];

foreach (array_chunk($allIds, $chunkSize) as $chunk) {
    $results = $service->bulkApproveStreetNumberingPlates($chunk);

    $allResults['success'] = array_merge(
        $allResults['success'],
        $results['success']
    );
    $allResults['failed'] = array_merge(
        $allResults['failed'],
        $results['failed']
    );
}
```

---

## Error Handling

### Individual Record Failures Don't Stop Batch

```php
// Even if some records fail, others continue processing
$results = $service->bulkApproveStreetNumberingPlates([1, 2, 3, 4, 5]);

// Might return:
[
    'success' => [[...3 items...]],  // 3 succeeded
    'failed' => [[...2 items...]],   // 2 failed
    'total' => 5,
]
```

### Validation Failures

```php
// Invalid status throws error before processing
$results = $service->bulkUpdateProductionStatus($ids, 'invalid_status');
// All records marked as failed with validation error
```

### Database Transaction Rollback

```php
// If catastrophic failure occurs
try {
    $results = $service->bulkApproveStreetNumberingPlates($ids);
} catch (\Throwable $e) {
    // Transaction automatically rolled back
    Log::critical("Batch operation transaction failed: {$e->getMessage()}");
}
```

---

## Monitoring & Logging

### View Batch Operation Logs

```php
// In Laravel tinker or Artisan command
use Illuminate\Support\Facades\Log;

// Check recent logs
tail storage/logs/laravel.log

// Search for failures
grep -i "failed to approve" storage/logs/laravel.log
```

### Track Changes

```php
use App\Models\StreetNumberingPlateRequest;

// View recent updates for auditing
$requests = StreetNumberingPlateRequest::whereDate('updated_at', today())
    ->where('status', 'approved')
    ->orderBy('updated_at', 'desc')
    ->get();
```

---

## Best Practices

### ✅ DO

- Validate IDs before batch operation
- Confirm selection with user before bulk actions
- Log batch operations for audit trail
- Use appropriate batch sizes (10-100 records)
- Check for duplicate IDs before processing

### ❌ DON'T

- Process all records in one batch (use chunking)
- Skip error checking on failed operations
- Process invalid record IDs (validate first)
- Modify batch operation logic during production
- Ignore database locks on concurrent operations

---

## API Reference

```php
// Service: App\Services\BatchOperationService

// Street Numbering Plate Operations
bulkApproveStreetNumberingPlates(array $ids, string $notes = ''): array
bulkRejectStreetNumberingPlates(array $ids, string $rejectionReason): array
bulkUpdateProductionStatus(array $ids, string $newStatus): array

// Address Indexing Operations
bulkApproveAddressIndexing(array $ids, string $notes = ''): array
bulkRejectAddressIndexing(array $ids, string $rejectionReason): array

// Street Application Operations
bulkApproveStreetApplications(array $ids, string $notes = ''): array
bulkRejectStreetApplications(array $ids, string $rejectionReason): array

// Utilities
exportResultsToCsv(array $results, string $filename): string
```

---

## Testing

```php
// In tests/Feature/BatchOperationTest.php

public function test_bulk_approve_street_numbering_plates()
{
    $ids = StreetNumberingPlateRequest::factory()
        ->count(5)
        ->create(['status' => 'pending'])
        ->pluck('id')
        ->toArray();

    $service = app(BatchOperationService::class);
    $results = $service->bulkApproveStreetNumberingPlates($ids);

    $this->assertCount(5, $results['success']);
    $this->assertEmpty($results['failed']);

    $this->assertTrue(
        StreetNumberingPlateRequest::whereIn('id', $ids)
            ->where('status', 'approved')
            ->count() === 5
    );
}
```

---

## Version

Created: April 7, 2026
Last Updated: April 7, 2026
