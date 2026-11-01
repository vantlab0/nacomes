@extends('layouts.admin')

@section('title', 'Elections — NACOMES Admin')
@section('page-title', 'Elections')

@section('content')
<div class="space-y-6">
    <!-- Top Alert / Governance Status Banner -->
    <div class="bg-navy-950 text-white rounded-xl p-6 border border-navy-800 shadow-sm relative overflow-hidden">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-yellow-400 font-mono">Current Status:</span>
                    @if ($election->status === 'open')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            POLLS OPEN (VOTING ACTIVE)
                        </span>
                    @elseif ($election->status === 'closed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            POLLS CLOSED (BALLOTING CONCLUDED)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            DRAFT MODE (NOT ACCESSIBLE TO VOTERS)
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold tracking-tight text-white">{{ $election->title }}</h2>
                <p class="text-xs text-slate-300 max-w-xl">
                    Control balloting availability, manage scheduling parameters, and inspect live certified audit tallies.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <button 
                    type="button" 
                    data-modal-open="status-modal"
                    class="px-5 py-2.5 rounded-lg bg-yellow-400 hover:bg-yellow-300 text-navy-950 font-bold text-xs border border-yellow-400 transition cursor-pointer shadow-xs"
                >
                    Change Polls Status...
                </button>
            </div>
        </div>
    </div>

    <!-- Turnout Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Eligible Electorate</span>
            <div class="text-2xl font-bold text-navy-950 font-mono">{{ number_format($verifiedStudentsCount) }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Verified &amp; active student accounts</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Ballots Cast</span>
            <div class="text-2xl font-bold text-royal font-mono">{{ number_format($distinctVotersCount) }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Unique voters with recorded ballots</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Overall Turnout</span>
            <div class="text-2xl font-bold text-emerald-600 font-mono">{{ $turnoutPercentage }}%</div>
            <div class="w-full bg-slate-100 h-2 rounded-full mt-2 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $turnoutPercentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Two-Column Section: Settings & Certified Results -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Column 1: Election Settings Form -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-2xs space-y-5 lg:col-span-1">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-navy-950">Election Configuration</h3>
                <p class="text-xs text-slate-500 mt-0.5">Edit title, schedule, and guidelines.</p>
            </div>

            <form method="POST" action="{{ route('admin.election.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="election_title" class="block text-xs font-bold text-navy-900 mb-1">Election Title *</label>
                    <input 
                        type="text" 
                        id="election_title" 
                        name="title" 
                        required 
                        value="{{ old('title', $election->title) }}" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-royal focus:ring-royal shadow-2xs px-3 py-2"
                    >
                </div>

                <div>
                    <label for="election_description" class="block text-xs font-bold text-navy-900 mb-1">Description / Electorate Notice</label>
                    <textarea 
                        id="election_description" 
                        name="description" 
                        rows="3" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-royal focus:ring-royal shadow-2xs px-3 py-2"
                    >{{ old('description', $election->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="starts_at" class="block text-xs font-bold text-navy-900 mb-1">Voting Starts At</label>
                        <input 
                            type="datetime-local" 
                            id="starts_at" 
                            name="starts_at" 
                            value="{{ old('starts_at', $election->starts_at?->format('Y-m-d\TH:i')) }}" 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-royal focus:ring-royal shadow-2xs px-3 py-2 font-mono"
                        >
                    </div>

                    <div>
                        <label for="ends_at" class="block text-xs font-bold text-navy-900 mb-1">Voting Concludes At</label>
                        <input 
                            type="datetime-local" 
                            id="ends_at" 
                            name="ends_at" 
                            value="{{ old('ends_at', $election->ends_at?->format('Y-m-d\TH:i')) }}" 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-royal focus:ring-royal shadow-2xs px-3 py-2 font-mono"
                        >
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-2.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 font-bold text-xs shadow-2xs transition"
                    >
                        Save Configuration
                    </button>
                </div>
            </form>

            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-950 space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-bold">Live Public Poll Active</span>
                </div>
                <p class="text-[11px] text-emerald-800 leading-relaxed">
                    Real-time vote tallies and ballot progress are displayed publicly to all students by default.
                </p>
            </div>
        </div>

        <!-- Column 2 & 3: Certified Live Results Breakdown -->
        <div class="space-y-6 lg:col-span-2">
            <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
                <div>
                    <h3 class="text-base font-bold text-navy-950">Administrative Results Ledger</h3>
                    <p class="text-xs text-slate-500">Live certified breakdown per executive office.</p>
                </div>
                <span class="text-[11px] font-mono font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">
                    {{ count($results) }} {{ Str::plural('Office', count($results)) }}
                </span>
            </div>

            @forelse ($results as $item)
                @php
                    $position = $item['position'];
                    $totalVotes = $item['total_votes'];
                    $candidates = $item['candidates'];
                @endphp

                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-royal font-mono">Office #{{ $position->display_order }}</span>
                            <h4 class="text-base font-bold text-navy-950">{{ $position->title }}</h4>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-xs font-mono font-bold text-navy-900">{{ number_format($totalVotes) }}</span>
                            <span class="text-[11px] text-slate-500 block">Total Votes Recorded</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse ($candidates as $index => $candData)
                            @php
                                $candidate = $candData['candidate'];
                                $votes = $candData['votes'];
                                $pct = $candData['percentage'];
                                $isLeading = $index === 0 && $votes > 0;
                            @endphp

                            <div class="p-3.5 rounded-lg {{ $isLeading ? 'bg-navy-50/60 border border-navy-100' : 'bg-slate-50 border border-slate-100' }} space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @if ($candidate->photo_url)
                                            <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-navy-200 text-navy-800 flex items-center justify-center font-bold text-xs font-mono">
                                                {{ strtoupper(substr($candidate->name, 0, 2)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-navy-950 text-xs">{{ $candidate->name }}</span>
                                                @if ($isLeading)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-400 text-navy-950">
                                                        Leading
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-slate-500">{{ $candidate->academic_level }}</span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs font-bold font-mono text-navy-950">{{ number_format($votes) }} votes</span>
                                        <span class="text-xs font-mono text-slate-500 block">({{ $pct }}%)</span>
                                    </div>
                                </div>

                                <!-- Visual Bar -->
                                <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                    <div 
                                        class="{{ $isLeading ? 'bg-royal' : 'bg-slate-400' }} h-full rounded-full transition-all duration-500" 
                                        style="width: {{ $pct }}%"
                                    ></div>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs">
                                No candidates associated with this position.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-slate-200/80 p-8 text-center text-slate-500">
                    No positions or candidates found to tally results for.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= STATUS TOGGLE MODAL ================= -->
<div id="status-modal" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-navy-950">Change Poll Status</h3>
            <button type="button" data-modal-close="status-modal" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1">&times;</button>
        </div>

        <p class="text-xs text-charcoal-700 leading-relaxed">
            Select the new operating status for this election. Note that opening polls allows verified students to cast real votes, while closing polls strictly locks all ballot submissions.
        </p>

        <form method="POST" action="{{ route('admin.election.toggle-status') }}" class="space-y-4">
            @csrf

            <div class="space-y-2.5">
                <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="status" value="open" {{ $election->status === 'open' ? 'checked' : '' }} class="mt-0.5 text-royal focus:ring-royal">
                    <div>
                        <span class="text-xs font-bold text-emerald-700 block">Open (Active Balloting)</span>
                        <span class="text-[11px] text-slate-500">Students can access /vote and submit their ballots.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="status" value="closed" {{ $election->status === 'closed' ? 'checked' : '' }} class="mt-0.5 text-royal focus:ring-royal">
                    <div>
                        <span class="text-xs font-bold text-red-700 block">Closed (Concluded)</span>
                        <span class="text-[11px] text-slate-500">Voting is halted. No new ballots will be accepted.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="status" value="draft" {{ $election->status === 'draft' ? 'checked' : '' }} class="mt-0.5 text-royal focus:ring-royal">
                    <div>
                        <span class="text-xs font-bold text-amber-700 block">Draft (Setup Phase)</span>
                        <span class="text-[11px] text-slate-500">Positions and candidates can be configured in private.</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button 
                    type="button" 
                    data-modal-close="status-modal"
                    class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 text-xs font-bold shadow-2xs transition"
                >
                    Confirm Status Change
                </button>
            </div>
        </form>
    </div>
</div>


@endsection
