<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-sync-alt" style="color:var(--accent);margin-right:10px;"></i>Apply for Street
                Revalidation</h2>
            <p>Request revalidation of an existing street or create a new revalidation application</p>
        </div>
    </div>

    @if ($submitted && $lastRevalidation)
        <!-- Success State -->
        <div class="card" style="max-width:580px;margin:0 auto;text-align:center;padding:40px;">
            <div
                style="width:72px;height:72px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent);">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="font-size:20px;font-weight:700;margin-bottom:8px;">Revalidation Request Submitted!</h3>
            <p style="color:var(--text-secondary);margin-bottom:20px;">Your street revalidation request for
                <strong>{{ $lastRevalidation->street_name }}</strong> has been reviewed and is pending payment.</p>

            <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:24px;">
                <div style="text-align:left;font-size:13px;">
                    <div style="margin-bottom:10px;"><span style="color:var(--text-secondary);">Street
                            Name</span><br><strong>{{ $lastRevalidation->street_name }}</strong></div>
                    <div style="margin-bottom:10px;"><span
                            style="color:var(--text-secondary);">Town</span><br><strong>{{ $lastRevalidation->town }}</strong>
                    </div>
                    <div><span style="color:var(--text-secondary);">Status</span><br><span
                            style="background:#fff3cd;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;">PENDING
                            PAYMENT</span></div>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <button wire:click="newApplication" class="btn btn-outline"><i class="fas fa-plus"></i> New
                    Request</button>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-primary">View My Requests</a>
            </div>
        </div>
    @else
        <!-- Multi-step Form -->
        <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;">
            <div>
                <!-- Tab Selection -->
                <div style="display:flex;gap:12px;margin-bottom:24px;">
                    <button wire:click="setTab('existing')"
                        style="flex:1;padding:12px;border:none;border-radius:var(--radius-sm);font-weight:600;cursor:pointer;background:{{ $tab === 'existing' ? 'var(--accent)' : 'var(--bg-input)' }};color:{{ $tab === 'existing' ? 'white' : 'var(--text-primary)' }};">
                        <i class="fas fa-list"></i> Existing Street
                    </button>
                    <button wire:click="setTab('new')"
                        style="flex:1;padding:12px;border:none;border-radius:var(--radius-sm);font-weight:600;cursor:pointer;background:{{ $tab === 'new' ? 'var(--accent)' : 'var(--bg-input)' }};color:{{ $tab === 'new' ? 'white' : 'var(--text-primary)' }};">
                        <i class="fas fa-plus"></i> Record Street
                    </button>
                </div>

                <!-- Step Indicator -->
                <div style="display:flex;gap:8px;margin-bottom:24px;">
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 1 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            1</div>
                        <small
                            style="color:{{ $step >= 1 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">{{ $tab === 'existing' ? 'Select' : 'Details' }}</small>
                    </div>
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 2 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            2</div>
                        <small
                            style="color:{{ $step >= 2 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Reason</small>
                    </div>
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 3 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            3</div>
                        <small
                            style="color:{{ $step >= 3 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Docs</small>
                    </div>
                </div>

                <!-- Step 1 -->
                @if ($step === 1)
                    <div class="card">
                        @if ($tab === 'existing')
                            <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-road"
                                    style="color:var(--accent);margin-right:8px;"></i>Select Street to Revalidate</h3>

                            <div style="margin-bottom:20px;">
                                <input type="text" placeholder="Search streets..."
                                    style="width:100%;margin-bottom:12px;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);font-size:14px;font-family:inherit;" 
                                    wire:model.live="search_street"
                                    @if ($tab === 'existing')  @endif>

                                <div style="max-height:400px;overflow-y:auto;">
                                    @forelse($streets as $street)
                                        <div style="padding:12px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px;cursor:pointer;background:{{ $street_id === $street->id ? 'var(--accent-light)' : 'transparent' }};border-color:{{ $street_id === $street->id ? 'var(--accent)' : 'var(--border)' }};"
                                            wire:click="selectStreet({{ $street->id }})">
                                            <div style="font-weight:600;font-size:14px;">{{ $street->name }}</div>
                                            <div style="font-size:12px;color:var(--text-secondary);">
                                                {{ $street->town }} • {{ ucfirst($street->type) }}</div>
                                        </div>
                                    @empty
                                        <div style="padding:20px;text-align:center;color:var(--text-secondary);">
                                            <small>No active streets found</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            @if ($selectedStreet)
                                <div
                                    style="background:var(--accent-light);border-radius:var(--radius-sm);padding:16px;margin-top:16px;">
                                    <div style="font-size:13px;">
                                        <div style="margin-bottom:8px;"><span
                                                style="color:var(--text-secondary);">Selected
                                                Street:</span><br><strong>{{ $selectedStreet->name }}</strong></div>
                                        <div><span
                                                style="color:var(--text-secondary);">Town:</span><br><strong>{{ $selectedStreet->town }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @error('street_id')
                                <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                            @enderror
                        @else
                            <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-road"
                                    style="color:var(--accent);margin-right:8px;"></i>Street Details</h3>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Street Name *</label>
                                    <input wire:model.blur="street_name" type="text" placeholder="e.g. Nnewi Road">
                                    @error('street_name')
                                        <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Town *</label>
                                    <input wire:model.blur="town" type="text" placeholder="e.g. Abagana Town">
                                    @error('town')
                                        <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div style="display:flex;gap:10px;margin-top:24px;">
                            <button type="button" wire:click="previousStep" class="btn btn-outline"
                                style="visibility:hidden;">Previous</button>
                            <button type="button" wire:click="nextStep" class="btn btn-primary"
                                style="margin-left:auto;">Next</button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                @elseif($step === 2)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-comments"
                                style="color:var(--accent);margin-right:8px;"></i>Revalidation Details</h3>

                        <div class="form-grid">
                            <div class="form-group" style="grid-column:1/-1;">
                                <label>Reason for Revalidation *</label>
                                <textarea wire:model="reason" rows="4" placeholder="Explain why this street needs to be revalidated..."
                                    style="resize:vertical;"></textarea>
                                @error('reason')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group" style="grid-column:1/-1;">
                                <label>Current Status of Street *</label>
                                <select wire:model="current_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="disputed">Disputed</option>
                                    <option value="under_review">Under Review</option>
                                </select>
                                @error('current_status')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div style="display:flex;gap:10px;margin-top:24px;">
                            <button type="button" wire:click="previousStep"
                                class="btn btn-outline">Previous</button>
                            <button type="button" wire:click="nextStep" class="btn btn-primary"
                                style="margin-left:auto;">Next</button>
                        </div>
                    </div>

                    <!-- Step 3 -->
                @elseif($step === 3)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-file"
                                style="color:var(--accent);margin-right:8px;"></i>Supporting Documents</h3>

                        <div style="margin-bottom:20px;">
                            <label style="display:block;margin-bottom:12px;font-weight:600;font-size:13px;">Upload
                                Documents (Max 5)</label>
                            <div
                                style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:24px;text-align:center;">
                                <div style="margin-bottom:12px;font-size:24px;color:var(--text-secondary);">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <input type="file" wire:model="uploadedDocuments" multiple style="display:none;"
                                    id="docInput">
                                <label for="docInput"
                                    style="cursor:pointer;color:var(--accent);font-weight:600;">Click to select
                                    files</label>
                                <small style="display:block;margin-top:8px;color:var(--text-secondary);">PDF, DOC, or
                                    Image files (max 10MB each)</small>
                            </div>
                        </div>

                        @if (count($uploadedDocuments) > 0)
                            <div style="margin-bottom:20px;">
                                <button type="button" wire:click="addSupportingDocument"
                                    wire:loading.attr="disabled" class="btn btn-secondary btn-sm">
                                    <span wire:loading.remove><i class="fas fa-upload"></i> Upload Documents</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Uploading...</span>
                                </button>
                            </div>
                        @endif

                        <!-- Uploaded Documents -->
                        @if (count($supporting_documents) > 0)
                            <div style="margin-bottom:20px;">
                                <label
                                    style="display:block;margin-bottom:12px;font-weight:600;font-size:13px;">Uploaded
                                    Documents ({{ count($supporting_documents) }})</label>
                                <div
                                    style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;">
                                    @foreach ($supporting_documents as $index => $doc)
                                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-light);"
                                            @if ($loop->last) style="border-bottom:none;" @endif>
                                            <div style="display:flex;align-items:center;gap:8px;flex:1;">
                                                <i class="fas fa-file-pdf" style="color:var(--danger);"></i>
                                                <small
                                                    style="color:var(--text-secondary);">{{ basename($doc) }}</small>
                                            </div>
                                            <button type="button" wire:click="removeDocument({{ $index }})"
                                                style="background:none;border:none;color:var(--text-secondary);cursor:pointer;padding:4px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <small style="display:block;color:var(--text-secondary);margin-top:16px;margin-bottom:20px;"><i
                                class="fas fa-info-circle"></i> Documents are optional but highly recommended for
                            faster approval</small>

                        <div style="display:flex;gap:10px;margin-top:24px;">
                            <button type="button" wire:click="previousStep"
                                class="btn btn-outline">Previous</button>
                            <button type="button" wire:click="submit" wire:loading.attr="disabled"
                                class="btn btn-primary" style="margin-left:auto;">
                                <span wire:loading.remove><i class="fas fa-check"></i> Submit Request</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin"></i> Submitting...</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div>
                <div class="card"
                    style="background:var(--accent-light);border:1px solid var(--accent);margin-bottom:16px;">
                    <h4 style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--accent);"><i
                            class="fas fa-info-circle" style="margin-right:6px;"></i>About Revalidation</h4>
                    <p style="font-size:13px;line-height:1.6;color:var(--text-secondary);">
                        Revalidation allows you to update or verify a street's information in the system, addressing any
                        disputes or changes in status.
                    </p>
                </div>

                <div class="card">
                    <h4 style="font-size:13px;font-weight:700;margin-bottom:12px;"><i class="fas fa-list"
                            style="color:var(--accent);margin-right:6px;"></i>Process</h4>
                    <ul style="font-size:12px;line-height:1.8;color:var(--text-secondary);">
                        <li><span style="color:{{ $step >= 1 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            {{ $tab === 'existing' ? 'Select street' : 'Record street' }}</li>
                        <li><span style="color:{{ $step >= 2 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Provide reason</li>
                        <li><span style="color:{{ $step >= 3 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Upload docs</li>
                        <li>Make payment</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
