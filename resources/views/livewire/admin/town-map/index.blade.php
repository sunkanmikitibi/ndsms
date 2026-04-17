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

    <!-- Map Container -->
    <div class="card" style="margin-bottom:24px;padding:0;overflow:hidden;">
        <div id="town-map" style="width:100%;height:500px;"></div>
        @if ($selectedTown)
            <div style="padding:12px 16px;background:var(--bg-input);border-top:1px solid var(--border);">
                <p style="font-size:12px;color:var(--text-secondary);margin:0;">Town: <span
                        style="font-weight:700;">{{ $selectedTown }}</span> | Showing {{ $mapData['street_count'] }}
                    streets and {{ $mapData['address_count'] }} addresses</p>
            </div>
        @else
            <div style="padding:12px 16px;background:var(--bg-input);border-top:1px solid var(--border);">
                <p style="font-size:12px;color:var(--text-secondary);margin:0;">All Towns | Showing
                    {{ $mapData['street_count'] }} streets and {{ $mapData['address_count'] }} addresses</p>
            </div>
        @endif
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

@script
    <script type="module">
        import L from 'leaflet';
        import 'leaflet/dist/leaflet.css';

        // Initialize map when component is ready
        document.addEventListener('livewire:loaded', function() {
            initializeMap();
        });

        // Re-initialize map when Livewire updates
        document.addEventListener('livewire:updated', function() {
            if (window.townMap) {
                window.townMap.remove();
            }
            initializeMap();
        });

        function initializeMap() {
            const mapElement = document.getElementById('town-map');
            if (!mapElement) return;

            // Default center (Nigeria coordinates)
            const defaultCenter = [9.0820, 8.6753];
            const defaultZoom = 6;

            // Create map
            window.townMap = L.map('town-map').setView(defaultCenter, defaultZoom);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(window.townMap);

            // Get data from Livewire
            const streets = @js($streets);
            const addresses = @js($addresses);

            // Create marker groups
            const streetMarkers = L.layerGroup();
            const addressMarkers = L.layerGroup();

            // Add street markers
            streets.forEach(street => {
                if (street.start_latitude && street.start_longitude) {
                    const marker = L.marker([street.start_latitude, street.start_longitude], {
                        icon: L.divIcon({
                            className: 'street-marker',
                            html: '<div style="background-color: var(--accent); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">🏛️</div>',
                            iconSize: [40, 20],
                            iconAnchor: [20, 20]
                        })
                    });

                    marker.bindPopup(`
                    <div style="font-family: system-ui, sans-serif; max-width: 200px;">
                        <h4 style="margin: 0 0 8px 0; color: var(--accent); font-size: 14px;">${street.name}</h4>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Code:</strong> ${street.code}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Town:</strong> ${street.town}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Type:</strong> ${street.type}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Status:</strong> <span style="color: ${street.status === 'approved' ? 'var(--accent)' : 'var(--text-secondary)'};">${street.status}</span></p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Addresses:</strong> ${street.addresses_count}</p>
                        ${street.distance ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Distance:</strong> ${street.distance}km</p>` : ''}
                    </div>
                `);

                    streetMarkers.addLayer(marker);
                }
            });

            // Add address markers
            addresses.forEach(address => {
                if (address.latitude && address.longitude) {
                    const marker = L.marker([address.latitude, address.longitude], {
                        icon: L.divIcon({
                            className: 'address-marker',
                            html: '<div style="background-color: var(--info); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">🏠</div>',
                            iconSize: [35, 20],
                            iconAnchor: [17, 20]
                        })
                    });

                    marker.bindPopup(`
                    <div style="font-family: system-ui, sans-serif; max-width: 200px;">
                        <h4 style="margin: 0 0 8px 0; color: var(--info); font-size: 14px;">${address.house_number}</h4>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Street:</strong> ${address.street?.name || 'N/A'}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Town:</strong> ${address.town}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Owner:</strong> ${address.owner_name}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Phone:</strong> ${address.owner_phone}</p>
                        <p style="margin: 4px 0; font-size: 12px;"><strong>Status:</strong> <span style="color: ${address.status === 'approved' ? 'var(--accent)' : 'var(--text-secondary)'};">${address.status}</span></p>
                        ${address.last_verified_at ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Verified:</strong> ${new Date(address.last_verified_at).toLocaleDateString()}</p>` : ''}
                    </div>
                `);

                    addressMarkers.addLayer(marker);
                }
            });

            // Add marker groups to map
            streetMarkers.addTo(window.townMap);
            addressMarkers.addTo(window.townMap);

            // Add layer control
            const overlays = {
                "Streets": streetMarkers,
                "Addresses": addressMarkers
            };

            L.control.layers(null, overlays, {
                collapsed: false
            }).addTo(window.townMap);

            // Fit bounds if there are markers
            const allMarkers = [...streetMarkers.getLayers(), ...addressMarkers.getLayers()];
            if (allMarkers.length > 0) {
                const group = new L.featureGroup(allMarkers);
                window.townMap.fitBounds(group.getBounds().pad(0.1));
            }

            // Add legend
            const legend = L.control({
                position: 'bottomright'
            });
            legend.onAdd = function() {
                const div = L.DomUtil.create('div', 'info legend');
                div.innerHTML = `
                <div style="background: white; padding: 8px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); font-family: system-ui, sans-serif; font-size: 12px;">
                    <h4 style="margin: 0 0 8px 0; font-weight: bold;">Legend</h4>
                    <div style="display: flex; align-items: center; margin-bottom: 4px;">
                        <span style="background-color: var(--accent); color: white; padding: 2px 6px; border-radius: 2px; margin-right: 8px; font-size: 10px;">🏛️</span>
                        <span>Streets</span>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <span style="background-color: var(--info); color: white; padding: 2px 6px; border-radius: 2px; margin-right: 8px; font-size: 10px;">🏠</span>
                        <span>Addresses</span>
                    </div>
                </div>
            `;
                return div;
            };
            legend.addTo(window.townMap);
        }
    </script>
@endscript
