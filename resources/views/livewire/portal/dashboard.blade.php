<div>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="profile-header-info">
            <h2>{{ $user->name }}</h2>
            <div class="ph-meta">{{ $user->email }} &nbsp;·&nbsp; {{ $user->phone ?? 'No phone' }}</div>
            <div class="account-id-badge">
                <i class="fas fa-id-badge"></i>
                USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-file-alt"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['requests'] }}</h3>
                <span>My Requests</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-map-marker-alt"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['addresses'] }}</h3>
                <span>Registered Addresses</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-certificate"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['certificates'] }}</h3>
                <span>Certificates Ready</span>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="profile-tabs">
        <button wire:click="setTab('profile')" class="profile-tab {{ $activeTab === 'profile' ? 'active' : '' }}">
            <i class="fas fa-user"></i> Profile
        </button>
        <button wire:click="setTab('requests')" class="profile-tab {{ $activeTab === 'requests' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> My Requests
            <span class="tab-count">{{ $stats['requests'] }}</span>
        </button>
        <button wire:click="setTab('certificates')"
            class="profile-tab {{ $activeTab === 'certificates' ? 'active' : '' }}">
            <i class="fas fa-certificate"></i> My Certificates
            <span class="tab-count">{{ $stats['certificates'] }}</span>
        </button>
        <button wire:click="setTab('addresses')" class="profile-tab {{ $activeTab === 'addresses' ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> My Addresses
            <span class="tab-count">{{ $stats['addresses'] }}</span>
        </button>
        <button wire:click="setTab('field-tasks')"
            class="profile-tab {{ $activeTab === 'field-tasks' ? 'active' : '' }}">
            <i class="fas fa-tasks"></i> My Field Tasks
            <span class="tab-count">0</span>
        </button>
        <button wire:click="setTab('complaints')"
            class="profile-tab {{ $activeTab === 'complaints' ? 'active' : '' }}">
            <i class="fas fa-comment-dots"></i> My Complaints
            <span class="tab-count">0</span>
        </button>
    </div>

    <!-- ======================== PROFILE TAB ======================== -->
    @if ($activeTab === 'profile')
        <div>
            @if (!$editingProfile)
                <div class="profile-fields">
                    <div class="profile-field">
                        <div class="profile-field-label">Full Name</div>
                        <div class="profile-field-value">{{ $user->name }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Email</div>
                        <div class="profile-field-value">{{ $user->email }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Phone</div>
                        <div class="profile-field-value">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Town</div>
                        <div class="profile-field-value">{{ $user->town ?? '—' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Account ID</div>
                        <div class="profile-field-value"
                            style="font-family:'Space Mono',monospace;color:var(--accent);">
                            USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Registered</div>
                        <div class="profile-field-value">{{ $user->created_at->format('j M Y') }}</div>
                    </div>
                    <div style="grid-column:1/-1;display:flex;justify-content:flex-end;">
                        <button wire:click="$set('editingProfile', true)" class="btn btn-outline btn-sm">
                            <i class="fas fa-pen"></i> Edit Profile
                        </button>
                    </div>
                </div>
            @else
                <div class="card" style="max-width:600px;">
                    <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-pen"
                            style="color:var(--accent);margin-right:8px;"></i>Edit Profile</h3>
                    <form wire:submit="saveProfile">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input wire:model="name" type="text">
                                @error('name')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input wire:model="phone" type="text" placeholder="e.g. 08012345678">
                            </div>
                            <div class="form-group" style="grid-column:1/-1;">
                                <label>Town / LGA</label>
                                <input wire:model="town" type="text" placeholder="e.g. Nawfia">
                            </div>
                        </div>
                        <div class="modal-actions" style="margin-top:20px;">
                            <button type="button" wire:click="$set('editingProfile', false)"
                                class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    @endif

    <!-- ======================== MY REQUESTS TAB ======================== -->
    @if ($activeTab === 'requests')
        <div>
            @if ($myRequests->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h4>No requests yet</h4>
                    <p>You haven't submitted any service requests yet.</p>
                    <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;justify-content:center;">
                        <a href="{{ route('portal.register-street') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Street</a>
                        <a href="{{ route('portal.indexing.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-map-marker-alt"></i> Indexing</a>
                        <a href="{{ route('portal.plates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-indent"></i> Plate Request</a>
                    </div>
                </div>
            @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Request Details</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th style="width:35%;">Communication</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myRequests as $req)
                                <tr>
                                    <td>
                                        <div style="font-weight:700;color:var(--text-primary);">{{ $req['title'] }}</div>
                                        <div style="font-size:12px;color:var(--text-secondary);">{{ $req['sub'] }}</div>
                                    </td>
                                    <td>
                                        <span class="town-badge" style="background:var(--accent-light);color:var(--accent);border:none;">
                                            {{ ucfirst($req['type']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <span class="status-badge {{ $req['status'] }}">{{ str_replace('_', ' ', ucfirst($req['status'])) }}</span>
                                            
                                            @if ($req['status'] === 'awaiting_payment')
                                                @if($req['type'] === 'application')
                                                    <button wire:click="emitInitiateStreet({{ $req['id'] }})" class="btn btn-xs btn-primary">Pay</button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($req['admin_note'])
                                            <div class="admin-note-bubble" style="background:rgba(var(--accent-rgb), 0.05);padding:10px;border-radius:12px;border-left:4px solid var(--accent);margin-bottom:8px;">
                                                <div style="font-size:10px;text-transform:uppercase;font-weight:800;color:var(--accent);margin-bottom:4px;">
                                                    <i class="fas fa-user-shield"></i> Official Note
                                                </div>
                                                <div style="font-size:13px;line-height:1.4;">{{ $req['admin_note'] }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($req['user_note'])
                                            <div class="user-note-bubble" style="background:var(--bg-card);padding:10px;border-radius:12px;border:1px solid var(--border);margin-bottom:8px;text-align:right;">
                                                <div style="font-size:10px;text-transform:uppercase;font-weight:800;color:var(--text-secondary);margin-bottom:4px;">
                                                    My Reply <i class="fas fa-user"></i>
                                                </div>
                                                <div style="font-size:13px;line-height:1.4;color:var(--text-secondary);">{{ $req['user_note'] }}</div>
                                            </div>
                                        @endif

                                        @if($req['admin_note'] && !in_array($req['status'], ['approved', 'completed']))
                                            <button wire:click="openReplyModal({{ $req['id'] }}, '{{ $req['type'] }}')" class="btn btn-xs btn-outline" style="margin-top:4px;">
                                                <i class="fas fa-reply"></i> {{ $req['user_note'] ? 'Update Reply' : 'Send Reply' }}
                                            </button>
                                        @endif
                                        
                                        @if(!$req['admin_note'] && !$req['user_note'])
                                            <span style="color:var(--text-secondary);font-size:12px;font-style:italic;">No messages yet</span>
                                        @endif
                                    </td>
                                    <td style="color:var(--text-secondary);font-size:12px;">{{ $req['date']->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ======================== REPLY MODAL ======================== -->
    @if($showReplyModal)
    <div class="modal-backdrop" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;">
        <div class="card" style="width:100%;max-width:500px;background:var(--bg-main);box-shadow:0 20px 40px rgba(0,0,0,0.3);border:1px solid var(--border);">
            <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="font-size:18px;font-weight:800;"><i class="fas fa-reply" style="color:var(--accent);"></i> Reply to Registry</h3>
                <button wire:click="$set('showReplyModal', false)" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:20px;"><i class="fas fa-times"></i></button>
            </div>
            
            <div style="margin-bottom:20px;">
                <p style="font-size:14px;color:var(--text-secondary);margin-bottom:12px;">Use this field to provide additional information or respond to the administrator's notes.</p>
                
                <div class="form-group">
                    <label>Your Message / Response</label>
                    <textarea wire:model="userReply" rows="5" style="width:100%;padding:14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-size:14px;background:var(--bg-input);color:var(--text-primary);resize:vertical;" placeholder="Type your response here..."></textarea>
                    @error('userReply') <span style="color:var(--danger);font-size:12px;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-actions" style="display:flex;justify-content:flex-end;gap:12px;">
                <button wire:click="$set('showReplyModal', false)" class="btn btn-outline">Cancel</button>
                <button wire:click="submitReply" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-paper-plane"></i> Submit Reply</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Submitting...</span>
                </button>
            </div>
            
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);font-size:11px;color:var(--text-secondary);text-align:center;">
                <i class="fas fa-info-circle"></i> Replying will reset the status to **Pending** for re-review.
            </div>
        </div>
    </div>
    @endif

    <!-- ======================== CERTIFICATES TAB ======================== -->
    @if ($activeTab === 'certificates')
        <div>
            @php $approved = $myRequests->where('status', 'approved'); @endphp
            @if ($approved->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-certificate"></i>
                    <h4>No certificates yet</h4>
                    <p>Approved street registrations will appear here as certificates.</p>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
                    @foreach ($approved as $cert)
                        <div class="card" style="border:2px solid var(--accent);position:relative;overflow:hidden;">
                            <div
                                style="position:absolute;top:0;right:0;width:80px;height:80px;background:var(--accent-light);border-bottom-left-radius:50%;opacity:.6;">
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                                <div
                                    style="width:40px;height:40px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div>
                                    <div
                                        style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--accent);font-weight:700;">
                                        Street Certificate</div>
                                    <div style="font-size:14px;font-weight:700;">{{ $cert->street_name }}</div>
                                </div>
                            </div>
                            <div style="font-size:12px;color:var(--text-secondary);">Town: <strong
                                    style="color:var(--text-primary);">{{ $cert->town }}</strong></div>
                            <div style="font-size:12px;color:var(--text-secondary);margin-top:4px;">Approved: <strong
                                    style="color:var(--text-primary);">{{ $cert->reviewed_at?->format('d M Y') ?? '—' }}</strong>
                            </div>
                            <a href="{{ route('portal.certificates.street', $cert->id) }}" target="_blank"
                                class="btn btn-outline btn-sm"
                                style="margin-top:14px;width:100%;justify-content:center;">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- ======================== MY ADDRESSES TAB ======================== -->
    @if ($activeTab === 'addresses')
        <div>
            @if ($myAddresses->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4>No addresses</h4>
                    <p>Addresses registered under your name will appear here.</p>
                    <a href="{{ route('portal.register-address') }}" class="btn btn-primary"
                        style="margin-top:16px;"><i class="fas fa-plus"></i> Register Address</a>
                </div>
            @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>House No.</th>
                                <th>Street</th>
                                <th>Town</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myAddresses as $addr)
                                <tr>
                                    <td
                                        style="font-family:'Space Mono',monospace;color:var(--accent);font-weight:700;">
                                        {{ $addr->house_number }}</td>
                                    <td>{{ $addr->street?->name ?? '—' }}</td>
                                    <td><span class="town-badge">{{ $addr->town }}</span></td>
                                    <td>
                                        <span
                                            class="status-badge {{ $addr->status }}">{{ ucfirst($addr->status) }}</span>
                                        @if ($addr->status === 'awaiting_payment')
                                            <button wire:click="emitInitiatePaymentAddress({{ $addr->id }})"
                                                class="btn btn-sm btn-primary" style="margin-left:8px;">Pay
                                                Now</button>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        @if ($addr->status === 'active')
                                            <a href="{{ route('portal.certificates.address', $addr->id) }}"
                                                target="_blank" class="btn btn-outline btn-sm" title="Download Certificate">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- ======================== FIELD TASKS TAB ======================== -->
    @if ($activeTab === 'field-tasks')
        <div class="empty-state">
            <i class="fas fa-tasks"></i>
            <h4>No field tasks assigned</h4>
            <p>Tasks assigned to you by the registry office will appear here.</p>
        </div>
    @endif

    <!-- ======================== COMPLAINTS TAB ======================== -->
    @if ($activeTab === 'complaints')
        <div class="empty-state">
            <i class="fas fa-comment-dots"></i>
            <h4>No complaints submitted</h4>
            <p>Your submitted complaints and feedback will appear here.</p>
            <a href="{{ route('portal.complaints') }}" class="btn btn-primary" style="margin-top:16px;"><i
                    class="fas fa-plus"></i> Submit a Complaint</a>
        </div>
    @endif
</div>
