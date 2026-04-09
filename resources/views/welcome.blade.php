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
    </style>
</head>

<body class="text-on-surface selection:bg-tertiary-fixed selection:text-on-tertiary-fixed">
    <!-- TopAppBar -->
    <header
        class="docked full-width top-0 sticky z-50 bg-background/80 backdrop-blur-md shadow-[0px_20px_40px_rgba(11,38,25,0.06)]">
        <div class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('logo.jpeg') }}" alt="NDSMS Logo" class="h-10 w-auto">
                <span class="text-xl font-black text-primary-container tracking-tighter font-headline">NDSMS</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8">
                <a class="text-primary-container border-b-2 border-tertiary-fixed-dim pb-1 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#features">Features</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#how-it-works">How it Works</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#statistics">Statistics</a>
                <a class="text-on-surface-variant hover:text-primary-container transition-all duration-300 uppercase text-[12px] tracking-wider font-semibold font-headline"
                    href="#faq">FAQ</a>
            </nav>
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2 text-[12px] font-bold uppercase tracking-wider text-primary-container hover:bg-surface-container-low transition-all duration-300 font-headline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-5 py-2 text-[12px] font-bold uppercase tracking-wider text-primary-container hover:bg-surface-container-low transition-all duration-300 font-headline">Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-6 py-2.5 bg-primary-container text-on-primary rounded-xl font-bold text-sm hover:opacity-90 active:scale-[0.99] transition-all duration-300 shadow-sm">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-20 pb-32 px-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-7 z-10">
                    <span
                        class="inline-block px-4 py-1.5 bg-secondary-container text-on-secondary-container rounded-full text-xs font-bold tracking-widest uppercase mb-6 font-headline">Official
                        Civic Infrastructure</span>
                    <h1
                        class="text-4xl md:text-5xl font-black text-primary leading-[1.1] tracking-tight mb-8 font-headline">
                        Own Your Address <span class="text-secondary">Define Your Identity</span> Build Njikoka
                        Digitally.
                    </h1>
                    <h2 class="text-2xl md:text-2xl font-black">
                        Njikoka is stepping into the future.
                    </h2>
                    <p class="text-md text-on-surface-variant leading-relaxed max-w-2xl mb-10">
                        The Njikoka Digital Street Management System (NDSMS) is a revolutionary digital platform
                        designed to organize, standardize, and officially register streets, homes, and properties across
                        all communities in Njikoka Local Government.
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/portal/register-address') }}"
                            class="px-8 py-4 bg-primary-container text-on-primary rounded-xl font-bold text-lg flex items-center justify-center gap-2 hover:shadow-xl transition-all duration-300 group">
                            Register My Address
                            <span
                                class="material-symbols-outlined text-tertiary-fixed-dim group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <button
                            class="px-8 py-4 bg-surface-container-low text-primary rounded-xl font-bold text-lg hover:bg-surface-container-high transition-all duration-300">
                            Explore Ward Maps
                        </button>
                    </div>
                </div>
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-[2rem] overflow-hidden shadow-2xl aspect-square">
                        <img alt="Modern Civic Infrastructure" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6iQr-LBow9hQCseWibcj_R0hyk08mM1PlcAvSYP-iJVAX8l1uvSrGfP53M7QYuhR2ccYkU6lpoKtJ1V6zbdFTXDHVKA089xewj-5vTZNMSqp2eKfc6ks5waYI8UHigNaZLeoTn0UZRNDA37BxQnP4BDYUml16GNwmvQsJ5_z-GsPSHBsfUVod3MC5Tn3MWOa44aBA4sPjEEOo1bJw2n6ercbqV4BAt_CQjOURs_eZyBjfiUlo86kP1hXHzF9827UrEVA5nYpDrUM" />
                        <div class="absolute inset-0 bg-gradient-to-t from-primary-container/40 to-transparent"></div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -bottom-6 -left-6 bg-tertiary-fixed p-6 rounded-2xl shadow-xl max-w-[200px]">
                        <p class="text-on-tertiary-fixed text-sm font-bold font-headline leading-tight">Securing
                            Njikoka's digital future, one street at a time.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why NDSMS? - Asymmetric Bento Grid -->
        <section id="features" class="py-24 bg-surface-container-low px-8">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16">
                    <h2 class="text-sm font-black text-secondary tracking-[0.2em] uppercase mb-4 font-headline">The
                        Vision</h2>
                    <h3 class="text-4xl font-bold text-primary font-headline">Why NDSMS?</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="md:col-span-2 bg-surface-container-lowest p-10 rounded-3xl flex flex-col justify-between group hover:bg-primary-container transition-colors duration-500">
                        <div>
                            <span
                                class="material-symbols-outlined text-4xl text-secondary mb-6 group-hover:text-tertiary-fixed-dim">verified_user</span>
                            <h4 class="text-2xl font-bold text-primary mb-4 group-hover:text-on-primary font-headline">
                                Official Street Identity & Recognition</h4>
                            <p class="text-on-surface-variant group-hover:text-on-primary/80 leading-relaxed max-w-xl">
                                Give your street a recognized name and digital presence.
                                No more “behind the big tree” or “after the junction”—your location becomes globally
                                identifiable</p>
                        </div>
                        <div
                            class="mt-12 flex items-center gap-2 text-secondary font-bold group-hover:text-tertiary-fixed-dim">
                            <span>Learn about security protocols</span>
                            <span class="material-symbols-outlined">chevron_right</span>
                        </div>
                    </div>
                    <div class="bg-primary-container p-10 rounded-3xl text-on-primary">
                        <span class="material-symbols-outlined text-4xl text-tertiary-fixed-dim mb-6">bolt</span>
                        <h4 class="text-2xl font-bold mb-4 font-headline"> Accurate Digital Address System</h4>
                        <p class="text-on-primary/70 leading-relaxed">
                            Every registered property is assigned a unique digital address and QR code, making it easy
                            for:
                        <ul>
                            <li> Visitors</li>
                            <li> Delivery services</li>
                            <li> Emergency responders</li>
                            <li> Government agencies</li>
                        </ul>




                        </p>
                    </div>
                    <div class="bg-tertiary-fixed p-10 rounded-3xl">
                        <span class="material-symbols-outlined text-4xl text-primary mb-6">dynamic_form</span>
                        <h4 class="text-2xl font-bold text-on-tertiary-fixed mb-4 font-headline">
                            Global Visibility for Ndi Njikoka
                        </h4>
                        <p class="text-on-tertiary-fixed-variant leading-relaxed">
                            Whether you are in Nigeria or abroad, you can:

                        <ul>
                            <li>Register your family house</li>
                            <li>Track and manage your property</li>
                            <li>Secure your street name</li>
                            <li>All from anywhere in the world.</li>
                        </ul>



                        </p>
                    </div>
                    <div class="md:col-span-2 relative rounded-3xl overflow-hidden min-h-[300px]">
                        <img alt="Modern Governance" class="absolute inset-0 w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDK09iKmr_SV8X4LsTeo8KWzaX_LQ4awFqPPGWQD9VPDXRyFFyrPmzePPF2CUnOdXPUFhSrHWUYTR4OtY2OHTCpycTVNWAc0kJjpmsTTnglnJtZBkaCWeE0qG7XXQnsBkPPeq6QkG447OravwrXzg9lJeg7KTUOdJSrarxR_M1Zyi7oXDkxFvtUYEpt-cumIgWXjeRNvLnyQ4xs3IN3qfXlIJY-4OmyUz2lqXUjK5JQ8QmBNf35Vck0gKiWmz1JKFfw46q--YRLvJ4" />
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary-container via-primary-container/60 to-transparent p-10 flex flex-col justify-center">
                            <h4 class="text-3xl font-bold text-on-primary mb-2 font-headline">Boost to Business &
                                Economic Growth</h4>
                            <p
                                class="text-on-surface-variant group-hover:text-on-primary/80 leading-relaxed max-w-xl text-white">
                                With a structured address system:
                            <ul class="text-white">
                                <li>Businesses become easier to locate</li>
                                <li>Logistics and delivery improve</li>
                                <li>Investors gain confidence in Njikoka</li>

                            </ul>
                            </p>
                            <p class="text-white">This opens the door to economic expansion and digital commerce..</p>
                        </div>
                    </div>


                </div>
            </div>
        </section>

        <!-- More Why NDSMS -->
        <section class="py-24 bg-surface px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-sm font-black text-secondary tracking-[0.2em] uppercase mb-4 font-headline">More
                        Reasons</h2>
                    <h3 class="text-4xl font-bold text-primary font-headline">More reasons to choose NDSMS</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-surface-container-low p-10 rounded-3xl border border-outline flex flex-col gap-6">
                        <span class="material-symbols-outlined text-4xl text-secondary">public</span>
                        <h4 class="text-2xl font-bold text-primary font-headline">Smart Mapping & GIS Integration
                        </h4>
                        <p class="text-on-surface-variant leading-relaxed">The platform leverages modern GIS technology
                            to:</p>
                        <ul>
                            <li>Map all streets digitally</li>
                            <li>Track development</li>
                            <li>Support urban planning</li>
                        </ul>
                        <p class="text-sm font-semibold text-secondary">Njikoka becomes a Smart Local Government Area.
                        </p>
                    </div>
                    <div class="bg-surface-container-low p-10 rounded-3xl border border-outline flex flex-col gap-6">
                        <span class="material-symbols-outlined text-4xl text-secondary">credit_card</span>
                        <h4 class="text-2xl font-bold text-primary font-headline"> Easy Online Registration & Payment
                        </h4>
                        <p class="text-on-surface-variant leading-relaxed">With a few clicks, you can:</p>
                        <ul>
                            <li>Apply for street naming</li>
                            <li>Register your house</li>
                            <li>Make secure payments</li>
                        </ul>
                        <p class="text-sm font-semibold text-secondary">No long queues. No manual paperwork.</p>
                    </div>
                    <div class="bg-surface-container-low p-10 rounded-3xl border border-outline flex flex-col gap-6">
                        <span class="material-symbols-outlined text-4xl text-secondary">document_scanner</span>
                        <h4 class="text-2xl font-bold text-primary font-headline">🧾 Digital Certificate of
                            Registration</h4>
                        <p class="text-on-surface-variant leading-relaxed">Every successful registration comes with:
                        </p>
                        <ul>
                            <li>A verified digital certificate</li>
                            <li>Government-backed approval</li>
                            <li>Permanent digital record</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Services -->
        <section class="py-24 px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-4xl md:text-5xl font-black text-primary font-headline mb-6 tracking-tight">System
                        Core Services</h2>
                    <div class="w-24 h-1 bg-tertiary-fixed-dim mx-auto"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">location_on</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">Register a New Street Name</h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Instant verification of residential
                            and commercial addresses for KYC and legal documentation.</p>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">map</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">Claim and register your House Address
                        </h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">A comprehensive, searchable database
                            of every mapped street within the Njikoka jurisdiction.</p>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">layers</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">
                            Upload property details and documents
                        </h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">
                            Detailed spatial data visualization
                            per ward, highlighting zoning and administrative boundaries.
                        </p>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">badge</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">
                            Track approval status in real-time
                        </h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Monitor your approval progress in
                            real time with instant updates as your registration moves through verification and
                            certification.</p>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">verified</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">
                            Download your Digital Address Certificate
                        </h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Download your official digital
                            address certificate instantly, complete with verified government approval and a unique QR
                            code for your property.</p>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="w-16 h-16 bg-surface-container-high rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-3xl">verified</span>
                        </div>
                        <h5 class="text-xl font-bold text-primary font-headline">
                            Access your QR-coded property identity
                        </h5>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Easily access your QR-coded property
                            identity anytime with a secure digital certificate for your registered address.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="statistics" class="py-20 px-8 bg-primary-container text-on-primary">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-md">
                    <h2 class="text-4xl font-bold font-headline mb-4">Modernizing our Community</h2>
                    <p class="text-on-primary/60">Our progress in numbers. We are rapidly expanding our digital
                        footprint to cover every corner of Njikoka.</p>
                </div>
                <div class="grid grid-cols-2 gap-16">
                    <div class="text-center">
                        <div class="text-6xl font-black text-tertiary-fixed-dim font-headline mb-2">12+</div>
                        <div class="text-xs uppercase tracking-widest font-bold text-on-primary/40">Registered Wards
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-6xl font-black text-tertiary-fixed-dim font-headline mb-2">1000+</div>
                        <div class="text-xs uppercase tracking-widest font-bold text-on-primary/40">Streets Mapped
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works -->
        <section id="how-it-works" class="py-24 px-8 bg-surface">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-sm font-black text-secondary tracking-[0.2em] uppercase mb-4 font-headline">Process
                    </h2>
                    <h3 class="text-4xl font-bold text-primary font-headline">How it Works</h3>
                </div>
                <div class="space-y-4">
                    <!-- Step 1 -->
                    <div
                        class="bg-surface-container-low p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center border-l-8 border-secondary">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-2xl font-black font-headline">
                            01</div>
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-primary font-headline mb-2">Register</h4>
                            <p class="text-on-surface-variant">Provide your property details and contact information
                                through our secure digital portal.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-outline text-4xl">app_registration</span>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div
                        class="bg-surface-container-low p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center border-l-8 border-tertiary-fixed-dim">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-2xl font-black font-headline">
                            02</div>
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-primary font-headline mb-2">Pay</h4>
                            <p class="text-on-surface-variant">Complete the administrative processing fee using our
                                integrated, government-approved payment gateway.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-outline text-4xl">payments</span>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div
                        class="bg-surface-container-low p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center border-l-8 border-secondary">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-2xl font-black font-headline">
                            03</div>
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-primary font-headline mb-2">Verify</h4>
                            <p class="text-on-surface-variant">A field officer will visit the location to confirm
                                spatial data and street naming compliance.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-outline text-4xl">fact_check</span>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div
                        class="bg-surface-container-low p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center border-l-8 border-tertiary-fixed-dim">
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full bg-primary-container text-on-primary flex items-center justify-center text-2xl font-black font-headline">
                            04</div>
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-primary font-headline mb-2">Get Certificate</h4>
                            <p class="text-on-surface-variant">Download your official Digital Address Certificate,
                                complete with a unique QR code for verification.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="material-symbols-outlined text-outline text-4xl">workspace_premium</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-8 overflow-hidden relative">
            <div class="max-w-7xl mx-auto">
                <div class="bg-primary-container rounded-[3rem] p-12 md:p-20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1/2 h-full opacity-10">
                        <img alt="Digital Infrastructure" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCm7JRHqinbcO8DBgaoparidlG0DWS_hEZGgSX7dWkFageE-6rvvAcHoNcUa8XDZUC5Tk2gdwpuIBzDlBfME-vqbs8w1tpGuIoWW8eu9GEgzlRDlDUsnMZqY120cLg8ToJCrZBmPrKthzsZ2qDBXYIZgBNsSYtukH0j6lqTvJCpH_HeHMOylDgHz8QVNwG655vtmh5TWfnaafzEbNAr84IpbyW-2xL3jpEC_r9UDWgxf8ZWNMfiJyFNqhN0l_FJGhtCrSar3uE6hf8" />
                    </div>
                    <div class="relative z-10 max-w-2xl">
                        <h2 class="text-4xl md:text-5xl font-black text-on-primary mb-8 font-headline leading-tight">
                            Ready to verify your digital presence in Njikoka?</h2>
                        <p class="text-on-primary/60 text-lg mb-10">Join thousands of citizens already contributing to
                            a smarter, safer, and more organized community.</p>
                        <a href="{{ url('/portal/register-address') }}"
                            class="px-10 py-5 bg-tertiary-fixed text-on-tertiary-fixed rounded-xl font-bold text-xl hover:scale-105 active:scale-95 transition-all duration-300 inline-block">Register
                            My Address Now</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="full-width pt-16 pb-8 bg-primary-container">
        <div class="flex flex-col md:flex-row justify-between items-start w-full px-12 max-w-7xl mx-auto gap-8">
            <div class="flex flex-col gap-4 max-w-xs">
                <div class="text-xl font-bold text-tertiary-fixed-dim font-headline">NDSMS</div>
                <p class="text-surface-container-low leading-relaxed font-headline text-sm">Empowering Njikoka with
                    precise digital governance and spatial administrative excellence.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 flex-1">
                <div class="flex flex-col gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-xs tracking-widest font-headline">Quick
                        Links</span>
                    <nav class="flex flex-col gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm"
                            href="#">Privacy Policy</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm"
                            href="#">Terms of Service</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm"
                            href="#">Contact Support</a>
                    </nav>
                </div>
                <div class="flex flex-col gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-xs tracking-widest font-headline">Official</span>
                    <nav class="flex flex-col gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm"
                            href="#">Government Portal</a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm"
                            href="#">Citizen Services</a>
                    </nav>
                </div>
                <div class="flex flex-col gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-xs tracking-widest font-headline">Contact</span>
                    <div class="flex flex-col gap-3">
                        <a href="mailto:info@njikokadsms.online"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-sm">
                            info@njikokadsms.online
                        </a>
                        <a href="tel:+2348036052303"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-sm">
                            +234 803 605 2303
                        </a>
                        <a href="tel:+2348025796226"
                            class="text-outline-variant hover:text-white transition-colors font-headline text-sm">
                            +234 802 579 6226
                        </a>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <span
                        class="text-tertiary-fixed-dim uppercase font-bold text-xs tracking-widest font-headline">Follow</span>
                    <div class="flex flex-col gap-2">
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm flex items-center gap-2"
                            href="https://facebook.com/njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-base">facebook</span>
                            Facebook
                        </a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm flex items-center gap-2"
                            href="https://instagram.com/njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-base">photo_camera</span>
                            Instagram
                        </a>
                        <a class="text-outline-variant hover:text-white transition-colors font-headline text-sm flex items-center gap-2"
                            href="https://youtube.com/@njikokadsms" target="_blank">
                            <span class="material-symbols-outlined text-base">video_library</span>
                            YouTube
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <div class="w-full px-12 max-w-7xl mx-auto mt-12 pt-8 border-t border-white/10">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 mb-8">
                <span class="material-symbols-outlined text-2xl text-tertiary-fixed-dim">location_on</span>
                <div class="flex flex-col gap-1">
                    <h3 class="text-tertiary-fixed-dim uppercase font-bold text-xs tracking-widest font-headline">
                        Address</h3>
                    <p class="text-surface-container-low font-headline text-sm">Njikoka Local Government Head Quarters,
                        Abagana</p>
                </div>
            </div>
            <p class="text-surface-container-low text-[12px] font-headline opacity-60">© 2024 Njikoka Digital Street
                Management System. An Official Civic Estate Initiative.</p>
        </div>
    </footer>
</body>

</html>
