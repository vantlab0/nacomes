@extends('layouts.admin')

@section('title', 'Admin Dashboard — NACOMES Elections')
@section('page-title', 'Electoral Overview & Dashboard')

@section('content')
    <!-- Primary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Total Registered Students -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Registered Students</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-navy-950 font-mono">{{ number_format($totalStudents) }}</span>
                <span class="text-xs font-medium text-slate-500">{{ $suspendedStudents }} suspended</span>
            </div>
            <div class="text-[11px] text-slate-400">Undergraduate student roster</div>
        </div>

        <!-- Metric 2: Verified Students -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Verified Voters</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-emerald-700 font-mono">{{ number_format($verifiedStudents) }}</span>
                <span class="text-xs font-medium text-emerald-600 font-mono">
                    {{ $totalStudents > 0 ? round(($verifiedStudents / $totalStudents) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="text-[11px] text-slate-400">Institutional email accredited</div>
        </div>

        <!-- Metric 3: Overall Turnout -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Overall Turnout</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-royal-700 font-mono">{{ $turnoutPercentage }}%</span>
                <span class="text-xs font-medium text-slate-600 font-mono">{{ $distinctVotersCount }} ballots</span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                <div class="bg-royal-600 h-full rounded-full" style="width: {{ min(100, $turnoutPercentage) }}%"></div>
            </div>
        </div>

        <!-- Metric 4: Election Status & Offices -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-2">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Election Status</span>
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider {{ $election && $election->isOpen() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                    <span class="w-2 h-2 rounded-full {{ $election && $election->isOpen() ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $election ? $election->status : 'Draft' }}
                </span>
                <span class="text-xs text-slate-500">{{ $totalPositions }} Offices</span>
            </div>
            <div class="text-[11px] text-slate-400">{{ $publishedCandidates }} published aspirants</div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    @php
        $regIsOpen = \App\Models\Setting::registrationIsOpen();
    @endphp
    <div class="bg-navy-950 text-white rounded-xl p-6 border border-navy-800 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2.5">
                <h2 class="text-base font-bold text-white">Active Election: {{ $election ? $election->title : 'NACOMES Elections' }}</h2>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold font-mono uppercase {{ $regIsOpen ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $regIsOpen ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                    Registration: {{ $regIsOpen ? 'OPEN' : 'CLOSED' }}
                </span>
            </div>
            <p class="text-xs text-slate-300">
                Public Results: <strong class="text-emerald-400 font-mono">Live Poll Progress Displayed</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('admin.registration.toggle') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-lg text-xs font-bold transition shadow-xs cursor-pointer {{ $regIsOpen ? 'bg-navy-900 hover:bg-navy-850 text-red-300 hover:text-red-200 border border-red-500/40' : 'bg-emerald-600 hover:bg-emerald-500 text-white border border-emerald-500' }}"
                >
                    {{ $regIsOpen ? 'Close Registration' : 'Open Registration' }}
                </button>
            </form>
            <a href="{{ route('admin.positions.index') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 transition shadow-xs">
                + Add Candidate
            </a>
            <a href="{{ route('admin.positions.index') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-navy-900 hover:bg-navy-850 border border-slate-600 transition shadow-xs">
                Manage Positions
            </a>
            <a href="{{ route('admin.voters.index') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-navy-900 hover:bg-navy-850 border border-slate-600 transition shadow-xs">
                Voter Directory
            </a>
            <a href="{{ route('admin.election.index') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-navy-800 hover:bg-navy-700 border border-slate-600 transition shadow-xs">
                View Elections
            </a>
        </div>
    </div>

    <!-- Recent Administrative Activity Audit Log -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-navy-950">Recent Administrative Audit Trail</h3>
                <p class="text-xs text-slate-500">Security audit records of governance actions</p>
            </div>
            <span class="text-xs text-slate-400">{{ $recentLogs->count() }} recent events</span>
        </div>

        @if ($recentLogs->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                No administrative activity recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Timestamp</th>
                            <th class="px-5 py-3">Administrator</th>
                            <th class="px-5 py-3">Action</th>
                            <th class="px-5 py-3">Description</th>
                            <th class="px-5 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-charcoal-700">
                        @foreach ($recentLogs as $log)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $log->created_at->format('M d, Y • h:i:s A') }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap font-medium text-navy-950">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-100 text-slate-700">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-600 max-w-md truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap font-mono text-slate-400 text-[11px]">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
