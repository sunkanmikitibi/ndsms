<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-road" style="color:var(--accent);margin-right:10px;"></i>Register Street</h2>
            <p>Submit a new street registration application for Njikoka LGA</p>
        </div>
    </div>

    @if($submitted && $lastApplication)
    <!-- Success -->
    <div class="card" style="max-width:580px;margin:0 auto;text-align:center;padding:40px;">
        <div style="width:72px;height:72px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent);">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:8px;">Application Submitted!</h3>
        <p style="color:var(--text-secondary);margin-bottom:20px;">Your street registration request for <strong>{{ $lastApplication->street_name }}</strong> has been received and is pending review.</p>
        <div style="background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;margin-bottom:24px;text-align:left;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;">
                <div><span style="color:var(--text-secondary);">Street Name</span><br><strong>{{ $lastApplication->street_name }}</strong></div>
                <div><span style="color:var(--text-secondary);">Town</span><br><strong>{{ $lastApplication->town }}</strong></div>
                <div><span style="color:var(--text-secondary);">Type</span><br><strong>{{ ucfirst($lastApplication->type) }}</strong></div>
                <div><span style="color:var(--text-secondary);">Status</span><br><span class="status-badge pending">Pending Review</span></div>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <button wire:click="newApplication" class="btn btn-outline"><i class="fas fa-plus"></i> New Application</button>
            <a href="{{ route('portal.dashboard') }}" class="btn btn-primary">View My Requests</a>
        </div>
    </div>

    @else
    <!-- Form -->
    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;"><i class="fas fa-road" style="color:var(--accent);margin-right:8px;"></i>Street Details</h3>
            <form wire:submit="submit">
                <div class="form-grid">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Street Name *</label>
                        <input wire:model.blur="street_name" type="text" placeholder="e.g. Nnewi Road">
                        @error('street_name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Town *</label>
                        <input wire:model="town" type="text" placeholder="e.g. Abagana Town">
                        @error('town')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Type *</label>
                        <select wire:model="type">
                            <option value="street">Street</option>
                            <option value="avenue">Avenue</option>
                            <option value="road">Road</option>
                            <option value="lane">Lane</option>
                            <option value="close">Close</option>
                            <option value="crescent">Crescent</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Description</label>
                        <textarea wire:model="description" rows="4" placeholder="Brief description of the street location, landmarks, etc." style="resize:vertical;"></textarea>
                    </div>

                    <!-- Map Coordinates Selection -->
                    <div class="form-group" style="grid-column: 1/-1;" x-data="{
                        map: null,
                        startMarker: null,
                        endMarker: null,
                        polyline: null,
                        selectionMode: 'start', // 'start' or 'end'
                        initMap() {
                            let defaultLat = 6.1667;
                            let defaultLng = 7.0000;
                            this.map = L.map($refs.mapContainer).setView([defaultLat, defaultLng], 12);
                            
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(this.map);

                            // Recover state if Livewire already has coords
                            if ($wire.start_latitude && $wire.start_longitude) {
                                this.setMarker('start', $wire.start_latitude, $wire.start_longitude);
                            }
                            if ($wire.end_latitude && $wire.end_longitude) {
                                this.setMarker('end', $wire.end_latitude, $wire.end_longitude);
                            }

                            this.map.on('click', (e) => {
                                this.setMarker(this.selectionMode, e.latlng.lat, e.latlng.lng);
                                if (this.selectionMode === 'start') {
                                    $wire.set('start_latitude', e.latlng.lat);
                                    $wire.set('start_longitude', e.latlng.lng);
                                    this.selectionMode = 'end'; // auto switch to end
                                } else {
                                    $wire.set('end_latitude', e.latlng.lat);
                                    $wire.set('end_longitude', e.latlng.lng);
                                    this.selectionMode = 'start';
                                }
                                this.calcDistance();
                            });
                        },
                        setMarker(type, lat, lng) {
                            if (type === 'start') {
                                if (this.startMarker) this.map.removeLayer(this.startMarker);
                                this.startMarker = L.marker([lat, lng], {icon: L.icon({
                                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
                                })}).addTo(this.map);
                            } else {
                                if (this.endMarker) this.map.removeLayer(this.endMarker);
                                this.endMarker = L.marker([lat, lng], {icon: L.icon({
                                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
                                })}).addTo(this.map);
                            }
                            this.calcDistance();
                        },
                        calcDistance() {
                            if (this.startMarker && this.endMarker) {
                                let start = this.startMarker.getLatLng();
                                let end = this.endMarker.getLatLng();
                                let dist = start.distanceTo(end); // in meters
                                $wire.set('distance', (dist).toFixed(2));

                                if (this.polyline) this.map.removeLayer(this.polyline);
                                this.polyline = L.polyline([start, end], {color: 'red', weight: 3, opacity: 0.7, dashArray: '10, 10'}).addTo(this.map);

                                // fit bounds
                                this.map.fitBounds([start, end], {padding: [50, 50]});
                            }
                        },
                        selectMode(mode) {
                            this.selectionMode = mode;
                        }
                    }" x-init="initMap()">
                        <label>STREET EXTENT (START & END POINTS)</label>
                        
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <button type="button" @click="selectMode('start')" :style="selectionMode === 'start' ? 'background:var(--success);color:#fff;border:none;' : 'background:var(--bg-input);border:1px solid var(--border);'" style="flex:1; padding:8px; border-radius:4px; font-size:12px; cursor:pointer;">
                                <i class="fas fa-map-marker-alt"></i> Set Start Point
                            </button>
                            <button type="button" @click="selectMode('end')" :style="selectionMode === 'end' ? 'background:var(--danger);color:#fff;border:none;' : 'background:var(--bg-input);border:1px solid var(--border);'" style="flex:1; padding:8px; border-radius:4px; font-size:12px; cursor:pointer;">
                                <i class="fas fa-map-marker-alt"></i> Set End Point
                            </button>
                        </div>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:10px; font-size:12px;">
                            <div>
                                <span style="display:block;margin-bottom:4px;color:var(--text-secondary);">Start (Lat/Lng)</span>
                                <input type="text" wire:model="start_latitude" placeholder="Lat" readonly style="background:var(--bg-card);opacity:0.8;padding:4px;font-size:11px;">
                                <input type="text" wire:model="start_longitude" placeholder="Lng" readonly style="background:var(--bg-card);opacity:0.8;padding:4px;font-size:11px;margin-top:2px;">
                            </div>
                            <div>
                                <span style="display:block;margin-bottom:4px;color:var(--text-secondary);">End (Lat/Lng)</span>
                                <input type="text" wire:model="end_latitude" placeholder="Lat" readonly style="background:var(--bg-card);opacity:0.8;padding:4px;font-size:11px;">
                                <input type="text" wire:model="end_longitude" placeholder="Lng" readonly style="background:var(--bg-card);opacity:0.8;padding:4px;font-size:11px;margin-top:2px;">
                            </div>
                            <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; background:var(--accent-light); border-radius:var(--radius-sm); border:1px solid var(--accent); padding:10px;">
                                <span style="font-size:10px;color:var(--accent);text-transform:uppercase;font-weight:700;">Distance</span>
                                <div style="font-size:16px;font-weight:800;color:var(--accent);margin-top:4px;">
                                    <span x-text="$wire.distance ? $wire.distance + ' m' : '---'"></span>
                                </div>
                            </div>
                        </div>

                        <div wire:ignore>
                            <div x-ref="mapContainer" style="height:350px; border-radius:var(--radius); border:1px solid var(--border); z-index:1;"></div>
                            <p style="font-size:12px; color:var(--text-secondary); margin-top:8px;"><i class="fas fa-info-circle"></i> Click to place start point, click again to place end point. Distance is computed automatically.</p>
                        </div>
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="fas fa-paper-plane"></i> Submit Application</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin"></i> Submitting…</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info panel -->
        <div>
            <div class="card" style="margin-bottom:16px;">
                <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--accent);"><i class="fas fa-info-circle" style="margin-right:6px;"></i>What Happens Next?</h4>
                <ol style="padding-left:18px;font-size:13px;color:var(--text-secondary);line-height:1.9;">
                    <li>Your application is reviewed by the Registry Office</li>
                    <li>You may be asked for additional documents</li>
                    <li>Payment is required after approval</li>
                    <li>Street is registered and you receive a certificate</li>
                </ol>
            </div>
            <div class="card" style="background:var(--accent-gold-light);border-color:var(--accent-gold);">
                <h4 style="font-size:13px;font-weight:700;color:var(--accent-gold);margin-bottom:6px;"><i class="fas fa-receipt" style="margin-right:6px;"></i>Processing Fee</h4>
                <div style="font-size:22px;font-weight:800;font-family:'Space Mono',monospace;color:var(--accent-gold);">₦{{ number_format($feeAmount, 2) }}</div>
                <p style="font-size:12px;color:var(--text-secondary);margin-top:4px;">Payable after approval</p>
            </div>
        </div>
    </div>
    @endif
</div>
