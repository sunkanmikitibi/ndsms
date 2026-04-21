<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-brain" style="color:var(--accent);margin-right:10px;"></i>AI Address Lookup</h2>
            <p>Use natural language to find any registered street or property</p>
        </div>
    </div>

    <div style="max-width:800px; margin:0 auto;">
        <!-- Search Input -->
        <div class="card" style="padding:24px; border:2px solid var(--accent); background:linear-gradient(135deg, var(--bg-card) 0%, rgba(var(--accent-rgb), 0.05) 100%);">
            <div style="position:relative; margin-bottom:12px;">
                <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--accent); font-size:18px;"></i>
                <input type="text" wire:model.live.debounce.300ms="query" 
                       placeholder="e.g. 'The blue house owned by Obi' or 'Abuja road in Oji River'..."
                       style="width:100%; padding:15px 15px 15px 50px; border-radius:var(--radius); border:none; background:var(--bg-input); color:var(--text-primary); font-size:16px; box-shadow:var(--shadow-sm); outline:none; transition:box-shadow var(--transition);"
                       onfocus="this.style.boxShadow='0 0 0 3px rgba(var(--accent-rgb), 0.2)'"
                       onblur="this.style.boxShadow='var(--shadow-sm)'"
                >
                @if($isSearching)
                    <div style="position:absolute; right:16px; top:50%; transform:translateY(-50%);">
                        <i class="fas fa-circle-notch fa-spin" style="color:var(--accent);"></i>
                    </div>
                @endif
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase; letter-spacing:1px; font-weight:700;">
                    <i class="fas fa-microchip"></i> Neural Search Engine Active
                </div>
                <div style="font-size:12px; color:var(--accent); font-weight:600;">
                    Type at least 3 characters
                </div>
            </div>
        </div>

        <!-- Results -->
        <div style="margin-top:30px;">
            @if(empty($results) && strlen($query) >= 3 && !$isSearching)
                <div class="card" style="text-align:center; padding:40px;">
                    <i class="fas fa-search-minus" style="font-size:40px; color:var(--border); margin-bottom:16px;"></i>
                    <h4 style="color:var(--text-primary);">No matches found</h4>
                    <p style="color:var(--text-secondary);">Our AI couldn't find anything matching your description. Try using different keywords.</p>
                </div>
            @endif

            <div style="display:grid; gap:16px;">
                @foreach($results as $index => $result)
                    <div class="card" style="padding:16px; transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;"
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'"
                         onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow)'">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div style="display:flex; gap:16px; align-items:center;">
                                <div style="width:48px; height:48px; border-radius:12px; background:{{ $result['type'] == 'street' ? 'var(--accent)' : 'var(--accent-gold)' }}; color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                    <i class="fas {{ $result['type'] == 'street' ? 'fa-road' : 'fa-house-user' }}"></i>
                                </div>
                                <div>
                                    <div style="font-size:11px; font-weight:700; color:var(--accent); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                                        {{ $result['type'] }} Match
                                    </div>
                                    <h4 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">{{ $result['title'] }}</h4>
                                    <p style="font-size:13px; color:var(--text-secondary);">{{ $result['subtitle'] }}</p>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:10px; color:var(--text-secondary); text-transform:uppercase; margin-bottom:4px;">Confidence</div>
                                <div style="font-size:14px; font-weight:800; color:{{ $result['score'] > 70 ? 'var(--success)' : ($result['score'] > 40 ? 'var(--accent)' : 'var(--text-secondary)') }}">
                                    {{ $result['score'] }}%
                                </div>
                                <div style="width:60px; height:4px; background:var(--border); border-radius:2px; margin-top:4px; overflow:hidden;">
                                    <div style="width:{{ $result['score'] }}%; height:100%; background:{{ $result['score'] > 70 ? 'var(--success)' : 'var(--accent)' }};"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; gap:12px;">
                                @foreach($result['metadata'] as $key => $value)
                                    <div style="font-size:11px; background:var(--bg-input); padding:4px 8px; border-radius:4px; color:var(--text-secondary);">
                                        <span style="font-weight:700; color:var(--text-primary);">{{ str_replace('_', ' ', $key) }}:</span> {{ $value }}
                                    </div>
                                @endforeach
                            </div>
                            <a href="/portal/verification?type={{ $result['type'] }}&query={{ $result['type'] == 'street' ? $result['metadata']['code'] : $result['metadata']['house_number'] }}" 
                               class="btn btn-outline btn-sm" style="font-size:11px; padding:6px 12px;">
                                Full Metadata <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Helper Info -->
        @if(empty($results) && strlen($query) < 3)
            <div style="margin-top:40px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="card" style="padding:20px;">
                    <i class="fas fa-lightbulb" style="color:var(--accent); margin-bottom:12px; font-size:20px;"></i>
                    <h5 style="font-size:14px; font-weight:700; margin-bottom:8px;">Search Tips</h5>
                    <ul style="font-size:12px; color:var(--text-secondary); padding-left:16px; line-height:1.6;">
                        <li>Search by street name or town</li>
                        <li>Search by property owner name</li>
                        <li>Search by house number</li>
                        <li>Combine keywords for better results</li>
                    </ul>
                </div>
                <div class="card" style="padding:20px;">
                    <i class="fas fa-shield-halved" style="color:var(--accent); margin-bottom:12px; font-size:20px;"></i>
                    <h5 style="font-size:14px; font-weight:700; margin-bottom:8px;">Verified Data Only</h5>
                    <p style="font-size:12px; color:var(--text-secondary); line-height:1.6;">
                        The AI engine only indices official, approved records from the NDSMS database. Recent applications may take 24-48 hours to appear.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <style>
        :root {
            --accent-rgb: 0, 81, 145;
        }
    </style>
</div>
