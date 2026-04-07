<?php

namespace App\Services;

use App\Models\StreetNumberingPlateRequest;
use App\Models\AddressIndexingRequest;
use App\Models\StreetApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchOperationService
{
    /**
     * Bulk approve requests
     */
    public function bulkApproveStreetNumberingPlates(array $ids, string $notes = ''): array
    {
        return DB::transaction(function () use ($ids, $notes) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $request = StreetNumberingPlateRequest::findOrFail($id);

                    if ($request->status === 'pending') {
                        $request->update([
                            'status' => 'approved',
                            'admin_notes' => $notes ?: $request->admin_notes,
                            'approved_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'reference' => $request->reference_number,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to approve street numbering plate {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk reject requests
     */
    public function bulkRejectStreetNumberingPlates(array $ids, string $rejectionReason): array
    {
        if (!$rejectionReason) {
            return [
                'success' => [],
                'failed' => array_map(fn ($id) => [
                    'id' => $id,
                    'error' => 'Rejection reason is required',
                ], $ids),
                'total' => count($ids),
            ];
        }

        return DB::transaction(function () use ($ids, $rejectionReason) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $request = StreetNumberingPlateRequest::findOrFail($id);

                    if ($request->status === 'pending') {
                        $request->update([
                            'status' => 'rejected',
                            'rejection_reason' => $rejectionReason,
                            'rejected_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'reference' => $request->reference_number,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to reject street numbering plate {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk update production status
     */
    public function bulkUpdateProductionStatus(array $ids, string $newStatus): array
    {
        $validStatuses = ['in_production', 'ready', 'delivered', 'installed'];

        if (!in_array($newStatus, $validStatuses)) {
            return [
                'success' => [],
                'failed' => array_map(fn ($id) => [
                    'id' => $id,
                    'error' => "Invalid status. Expected one of: " . implode(', ', $validStatuses),
                ], $ids),
                'total' => count($ids),
            ];
        }

        return DB::transaction(function () use ($ids, $newStatus) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
                'status_changed_to' => $newStatus,
            ];

            foreach ($ids as $id) {
                try {
                    $request = StreetNumberingPlateRequest::findOrFail($id);
                    $request->update([
                        'status' => $newStatus,
                        'updated_at' => now(),
                    ]);

                    $results['success'][] = [
                        'id' => $id,
                        'reference' => $request->reference_number,
                    ];
                } catch (\Exception $e) {
                    Log::error("Failed to update status for {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk approve address indexing requests
     */
    public function bulkApproveAddressIndexing(array $ids, string $notes = ''): array
    {
        return DB::transaction(function () use ($ids, $notes) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $request = AddressIndexingRequest::findOrFail($id);

                    if ($request->status === 'pending') {
                        $request->update([
                            'status' => 'approved',
                            'admin_note' => $notes ?: $request->admin_note,
                            'reviewed_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'address' => $request->address_line,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to approve address indexing {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk reject address indexing requests
     */
    public function bulkRejectAddressIndexing(array $ids, string $rejectionReason): array
    {
        if (!$rejectionReason) {
            return [
                'success' => [],
                'failed' => array_map(fn ($id) => [
                    'id' => $id,
                    'error' => 'Rejection reason is required',
                ], $ids),
                'total' => count($ids),
            ];
        }

        return DB::transaction(function () use ($ids, $rejectionReason) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $request = AddressIndexingRequest::findOrFail($id);

                    if ($request->status === 'pending') {
                        $request->update([
                            'status' => 'rejected',
                            'admin_note' => $rejectionReason,
                            'reviewed_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'address' => $request->address_line,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to reject address indexing {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk approve street applications
     */
    public function bulkApproveStreetApplications(array $ids, string $notes = ''): array
    {
        return DB::transaction(function () use ($ids, $notes) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $app = StreetApplication::findOrFail($id);

                    if ($app->status === 'pending') {
                        $app->update([
                            'status' => 'approved',
                            'admin_note' => $notes ?: $app->admin_note,
                            'reviewed_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'street' => $app->street_name,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to approve street application {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Bulk reject street applications
     */
    public function bulkRejectStreetApplications(array $ids, string $rejectionReason): array
    {
        if (!$rejectionReason) {
            return [
                'success' => [],
                'failed' => array_map(fn ($id) => [
                    'id' => $id,
                    'error' => 'Rejection reason is required',
                ], $ids),
                'total' => count($ids),
            ];
        }

        return DB::transaction(function () use ($ids, $rejectionReason) {
            $results = [
                'success' => [],
                'failed' => [],
                'total' => count($ids),
            ];

            foreach ($ids as $id) {
                try {
                    $app = StreetApplication::findOrFail($id);

                    if ($app->status === 'pending') {
                        $app->update([
                            'status' => 'rejected',
                            'admin_note' => $rejectionReason,
                            'reviewed_at' => now(),
                        ]);

                        $results['success'][] = [
                            'id' => $id,
                            'street' => $app->street_name,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to reject street application {$id}: " . $e->getMessage());
                    $results['failed'][] = [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Export results to CSV
     */
    public function exportResultsToCsv(array $results, string $filename = 'batch_operations'): string
    {
        $csv = "ID,Reference/Address/Street,Status\n";

        foreach ($results['success'] as $item) {
            $reference = $item['reference'] ?? $item['address'] ?? $item['street'] ?? '';
            $csv .= "{$item['id']},{$reference},Success\n";
        }

        foreach ($results['failed'] as $item) {
            $error = $item['error'] ?? 'Unknown error';
            $csv .= "{$item['id']},,Failed: {$error}\n";
        }

        return $csv;
    }
}
