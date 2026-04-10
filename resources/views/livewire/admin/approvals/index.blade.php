<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-clipboard-check" style="color:var(--accent);margin-right:10px;"></i>Approvals</h2>
        <p>Review and process street registration applications</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <button wire:click="setFilter('')" class="filter-tab {{ $filterStatus === '' ? 'active' : '' }}">
        All Applications
    </button>
    <button wire:click="setFilter('pending')" class="filter-tab {{ $filterStatus === 'pending' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> Pending
        @if($counts['pending'] > 0)<span style="background:var(--accent-gold);color:#000;padding:1px 7px;border-radius:10px;font-size:10px;margin-left:4px;">{{ $counts['pending'] }}</span>@endif
    </button>
    <button wire:click="setFilter('awaiting_payment')" class="filter-tab {{ $filterStatus === 'awaiting_payment' ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i> Awaiting Payment
        @if($counts['awaiting_payment'] > 0)<span style="background:var(--info);color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;margin-left:4px;">{{ $counts['awaiting_payment'] }}</span>@endif
    </button>
    <button wire:click="setFilter('approved')" class="filter-tab {{ $filterStatus === 'approved' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> Approved
    </button>
    <button wire:click="setFilter('rejected')" class="filter-tab {{ $filterStatus === 'rejected' ? 'active' : '' }}">
        <i class="fas fa-times-circle"></i> Rejected
    </button>
</div>

<!-- Search -->
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by street name or applicant…">
    </div>
</div>

<!-- Applications -->
@forelse($items as $item)
<div class="approval-card">
    <div class="approval-header">
        <div>
            <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">{{ $item['display_type'] }}</span>
            <h4>{{ $item['title'] }}</h4>
        </div>
        <span class="status-badge {{ $item['status'] }}">{{ str_replace('_', ' ', ucfirst($item['status'])) }}</span>
    </div>
    <div class="approval-meta">
        <div class="approval-meta-item">
            <div class="am-label">
                @if($item['type'] === 'application') Applicant
                @elseif($item['type'] === 'field_report') Field Agent
                @else Resident/Applicant @endif
            </div>
            <div class="am-value">{{ $item['user_name'] }}</div>
        </div>
        <div class="approval-meta-item">
            <div class="am-label">Submitted</div>
            <div class="am-value">{{ $item['created_at']->format('d M Y') }}</div>
        </div>
    </div>
    <div class="approval-actions">
        <button wire:click="viewApplication({{ $item['id'] }}, '{{ $item['type'] }}')" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Review</button>
        @if($item['status'] === 'pending')
        <button wire:click="approve({{ $item['id'] }}, '{{ $item['type'] }}')" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Approve</button>
        @if($item['type'] === 'application')
        <button wire:click="markAwaitingPayment({{ $item['id'] }})" class="btn btn-gold btn-sm"><i class="fas fa-credit-card"></i> Await Payment</button>
        @endif
        <button wire:click="reject({{ $item['id'] }}, '{{ $item['type'] }}')" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reject</button>
        @endif
    </div>
</div>
@empty
<div class="empty-state" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:60px 20px;">
    <i class="fas fa-clipboard-check"></i>
    <h4>No items found</h4>
    <p>There are no {{ $filterStatus ?: '' }} items at the moment.</p>
</div>
@endforelse

<!-- Pagination Info (Simplified as we are manually merging for now) -->
<div class="pagination-wrapper" style="margin-top:16px;">
    <p>{{ count($items) }} items found</p>
</div>

