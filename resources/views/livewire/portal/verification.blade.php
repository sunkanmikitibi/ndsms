<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-shield-check" style="color:var(--accent);margin-right:10px;"></i>Address Verification</h2>
            <p>Verify the authenticity of any registered address in Njikoka LGA</p>
        </div>
    </div>

    <div class="verify-card">
        <h3 style="font-size:17px;font-weight:700;margin-bottom:6px;">Look Up an Address</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">Enter a house number, street name, or address code to verify its registration status.</p>

        <form wire:submit="search" style="display:flex;gap:10px;flex-wrap:wrap;">
            <input wire:model="lookup" type="text"
                placeholder="Enter house number or street name…"
                style="flex:1;min-width:200px;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-size:15px;background:var(--bg-input);color:var(--text-primary);">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove><i class="fas fa-search"></i> Verify</span>
                <span wire:loading><i class="fas fa-spinner fa-spin"></i></span>
            </button>
            @if($searched)
            <button type="button" wire:click="reset_search" class="btn btn-outline"><i class="fas fa-times"></i> Clear</button>
            @endif
        </form>
        @error('lookup')<p style="color:var(--danger);font-size:13px;margin-top:6px;">{{ $message }}</p>@enderror

        @if($searched)
        <div class="verify-result">
            @if($result)
            <div style="text-align:center;margin-bottom:20px;">
                <div class="verify-status-icon success"><i class="fas fa-check-circle"></i></div>
                <h3 style="font-size:20px;font-weight:700;color:var(--accent);">Address Verified</h3>
                <p style="color:var(--text-secondary);font-size:14px;">This address is officially registered in the NDSMS database.</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="profile-field">
                    <div class="profile-field-label">House Number</div>
                    <div class="profile-field-value" style="font-family:'Space Mono',monospace;color:var(--accent);">{{ $result->house_number }}</div>
                </div>
                <div class="profile-field">
                    <div class="profile-field-label">Street</div>
                    <div class="profile-field-value">{{ $result->street?->name ?? '—' }}</div>
                </div>
                <div class="profile-field">
                    <div class="profile-field-label">Town</div>
                    <div class="profile-field-value">{{ $result->town }}</div>
                </div>
                <div class="profile-field">
                    <div class="profile-field-label">Owner</div>
                    <div class="profile-field-value">{{ $result->owner_name }}</div>
                </div>
                <div class="profile-field">
                    <div class="profile-field-label">Status</div>
                    <div class="profile-field-value"><span class="status-badge {{ $result->status }}">{{ ucfirst($result->status) }}</span></div>
                </div>
                <div class="profile-field">
                    <div class="profile-field-label">Registered</div>
                    <div class="profile-field-value">{{ $result->created_at->format('d M Y') }}</div>
                </div>
            </div>
            @else
            <div style="text-align:center;">
                <div class="verify-status-icon fail"><i class="fas fa-times-circle"></i></div>
                <h3 style="font-size:20px;font-weight:700;color:var(--danger);">Not Found</h3>
                <p style="color:var(--text-secondary);font-size:14px;margin-top:8px;">No registered address matches your search. Please check the details and try again.</p>
                <a href="{{ route('portal.register-address') }}" class="btn btn-primary" style="margin-top:16px;">
                    <i class="fas fa-plus"></i> Register This Address
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
