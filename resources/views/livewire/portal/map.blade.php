<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-map-location-dot" style="color:var(--accent);margin-right:10px;"></i>Interactive Town Map</h2>
            <p>Explore Njikoka LGA towns and street coverage visually</p>
        </div>
        <div style="display:flex; gap:10px;">
            <select wire:model.live="selectedTown" class="form-control" style="width:200px; padding: 10px; border-radius: var(--radius-sm); border: 1.5px solid var(--border);">
                <option value="">All Towns</option>
                @foreach($towns as $town)
                    <option value="{{ $town }}">{{ $town }}</option>
                @endforeach
            </select>
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-secondary);"></i>
                <input type="text" wire:model.live="search" placeholder="Search streets/houses..." 
                       style="padding:10px 10px 10px 35px; border-radius: var(--radius-sm); border: 1.5px solid var(--border); width:250px;">
            </div>
        </div>
    </div>

    <div class="card" style="padding:0; overflow:hidden; position:relative; height:calc(100vh - 250px); min-height:500px;">
        <div id="portal-map" style="width:100%; height:100%; z-index:1;" wire:ignore></div>
        
        <!-- Legend Overlay -->
        <div style="position:absolute; bottom:20px; right:20px; background:rgba(255,255,255,0.9); padding:15px; border-radius:var(--radius); border:1px solid var(--border); z-index:1000; box-shadow:var(--shadow);">
            <div style="font-size:12px; font-weight:700; margin-bottom:10px; color:var(--text-primary);">MAP LEGEND</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-secondary);">
                    <div style="width:12px; height:12px; border-radius:50%; background:#005191; border:2px solid #fff; box-shadow:0 0 0 1px #005191;"></div>
                    <span>Approved Streets</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-secondary);">
                    <div style="width:12px; height:12px; border-radius:50%; background:#f59e0b; border:2px solid #fff; box-shadow:0 0 0 1px #f59e0b;"></div>
                    <span>Active Addresses</span>
                </div>
            </div>
        </div>

        <!-- Stats Overlay -->
        <div style="position:absolute; top:20px; left:20px; background:rgba(255,255,255,0.9); padding:12px 20px; border-radius:var(--radius); border:1px solid var(--border); z-index:1000; display:flex; gap:20px;">
            <div>
                <div style="font-size:10px; text-transform:uppercase; color:var(--text-secondary);">Streets</div>
                <div style="font-size:18px; font-weight:700; color:var(--accent);">{{ count($mapData['streets']) }}</div>
            </div>
            <div style="width:1px; background:var(--border);"></div>
            <div>
                <div style="font-size:10px; text-transform:uppercase; color:var(--text-secondary);">Addresses</div>
                <div style="font-size:18px; font-weight:700; color:var(--accent);">{{ count($mapData['addresses']) }}</div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            const mapContainer = document.getElementById('portal-map');
            if (!mapContainer) return;

            // Initialize map
            const map = L.map('portal-map').setView([6.2209, 7.0670], 13); // LGA coordinates
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const streetIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background:#005191;width:15px;height:15px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 5px rgba(0,0,0,0.3);'></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const addressIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background:#f59e0b;width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 5px rgba(0,0,0,0.3);'></div>",
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            let markers = [];

            function renderMarkers(data) {
                // Clear existing markers
                markers.forEach(m => map.removeLayer(m));
                markers = [];

                const bounds = L.latLngBounds();
                let hasData = false;

                // Render Streets
                data.streets.forEach(street => {
                    if (street.latitude && street.longitude) {
                        const marker = L.marker([street.latitude, street.longitude], {icon: streetIcon})
                            .addTo(map)
                            .bindPopup(`
                                <div style="padding:5px;">
                                    <div style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);">Street</div>
                                    <div style="font-weight:700;font-size:14px;margin-bottom:5px;">${street.name}</div>
                                    <div style="font-size:12px;color:var(--text-primary);">Town: ${street.town}</div>
                                    <div style="font-size:12px;color:var(--text-primary);">Code: ${street.code}</div>
                                    <div style="margin-top:10px;">
                                        <a href="/portal/verification?type=street&query=${street.code}" style="color:var(--accent);font-size:12px;font-weight:700;text-decoration:none;">
                                            <i class="fas fa-shield-check"></i> Verify Details
                                        </a>
                                    </div>
                                </div>
                            `);
                        markers.push(marker);
                        bounds.extend([street.latitude, street.longitude]);
                        hasData = true;
                    }
                });

                // Render Addresses
                data.addresses.forEach(address => {
                    if (address.latitude && address.longitude) {
                        const marker = L.marker([address.latitude, address.longitude], {icon: addressIcon})
                            .addTo(map)
                            .bindPopup(`
                                <div style="padding:5px;">
                                    <div style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);">Address</div>
                                    <div style="font-weight:700;font-size:14px;margin-bottom:5px;">${address.house_number}, ${address.street ? address.street.name : 'Unknown Street'}</div>
                                    <div style="font-size:12px;color:var(--text-primary);">Owner: ${address.owner_name}</div>
                                    <div style="margin-top:10px;">
                                        <a href="/portal/verification?type=address&query=${address.house_number}" style="color:var(--accent);font-size:12px;font-weight:700;text-decoration:none;">
                                            <i class="fas fa-shield-check"></i> Verify Details
                                        </a>
                                    </div>
                                </div>
                            `);
                        markers.push(marker);
                        bounds.extend([address.latitude, address.longitude]);
                        hasData = true;
                    }
                });

                if (hasData) {
                    map.fitBounds(bounds, {padding: [50, 50]});
                }
            }

            // Initial render
            renderMarkers(@json($mapData));

            // Listen for Livewire updates
            Livewire.on('map-updated', (data) => {
                renderMarkers(data);
            });
        });
    </script>
    @endpush
</div>