<!-- Review Modal -->
@if($showModal && $viewItem)
<div class="modal-overlay open" wire:click.self="$set('showModal', false)">
    <div class="modal" style="max-width:700px; max-height:90vh; overflow-y:auto;">
        <h3><i class="fas fa-clipboard-check" style="color:var(--accent);"></i> 
            @if($viewType === 'application') Street Application Review
            @elseif($viewType === 'field_report') Field Report Review
            @else Address Registration Review @endif
        </h3>
        
        @if($viewType === 'application')
            <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Street Name</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->street_name }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Type</div><div style="font-weight:700;margin-top:4px;">{{ ucfirst($viewItem->type ?? 'street') }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Applicant</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->user?->name }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Town</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->town }}</div></div>
                </div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);">
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Description</div>
                    <div style="margin-top:4px;font-size:13px;">{{ $viewItem->description }}</div>
                </div>
            </div>
        @elseif($viewType === 'address')
            <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">House Number</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->house_number }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Street</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->street?->name ?? '—' }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Applicant</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->applicant_name }}</div></div>
                    <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Phone</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->applicant_phone }}</div></div>
                </div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Owner Name</div><div style="font-weight:700;margin-top:4px;">{{ $viewItem->owner_name }}</div></div>
                        <div><div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary);font-weight:600;">Location (GPS)</div><div style="font-weight:700;margin-top:4px;font-size:11px;">{{ $viewItem->latitude }}, {{ $viewItem->longitude }}</div></div>
                    </div>
                </div>
            </div>
        @else
            <!-- Field Report Details -->
            <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <div>
                        <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Report Type</span>
                        <div style="font-weight:700;">{{ $viewItem->type === 'form_a' ? 'Street Revalidation' : 'New Street Suggestion' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Agent</span>
                        <div style="font-weight:700;">{{ $viewItem->user?->name }} ({{ $viewItem->data['agent_id'] ?? 'N/A' }})</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; font-size:13px;">
                    @if($viewItem->type === 'form_a')
                        <div><strong>Street Name:</strong> {{ $viewItem->data['street_name'] ?? '—' }}</div>
                        <div><strong>Street Code:</strong> {{ $viewItem->data['street_code'] ?? '—' }}</div>
                        <div><strong>Condition:</strong> {{ ucfirst($viewItem->data['condition'] ?? '—') }}</div>
                        <div><strong>Towns:</strong> {{ is_array($viewItem->data['towns'] ?? null) ? implode(', ', $viewItem->data['towns']) : '—' }}</div>
                    @else
                        <div><strong>Proposed Name:</strong> {{ $viewItem->data['proposed_name'] ?? '—' }}</div>
                        <div><strong>Proposed Type:</strong> {{ is_array($viewItem->data['proposed_type'] ?? null) ? implode(', ', $viewItem->data['proposed_type']) : '—' }}</div>
                        <div><strong>Town:</strong> {{ $viewItem->data['town'] ?? '—' }}</div>
                        <div><strong>Area:</strong> {{ $viewItem->data['area'] ?? '—' }}</div>
                    @endif
                </div>

                <div style="margin-top:16px;">
                    <div style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Observation / Justification</div>
                    <div style="background:#fff; border:1px solid var(--border); border-radius:4px; padding:10px; margin-top:4px; font-size:12px; max-height:100px; overflow-y:auto;">
                        {{ $viewItem->type === 'form_a' ? ($viewItem->data['observation_notes'] ?? 'No notes') : ($viewItem->data['justification'] ?? 'No justification') }}
                    </div>
                </div>
                
                @if(isset($viewItem->data['gps']) || isset($viewItem->data['waypoints']))
                <div style="margin-top:12px;">
                    <div style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Location Metadata</div>
                    <div style="font-size:11px; font-family:monospace; margin-top:4px;">
                        @if($viewItem->type === 'form_a')
                            Start: {{ $viewItem->data['gps']['start_lat'] ?? '—' }}, {{ $viewItem->data['gps']['start_lng'] ?? '—' }} | 
                            End: {{ $viewItem->data['gps']['end_lat'] ?? '—' }}, {{ $viewItem->data['gps']['end_lng'] ?? '—' }}
                        @else
                            {{ count($viewItem->data['waypoints'] ?? []) }} waypoint(s) recorded.
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @endif

        <div class="form-group" style="margin-bottom:20px;">
            <label>Admin Note (optional)</label>
            <textarea wire:model="adminNote" rows="3" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-size:14px;background:var(--bg-input);color:var(--text-primary);resize:vertical;" placeholder="Add a note for the submitter…"></textarea>
        </div>
        
        <div class="modal-actions">
            <button wire:click="$set('showModal', false)" class="btn btn-outline">Close</button>
            @if($viewItem->status === 'pending')
            <button wire:click="reject({{ $viewItem->id }}, '{{ $viewType }}')" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reject</button>
            @if($viewType === 'application')
            <button wire:click="markAwaitingPayment({{ $viewItem->id }})" class="btn btn-gold btn-sm"><i class="fas fa-credit-card"></i> Await Payment</button>
            @endif
            <button wire:click="approve({{ $viewItem->id }}, '{{ $viewType }}')" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Approve</button>
            @endif
        </div>
    </div>
</div>
@endif
</div>
