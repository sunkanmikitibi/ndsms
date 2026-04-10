<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NDSMS - Njikoka Digital Street Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Public+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--color-background);
        }

        .tonal-transition-bg {
            background: linear-gradient(to bottom, rgba(250, 249, 246, 0.8), rgba(244, 243, 241, 0.8));
        }

        @media (max-width: 640px) {
            .text-2xl {
                font-size: 1.25rem;
            }
        }
    </style>
    <script defer>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Close menu when a link is clicked
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });
    </script>
</head>

<body class="text-on-surface selection:bg-tertiary-fixed selection:text-on-tertiary-fixed">
    <!-- TopAppBar -->
    <header
        class="docked full-width top-0 sticky z-50 bg-background/80 backdrop-blur-md shadow-[0px_20px_40px_rgba(11,38,25,0.06)]">
        <div class="flex justify-between items-center w-full px-4 md:px-8 py-4 max-w-7xl mx-auto">
            <a href="/" class="flex items-center gap-2 md:gap-3">
                <img src="{{ asset('logo.jpeg') }}" alt="NDSMS Logo" class="h-8 md:h-10 w-auto">
                <span
                    class="text-base md:text-xl font-black text-primary-container tracking-tighter font-headline">NDSMS</span>
            </a>
            <nav class="hidden md:flex items-center space-x-6 lg:space-x-8">
                <a class="text-primary-container border-b-2 border-tertiary-fixed-dim pb-1 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#features">Features</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#how-it-works">How it Works</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#statistics">Statistics</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#faq">FAQ</a>
            </nav>
            <div class="flex items-center gap-2 md:gap-4">
                @if (Route::has('login'))
                    <nav class="hidden sm:flex items-center gap-2 md:gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-3 md:px-5 py-2 text-[11px] md:text-[12px] font-bold uppercase tracking-wider text-primary-container hover:bg-surface-container-low transition-all duration-300 font-headline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-3 md:px-5 py-2 text-[11px] md:text-[12px] font-bold uppercase tracking-wider text-primary-container hover:bg-surface-container-low transition-all duration-300 font-headline">Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-3 md:px-6 py-2 md:py-2.5 bg-primary-container text-on-primary rounded-lg md:rounded-xl font-bold text-[11px] md:text-sm hover:opacity-90 active:scale-[0.99] transition-all duration-300 shadow-sm">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn"
                    class="md:hidden p-2 rounded-lg hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-primary-container">menu</span>
                </button>
            </div>
        </div>
        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-background border-t border-outline">
            <nav class="flex flex-col px-4 py-4 space-y-3">
                <a class="text-primary-container pb-2 uppercase text-xs tracking-wider font-semibold font-headline"
                    href="#features">Features</a>
                <a class="text-on-surface-variant pb-2 uppercase text-xs tracking-wider font-semibold font-headline hover:text-primary-container"
                    href="#how-it-works">How it Works</a>
                <a class="text-on-surface-variant pb-2 uppercase text-xs tracking-wider font-semibold font-headline hover:text-primary-container"
                    href="#statistics">Statistics</a>
                <a class="text-on-surface-variant pb-2 uppercase text-xs tracking-wider font-semibold font-headline hover:text-primary-container"
                    href="#faq">FAQ</a>
                @if (Route::has('login'))
                    @guest
                        <div class="border-t border-outline pt-3 mt-3 flex flex-col gap-2">
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-primary-container hover:bg-surface-container-low transition-all duration-300 font-headline text-center">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-4 py-2 bg-primary-container text-on-primary rounded-lg font-bold text-xs hover:opacity-90 transition-all duration-300 shadow-sm text-center">Register</a>
                            @endif
                        </div>
                    @endguest
                @endif
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-12 md:pt-20 pb-20 md:pb-32 px-4 md:px-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-16 items-center">
                <div class="lg:col-span-7 z-10">
                    <span
                        class="inline-block px-3 py-1 md:px-4 md:py-1.5 bg-secondary-container text-on-secondary-container rounded-full text-[10px] md:text-xs font-bold tracking-widest uppercase mb-4 md:mb-6 font-headline">Official
                        Civic Infrastructure</span>
                    <h1
                        class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-primary leading-[1.1] tracking-tight mb-4 md:mb-8 font-headline">
                        Own Your Address <span class="text-secondary">Define Your Identity</span> Build Njikoka
                        Digitally.
                    </h1>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-black mb-4 md:mb-6">
                        Njikoka is stepping into the future.
                    </h2>
                    <p class="text-sm md:text-base text-on-surface-variant leading-relaxed max-w-2xl mb-6 md:mb-10">
                        The Njikoka Digital Street Management System (NDSMS) is a revolutionary digital platform
                        designed to organize, standardize, and officially register streets, homes, and properties across
                        all communities in Njikoka Local Government.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                        <a href="{{ url('/portal/register-address') }}"
                            class="px-6 md:px-8 py-3 md:py-4 bg-primary-container text-on-primary rounded-lg md:rounded-xl font-bold text-sm md:text-lg flex items-center justify-center gap-2 hover:shadow-xl transition-all duration-300 group">
                            Register My Address
                            <span
                                class="material-symbols-outlined text-tertiary-fixed-dim group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>

                    </div>
                </div>
                <div class="lg:col-span-5 relative mt-8 lg:mt-0">
                    <div
                        class="relative rounded-2xl md:rounded-[2rem] overflow-hidden shadow-2xl aspect-video md:aspect-square">
                        <img alt="Modern Civic Infrastructure" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6iQr-LBow9hQCseWibcj_R0hyk08mM1PlcAvSYP-iJVAX8l1uvSrGfP53M7QYuhR2ccYkU6lpoKtJ1V6zbdFTXDHVKA089xewj-5vTZNMSqp2eKfc6ks5waYI8UHigNaZLeoTn0UZRNDA37BxQnP4BDYUml16GNwmvQsJ5_z-GsPSHBsfUVod3MC5Tn3MWOa44aBA4sPjEEOo1bJw2n6ercbqV4BAt_CQjOURs_eZyBjfiUlo86kP1hXHzF9827UrEVA5nYpDrUM" />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary-container/40 to-transparent"></div>
                    </div>
                    <!-- Decorative Elements -->
                    <div
                        class="mt-4 md:absolute md:-bottom-6 md:-left-6 bg-tertiary-fixed p-4 md:p-6 rounded-xl md:rounded-2xl shadow-xl max-w-[200px]">
                        <p class="text-on-tertiary-fixed text-xs md:text-sm font-bold font-headline leading-tight">
                            Securing
                            Njikoka's digital future, one street at a time.</p>
                    </div>
                </div>
            </div>
        </section>

        <x-why-us />




        <!-- Core Services -->
        <section class="py-16 md:py-24 px-4 md:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12 md:mb-20">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-primary font-headline mb-4 md:mb-6 tracking-tight">
                        System
                        Core Services</h2>
                    <div class="w-24 h-1 bg-tertiary-fixed-dim mx-auto"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-12">
                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span
                                class="material-symbols-outlined text-secondary text-2xl md:text-3xl">location_on</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">Register a
                            New Street
                            Name</h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">Instant
                            verification of
                            residential
                            and commercial addresses for KYC and legal documentation.</p>
                    </div>
                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-2xl md:text-3xl">map</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">Claim and
                            register your
                            House Address
                        </h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">A
                            comprehensive,
                            searchable database
                            of every mapped street within the Njikoka jurisdiction.</p>
                    </div>
                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-2xl md:text-3xl">layers</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">
                            Upload property details and documents
                        </h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">
                            Detailed spatial data visualization
                            per town, highlighting zoning and administrative boundaries.
                        </p>
                    </div>
                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-2xl md:text-3xl">badge</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">
                            Track approval status in real-time
                        </h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">Monitor your
                            approval
                            progress in
                            real time with instant updates as your registration moves through verification
                            and
                            certification.</p>
                    </div>
                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-2xl md:text-3xl">verified</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">
                            Download your Digital Address Certificate
                        </h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">Download your
                            official
                            digital
                            address certificate instantly, complete with verified government approval and a
                            unique QR
                            code for your property.</p>
                    </div>

                    <div class="flex flex-col gap-4 md:gap-6">
                        <div
                            class="w-14 md:w-16 h-14 md:h-16 bg-surface-container-high rounded-lg md:rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-2xl md:text-3xl">verified</span>
                        </div>
                        <h5 class="text-base md:text-xl font-bold text-primary font-headline">
                            Access your QR-coded property identity
                        </h5>
                        <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed">Easily access
                            your
                            QR-coded property
                            identity anytime with a secure digital certificate for your registered address.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="statistics" class="py-12 md:py-20 px-4 md:px-8 bg-primary-container text-on-primary">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 md:gap-12">
                <div class="max-w-md">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold font-headline mb-3 md:mb-4">
                        Modernizing our
                        Community</h2>
                    <p class="text-sm md:text-base text-on-primary/60">Our progress in numbers. We are
                        rapidly
                        expanding our digital
                        footprint to cover every corner of Njikoka.</p>
                </div>
                <div class="grid grid-cols-2 gap-8 md:gap-16">
                    <div class="text-center">
                        <div class="text-4xl md:text-6xl font-black text-tertiary-fixed-dim font-headline mb-2">
                            6
                        </div>
                        <div class="text-[10px] md:text-xs uppercase tracking-widest font-bold text-on-primary/40">
                            Towns in Njikoka
                            <br>
                            Abba | Abagana | Enugwu Ukwu | Enugwu Agidi | Nimo | Nawfia
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl md:text-6xl font-black text-tertiary-fixed-dim font-headline mb-2">
                            18
                        </div>
                        <div class="text-[10px] md:text-xs uppercase tracking-widest font-bold text-on-primary/40">
                            Registered Towns
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works -->
        <section id="how-it-works" class="py-16 md:py-24 px-4 md:px-8 bg-surface">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-12 md:mb-16">
                    <h2
                        class="text-xs font-black text-secondary tracking-[0.2em] uppercase mb-2 md:mb-4 font-headline">
                        Simple
                        Process
                    </h2>
                    <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary font-headline">
                        Powerful Impact.
                    </h3>
                </div>
                <div class="space-y-3 md:space-y-4">
                    <!-- Step 1 -->
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-secondary">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            01</div>
                        <div class="flex-grow">
                            <h4 class="text-base md:text-xl font-bold text-primary font-headline mb-1 md:mb-2">
                                Visit the portal
                            </h4>
                            <p class="text-xs md:text-base text-on-surface-variant">Visit the portal to
                                begin your
                                journey by submitting
                                property details and contact information through our secure digital gateway.
                            </p>
                        </div>
                        <div class="flex-shrink-0 hidden md:block">
                            <span
                                class="material-symbols-outlined text-outline text-3xl md:text-4xl">app_registration</span>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-tertiary-fixed-dim">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            02</div>
                        <div class="flex-grow">
                            <h4 class="text-base md:text-xl font-bold text-primary font-headline mb-1 md:mb-2">
                                Create
                                your account</h4>
                            <p class="text-xs md:text-base text-on-surface-variant">Create your account and
                                complete
                                the administrative
                                processing fee using our integrated, government-approved payment gateway.
                            </p>
                        </div>
                        <div class="flex-shrink-0 hidden md:block">
                            <span class="material-symbols-outlined text-outline text-3xl md:text-4xl">payments</span>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-secondary">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            03</div>
                        <div class="flex-grow">
                            <h4 class="text-base md:text-xl font-bold text-primary font-headline mb-1 md:mb-2">
                                Submit
                                your street or
                                property details</h4>
                            <p class="text-xs md:text-base text-on-surface-variant">Submit your street or
                                property
                                details and a field
                                officer will visit the location to confirm spatial data and street naming
                                compliance.
                            </p>
                        </div>
                        <div class="flex-shrink-0 hidden md:block">
                            <span class="material-symbols-outlined text-outline text-3xl md:text-4xl">fact_check</span>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-tertiary-fixed-dim">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            04</div>
                        <div class="flex-grow">
                            <h4 class="text-base md:text-xl font-bold text-primary font-headline mb-1 md:mb-2">
                                Make payment online
                            </h4>
                            <p class="text-xs md:text-base text-on-surface-variant">Make payment online and
                                download
                                your official Digital
                                Address Certificate, complete with a unique QR code for verification.</p>
                        </div>
                        <div class="flex-shrink-0 hidden md:block">
                            <span
                                class="material-symbols-outlined text-outline text-3xl md:text-4xl">workspace_premium</span>
                        </div>
                    </div>
                    {{-- Step 5 --}}
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-secondary">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            05</div>
                        <div class="flex-grow">
                            <h4 class="text-base md:text-xl font-bold text-primary font-headline mb-1 md:mb-2">
                                Access your QR-coded property identity
                            </h4>
                            <p class="text-xs md:text-base text-on-surface-variant">Access your QR-coded
                                property
                                identity anytime with a
                                secure digital certificate for your registered address.</p>
                        </div>
                    </div>

                    {{-- Step 6 --}}
                    <div
                        class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl flex flex-col md:flex-row gap-4 md:gap-8 items-start md:items-center border-l-8 border-tertiary-fixed-dim">
                        <div
                            class="flex-shrink-0 w-12 md:w-16 h-12 md:h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-xl md:text-2xl font-black font-headline">
                            06</div>
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-primary font-headline mb-2">
                                Receive your digital address
                            </h4>
                            <p class="text-on-surface-variant">Receive your digital address and use your
                                verified
                                digital address for official documentation, deliveries, and more.</p>
                        </div>
                    </div>
                </div>
        </section>

        <!-- Government-Driven Digital Transformation -->
        <section class="py-16 md:py-24 px-4 md:px-8 bg-primary-container text-on-primary">
            <div class="max-w-7xl mx-auto">
                <div class="mb-12 md:mb-16">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black mb-4 md:mb-8 font-headline leading-tight">
                        🏛️ A
                        Government-Driven Digital Transformation</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-12">
                    <div class="bg-on-primary/10 p-6 md:p-10 rounded-2xl md:rounded-3xl border border-on-primary/20">
                        <h4 class="text-lg md:text-2xl font-bold mb-3 md:mb-4 font-headline">Modernizing
                            Njikoka</h4>
                        <p class="text-sm md:text-base text-on-primary/80 leading-relaxed">Bringing
                            cutting-edge
                            technology to every corner
                            of our community, enabling seamless digital governance and modern infrastructure
                            management.
                        </p>
                    </div>
                    <div class="bg-on-primary/10 p-6 md:p-10 rounded-2xl md:rounded-3xl border border-on-primary/20">
                        <h4 class="text-lg md:text-2xl font-bold mb-3 md:mb-4 font-headline">Improving
                            Governance</h4>
                        <p class="text-sm md:text-base text-on-primary/80 leading-relaxed">Establishing
                            transparent,
                            efficient, and
                            accountable systems that empower citizens and strengthen institutional
                            decision-making
                            processes.</p>
                    </div>
                    <div class="bg-on-primary/10 p-6 md:p-10 rounded-2xl md:rounded-3xl border border-on-primary/20">
                        <h4 class="text-lg md:text-2xl font-bold mb-3 md:mb-4 font-headline">Enhancing
                            Data-Driven
                            Planning</h4>
                        <p class="text-sm md:text-base text-on-primary/80 leading-relaxed">Leveraging
                            comprehensive
                            spatial data and
                            analytics to support strategic urban development and evidence-based policy
                            decisions.</p>
                    </div>
                </div>
                <div
                    class="mt-8 md:mt-16 p-6 md:p-12 bg-on-primary/20 rounded-2xl md:rounded-3xl border border-on-primary/40">
                    <p class="text-base md:text-xl font-bold text-on-primary leading-relaxed">It is not
                        just a
                        platform—it is a
                        legacy infrastructure for future generations.</p>
                </div>
            </div>
        </section>

        <!-- Secure Your Street Section -->
        <section class="py-16 md:py-24 px-4 md:px-8 bg-surface">
            <div class="max-w-7xl mx-auto">
                <div
                    class="bg-gradient-to-r from-secondary via-secondary to-tertiary-fixed rounded-2xl md:rounded-[3rem] p-8 md:p-16 lg:p-24 text-on-secondary">
                    <div class="max-w-3xl mx-auto text-center">
                        <h2
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-black mb-4 md:mb-8 font-headline leading-tight">
                            Secure Your
                            Street. Register Your Property Today</h2>
                        <p class="text-sm md:text-base lg:text-lg mb-2 md:mb-4 text-on-secondary/80">Don't
                            wait for
                            others to name your street.</p>
                        <p class="text-sm md:text-base lg:text-lg font-bold mb-6 md:mb-12 text-on-secondary/90">
                            Be
                            among the pioneers shaping the
                            digital future of Njikoka.</p>
                        <div class="flex flex-col md:flex-row justify-center gap-4 md:gap-6 mb-8 md:mb-12">
                            <div class="flex items-start gap-2 md:gap-3">
                                <span class="text-2xl md:text-3xl">👉</span>
                                <div class="text-left">
                                    <p class="font-bold mb-1 text-xs md:text-base">Visit</p>
                                    <p class="text-sm md:text-lg font-headline text-on-secondary font-bold">
                                        www.ndsms.gov.ng</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 md:gap-3">
                                <span class="text-2xl md:text-3xl">👉</span>
                                <div class="text-left">
                                    <p class="font-bold mb-1 text-xs md:text-base">Register</p>
                                    <p class="text-sm md:text-lg font-headline text-on-secondary font-bold">
                                        In minutes
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 md:gap-3">
                                <span class="text-2xl md:text-3xl">👉</span>
                                <div class="text-left">
                                    <p class="font-bold mb-1 text-xs md:text-base">Own</p>
                                    <p class="text-sm md:text-lg font-headline text-on-secondary font-bold">
                                        Your
                                        digital address
                                    </p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ url('/portal/register-address') }}"
                            class="px-6 md:px-10 py-3 md:py-5 bg-on-secondary text-secondary rounded-lg md:rounded-xl font-bold text-sm md:text-xl hover:scale-105 active:scale-95 transition-all duration-300 inline-block">Start
                            Registration Now</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-16 md:py-24 px-4 md:px-8 overflow-hidden relative">
            <div class="max-w-7xl mx-auto">
                <div
                    class="bg-primary-container rounded-2xl md:rounded-[3rem] p-8 md:p-12 lg:p-20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 hidden md:block">
                        <img alt="Digital Infrastructure" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCm7JRHqinbcO8DBgaoparidlG0DWS_hEZGgSX7dWkFageE-6rvvAcHoNcUa8XDZUC5Tk2gdwpuIBzDlBfME-vqbs8w1tpGuIoWW8eu9GEgzlRDlDUsnMZqY120cLg8ToJCrZBmPrKthzsZ2qDBXYIZgBNsSYtukH0j6lqTvJCpH_HeHMOylDgHz8QVNwG655vtmh5TWfnaafzEbNAr84IpbyW-2xL3jpEC_r9UDWgxf8ZWNMfiJyFNqhN0l_FJGhtCrSar3uE6hf8" />
                    </div>
                    <div class="relative z-10 max-w-2xl">
                        <h2
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-on-primary mb-4 md:mb-8 font-headline leading-tight">
                            Ready to verify your digital presence in Njikoka?</h2>
                        <p class="text-on-primary/60 text-sm md:text-base lg:text-lg mb-6 md:mb-10">Join
                            thousands of
                            citizens already contributing to
                            a smarter, safer, and more organized community.</p>
                        <a href="{{ url('/portal/register-address') }}"
                            class="px-6 md:px-10 py-3 md:py-5 bg-tertiary-fixed text-on-tertiary-fixed rounded-lg md:rounded-xl font-bold text-sm md:text-xl hover:scale-105 active:scale-95 transition-all duration-300 inline-block">Register
                            My Address Now</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="full-width pt-12 md:pt-16 pb-8 bg-primary-container">
        <div
            class="flex flex-col md:flex-row justify-between items-start w-full px-4 md:px-8 lg:px-12 max-w-7xl mx-auto gap-6 md:gap-8">
            <div class="flex flex-col gap-3 md:gap-4 max-w-xs">
                <div class="text-lg md:text-xl font-bold text-tertiary-fixed-dim font-headline">NDSMS</div>
                <p class="text-surface-container-low leading-relaxed font-headline text-xs md:text-sm">
                    Empowering Njikoka with precise digital governance and spatial administrative excellence.
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 lg:gap-12 flex-1">
                <div class="flex flex-col gap-3 md:gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-[10px] md:text-xs tracking-widest font-headline">Quick
                        Links</span>
                    <nav class="flex flex-col gap-1 md:gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm"
                            href="#">Privacy Policy</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm"
                            href="#">Terms of Service</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm"
                            href="#">Contact Support</a>
                    </nav>
                </div>
                <div class="flex flex-col gap-3 md:gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-[10px] md:text-xs tracking-widest font-headline">Official</span>
                    <nav class="flex flex-col gap-1 md:gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm"
                            href="#">Government Portal</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm"
                            href="#">Citizen Services</a>
                    </nav>
                </div>
                <div class="flex flex-col gap-3 md:gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-[10px] md:text-xs tracking-widest font-headline">Contact</span>
                    <div class="flex flex-col gap-2 md:gap-3">
                        <a href="mailto:info@njikokadsms.online"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm">info@njikokadsms.online</a>
                        <a href="tel:+2348036052303"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm">+234
                            803 605 2303</a>
                        <a href="tel:+2348025796226"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm">+234
                            802 579 6226</a>
                    </div>
                </div>
                <div class="flex flex-col gap-3 md:gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-[10px] md:text-xs tracking-widest font-headline">Follow</span>
                    <div class="flex flex-col gap-1 md:gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm flex items-center gap-2"
                            href="https://facebook.com/njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-sm md:text-base">facebook</span>
                            <span class="hidden md:inline">Facebook</span>
                        </a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm flex items-center gap-2"
                            href="https://instagram.com/njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-sm md:text-base">photo_camera</span>
                            <span class="hidden md:inline">Instagram</span>
                        </a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-xs md:text-sm flex items-center gap-2"
                            href="https://youtube.com/@njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-sm md:text-base">video_library</span>
                            <span class="hidden md:inline">YouTube</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <div
            class="w-full px-4 md:px-8 lg:px-12 max-w-7xl mx-auto mt-8 md:mt-12 pt-6 md:pt-8 border-t border-white/10">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 mb-6 md:mb-8">
                <span class="material-symbols-outlined text-xl md:text-2xl text-tertiary-fixed-dim">location_on</span>
                <div class="flex flex-col gap-1">
                    <h3
                        class="text-tertiary-fixed-dim uppercase font-bold text-[10px] md:text-xs tracking-widest font-headline">
                        Address</h3>
                    <p class="text-surface-container-low font-headline text-xs md:text-sm">Njikoka Local Government
                        Head Quarters, Abagana</p>
                </div>
            </div>
            <p class="text-surface-container-low text-[10px] md:text-[12px] font-headline opacity-60">©
                {{ date('Y') }} Njikoka
                Digital Street Management System. An Official Civic Estate Initiative.</p>
        </div>
    </footer>
</body>

</html>
