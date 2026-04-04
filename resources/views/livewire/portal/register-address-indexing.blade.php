<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-map-marker-alt" style="color:var(--accent);margin-right:10px;"></i>Apply for Address
                Indexing</h2>
            <p>Index your property address on Google Maps and integrate it into the NDSMS database</p>
        </div>
    </div>

    @if ($submitted && $lastRequest)
        <!-- Success State -->
        <div class="card" style="max-width:580px;margin:0 auto;text-align:center;padding:40px;">
            <div
                style="width:72px;height:72px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent);">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="font-size:20px;font-weight:700;margin-bottom:8px;">Request Submitted!</h3>
            <p style="color:var(--text-secondary);margin-bottom:20px;">Your address indexing request has been received
                and a payment is required to proceed.</p>

            <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:24px;">
                <div style="text-align:left;font-size:13px;">
                    <div style="margin-bottom:10px;"><span
                            style="color:var(--text-secondary);">Address</span><br><strong>{{ $lastRequest->address_line }}</strong>
                    </div>
                    <div style="margin-bottom:10px;"><span
                            style="color:var(--text-secondary);">Owner</span><br><strong>{{ $lastRequest->owner_name }}</strong>
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
                <!-- Step Indicator -->
                <div style="display:flex;gap:8px;margin-bottom:24px;justify-content:space-between;">
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 1 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            1</div>
                        <small
                            style="color:{{ $step >= 1 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Your
                            Info</small>
                    </div>
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 2 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            2</div>
                        <small
                            style="color:{{ $step >= 2 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Address</small>
                    </div>
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 3 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            3</div>
                        <small
                            style="color:{{ $step >= 3 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Owner</small>
                    </div>
                    <div style="text-align:center;flex:1;">
                        <div
                            style="width:32px;height:32px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;font-weight:700;{{ $step >= 4 ? 'background:var(--accent);color:white;' : 'background:var(--bg-input);color:var(--text-secondary);' }}">
                            4</div>
                        <small
                            style="color:{{ $step >= 4 ? 'var(--text-primary)' : 'var(--text-secondary)' }};font-weight:600;">Images</small>
                    </div>
                </div>

                <!-- Step 1: Applicant Info -->
                @if ($step === 1)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-user"
                                style="color:var(--accent);margin-right:8px;"></i>Your Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input wire:model.blur="applicant_name" type="text" placeholder="Your full name">
                                @error('applicant_name')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input wire:model.blur="applicant_phone" type="tel" placeholder="Your phone number">
                                @error('applicant_phone')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;margin-top:24px;">
                            <button type="button" wire:click="previousStep" class="btn btn-outline"
                                style="visibility:hidden;">Previous</button>
                            <button type="button" wire:click="nextStep" class="btn btn-primary"
                                style="margin-left:auto;">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Address Details -->
                @elseif($step === 2)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-map-pin"
                                style="color:var(--accent);margin-right:8px;"></i>Address Details</h3>
                        <div class="form-grid">
                            <div class="form-group" style="grid-column:1/-1;">
                                <label>Full Address *</label>
                                <input wire:model.blur="address_line" type="text"
                                    placeholder="e.g. 5, Ikeja Road, Abagana">
                                @error('address_line')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>House Number *</label>
                                <input wire:model.blur="house_number" type="text" placeholder="e.g. 5">
                                @error('house_number')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input wire:model.blur="description" type="text"
                                    placeholder="e.g. First house before intersection">
                            </div>

                            <!-- Coordinates Input -->
                            <div
                                style="grid-column:1/-1;padding:16px;background:var(--bg-input);border-radius:var(--radius-sm);">
                                <label
                                    style="display:block;margin-bottom:12px;font-weight:600;font-size:13px;">Coordinates</label>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                    <div>
                                        <label style="font-size:12px;color:var(--text-secondary);">Latitude *</label>
                                        <input wire:model.blur="latitude" type="number" step="0.00001"
                                            placeholder="e.g. 6.1234" style="margin-top:4px;">
                                        @error('latitude')
                                            <span style="color:var(--danger);font-size:11px;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label style="font-size:12px;color:var(--text-secondary);">Longitude *</label>
                                        <input wire:model.blur="longitude" type="number" step="0.00001"
                                            placeholder="e.g. 7.5678" style="margin-top:4px;">
                                        @error('longitude')
                                            <span style="color:var(--danger);font-size:11px;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <small style="color:var(--text-secondary);display:block;margin-top:8px;">Use Google Maps
                                    to find coordinates (right-click → coordinates)</small>
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;margin-top:24px;">
                            <button type="button" wire:click="previousStep"
                                class="btn btn-outline">Previous</button>
                            <button type="button" wire:click="nextStep" class="btn btn-primary"
                                style="margin-left:auto;">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Owner Information -->
                @elseif($step === 3)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-home"
                                style="color:var(--accent);margin-right:8px;"></i>Property Owner Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Owner Name *</label>
                                <input wire:model.blur="owner_name" type="text"
                                    placeholder="Property owner's name">
                                @error('owner_name')
                                    <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Owner Phone *</label>
                                <input wire:model.blur="owner_phone" type="tel"
                                    placeholder="Property owner's phone">
                                @error('owner_phone')
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

                    <!-- Step 4: Property Images -->
                @elseif($step === 4)
                    <div class="card">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-images"
                                style="color:var(--accent);margin-right:8px;"></i>Property Images</h3>

                        <div style="margin-bottom:20px;">
                            <label style="display:block;margin-bottom:12px;font-weight:600;">Upload Property Photos
                                (Max 5)</label>
                            <div
                                style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:24px;text-align:center;">
                                <div style="margin-bottom:12px;font-size:24px;color:var(--text-secondary);">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <input type="file" wire:model="uploadedImages" multiple accept="image/*"
                                    style="display:none;" id="imageInput">
                                <label for="imageInput"
                                    style="cursor:pointer;color:var(--accent);font-weight:600;">Click to select
                                    images</label>
                                <small style="display:block;margin-top:8px;color:var(--text-secondary);">or drag and
                                    drop</small>
                            </div>
                            @error('uploadedImages.*')
                                <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                            @enderror
                        </div>

                        @if (count($uploadedImages) > 0)
                            <div style="margin-bottom:20px;">
                                <button type="button" wire:click="addPropertyImage" wire:loading.attr="disabled"
                                    class="btn btn-secondary btn-sm">
                                    <span wire:loading.remove><i class="fas fa-upload"></i> Upload Images</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Uploading...</span>
                                </button>
                            </div>
                        @endif

                        <!-- Uploaded Images Preview -->
                        @if (count($property_images) > 0)
                            <div style="margin-bottom:20px;">
                                <label
                                    style="display:block;margin-bottom:12px;font-weight:600;font-size:13px;">Uploaded
                                    Images ({{ count($property_images) }})</label>
                                <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:12px;">
                                    @foreach ($property_images as $index => $image)
                                        <div
                                            style="position:relative;border-radius:var(--radius-sm);overflow:hidden;aspect-ratio:1;">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                style="width:100%;height:100%;object-fit:cover;" alt="Property image">
                                            <button type="button" wire:click="removeImage({{ $index }})"
                                                style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,0.6);color:white;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

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
                            class="fas fa-info-circle" style="margin-right:6px;"></i>About This Service</h4>
                    <p style="font-size:13px;line-height:1.6;color:var(--text-secondary);">
                        Address indexing enables your property to be found on Google Maps and integrated into our
                        comprehensive street management database.
                    </p>
                </div>

                <div class="card">
                    <h4 style="font-size:13px;font-weight:700;margin-bottom:12px;"><i class="fas fa-list"
                            style="color:var(--accent);margin-right:6px;"></i>Checklist</h4>
                    <ul style="font-size:12px;line-height:1.8;color:var(--text-secondary);">
                        <li><span style="color:{{ $step >= 1 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Your contact information</li>
                        <li><span style="color:{{ $step >= 2 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Complete address</li>
                        <li><span style="color:{{ $step >= 3 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Owner details</li>
                        <li><span style="color:{{ $step >= 4 ? 'var(--accent)' : 'var(--text-secondary)' }};">✓</span>
                            Property photos</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
