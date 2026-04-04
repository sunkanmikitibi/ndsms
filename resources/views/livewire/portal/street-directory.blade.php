<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-list-ul" style="color:var(--accent);margin-right:10px;"></i>Street Directory</h2>
            <p>Browse all officially registered streets in Njikoka LGA</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="dir-stats-row">
        <div class="dir-stat-chip"><i class="fas fa-road" style="color:var(--accent);"></i> <strong>{{ $totals['streets'] }}</strong> Streets</div>
        <div class="dir-stat-chip"><i class="fas fa-map-marker-alt" style="color:var(--accent);"></i> <strong>{{ $totals['addresses'] }}</strong> Addresses</div>
        <div class="dir-stat-chip"><i class="fas fa-map" style="color:var(--accent);"></i> <strong>{{ $totals['wards'] }}</strong> Wards</div>
    </div>

    <!-- Search Bar -->
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
        <div class="search-box" style="max-width:none;flex:1;">
            <i class="fas fa-search"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by street name or code…">
        </div>
        <select wire:model.live="filterWard" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
            <option value="">All Wards</option>
            @foreach($wards as $ward)
                <option value="{{ $ward }}">{{ $ward }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterType" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
            <option value="">All Types</option>
            <option value="street">Street</option>
            <option value="avenue">Avenue</option>
            <option value="road">Road</option>
            <option value="lane">Lane</option>
            <option value="close">Close</option>
            <option value="crescent">Crescent</option>
        </select>
    </div>

    <!-- Grid -->
    @if($streets->isEmpty())
    <div class="empty-state">
        <i class="fas fa-road"></i>
        <h4>No streets found</h4>
        <p>Try adjusting your search or filters.</p>
    </div>
    @else
    <div class="dir-grid">
        @foreach($streets as $street)
        <div class="dir-card">
            <div class="dir-card-header">
                <div>
                    <div class="dir-card-name">{{ $street->name }}</div>
                    <div class="dir-card-code">{{ $street->code ?? 'No code' }}</div>
                </div>
                <span class="ward-badge" style="background:var(--info-light);color:var(--info);">{{ ucfirst($street->type) }}</span>
            </div>
            <div style="margin-top:12px;font-size:13px;color:var(--text-secondary);">
                <i class="fas fa-map" style="width:16px;"></i> {{ $street->ward }}
            </div>
            <div style="margin-top:6px;font-size:13px;color:var(--text-secondary);">
                <i class="fas fa-home" style="width:16px;"></i>
                <strong style="color:var(--text-primary);">{{ $street->addresses_count }}</strong> registered addresses
            </div>
            @if($street->description)
            <div style="margin-top:10px;font-size:12px;color:var(--text-secondary);line-height:1.5;">{{ Str::limit($street->description, 80) }}</div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="pagination-wrapper" style="margin-top:16px;">
        <p>Showing {{ $streets->firstItem() }}–{{ $streets->lastItem() }} of {{ $streets->total() }} streets</p>
        <div class="pagination-links">{{ $streets->links() }}</div>
    </div>
    @endif
</div>
