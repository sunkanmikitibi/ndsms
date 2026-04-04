@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('adminDark') === '1', sidebarOpen: false }" x-init="$watch('darkMode', v => {
    document.documentElement.classList.toggle('dark', v);
    localStorage.setItem('adminDark', v ? '1' : '0')
});
document.documentElement.classList.toggle('dark', darkMode)"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — NDSMS Admin</title>
    <meta name="description" content="Njikoka Digital Street Management System — Admin Backend">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/admin.css'])
    @livewireStyles
</head>

<body>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle" @click="sidebarOpen = !sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" :class="{ open: sidebarOpen }" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">N</div>
                <div class="sidebar-logo-text">
                    <h1>NDSMS</h1>
                    <span>Admin Portal</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="role-indicator">
                <div class="role-dot {{ auth()->user()?->hasRole('super-admin') ? 'super' : 'admin' }}"></div>
                <div class="role-name">
                    {{ auth()->user()?->name }}
                    <span>{{ auth()->user()?->roles->pluck('name')->map(fn($n) => ucfirst($n))->join(', ') ?: 'Staff' }}</span>
                </div>
            </div>

            <div class="nav-section-title">Main</div>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            @canany(['view addresses', 'manage addresses'])
                <div class="nav-section-title">Address Registry</div>
                <a href="{{ route('admin.addresses.index') }}"
                    class="nav-item {{ request()->routeIs('admin.addresses.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
                <a href="{{ route('admin.streets.index') }}"
                    class="nav-item {{ request()->routeIs('admin.streets.*') ? 'active' : '' }}">
                    <i class="fas fa-road"></i> Streets
                </a>
            @endcanany

            @canany(['view approvals', 'manage approvals', 'submit street applications'])
                <div class="nav-section-title">Workflow</div>
                <a href="{{ route('admin.approvals.index') }}"
                    class="nav-item {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i> Approvals
                    @php $pendingCount = \App\Models\StreetApplication::where('status','pending')->count(); @endphp
                    @if ($pendingCount > 0)
                        <span class="badge">{{ $pendingCount }}</span>
                    @endif
                </a>
                @canany(['view approvals', 'manage approvals'])
                    <a href="{{ route('admin.street-applications.index') }}"
                        class="nav-item {{ request()->routeIs('admin.street-applications.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Street Applications
                    </a>
                    <a href="{{ route('admin.field-reports.index') }}"
                        class="nav-item {{ request()->routeIs('admin.field-reports.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i> Field Reports
                    </a>
                @endcanany
            @endcanany



            @can('view payments')
                <div class="nav-section-title">Financial</div>
                <a href="{{ route('admin.payments.index') }}"
                    class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
            @endcan

            @canany(['manage settings', 'view settings'])
                <a href="{{ route('admin.fee-schedules.index') }}"
                    class="nav-item {{ request()->routeIs('admin.fee-schedules.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i> Fee Schedules
                </a>
            @endcanany

            @can('view reports')
                <div class="nav-section-title">Analytics</div>
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('admin.map.index') }}"
                    class="nav-item {{ request()->routeIs('admin.map.*') ? 'active' : '' }}">
                    <i class="fas fa-map"></i> Ward Map
                </a>
            @endcan

            @role('super-admin')
                <div class="nav-section-title">Administration</div>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="{{ route('admin.roles.index') }}"
                    class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Roles
                </a>
                <a href="{{ route('admin.permissions.index') }}"
                    class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-lock"></i> Permissions
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            @endrole
        </nav>

        <div class="sidebar-footer">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:11px;">Dark Mode</span>
                <button class="dark-toggle" :class="{ on: darkMode }" @click="darkMode = !darkMode"
                    type="button"></button>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </button>
            </form>
            <div style="margin-top:10px;opacity:0.5;">NDSMS v1.0 — Njikoka LGA</div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        {{ $slot }}
    </main>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container"></div>

    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', ({
                type,
                message
            }) => {
                const tc = document.getElementById('toast-container');
                const t = document.createElement('div');
                t.className = `toast ${type}`;
                const icons = {
                    success: 'check-circle',
                    error: 'times-circle',
                    info: 'info-circle'
                };
                t.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${message}`;
                tc.appendChild(t);
                setTimeout(() => t.remove(), 3050);
            });
        });
    </script>
</body>

</html>
