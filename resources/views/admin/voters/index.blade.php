@extends('layouts.admin')

@section('title', 'Voter Management — NACOMES Elections')
@section('page-title', 'Student Voter Directory & Accreditation')

@section('content')
    <!-- Search & Filter Controls -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-4">
        <form method="GET" action="{{ route('admin.voters.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search by student name, matriculation number, or email..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-md border border-slate-300 text-xs text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-900 transition"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2" />
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <select
                    name="status"
                    class="px-3 py-2.5 rounded-md border border-slate-300 text-xs text-charcoal-900 focus:outline-none focus:ring-2 focus:ring-navy-900 transition bg-white"
                >
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Students</option>
                    <option value="verified" {{ $statusFilter === 'verified' ? 'selected' : '' }}>Verified Only</option>
                    <option value="unverified" {{ $statusFilter === 'unverified' ? 'selected' : '' }}>Unverified Only</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-navy-950 hover:bg-navy-900 transition shrink-0"
                >
                    Apply Filter
                </button>

                @if ($search || $statusFilter !== 'all')
                    <a
                        href="{{ route('admin.voters.index') }}"
                        class="px-3 py-2.5 rounded-md text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition shrink-0"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Voters Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-navy-950">
                Registered Student Accounts ({{ $voters->total() }})
            </h3>
            <span class="text-xs text-slate-400">Page {{ $voters->currentPage() }} of {{ $voters->lastPage() }}</span>
        </div>

        @if ($voters->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400 space-y-1">
                <p class="font-medium text-slate-600 text-sm">No student voters found matching your criteria.</p>
                <p>Try refining your search terms or clearing the filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5">Student Name</th>
                            <th class="px-5 py-3.5">Matriculation No</th>
                            <th class="px-5 py-3.5">Student Email</th>
                            <th class="px-5 py-3.5">Accreditation</th>
                            <th class="px-5 py-3.5">Account Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-charcoal-700">
                        @foreach ($voters as $voter)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-navy-950">
                                    {{ $voter->name }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-royal-700 font-medium">
                                    {{ $voter->matric_no }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{ $voter->email }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($voter->email_verified_at)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            ✓ Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Unverified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($voter->is_suspended)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-200">
                                            Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                    <!-- View Details Button -->
                                    <button
                                        type="button"
                                        data-modal-open="voter-modal-{{ $voter->id }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 border border-slate-300 shadow-xs transition cursor-pointer"
                                    >
                                        View Details
                                    </button>

                                    <!-- Suspend / Reactivate Button -->
                                    <button
                                        type="button"
                                        data-modal-open="suspend-modal-{{ $voter->id }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-xs cursor-pointer {{ $voter->is_suspended ? 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300' : 'text-red-700 bg-red-50 hover:bg-red-100 border border-red-200' }}"
                                    >
                                        {{ $voter->is_suspended ? 'Reactivate' : 'Suspend' }}
                                    </button>
                                </td>
                            </tr>

                            <!-- Voter Details Modal -->
                            <div id="voter-modal-{{ $voter->id }}" class="admin-modal fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
                                <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 space-y-5 shadow-2xl text-charcoal-900">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                        <h4 class="text-sm font-bold text-navy-950">Student Voter Profile</h4>
                                        <button type="button" data-modal-close="voter-modal-{{ $voter->id }}" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
                                    </div>

                                    <div class="space-y-3 text-xs">
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Full Name</span>
                                            <strong class="text-navy-950">{{ $voter->name }}</strong>
                                        </div>
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Matriculation</span>
                                            <strong class="font-mono text-royal-700">{{ $voter->matric_no }}</strong>
                                        </div>
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Student Email</span>
                                            <strong class="text-slate-800">{{ $voter->email }}</strong>
                                        </div>
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Email Verification</span>
                                            <span class="{{ $voter->email_verified_at ? 'text-emerald-700 font-semibold' : 'text-amber-700' }}">
                                                {{ $voter->email_verified_at ? $voter->email_verified_at->format('M d, Y • h:i A') : 'Pending verification' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Registration Date</span>
                                            <span class="text-slate-600">{{ $voter->created_at->format('M d, Y • h:i A') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                            <span class="text-slate-500">Terms Accepted</span>
                                            <span class="text-slate-600">{{ $voter->terms_accepted_at ? $voter->terms_accepted_at->format('M d, Y') : 'Confirmed' }}</span>
                                        </div>
                                    </div>

                                    <div class="pt-2 text-right">
                                        <button type="button" data-modal-close="voter-modal-{{ $voter->id }}" class="px-4 py-2 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Suspend / Reactivate Confirmation Modal -->
                            <div id="suspend-modal-{{ $voter->id }}" class="admin-modal fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
                                <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 space-y-5 shadow-2xl text-charcoal-900">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl {{ $voter->is_suspended ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }} flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="text-sm font-bold text-navy-950">
                                                Confirm {{ $voter->is_suspended ? 'Reactivation' : 'Suspension' }}
                                            </h4>
                                            <p class="text-xs text-slate-600">
                                                @if ($voter->is_suspended)
                                                    Are you sure you want to restore voter portal access for <strong>{{ $voter->name }}</strong> ({{ $voter->matric_no }})?
                                                @else
                                                    Are you sure you want to suspend voter access for <strong>{{ $voter->name }}</strong> ({{ $voter->matric_no }})? They will not be able to log in or cast votes.
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-slate-50 rounded-lg text-[11px] text-slate-500">
                                        <strong>Integrity Guarantee:</strong> Suspending or reactivating this account will never alter, delete, or rewrite any previously recorded votes in the official ledger.
                                    </div>

                                    <div class="flex items-center justify-end gap-2.5 pt-2">
                                        <button type="button" data-modal-close="suspend-modal-{{ $voter->id }}" class="px-4 py-2 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                            Cancel
                                        </button>

                                        <form method="POST" action="{{ route('admin.voters.toggle-suspension', $voter) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-4 py-2 rounded-md text-xs font-semibold text-white transition {{ $voter->is_suspended ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' }}"
                                            >
                                                Yes, {{ $voter->is_suspended ? 'Reactivate Student' : 'Suspend Student' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $voters->links() }}
            </div>
        @endif
    </div>
@endsection
