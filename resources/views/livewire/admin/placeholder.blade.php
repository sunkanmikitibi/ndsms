<x-layouts.admin :title="$title">
    <div class="page-header">
        <div>
            <h2><i class="fas {{ $icon }}" style="color:var(--accent);margin-right:10px;"></i>{{ $title }}</h2>
            <p>This section is under active development.</p>
        </div>
    </div>
    <div class="card" style="text-align:center;padding:80px 20px;">
        <div class="empty-state">
            <i class="fas {{ $icon }}"></i>
            <h4>{{ $title }} — Coming Soon</h4>
            <p>This module is under active development and will be available shortly.</p>
        </div>
    </div>
</x-layouts.admin>
