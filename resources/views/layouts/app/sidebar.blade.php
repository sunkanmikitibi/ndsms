<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            @if (auth()->check() && (auth()->user()->hasRole('staff') || auth()->user()->hasRole('super-admin')))
                <flux:sidebar.group :heading="__('Staff Portal')" class="grid">
                    <flux:sidebar.item icon="map-pin" :href="route('portal.register-address-indexing')"
                        :current="request()->routeIs('portal.register-address-indexing')" wire:navigate>
                        {{ __('Address Indexing') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="arrow-path" :href="route('portal.street-revalidation')"
                        :current="request()->routeIs('portal.street-revalidation')" wire:navigate>
                        {{ __('Street Revalidation') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="plus-circle" :href="route('portal.register-street')"
                        :current="request()->routeIs('portal.register-street')" wire:navigate>
                        {{ __('Register Street') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="home" :href="route('portal.register-address')"
                        :current="request()->routeIs('portal.register-address')" wire:navigate>
                        {{ __('Register Address') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Tools & Resources')" class="grid">
                    <flux:sidebar.item icon="book-open" :href="route('portal.fee-schedule')"
                        :current="request()->routeIs('portal.fee-schedule')" wire:navigate>
                        {{ __('Fee Schedule') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="map" :href="route('portal.map')"
                        :current="request()->routeIs('portal.map')" wire:navigate>
                        {{ __('Street Directory') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="qr-code" :href="route('portal.qr-scanner')"
                        :current="request()->routeIs('portal.qr-scanner')" wire:navigate>
                        {{ __('QR Scanner') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="chat-bubble-left" :href="route('portal.complaints')"
                        :current="request()->routeIs('portal.complaints')" wire:navigate>
                        {{ __('Complaints') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif

            @if (auth()->check() && auth()->user()->hasRole('admin|super-admin'))
                <flux:sidebar.group :heading="__('Admin Panel')" class="grid">
                    <flux:sidebar.item icon="arrow-right" :href="route('admin.dashboard')"
                        :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('Admin Dashboard') }}
                    </flux:sidebar.item>

                    @can('view addresses')
                        <flux:sidebar.item icon="building-office" :href="route('admin.addresses.index')"
                            :current="request()->routeIs('admin.addresses.index')" wire:navigate>
                            {{ __('Addresses') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="road" :href="route('admin.streets.index')"
                            :current="request()->routeIs('admin.streets.index')" wire:navigate>
                            {{ __('Streets') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('view approvals')
                        <flux:sidebar.item icon="check-circle" :href="route('admin.approvals.index')"
                            :current="request()->routeIs('admin.approvals.index')" wire:navigate>
                            {{ __('Approvals') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('view payments')
                        <flux:sidebar.item icon="credit-card" :href="route('admin.payments.index')"
                            :current="request()->routeIs('admin.payments.index')" wire:navigate>
                            {{ __('Payments') }}
                        </flux:sidebar.item>
                    @endcan

                    @can('view reports')
                        <flux:sidebar.item icon="chart-bar" :href="route('admin.reports.index')"
                            :current="request()->routeIs('admin.reports.index')" wire:navigate>
                            {{ __('Reports') }}
                        </flux:sidebar.item>
                    @endcan

                    @role('super-admin')
                        <flux:sidebar.item icon="users" :href="route('admin.users.index')"
                            :current="request()->routeIs('admin.users.index')" wire:navigate>
                            {{ __('Users & Roles') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings.index')"
                            :current="request()->routeIs('admin.settings.index')" wire:navigate>
                            {{ __('Settings') }}
                        </flux:sidebar.item>
                    @endrole
                </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
