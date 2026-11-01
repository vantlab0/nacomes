@extends('layouts.app')

@section('title', 'Election Not Open — NACOMES Elections')
@section('description', 'The NACOMES departmental election is currently closed or scheduled for an upcoming date.')

@section('content')
    <div class="min-h-[calc(100vh-14rem)] py-16 bg-slate-50 flex flex-col justify-center sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto text-center px-4 space-y-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-950 border border-navy-800 flex items-center justify-center text-yellow-400 mx-auto shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2" />
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2" stroke-linecap="round" />
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2" stroke-linecap="round" />
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2" />
                </svg>
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    Balloting Inactive
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-950 tracking-tight">
                    Voting Booth Currently Closed
                </h1>
                <p class="text-xs sm:text-sm text-charcoal-600 leading-relaxed">
                    @if ($election)
                        The {{ $election->title }} is currently marked as <strong class="text-navy-950 font-bold">{{ $election->status }}</strong>. Electronic balloting is only permitted during authorized voting windows.
                    @else
                        There is currently no active election open for voting. Please check back when official balloting commences.
                    @endif
                </p>
            </div>

            <div class="p-5 rounded-xl bg-white border border-slate-200 text-xs text-charcoal-700 text-left space-y-2 shadow-2xs">
                <strong class="text-navy-950 block font-bold">What you can do right now:</strong>
                <ul class="space-y-1.5 list-disc list-inside text-slate-600">
                    <li>Browse verified candidate profiles on the <a href="{{ route('candidates') }}" class="text-royal-700 font-semibold hover:underline">Candidates page</a>.</li>
                    <li>Ensure your institutional student email is verified.</li>
                    <li>Inspect the departmental electoral guidelines.</li>
                </ul>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-md text-xs font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                    &larr; Return to Homepage
                </a>
                <a href="{{ route('candidates') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-md text-xs font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition shadow-2xs">
                    Explore Candidates
                </a>
            </div>
        </div>
    </div>
@endsection
