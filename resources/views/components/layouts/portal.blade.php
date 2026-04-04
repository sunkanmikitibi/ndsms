@props(['title' => 'Portal'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode: localStorage.getItem('ndsmsPortalDark') === '1',
        sidebarOpen: false,
        currentSection: '{{ request()->routeIs('portal.dashboard') ? 'dashboard' : (request()->segment(2) ?? 'dashboard') }}'
    }"
    x-init="
        $watch('darkMode', v => {
            document.documentElement.classList.toggle('dark', v);
            localStorage.setItem('ndsmsPortalDark', v ? '1' : '0');
        });
        document.documentElement.classList.toggle('dark', darkMode);
    "
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — NDSMS Portal</title>
    <meta name="description" content="Njikoka Digital Street Management System — Citizen Portal">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/css/portal.css'])
    @livewireStyles
</head>
<body>

<!-- Mobile Toggle -->
<button class="mobile-toggle" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
    <i class="fas fa-bars"></i>
</button>

<!-- Mobile Overlay -->
<div class="mobile-overlay" :class="{ open: sidebarOpen }" @click="sidebarOpen = false"></div>

<!-- ===================== SIDEBAR ===================== -->
<aside class="sidebar" :class="{ open: sidebarOpen }">

    <!-- Logo -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">N</div>
            <div class="sidebar-logo-text">
                <h1>NDSMS</h1>
                <span>Njikoka LGA</span>
            </div>
        </div>
        <!-- Notification Bell -->
        <button class="notif-bell" type="button" style="margin-left:auto;" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notif-count">0</span>
        </button>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="nav-section-title">My Account</div>
        <a href="{{ route('portal.dashboard') }}"
           class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard &amp; Certificates
        </a>

        <div class="nav-section-title">Public</div>
        <a href="{{ route('portal.register-address') }}"
           class="nav-item {{ request()->routeIs('portal.register-address') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt"></i> Register Address
        </a>
        <a href="{{ route('portal.register-street') }}"
           class="nav-item {{ request()->routeIs('portal.register-street') ? 'active' : '' }}">
            <i class="fas fa-road"></i> Register Street
        </a>
        <a href="{{ route('portal.verification') }}"
           class="nav-item {{ request()->routeIs('portal.verification') ? 'active' : '' }}">
            <i class="fas fa-shield-check"></i> Verification
        </a>
        <a href="{{ route('portal.qr-scanner') }}"
           class="nav-item {{ request()->routeIs('portal.qr-scanner') ? 'active' : '' }}">
            <i class="fas fa-qrcode"></i> QR Scanner
        </a>
        <a href="{{ route('portal.street-directory') }}"
           class="nav-item {{ request()->routeIs('portal.street-directory') ? 'active' : '' }}">
            <i class="fas fa-list-ul"></i> Street Directory
        </a>
        <a href="{{ route('portal.fee-schedule') }}"
           class="nav-item {{ request()->routeIs('portal.fee-schedule') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Fee Schedule
        </a>
        <a href="{{ route('portal.map') }}"
           class="nav-item {{ request()->routeIs('portal.map') ? 'active' : '' }}">
            <i class="fas fa-map"></i> Interactive Map
        </a>
        <a href="{{ route('portal.ai-lookup') }}"
           class="nav-item {{ request()->routeIs('portal.ai-lookup') ? 'active' : '' }}">
            <i class="fas fa-robot"></i> AI Address Lookup
        </a>
        <a href="{{ route('portal.complaints') }}"
           class="nav-item {{ request()->routeIs('portal.complaints') ? 'active' : '' }}">
            <i class="fas fa-comment-dots"></i> Complaints &amp; Feedback
        </a>

        @hasrole('field-officer|super-admin')
        <div class="nav-section-title" style="margin-top:20px;">Field Operations</div>
        <a href="{{ route('portal.field-agent-forms') }}"
           class="nav-item {{ request()->routeIs('portal.field-agent-forms') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Field Agent Forms
        </a>
        @endhasrole
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        @auth
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                <span class="sidebar-user-id">Account: USR-{{ str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin:14px 0 10px;">
            <span style="font-size:11px;opacity:.7;">Dark Mode</span>
            <button class="dark-toggle" :class="{ on: darkMode }" @click="darkMode = !darkMode" type="button"></button>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </form>
        @endauth
        <div style="margin-top:12px;opacity:0.4;font-size:10px;">© {{ date('Y') }} Njikoka LGA · NDSMS v1.0</div>
    </div>
</aside>

<!-- ===================== MAIN ===================== -->
<main class="main">
    {{ $slot }}
</main>

<!-- Toast container -->
<div id="toast-container" class="toast-container"></div>

@livewireScripts
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('toast', ({ type, message }) => {
        const tc = document.getElementById('toast-container');
        const t  = document.createElement('div');
        t.className = `toast ${type}`;
        const icons = { success: 'check-circle', error: 'times-circle', info: 'info-circle', warning: 'exclamation-triangle' };
        t.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${message}`;
        tc.appendChild(t);
        setTimeout(() => t.remove(), 3200);
    });
});
</script>
</body>
</html>
