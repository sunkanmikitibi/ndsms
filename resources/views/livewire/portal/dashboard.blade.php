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
        <button wire:click="setTab('certificates')" class="profile-tab {{ $activeTab === 'certificates' ? 'active' : '' }}">
            <i class="fas fa-certificate"></i> My Certificates
            <span class="tab-count">{{ $stats['certificates'] }}</span>
        </button>
        <button wire:click="setTab('addresses')" class="profile-tab {{ $activeTab === 'addresses' ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> My Addresses
            <span class="tab-count">{{ $stats['addresses'] }}</span>
        </button>
        <button wire:click="setTab('field-tasks')" class="profile-tab {{ $activeTab === 'field-tasks' ? 'active' : '' }}">
            <i class="fas fa-tasks"></i> My Field Tasks
            <span class="tab-count">0</span>
        </button>
        <button wire:click="setTab('complaints')" class="profile-tab {{ $activeTab === 'complaints' ? 'active' : '' }}">
            <i class="fas fa-comment-dots"></i> My Complaints
            <span class="tab-count">0</span>
        </button>
    </div>

    <!-- ======================== PROFILE TAB ======================== -->
    @if($activeTab === 'profile')
    <div>
        @if(!$editingProfile)
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
                <div class="profile-field-value" style="font-family:'Space Mono',monospace;color:var(--accent);">
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
            <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i>Edit Profile</h3>
            <form wire:submit="saveProfile">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input wire:model="name" type="text">
                        @error('name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
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
                    <button type="button" wire:click="$set('editingProfile', false)" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
        @endif
    </div>
    @endif

    <!-- ======================== MY REQUESTS TAB ======================== -->
    @if($activeTab === 'requests')
    <div>
        @if($myRequests->isEmpty())
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h4>No requests yet</h4>
                <p>You haven't submitted any street registration requests.</p>
                <a href="{{ route('portal.register-street') }}" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Register a Street</a>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Street Name</th>
                        <th>Ward</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Admin Note</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myRequests as $req)
                    <tr>
                        <td style="font-weight:600;">{{ $req->street_name }}</td>
                        <td>{{ $req->ward }}</td>
                        <td><span class="ward-badge">{{ ucfirst($req->type) }}</span></td>
                        <td><span class="status-badge {{ $req->status }}">{{ str_replace('_', ' ', ucfirst($req->status)) }}</span></td>
                        <td style="color:var(--text-secondary);font-size:13px;">{{ $req->admin_note ?? '—' }}</td>
                        <td style="color:var(--text-secondary);font-size:12px;">{{ $req->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    <!-- ======================== CERTIFICATES TAB ======================== -->
    @if($activeTab === 'certificates')
    <div>
        @php $approved = $myRequests->where('status', 'approved'); @endphp
        @if($approved->isEmpty())
            <div class="empty-state">
                <i class="fas fa-certificate"></i>
                <h4>No certificates yet</h4>
                <p>Approved street registrations will appear here as certificates.</p>
            </div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
            @foreach($approved as $cert)
            <div class="card" style="border:2px solid var(--accent);position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:var(--accent-light);border-bottom-left-radius:50%;opacity:.6;"></div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:40px;height:40px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--accent);font-weight:700;">Street Certificate</div>
                        <div style="font-size:14px;font-weight:700;">{{ $cert->street_name }}</div>
                    </div>
                </div>
                <div style="font-size:12px;color:var(--text-secondary);">Ward: <strong style="color:var(--text-primary);">{{ $cert->ward }}</strong></div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:4px;">Approved: <strong style="color:var(--text-primary);">{{ $cert->reviewed_at?->format('d M Y') ?? '—' }}</strong></div>
                <button class="btn btn-outline btn-sm" style="margin-top:14px;width:100%;justify-content:center;" onclick="window.print()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    <!-- ======================== MY ADDRESSES TAB ======================== -->
    @if($activeTab === 'addresses')
    <div>
        @if($myAddresses->isEmpty())
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <h4>No addresses</h4>
                <p>Addresses registered under your name will appear here.</p>
                <a href="{{ route('portal.register-address') }}" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Register Address</a>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>House No.</th><th>Street</th><th>Ward</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($myAddresses as $addr)
                    <tr>
                        <td style="font-family:'Space Mono',monospace;color:var(--accent);font-weight:700;">{{ $addr->house_number }}</td>
                        <td>{{ $addr->street?->name ?? '—' }}</td>
                        <td><span class="ward-badge">{{ $addr->ward }}</span></td>
                        <td><span class="status-badge {{ $addr->status }}">{{ ucfirst($addr->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    <!-- ======================== FIELD TASKS TAB ======================== -->
    @if($activeTab === 'field-tasks')
    <div class="empty-state">
        <i class="fas fa-tasks"></i>
        <h4>No field tasks assigned</h4>
        <p>Tasks assigned to you by the registry office will appear here.</p>
    </div>
    @endif

    <!-- ======================== COMPLAINTS TAB ======================== -->
    @if($activeTab === 'complaints')
    <div class="empty-state">
        <i class="fas fa-comment-dots"></i>
        <h4>No complaints submitted</h4>
        <p>Your submitted complaints and feedback will appear here.</p>
        <a href="{{ route('portal.complaints') }}" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Submit a Complaint</a>
    </div>
    @endif
</div>
