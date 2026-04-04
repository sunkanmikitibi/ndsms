<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gradient-to-b from-neutral-950 via-neutral-900 to-neutral-950 antialiased">
    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-600/5 rounded-full blur-3xl animate-pulse"
            style="animation-delay: 1s;"></div>
    </div>

    <div class="relative flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <!-- Logo Section -->
            <div class="flex flex-col items-center gap-3 mb-2">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center transition-all duration-300" wire:navigate>
                    <img src="{{ asset('logo.jpeg') }}" alt="NDSMS Logo"
                        class="h-14 w-auto rounded-lg shadow-lg hover:shadow-emerald-500/25">
                </a>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </div>

            <!-- Form Container -->
            <div
                class="flex flex-col gap-6 bg-gradient-to-b from-neutral-900/50 to-neutral-950/50 backdrop-blur-xl border border-neutral-800/50 rounded-2xl p-8 shadow-2xl">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
