@extends('layouts.app')

@section('title', 'NACOMES — National Association of Computer Engineering Students | Departmental Election')
@section('description', 'Official departmental voting portal for the National Association of Computer Engineering Students. Cast your vote securely and inspect candidate profiles.')

@section('content')
    <!-- 1. Hero Section -->
    <section class="relative bg-navy-950 text-white overflow-hidden" aria-label="Departmental Election Introduction">
        <!-- Background Photography with High-Contrast Navy Overlay -->
        <div class="absolute inset-0 z-0">
            <img
                src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1920&q=80"
                alt="University Computer Engineering students collaborating on campus"
                class="w-full h-full object-cover object-center filter brightness-60"
                loading="eager"
            />
            <!-- Clean Neutral Dark Overlay -->
            <div class="absolute inset-0 bg-navy-950/90"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36">
            <div class="max-w-3xl space-y-6">
                <!-- Neutral Status / Department Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-navy-900/85 border border-navy-700/80 text-xs text-slate-200">
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                    <span class="font-medium tracking-wide">Accredited Student Electoral Portal</span>
                    <span class="text-slate-500">•</span>
                    <span class="text-slate-300">Department of Computer Engineering</span>
                </div>

                <!-- Strong Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Shaping the Future of <br class="hidden sm:inline" />
                    <span class="text-yellow-400">Computer Engineering</span> Leadership
                </h1>

                <!-- Short Supporting Text -->
                <p class="text-base sm:text-lg lg:text-xl text-slate-200 leading-relaxed font-normal">
                    The official digital voting platform for the National Association of Computer Engineering Students (NACOMES). Accredited students can review certified candidate profiles and cast secure, confidential ballots.
                </p>

                <!-- Primary & Secondary CTAs -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <!-- Primary Yellow CTA -->
                    <a href="{{ route('vote') }}" class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-md font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 shadow-sm transition-colors text-center text-base focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-navy-950">
                        <span>Vote Now</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    <!-- Secondary Results CTA -->
                    <a href="{{ route('results') }}" class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-md font-semibold text-white border border-slate-400/60 hover:border-yellow-400 hover:text-yellow-400 hover:bg-white/5 transition-all text-center text-base focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>View Live Results</span>
                    </a>
                </div>

                <!-- Notice / Accreditation note (No invented dates) -->
                <div class="pt-3 text-xs text-slate-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Voter accreditation is open to matriculated undergraduate students in the department.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Electoral Integrity Assurance Banner -->
    <section class="bg-white border-b border-slate-200 py-8 shadow-xs" aria-label="Electoral Principles">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pillar 1 -->
                <div class="flex items-start gap-3.5 p-3 rounded-lg">
                    <div class="w-10 h-10 rounded bg-navy-900 text-yellow-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-charcoal-900">Secret &amp; Encrypted Ballot</h2>
                        <p class="text-xs text-charcoal-600 mt-1">Your vote choices remain strictly confidential and cryptographically decoupled from your student ID.</p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="flex items-start gap-3.5 p-3 rounded-lg">
                    <div class="w-10 h-10 rounded bg-navy-900 text-yellow-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-charcoal-900">Verified Student Register</h2>
                        <p class="text-xs text-charcoal-600 mt-1">Only active, eligible Computer Engineering students on the accredited departmental roster may participate.</p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="flex items-start gap-3.5 p-3 rounded-lg">
                    <div class="w-10 h-10 rounded bg-navy-900 text-yellow-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-charcoal-900">One Student, One Vote</h2>
                        <p class="text-xs text-charcoal-600 mt-1">Strict electronic validation ensures zero duplicates, zero proxy votes, and verifiable tamper-proof counts.</p>
                    </div>
                </div>

                <!-- Pillar 4 -->
                <div class="flex items-start gap-3.5 p-3 rounded-lg">
                    <div class="w-10 h-10 rounded bg-navy-900 text-yellow-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-charcoal-900">Mobile &amp; Desktop Access</h2>
                        <p class="text-xs text-charcoal-600 mt-1">Cast your ballot conveniently and safely from your smartphone, tablet, or workstation browser.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Election Information & How to Participate -->
    <section id="election-info" class="py-20 bg-slate-50 border-b border-slate-200 scroll-mt-20" aria-label="Election Information">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="max-w-3xl mx-auto text-center space-y-3">
                <span class="inline-block text-xs font-bold tracking-widest text-royal-700 uppercase bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                    Election Information &amp; Process
                </span>
                <h2 class="text-3xl font-extrabold text-navy-950 tracking-tight sm:text-4xl">
                    Democracy in Action for Computer Engineering
                </h2>
                <p class="text-base text-charcoal-600 leading-relaxed">
                    The NACOMES departmental election provides every matriculated student with an equal voice in choosing the executive leadership representing our academic welfare, technical programs, and industry initiatives.
                </p>
            </div>

            <!-- Two-Column Overview & Campus Context -->
            <div class="mt-14 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Textual Overview -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-navy-900">Electoral Mission &amp; Purpose</h3>
                        <p class="text-sm text-charcoal-700 leading-relaxed">
                            Under the constitution of the National Association of Computer Engineering Students, democratic voting is conducted to elect executive officers for each academic session. This platform ensures transparent, audit-ready elections with real-time participation monitoring while preserving absolute ballot secrecy.
                        </p>
                    </div>

                    <!-- Highlight List -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span class="text-sm text-charcoal-700"><strong>Autonomous Electoral Commission:</strong> Oversaw by accredited student commissioners and departmental academic advisors.</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span class="text-sm text-charcoal-700"><strong>Equitable Student Representation:</strong> Every level from 100 through 500 Level has accredited voter participation.</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span class="text-sm text-charcoal-700"><strong>Immutable Voting Log:</strong> Digital record-keeping guarantees verifiable collation without storing individual vote choices alongside student identity.</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('vote') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-royal-700 hover:text-royal-800 transition-colors">
                            <span>Check your accreditation status</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Contextual Image Container -->
                <div class="lg:col-span-5">
                    <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white">
                        <img
                            src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=800&q=80"
                            alt="Modern university campus building and lecture hall"
                            class="w-full h-56 object-cover object-center"
                            loading="lazy"
                        />
                        <div class="p-6 bg-white space-y-2">
                            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                                <span>Official Department Notice</span>
                                <span class="text-royal-700 font-semibold">Session Elections</span>
                            </div>
                            <h4 class="text-base font-bold text-navy-950">Accredited Student Participation</h4>
                            <p class="text-xs text-charcoal-600 leading-relaxed">
                                Please ensure your institutional student email is active and that your matriculation number is registered on the departmental portal database.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How to Participate Step-by-Step Flow -->
            <div id="participation" class="mt-20 pt-16 border-t border-slate-200">
                <div class="max-w-2xl mx-auto text-center space-y-2">
                    <span class="text-xs font-bold tracking-widest text-slate-500 uppercase">Step-by-Step Guide</span>
                    <h3 class="text-2xl font-bold text-navy-950">How to Participate &amp; Cast Your Vote</h3>
                    <p class="text-sm text-charcoal-600">Follow these four straightforward steps to verify your student credentials and submit your ballot.</p>
                </div>

                <div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Step 1 -->
                    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-2xs relative flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-md bg-navy-900 text-yellow-400 font-bold flex items-center justify-center text-sm mb-4">
                                01
                            </div>
                            <h4 class="text-base font-bold text-navy-950">Register / Check Roster</h4>
                            <p class="text-xs text-charcoal-600 mt-2 leading-relaxed">
                                Confirm that your matriculation number and level are present on the certified departmental voter register.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 font-medium">
                            Eligibility verification
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-2xs relative flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-md bg-navy-900 text-yellow-400 font-bold flex items-center justify-center text-sm mb-4">
                                02
                            </div>
                            <h4 class="text-base font-bold text-navy-950">Verify Email</h4>
                            <p class="text-xs text-charcoal-600 mt-2 leading-relaxed">
                                Receive a secure one-time passcode or authentication link delivered to your registered institutional student inbox.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 font-medium">
                            Institutional mailbox verification
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-2xs relative flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-md bg-navy-900 text-yellow-400 font-bold flex items-center justify-center text-sm mb-4">
                                03
                            </div>
                            <h4 class="text-base font-bold text-navy-950">Log In Securely</h4>
                            <p class="text-xs text-charcoal-600 mt-2 leading-relaxed">
                                Enter your credentials into the certified student portal to unlock your personalised digital ballot booth.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 font-medium">
                            Accredited portal session
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-2xs relative flex flex-col justify-between">
                        <div>
                            <div class="w-9 h-9 rounded-md bg-yellow-400 text-navy-950 font-bold flex items-center justify-center text-sm mb-4">
                                04
                            </div>
                            <h4 class="text-base font-bold text-navy-950">Cast Your Ballot</h4>
                            <p class="text-xs text-charcoal-600 mt-2 leading-relaxed">
                                Select preferred candidates for each executive office, review your selections, and submit with instant verification.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 font-medium">
                            Confidential vote submission
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Candidate Discovery Section -->
    <section id="candidates" class="py-20 bg-white border-b border-slate-200 scroll-mt-20" aria-label="Candidates Discovery">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-10 border-b border-slate-200">
                <div class="max-w-2xl space-y-2">
                    <span class="text-xs font-bold tracking-widest text-royal-700 uppercase">Executive Offices</span>
                    <h2 class="text-3xl font-extrabold text-navy-950 tracking-tight sm:text-4xl">
                        Explore Candidates
                    </h2>
                    <p class="text-sm text-charcoal-600">
                        Review contested executive positions, inspect candidate credentials, and evaluate their leadership vision for NACOMES before casting your ballot.
                    </p>
                </div>

                <div>
                    <a href="{{ route('candidates') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition-colors">
                        <span>View All Candidates</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Offices Grid -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($offices as $office)
                    <div class="bg-slate-50 rounded-lg border border-slate-200 p-6 flex flex-col justify-between hover:border-royal-500/60 transition-colors group">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-navy-900 text-yellow-400">
                                    Executive Office
                                </span>
                                <span class="text-xs font-medium text-slate-500">
                                    {{ $office['candidates_count'] }} Aspirants
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-navy-950 group-hover:text-royal-700 transition-colors">
                                {{ $office['title'] }}
                            </h3>

                            <p class="text-xs text-charcoal-600 leading-relaxed">
                                {{ $office['description'] }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500">Profiles Verified</span>
                            <a href="{{ route('candidates') }}" class="text-xs font-bold text-royal-700 hover:text-royal-800 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                                Explore <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-slate-50 border border-dashed border-slate-300 rounded-xl p-8">
                        <div class="w-12 h-12 mx-auto rounded-full bg-slate-200/80 text-slate-500 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-navy-950">No Executive Positions Published Yet</h3>
                        <p class="text-xs sm:text-sm text-charcoal-600 max-w-md mx-auto mt-1">
                            Contested executive offices and registered candidate profiles will appear here dynamically as soon as they are added and published in the admin dashboard.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 5. Clear Instructions for Students & Guidelines -->
    <section id="guidelines" class="py-16 bg-slate-50 border-b border-slate-200" aria-label="Student Voting Guidelines">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-navy-950 rounded-2xl text-white p-8 sm:p-12 overflow-hidden relative">
                <div class="relative z-10 max-w-3xl space-y-6">
                    <span class="text-xs font-bold tracking-widest text-yellow-400 uppercase">Electoral Guidelines &amp; Code of Conduct</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Important Instructions for Every Student Voter
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        To maintain democratic integrity and transparency, all students participating in the NACOMES departmental election are required to observe these fundamental instructions:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="bg-navy-900/80 border border-navy-800 p-4 rounded-lg space-y-1.5">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <span class="text-yellow-400 font-bold">•</span> Sole Voter Authentication
                            </h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Do not share your login credentials, token, or student email access with anyone. Ballots cast are final and cannot be re-opened.
                            </p>
                        </div>

                        <div class="bg-navy-900/80 border border-navy-800 p-4 rounded-lg space-y-1.5">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <span class="text-yellow-400 font-bold">•</span> Single Ballot Submission
                            </h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Each accredited student is entitled to exactly one ballot. Once you confirm and submit, your voter session token is permanently retired.
                            </p>
                        </div>

                        <div class="bg-navy-900/80 border border-navy-800 p-4 rounded-lg space-y-1.5">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <span class="text-yellow-400 font-bold">•</span> Strict Ballot Secrecy
                            </h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                The electoral software is engineered so that ballot choices cannot be linked back to individual voter profiles or matriculation numbers.
                            </p>
                        </div>

                        <div class="bg-navy-900/80 border border-navy-800 p-4 rounded-lg space-y-1.5">
                            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                                <span class="text-yellow-400 font-bold">•</span> Verification &amp; Support
                            </h3>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                If you experience any technical difficulties or cannot locate your matriculation record, contact the departmental helpdesk immediately.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <a href="{{ route('vote') }}" class="px-6 py-3 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition-colors">
                            Proceed to Voting Desk
                        </a>
                        <a href="{{ route('login') }}" class="px-5 py-3 rounded-md font-medium text-sm text-slate-200 bg-navy-900 hover:bg-navy-800 border border-navy-700 transition-colors">
                            Student Login Portal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 6. Call to Action Banner -->
    <section class="py-16 bg-white" aria-label="Call to Action">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy-950 tracking-tight">
                Your Vote Defines the Future of NACOMES
            </h2>
            <p class="max-w-2xl mx-auto text-sm sm:text-base text-charcoal-600 leading-relaxed">
                Take part in building a stronger Computer Engineering department. Learn about each candidate, inspect their goals, and cast your ballot before polls conclude.
            </p>
            <div class="pt-2 flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="{{ route('vote') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-md font-semibold text-base text-navy-950 bg-yellow-400 hover:bg-yellow-300 shadow-sm transition-colors">
                    Cast Your Vote Now
                </a>
                <a href="{{ route('candidates') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-md font-medium text-base text-royal-700 hover:text-royal-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors">
                    Explore All Candidates
                </a>
            </div>
        </div>
    </section>
@endsection
