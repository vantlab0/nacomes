@extends('layouts.app')

@section('title', 'Digital Voting Booth — NACOMES Elections')
@section('description', 'Accredited digital voting booth for the National Association of Computer Engineering Students.')

@section('content')
    <!-- Page Header -->
    <section class="bg-navy-950 text-white py-12 sm:py-14 border-b border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <nav class="flex items-center gap-2 text-xs text-slate-400" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}" class="hover:text-yellow-400 transition-colors">Home</a>
                        <span>/</span>
                        <span class="text-slate-200">Voting Booth</span>
                    </nav>

                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white">
                        {{ $election->title }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Official Student Digital Ballot • Department of Computer Engineering
                    </p>
                </div>

                <!-- Verified Student Badge -->
                <div class="flex items-center gap-3 bg-navy-900 border border-navy-800 p-3 rounded-lg">
                    <div class="w-10 h-10 rounded-full bg-navy-800 text-yellow-400 font-bold text-sm flex items-center justify-center border border-navy-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="text-xs">
                        <span class="text-slate-200 font-bold block">{{ auth()->user()->name }}</span>
                        <span class="text-yellow-400 font-mono">{{ auth()->user()->matric_no }}</span>
                        <span class="text-emerald-400 ml-1.5 font-medium">● Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-slate-50 min-h-[calc(100vh-20rem)]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Global Flash Messages / Status -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-900 flex items-start gap-3 shadow-2xs" role="alert">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <strong class="font-bold block">Electoral Submission Confirmation:</strong>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Global Errors Alert -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs sm:text-sm text-red-900 space-y-2 shadow-2xs" role="alert">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2" />
                            <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" />
                            <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" />
                        </svg>
                        <span>Please correct the ballot issues before submitting:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-800 space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($isCompleted)
                <!-- Completion Screen: Confirmed Ballot Receipt -->
                <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 shadow-xs space-y-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div class="space-y-2 max-w-lg mx-auto">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Accredited Ballot Confirmed &amp; Sealed
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy-950">
                            Your Vote Has Been Recorded
                        </h2>
                        <p class="text-xs sm:text-sm text-charcoal-600 leading-relaxed">
                            Thank you for participating in the NACOMES departmental election. Your ballot was encrypted and recorded in the departmental voting ledger.
                        </p>
                    </div>

                    <!-- Digital Receipt Card -->
                    <div class="max-w-2xl mx-auto bg-slate-50 border border-slate-200 rounded-xl p-6 text-left space-y-4">
                        <div class="border-b border-slate-200 pb-3 flex items-center justify-between text-xs">
                            <span class="font-bold uppercase tracking-wider text-charcoal-800">Electoral Receipt</span>
                            <span class="font-mono text-slate-500">
                                Submitted: {{ $userVotes->first()?->created_at?->format('M d, Y • h:i A') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-500 block">Voter Name</span>
                                <strong class="text-navy-950">{{ auth()->user()->name }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Matriculation Number</span>
                                <strong class="text-navy-950 font-mono">{{ auth()->user()->matric_no }}</strong>
                            </div>
                        </div>

                        <!-- Confirmed Offices Breakdown -->
                        <div class="pt-3 border-t border-slate-200 space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Recorded Selections</span>
                            <div class="space-y-2">
                                @foreach ($userVotes as $vote)
                                    <div class="flex items-center justify-between text-xs bg-white p-2.5 rounded border border-slate-200">
                                        <div>
                                            <span class="font-bold text-navy-950">{{ $vote->position->title }}</span>
                                            <span class="text-slate-500 block text-[11px]">Selected: {{ $vote->candidate->name }} ({{ $vote->candidate->academic_level }})</span>
                                        </div>
                                        <span class="text-emerald-700 font-semibold text-[11px] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                            ✓ Confirmed
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('candidates') }}" class="px-5 py-2.5 rounded-md text-xs font-semibold text-charcoal-700 bg-slate-100 hover:bg-slate-200 transition">
                            View Candidate Directory
                        </a>
                        <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-md text-xs font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition shadow-2xs">
                            Return to Homepage
                        </a>
                    </div>
                </div>
            @else
                <!-- Active Voting Interface Flow -->
                <form id="ballot-form" method="POST" action="{{ route('vote.store') }}" class="space-y-10" novalidate>
                    @csrf
                    <input type="hidden" name="election_id" value="{{ $election->id }}">

                    <!-- Progress & Instructions Banner -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-royal-700">Official Ballot Progress</span>
                            <h2 class="text-base sm:text-lg font-bold text-navy-950">
                                Contested Executive Offices (<span id="selected-count">0</span> of {{ $positions->count() }} Selected)
                            </h2>
                            <p class="text-xs text-charcoal-600">
                                Select one certified candidate per executive office. You can review your choices before final submission.
                            </p>
                        </div>

                        <!-- Progress Bar Indicator -->
                        <div class="w-full sm:w-48 space-y-1.5 shrink-0">
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Progress</span>
                                <span id="progress-percentage">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                <div id="progress-bar-fill" class="bg-yellow-400 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Positions Sections -->
                    <div class="space-y-12">
                        @foreach ($positions as $index => $position)
                            @php
                                $alreadyVoted = isset($userVotes[$position->id]);
                            @endphp

                            <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6" id="position-container-{{ $position->id }}">
                                <!-- Position Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-navy-950 text-yellow-400 font-mono">
                                                Office {{ $index + 1 }} of {{ $positions->count() }}
                                            </span>
                                            <h3 class="text-lg font-bold text-navy-950">{{ $position->title }}</h3>
                                        </div>
                                        @if ($position->description)
                                            <p class="text-xs text-charcoal-600 mt-1 max-w-xl">{{ $position->description }}</p>
                                        @endif
                                    </div>

                                    @if ($alreadyVoted)
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">
                                            ✓ Previously Recorded
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-slate-500 self-start sm:self-auto">
                                            1 Selection Allowed
                                        </span>
                                    @endif
                                </div>

                                <!-- Candidate Selection Cards -->
                                @if ($position->publishedCandidates->isEmpty())
                                    <div class="p-6 text-center bg-slate-50 rounded-lg border border-dashed border-slate-300 text-xs text-slate-500">
                                        No published candidates are available for this office.
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach ($position->publishedCandidates as $candidate)
                                            @php
                                                $isSelected = old("votes.{$position->id}") == $candidate->id;
                                                $isPreviouslyCast = $alreadyVoted && $userVotes[$position->id]->candidate_id === $candidate->id;
                                            @endphp

                                            <!-- Candidate Card matching Design Spec -->
                                            <label
                                                class="candidate-card relative bg-navy-950 rounded-xl border cursor-pointer select-none overflow-hidden flex flex-col justify-between transition-all duration-200 {{ $isPreviouslyCast || $isSelected ? 'border-yellow-400 ring-2 ring-yellow-400/50' : 'border-yellow-500/30 hover:border-yellow-400/60' }}"
                                                data-position-id="{{ $position->id }}"
                                                data-candidate-id="{{ $candidate->id }}"
                                                data-candidate-name="{{ $candidate->name }}"
                                                data-position-name="{{ $position->title }}"
                                            >
                                                <!-- Radio Input (Visually hidden for keyboard and form accessibility) -->
                                                <input
                                                    type="radio"
                                                    name="votes[{{ $position->id }}]"
                                                    value="{{ $candidate->id }}"
                                                    class="sr-only candidate-radio"
                                                    data-position-id="{{ $position->id }}"
                                                    {{ $isPreviouslyCast || $isSelected ? 'checked' : '' }}
                                                    {{ $alreadyVoted ? 'disabled' : '' }}
                                                />

                                                <div>
                                                    <!-- Upper Half: Portrait -->
                                                    <div class="relative h-48 w-full overflow-hidden bg-navy-900">
                                                        <img
                                                            src="{{ $candidate->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80' }}"
                                                            alt="Photo of {{ $candidate->name }}"
                                                            class="w-full h-full object-cover object-top"
                                                            loading="lazy"
                                                        />
                                                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-transparent to-transparent opacity-80"></div>
                                                        <!-- Academic Level Badge -->
                                                        <div class="absolute top-2.5 right-2.5">
                                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-yellow-400 text-navy-950">
                                                                {{ $candidate->academic_level }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Information Rows -->
                                                    <div class="p-4 space-y-3">
                                                        <div>
                                                            <h4 class="text-base font-bold text-white tracking-tight">
                                                                {{ $candidate->name }}
                                                            </h4>
                                                            <p class="text-xs text-slate-300 mt-0.5">{{ $candidate->matric_no ?: 'CPE Accredited' }}</p>
                                                        </div>

                                                        <!-- Concise Info Rows with line icons -->
                                                        <div class="space-y-1.5 text-xs border-y border-navy-800/80 py-2.5">
                                                            <div class="flex items-center justify-between text-slate-300">
                                                                <span class="flex items-center gap-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                    <span>Department</span>
                                                                </span>
                                                                <span class="text-slate-200">Comp. Engr.</span>
                                                            </div>
                                                            <div class="flex items-center justify-between text-slate-300">
                                                                <span class="flex items-center gap-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <span>Status</span>
                                                                </span>
                                                                <span class="text-emerald-400 font-medium">Eligible</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Full-Width Visible Clickable Selection Button -->
                                                <div class="p-4 pt-0">
                                                    <div class="selection-indicator w-full py-3 px-4 rounded-lg text-xs font-bold text-center transition-all border-2 cursor-pointer {{ $isPreviouslyCast || $isSelected ? 'bg-yellow-400 text-navy-950 border-yellow-400 shadow-xs' : 'bg-slate-800 hover:bg-slate-700 text-white border-slate-600 hover:border-yellow-400' }}">
                                                        <!-- When selected, use the label 'Your selection' before submission rather than implying the vote has already been recorded -->
                                                        <span class="selection-label">
                                                            @if ($isPreviouslyCast)
                                                                ✓ Recorded
                                                            @elseif ($isSelected)
                                                                ✓ Your selection
                                                            @else
                                                                Select Candidate
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Review & Submit Section -->
                    <div class="bg-navy-950 rounded-2xl text-white p-6 sm:p-10 border border-navy-800 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-navy-800 pb-6">
                            <div>
                                <span class="text-xs font-bold tracking-widest text-yellow-400 uppercase">Ballot Submission Review</span>
                                <h3 class="text-xl font-bold text-white mt-1">Ready to Cast Your Official Vote?</h3>
                                <p class="text-xs text-slate-300 mt-1 max-w-lg leading-relaxed">
                                    Confirm that you have reviewed your selections and finalized your choices. Once submitted, your voting token will be permanently marked as spent.
                                </p>
                            </div>

                            <button
                                id="review-submit-button"
                                type="button"
                                class="px-8 py-3.5 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition-colors shadow-2xs text-center shrink-0"
                            >
                                Review &amp; Cast Ballot
                            </button>
                        </div>

                        <!-- Selections Preview Summary -->
                        <div class="space-y-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Current Selections Summary</span>
                            <div id="selections-summary-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                <!-- Populated dynamically by JS -->
                                <p class="text-slate-500 italic col-span-full">No candidates selected yet. Please click on candidate cards above.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Dialog / Modal -->
                    <div id="confirmation-modal" class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                        <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl text-charcoal-900 animate-in fade-in zoom-in-95 duration-150">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-800 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <h3 id="modal-title" class="text-lg font-bold text-navy-950">Confirm Ballot Submission</h3>
                                    <p class="text-xs text-charcoal-600">
                                        You are about to cast your official ballot for the Department of Computer Engineering elections.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-charcoal-700 space-y-2">
                                <strong class="text-navy-950 block font-bold">Important Notice:</strong>
                                <ul class="list-disc list-inside space-y-1 text-slate-600">
                                    <li>Ballots cast are final and cannot be modified or re-cast.</li>
                                    <li>Your ballot choice is encrypted and stored securely.</li>
                                    <li>An official verification receipt will be displayed immediately upon submission.</li>
                                </ul>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    id="modal-cancel-button"
                                    type="button"
                                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition"
                                >
                                    Review Choices
                                </button>
                                <button
                                    id="modal-confirm-submit-button"
                                    type="submit"
                                    class="px-6 py-2.5 rounded-md text-xs font-semibold text-navy-950 bg-yellow-400 hover:bg-yellow-300 transition shadow-2xs"
                                >
                                    Yes, Submit Official Ballot
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>

    <!-- Client-side Interactive Voting & Progress Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('ballot-form');
            if (!form) return;

            const radioInputs = document.querySelectorAll('.candidate-radio');
            const cards = document.querySelectorAll('.candidate-card');
            const selectedCountEl = document.getElementById('selected-count');
            const progressPercentEl = document.getElementById('progress-percentage');
            const progressBarFill = document.getElementById('progress-bar-fill');
            const summaryList = document.getElementById('selections-summary-list');
            const totalPositions = {{ $positions->count() }};

            const reviewBtn = document.getElementById('review-submit-button');
            const modal = document.getElementById('confirmation-modal');
            const cancelBtn = document.getElementById('modal-cancel-button');

            const updateSelectionsUI = () => {
                const selections = {};
                let count = 0;

                radioInputs.forEach(radio => {
                    const card = radio.closest('.candidate-card');
                    const indicator = card.querySelector('.selection-indicator');
                    const label = card.querySelector('.selection-label');

                    if (radio.checked) {
                        card.classList.add('border-yellow-400', 'ring-2', 'ring-yellow-400/50');
                        card.classList.remove('border-yellow-500/30');

                        indicator.classList.add('bg-yellow-400', 'text-navy-950', 'border-yellow-300', 'font-bold');
                        indicator.classList.remove('bg-navy-900', 'text-slate-300', 'border-slate-700/80');
                        label.textContent = '✓ Your selection';

                        const posName = card.dataset.positionName;
                        const candName = card.dataset.candidateName;
                        selections[posName] = candName;
                        count++;
                    } else if (!radio.disabled) {
                        card.classList.remove('border-yellow-400', 'ring-2', 'ring-yellow-400/50');
                        card.classList.add('border-yellow-500/30');

                        indicator.classList.remove('bg-yellow-400', 'text-navy-950', 'border-yellow-300', 'font-bold');
                        indicator.classList.add('bg-navy-900', 'text-slate-300', 'border-slate-700/80');
                        label.textContent = 'Select Candidate';
                    }
                });

                if (selectedCountEl) selectedCountEl.textContent = count;
                const percentage = totalPositions > 0 ? Math.round((count / totalPositions) * 100) : 0;
                if (progressPercentEl) progressPercentEl.textContent = `${percentage}%`;
                if (progressBarFill) progressBarFill.style.width = `${percentage}%`;

                // Update summary preview
                if (summaryList) {
                    if (count === 0) {
                        summaryList.innerHTML = '<p class="text-slate-500 italic col-span-full">No candidates selected yet. Please click on candidate cards above.</p>';
                    } else {
                        summaryList.innerHTML = Object.entries(selections).map(([pos, cand]) => `
                            <div class="bg-navy-900 p-3 rounded-lg border border-navy-800 flex items-center justify-between">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">${pos}</span>
                                    <strong class="text-white text-xs">${cand}</strong>
                                </div>
                                <span class="text-yellow-400 text-xs font-bold">Selected</span>
                            </div>
                        `).join('');
                    }
                }
            };

            // Event listener for card clicks
            cards.forEach(card => {
                card.addEventListener('click', (e) => {
                    const radio = card.querySelector('.candidate-radio');
                    if (radio && !radio.disabled) {
                        radio.checked = true;
                        updateSelectionsUI();
                    }
                });
            });

            // Initial UI state
            updateSelectionsUI();

            // Modal review trigger
            if (reviewBtn && modal) {
                reviewBtn.addEventListener('click', () => {
                    const count = document.querySelectorAll('.candidate-radio:checked').length;
                    if (count === 0) {
                        alert('Please select at least one candidate before submitting your ballot.');
                        return;
                    }
                    modal.classList.remove('hidden');
                });
            }

            if (cancelBtn && modal) {
                cancelBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
@endsection
