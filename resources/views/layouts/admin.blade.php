<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Console — NACOMES Elections')</title>

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
<body class="min-h-full flex flex-col antialiased text-charcoal-800">
    <div class="flex-1 flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 bg-navy-950 border-r border-navy-800 flex flex-col justify-between shrink-0">
            <div>
                <!-- Brand Header -->
                <div class="h-18 px-6 flex items-center justify-between border-b border-navy-800/80">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        @if ($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                            <img src="{{ asset('storage/' . $siteLogo) }}" alt="NACOMES" class="h-8 max-w-[120px] object-contain">
                        @else
                            <div class="w-8 h-8 rounded bg-navy-900 border border-yellow-400/40 text-yellow-400 flex items-center justify-center font-bold text-sm">
                                NC
                            </div>
                        @endif
                        <div>
                            <span class="text-sm font-bold text-white tracking-tight block">NACOMES Admin</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Electoral Console</span>
                        </div>
                    </a>

                    <!-- Mobile Toggle -->
                    <button id="admin-sidebar-toggle" class="md:hidden p-2 text-slate-400 hover:text-white focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav id="admin-nav" class="hidden md:block p-4 space-y-1.5 text-xs font-semibold" aria-label="Admin Navigation">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2" />
                            <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2" />
                            <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2" />
                            <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.voters.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.voters.*') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Voter Management</span>
                    </a>

                    <a href="{{ route('admin.department-students.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.department-students.*') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Eligible Matrics</span>
                    </a>

                    <a href="{{ route('admin.positions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.positions.*') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Positions &amp; Candidates</span>
                    </a>

                    <a href="{{ route('admin.election.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.election.*') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Elections</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-md transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-navy-900 text-yellow-400 border border-navy-800' : 'text-slate-300 hover:bg-navy-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>System Settings</span>
                    </a>

                    <div class="pt-4 border-t border-navy-800/80 my-2"></div>

                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2 rounded-md text-slate-400 hover:text-white hover:bg-navy-900 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>View Public Website &rarr;</span>
                    </a>
                </nav>
            </div>

            <!-- Admin Profile / Sign Out Bottom Bar -->
            <div class="p-4 border-t border-navy-800/80 bg-navy-950/80">
                <div class="flex items-center justify-between">
                    <div class="space-y-0.5 truncate pr-2">
                        <span class="text-xs font-bold text-white truncate block">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-yellow-400 font-mono block">{{ auth()->user()->email }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" title="Sign Out" class="p-2 text-slate-400 hover:text-red-400 hover:bg-navy-900 rounded transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-18 bg-white border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between shrink-0">
                <div>
                    <h1 class="text-lg font-bold text-navy-950">@yield('page-title', 'Administration')</h1>
                </div>

                <div class="flex items-center gap-4">
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-6 sm:p-8 space-y-6 max-w-7xl w-full mx-auto">
                <!-- Status Alerts -->
                @if (session('status'))
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2.5 shadow-2xs" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Error Banners -->
                @if ($errors->any())
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-900 space-y-1 shadow-2xs" role="alert">
                        <strong class="font-bold block">Validation or Operation Error:</strong>
                        <ul class="list-disc list-inside space-y-0.5 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Reusable Modal Controller Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Sidebar mobile toggle
            const toggle = document.getElementById('admin-sidebar-toggle');
            const nav = document.getElementById('admin-nav');
            if (toggle && nav) {
                toggle.addEventListener('click', () => {
                    nav.classList.toggle('hidden');
                });
            }

            // Universal modal opener: buttons with [data-modal-open="modal-id"]
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-modal-open');
                    const modal = document.getElementById(targetId);
                    if (modal) {
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    }
                });
            });

            // Universal modal closer: buttons with [data-modal-close="modal-id"]
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-modal-close');
                    const modal = document.getElementById(targetId);
                    if (modal) {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            // Close on backdrop click and ESC key
            document.querySelectorAll('.admin-modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.admin-modal:not(.hidden)').forEach(modal => {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    });
                }
            });
        });
    </script>
</body>
</html>
