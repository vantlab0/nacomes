@extends('layouts.app')

@section('title', 'Candidates Directory — NACOMES Elections')
@section('description', 'Discover certified aspirants running for executive positions in the NACOMES departmental election. Review credentials and verified candidate profiles.')

@section('content')
    <!-- Page Header -->
    <section class="bg-navy-950 text-white py-14 sm:py-16 border-b border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-3xl space-y-3">
                    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-2" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}" class="hover:text-yellow-400 transition-colors">Home</a>
                        <span>/</span>
                        <span class="text-slate-200">Candidates</span>
                    </nav>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-navy-900 border border-navy-700 text-xs text-yellow-400 font-medium">
                        <span class="w-2 h-2 rounded-full {{ $election && $election->isOpen() ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                        <span>{{ $election ? $election->title : 'Departmental Elections' }}</span>
                        <span class="text-slate-500">•</span>
                        <span class="uppercase tracking-wider text-[10px] text-slate-300">{{ $election ? ucfirst($election->status) : 'Scheduled' }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
                        Candidates Directory
                    </h1>
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                        Explore certified aspirants running for all executive offices in the Department of Computer Engineering. Evaluate credentials, verify eligibility, and cast your ballot.
                    </p>
                </div>

                <!-- Quick Action Button -->
                <div>
                    <a href="{{ route('vote') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition-colors shadow-2xs">
                        <span>Go to Voting Desk</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-14 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <!-- Live Poll Progress Status Banner -->
            <div class="p-4 rounded-xl bg-white border border-slate-200 text-xs text-navy-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="font-bold text-navy-950">Live Public Poll Progress</span>
                    <span class="text-slate-500 hidden sm:inline">&bull; Real-time ballot tallies and vote share percentages are updated live as students cast ballots.</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Live Poll Data
                    </span>
                </div>
            </div>

            <!-- Positions & Candidates Grid -->
            @forelse ($positions as $position)
                <div class="space-y-6">
                    <!-- Position Header -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                        <div>
                            <span class="text-xs font-bold tracking-widest text-royal-700 uppercase">Executive Office #{{ $position->display_order }}</span>
                            <h2 class="text-2xl font-bold text-navy-950 mt-0.5">{{ $position->title }}</h2>
                            @if ($position->description)
                                <p class="text-xs sm:text-sm text-charcoal-600 mt-1 max-w-2xl leading-relaxed">{{ $position->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-3 py-1 rounded bg-navy-950 text-yellow-400">
                                {{ $position->publishedCandidates->count() }} {{ \Illuminate\Support\Str::plural('Aspirant', $position->publishedCandidates->count()) }}
                            </span>
                            @if ($resultsPublished && isset($positionTotals[$position->id]))
                                <span class="text-xs font-medium text-slate-600 bg-slate-200/80 px-2.5 py-1 rounded">
                                    Total Votes: {{ $positionTotals[$position->id] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Candidates Cards Grid -->
                    @if ($position->publishedCandidates->isEmpty())
                        <div class="p-8 text-center bg-white rounded-xl border border-dashed border-slate-300 text-slate-500 space-y-2">
                            <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="1.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01" />
                            </svg>
                            <p class="text-sm font-medium text-charcoal-700">No published aspirants currently available for this position.</p>
                            <p class="text-xs text-slate-400">Accredited candidate listings are finalized by the Electoral Commission.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($position->publishedCandidates as $candidate)
                                <!-- Premium Candidate Card Design: Dark navy card, thin muted gold/yellow border, portrait in upper half, bold white name, accent level badge, 2 info rows, progress bar only when authorized -->
                                <div class="bg-navy-950 rounded-xl border border-yellow-500/30 overflow-hidden flex flex-col justify-between shadow-sm hover:border-yellow-400/70 transition-all group">
                                    <div>
                                        <!-- Upper Half: Candidate Portrait -->
                                        <div class="relative h-56 sm:h-60 w-full overflow-hidden bg-navy-900">
                                            <img
                                                src="{{ $candidate->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80' }}"
                                                alt="Portrait of {{ $candidate->name }}"
                                                class="w-full h-full object-cover object-top group-hover:scale-102 transition-transform duration-300"
                                                loading="lazy"
                                            />
                                            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-transparent to-transparent opacity-80"></div>
                                            <!-- Academic Level Accent Badge -->
                                            <div class="absolute top-3 right-3">
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-yellow-400 text-navy-950 shadow-sm">
                                                    {{ $candidate->academic_level }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Candidate Information Content -->
                                        <div class="p-5 space-y-4">
                                            <!-- Candidate Name -->
                                            <div>
                                                <h3 class="text-lg font-bold text-white tracking-tight group-hover:text-yellow-400 transition-colors">
                                                    {{ $candidate->name }}
                                                </h3>
                                                <p class="text-xs text-slate-300 mt-0.5">{{ $position->title }}</p>
                                            </div>

                                            <!-- 2 Concise Information Rows with appropriate line icons -->
                                            <div class="space-y-2 text-xs border-y border-navy-800/80 py-3">
                                                <!-- Row 1: Matriculation & Eligibility -->
                                                <div class="flex items-center justify-between text-slate-300">
                                                    <span class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                        </svg>
                                                        <span>Matriculation</span>
                                                    </span>
                                                    <span class="font-mono text-slate-200">{{ $candidate->matric_no ?: 'CPE Accredited' }}</span>
                                                </div>

                                                <!-- Row 2: Status & Validation -->
                                                <div class="flex items-center justify-between text-slate-300">
                                                    <span class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>Electoral Status</span>
                                                    </span>
                                                    <span class="text-emerald-400 font-semibold">Certified Aspirant</span>
                                                </div>
                                            </div>


                                            <!-- Thin Progress Bar (ONLY when vote-share results are authorized for display) -->
                                            @if ($resultsPublished && isset($voteCounts[$candidate->id]))
                                                <div class="space-y-1.5 pt-1">
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-slate-400">Vote Share</span>
                                                        <span class="text-yellow-400 font-bold font-mono">
                                                            {{ $voteCounts[$candidate->id]['percentage'] }}% ({{ $voteCounts[$candidate->id]['count'] }} votes)
                                                        </span>
                                                    </div>
                                                    <div class="w-full bg-navy-900 h-1.5 rounded-full overflow-hidden border border-navy-800">
                                                        <div class="bg-yellow-400 h-full rounded-full" style="width: {{ $voteCounts[$candidate->id]['percentage'] }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Bottom Action Buttons: Clear & Visible -->
                                    <div class="p-5 pt-0 flex items-center gap-2.5">
                                        <a href="{{ route('candidates.show', $candidate) }}" class="flex-1 text-center py-2.5 px-3 rounded-lg text-xs font-bold text-white bg-navy-900 hover:bg-navy-850 border border-slate-600 hover:border-slate-400 transition shadow-xs">
                                            View Candidate Profile
                                        </a>
                                        <a href="{{ route('vote') }}" class="py-2.5 px-4 rounded-lg text-xs font-bold text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 transition shadow-xs shrink-0">
                                            Vote &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-xl border border-slate-200 space-y-3">
                    <p class="text-base font-bold text-navy-950">No published election data found.</p>
                    <p class="text-xs text-charcoal-600">The Electoral Commission has not published contested positions for this session yet.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
