<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-map" style="color:var(--accent);margin-right:10px;"></i>Town Map</h2>
            <p>Geographic distribution of streets and addresses across towns</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card" style="margin-bottom:24px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">
            <!-- Town Select -->
            <div>
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">Select
                    Town</label>
                <select wire:model.live="selectedTown"
                    style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-size:14px;transition:border-color var(--transition);"
                    class="focus:outline-none focus:ring-2" onchange="this.style.borderColor='var(--accent)'">
                    <option value="">-- All Towns --</option>
                    @foreach ($towns as $town)
                        <option value="{{ $town }}">{{ $town }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Street Type Filter -->
            <div>
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">Street
                    Type</label>
                <select wire:model.live="filterType"
                    style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-size:14px;transition:border-color var(--transition);"
                    class="focus:outline-none focus:ring-2" onchange="this.style.borderColor='var(--accent)'">
                    <option value="all">All Types</option>
                    @foreach ($streetTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Export Button -->
            <div style="display:flex;align-items:flex-end;">
                <button wire:click="exportTownData" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- Coverage Statistics -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        <!-- Town Card -->
        <div class="card">
            <div
                style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;letter-spacing:0.5px;margin-bottom:8px;">
                Selected Town</div>
            <div style="font-size:24px;font-weight:800;color:var(--accent);margin-top:8px;">
                {{ $selectedTown ?: 'All Towns' }}
            </div>
        </div>

        <!-- Streets Card -->
        <div class="card" style="background:var(--accent-light);border:1px solid var(--accent);">
            <div
                style="font-size:11px;text-transform:uppercase;color:var(--accent);font-weight:700;letter-spacing:0.5px;margin-bottom:8px;">
                Streets</div>
            <div style="font-size:28px;font-weight:800;color:var(--accent);margin-top:8px;">
                {{ $mapData['street_count'] }}</div>
        </div>

        <!-- Addresses Card -->
        <div class="card" style="background:var(--info-light);border:1px solid var(--info);">
            <div
                style="font-size:11px;text-transform:uppercase;color:var(--info);font-weight:700;letter-spacing:0.5px;margin-bottom:8px;">
                Addresses</div>
            <div style="font-size:28px;font-weight:800;color:var(--info);margin-top:8px;">
                {{ $mapData['address_count'] }}</div>
        </div>

        <!-- Coverage Card -->
        <div class="card" style="background:var(--accent-gold-light);border:1px solid var(--accent-gold);">
            <div
                style="font-size:11px;text-transform:uppercase;color:var(--accent-gold);font-weight:700;letter-spacing:0.5px;margin-bottom:8px;">
                Coverage</div>
            <div style="font-size:28px;font-weight:800;color:var(--accent-gold);margin-top:8px;">
                {{ $mapData['coverage'] }}%</div>
        </div>
    </div>

    <!-- Map Placeholder -->
    <div class="card" style="margin-bottom:24px;padding:0;overflow:hidden;">
        <div
            style="width:100%;height:400px;background:var(--bg-input);display:flex;align-items:center;justify-content:center;">
            <div style="text-align:center;">
                <i class="fas fa-map"
                    style="font-size:48px;color:var(--text-secondary);margin-bottom:16px;display:block;"></i>
                <p style="color:var(--text-secondary);margin-bottom:12px;">Interactive map view (requires Google Maps
                    API integration)</p>
                @if ($selectedTown)
                    <p style="font-size:12px;color:var(--text-secondary);margin-top:8px;">Town: <span
                            style="font-weight:700;">{{ $selectedTown }}</span></p>
                @endif
                <p style="font-size:12px;color:var(--text-secondary);margin-top:8px;">
                    Showing {{ $mapData['street_count'] }} streets and {{ $mapData['address_count'] }} addresses
                </p>
            </div>
        </div>
    </div>

    <!-- Streets & Addresses List -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;">
        <!-- Streets -->
        <div class="card" style="padding:0;overflow:hidden;">
            <div
                style="background:var(--bg-input);padding:16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-road" style="color:var(--accent);"></i>
                <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0;">Streets</h3>
            </div>
            <div style="max-height:400px;overflow-y:auto;border-top:1px solid var(--border);">
                @forelse($streets as $street)
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);transition:background-color var(--transition);cursor:pointer;"
                        onmouseover="this.style.backgroundColor='var(--accent-light)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <div style="font-weight:600;color:var(--text-primary);margin-bottom:4px;">{{ $street->name }}
                        </div>
                        <div style="font-size:12px;color:var(--text-secondary);">
                            <span style="display:inline-block;margin-right:12px;"><i class="fas fa-tag"></i>
                                {{ $street->code }}</span>
                            <span style="display:inline-block;"><i class="fas fa-map-marker"></i>
                                {{ $street->town }}</span>
                        </div>
                        <div style="font-size:11px;margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                            <span
                                style="display:inline-flex;align-items:center;padding:4px 8px;border-radius:12px;background:var(--info-light);color:var(--info);font-weight:600;">
                                {{ $street->type }}
                            </span>
                            <span
                                style="display:inline-flex;align-items:center;padding:4px 8px;border-radius:12px;{{ $street->status === 'active' ? 'background:var(--accent-light);color:var(--accent);' : 'background:var(--border);color:var(--text-secondary);' }}font-weight:600;">
                                {{ $street->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding:40px 16px;text-align:center;color:var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                        <p>No streets found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Addresses -->
        <div class="card" style="padding:0;overflow:hidden;">
            <div
                style="background:var(--bg-input);padding:16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-home" style="color:var(--accent);"></i>
                <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0;">Addresses</h3>
            </div>
            <div style="max-height:400px;overflow-y:auto;border-top:1px solid var(--border);">
                @forelse($addresses as $address)
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);transition:background-color var(--transition);cursor:pointer;"
                        onmouseover="this.style.backgroundColor='var(--accent-light)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <div style="font-weight:600;color:var(--text-primary);margin-bottom:4px;">
                            {{ $address->house_number }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">
                            <i class="fas fa-road mr-1"></i> {{ $address->street?->name }}
                        </div>
                        <div style="font-size:11px;color:var(--text-secondary);margin-bottom:8px;">
                            <i class="fas fa-user"></i> {{ $address->owner_name }}
                        </div>
                        <div style="font-size:11px;">
                            <span
                                style="display:inline-flex;align-items:center;padding:4px 8px;border-radius:12px;{{ $address->status === 'verified' ? 'background:var(--accent-light);color:var(--accent);' : 'background:var(--info-light);color:var(--info);' }}font-weight:600;">
                                {{ $address->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding:40px 16px;text-align:center;color:var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                        <p>No addresses found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
