<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NACOMES — National Association of Computer Engineering Students')</title>
    <meta name="description" content="@yield('description', 'Official departmental voting platform for the National Association of Computer Engineering Students (NACOMES). Secure, transparent, and verifiable elections.')">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    @php
        $siteIcon = \App\Models\Setting::get('site_icon');
        $siteLogo = \App\Models\Setting::get('site_logo');
    @endphp
    @if ($siteIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteIcon))
        <link rel="icon" href="{{ asset('storage/' . $siteIcon) }}">
    @endif

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-charcoal-800 antialiased selection:bg-yellow-400 selection:text-navy-950">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-yellow-400 focus:text-navy-950 focus:font-semibold focus:rounded-md focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-navy-900">
        Skip to main content
    </a>

    <header class="sticky top-0 z-40 bg-navy-950 border-b border-navy-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo Area -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-400 rounded-md p-1">
                    @if ($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="NACOMES" class="h-11 max-w-[160px] object-contain">
                    @else
                        <!-- Text-based Brand Mark & Symbol -->
                        <div class="w-11 h-11 rounded-lg bg-navy-900 border border-navy-700/80 flex items-center justify-center text-yellow-400 shadow-inner group-hover:border-yellow-400/50 transition-colors">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="16" rx="2" />
                                <rect x="9" y="9" width="6" height="6" />
                                <path d="M9 1v3" />
                                <path d="M15 1v3" />
                                <path d="M9 20v3" />
                                <path d="M15 20v3" />
                                <path d="M20 9h3" />
                                <path d="M20 14h3" />
                                <path d="M1 9h3" />
                                <path d="M1 14h3" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight text-white group-hover:text-yellow-400 transition-colors">NACOMES</span>
                        <span class="text-[11px] font-medium tracking-wider uppercase text-slate-300">Computer Engineering</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium" aria-label="Main Navigation">
                    <a href="{{ route('home') }}" class="text-white hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('home') && !request()->query('section') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                        Home
                    </a>
                    <a href="{{ route('candidates') }}" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('candidates') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                        Candidates
                    </a>
                    <a href="{{ route('results') }}" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('results') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                        Live Results
                    </a>
                    <a href="{{ route('home') }}#election-info" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 border-transparent">
                        Election Information
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('register') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                            Register
                        </a>
                        <a href="{{ route('login') }}" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('login') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                            Login
                        </a>
                    @else
                        <a href="{{ route('vote') }}" class="text-slate-300 hover:text-yellow-400 transition-colors py-2 border-b-2 {{ request()->routeIs('vote') ? 'border-yellow-400 text-yellow-400' : 'border-transparent' }}">
                            Voting Desk
                        </a>
                    @endguest
                </nav>

                <!-- Desktop CTAs -->
                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-200 hover:text-white px-3.5 py-2 rounded-md hover:bg-navy-900 border border-transparent hover:border-navy-700 transition">
                            Portal Sign In
                        </a>
                        <a href="{{ route('vote') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 px-5 py-2.5 rounded-md shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-navy-950">
                            <span>Vote Now</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-md bg-navy-900 border border-navy-800 text-xs">
                            <span class="w-2 h-2 rounded-full {{ auth()->user()->hasVerifiedEmail() ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                            <span class="text-slate-200 font-medium">{{ auth()->user()->matric_no }}</span>
                        </div>
                        <a href="{{ route('vote') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 px-4 py-2 rounded-md shadow-sm transition">
                            <span>Voting Desk</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-slate-300 hover:text-white px-3 py-2 rounded-md hover:bg-navy-900 border border-transparent hover:border-navy-700 transition">
                                Sign Out
                            </button>
                        </form>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-button" type="button" class="p-2.5 rounded-md text-slate-300 hover:text-white hover:bg-navy-900 focus:outline-none focus:ring-2 focus:ring-yellow-400" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open navigation menu</span>
                        <!-- Hamburger Icon -->
                        <svg id="hamburger-icon" class="w-6 h-6 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close Icon -->
                        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-navy-800 bg-navy-950 px-4 pt-3 pb-6 space-y-3">
            <div class="flex flex-col space-y-1">
                <a href="{{ route('home') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                    Home
                </a>
                <a href="{{ route('candidates') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('candidates') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                    Candidates
                </a>
                <a href="{{ route('results') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('results') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                    Live Results
                </a>
                <a href="{{ route('home') }}#election-info" class="px-3 py-2.5 rounded-md text-base font-medium text-slate-200 hover:bg-navy-900 hover:text-white">
                    Election Information
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('register') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                        Register
                    </a>
                    <a href="{{ route('login') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('login') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                        Login
                    </a>
                @else
                    <a href="{{ route('vote') }}" class="px-3 py-2.5 rounded-md text-base font-medium {{ request()->routeIs('vote') ? 'bg-navy-900 text-yellow-400' : 'text-slate-200 hover:bg-navy-900 hover:text-white' }}">
                        Voting Desk
                    </a>
                @endguest
            </div>

            <div class="pt-3 border-t border-navy-800/80 flex flex-col gap-2.5">
                @guest
                    <a href="{{ route('vote') }}" class="w-full text-center py-3 px-4 rounded-md font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 shadow-sm transition">
                        Vote Now
                    </a>
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 px-4 rounded-md font-medium text-slate-200 bg-navy-900 hover:bg-navy-850 border border-navy-700/80 transition">
                        Portal Sign In
                    </a>
                @else
                    <div class="px-3 py-2 text-xs text-slate-300 bg-navy-900 rounded">
                        Signed in as <strong class="text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->matric_no }})
                    </div>
                    <a href="{{ route('vote') }}" class="w-full text-center py-2.5 px-4 rounded-md font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition">
                        Access Voting Desk
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center py-2 px-4 rounded-md font-medium text-xs text-red-400 hover:text-red-300 bg-navy-900 border border-navy-800 transition">
                            Sign Out
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    <!-- Reusable Footer -->
    <footer class="bg-navy-950 text-slate-300 border-t border-navy-800" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-navy-800/80">
                <!-- Association Identity & Overview -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        @if ($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                            <img src="{{ asset('storage/' . $siteLogo) }}" alt="NACOMES" class="h-12 w-auto max-w-[170px] object-contain">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-navy-900 border border-navy-700 flex items-center justify-center text-yellow-400 font-bold text-sm">
                                NC
                            </div>
                        @endif
                        <div>
                            <span class="text-lg font-bold text-white tracking-tight">NACOMES</span>
                            <p class="text-xs text-yellow-400 font-medium">Department of Computer Engineering</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-200">
                        National Association of Computer Engineering Students
                    </p>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        The official student governing body dedicated to advancing technical excellence, academic integrity, and transparent democratic representation within the Department of Computer Engineering.
                    </p>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h3 class="text-xs font-semibold tracking-wider text-slate-200 uppercase mb-4">Quick Navigation</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="{{ route('home') }}" class="text-slate-400 hover:text-yellow-400 transition-colors">Home</a>
                        </li>
                        <li>
                            <a href="{{ route('candidates') }}" class="text-slate-400 hover:text-yellow-400 transition-colors">Candidates Directory</a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}#election-info" class="text-slate-400 hover:text-yellow-400 transition-colors">Election Information</a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}#participation" class="text-slate-400 hover:text-yellow-400 transition-colors">How to Participate</a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="text-slate-400 hover:text-yellow-400 transition-colors">Student Login</a>
                        </li>
                    </ul>
                </div>

                <!-- Electoral Support & Verification -->
                <div>
                    <h3 class="text-xs font-semibold tracking-wider text-slate-200 uppercase mb-4">Electoral Information</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="{{ route('home') }}#guidelines" class="text-slate-400 hover:text-yellow-400 transition-colors">Voter Eligibility Rules</a>
                        </li>
                        <li>
                            <a href="{{ route('vote') }}" class="text-slate-400 hover:text-yellow-400 transition-colors">Accreditation Desk</a>
                        </li>
                        <li>
                            <span class="text-slate-400">Departmental Electoral Board</span>
                        </li>
                        <li>
                            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-950/60 border border-emerald-800/80 px-2 py-1 rounded">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Secure TLS 256-bit Encryption
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Credits & Vantlab Slot -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Copyright text (without invented registration numbers) -->
                <p class="text-xs text-slate-400 order-2 md:order-1 text-center md:text-left">
                    &copy; {{ date('Y') }} National Association of Computer Engineering Students. All rights reserved.
                </p>

                <!-- Designated Vantlab Logo & Credit Slot -->
                <div class="order-1 md:order-2 flex items-center gap-3 bg-navy-900/90 border border-navy-800 px-4 py-2 rounded-lg">
                    <!-- Vantlab Logo Asset Container: Checks if user supplied asset, otherwise renders crisp brand slot -->
                    <div class="flex items-center justify-center min-w-[28px] min-h-[28px]">
                        @if(file_exists(public_path('images/vantlab-logo.png')))
                            <img src="{{ asset('images/vantlab-logo.png') }}" alt="Vantlab" class="h-7 w-7 rounded-full object-contain bg-white p-0.5 border border-slate-300 shadow-2xs" />
                        @elseif(file_exists(public_path('images/vantlab-logo.jpg')))
                            <img src="{{ asset('images/vantlab-logo.jpg') }}" alt="Vantlab" class="h-7 w-7 rounded-full object-contain bg-white p-0.5 border border-slate-300 shadow-2xs" />
                        @elseif(file_exists(public_path('images/vantlab-logo.svg')))
                            <img src="{{ asset('images/vantlab-logo.svg') }}" alt="Vantlab" class="h-7 w-7 rounded-full object-contain bg-white p-0.5 border border-slate-300 shadow-2xs" />
                        @else
                            <!-- Styled Brand Placeholder Mark -->
                            <div class="w-7 h-7 rounded bg-royal-700/80 border border-royal-500/50 flex items-center justify-center text-yellow-400 shadow-sm" title="Vantlab Logo Slot">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <!-- Exact Required Credit Text -->
                    <span class="text-xs text-slate-300 tracking-wide">
                        Powered by <strong class="text-white font-semibold tracking-normal">Vantlab</strong>
                    </span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
