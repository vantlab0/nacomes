@extends('layouts.admin')

@section('title', $position->title . ' — Candidates Management — NACOMES Admin')
@section('page-title', 'Office: ' . $position->title)

@section('content')
<div class="space-y-6">
    <!-- Quick Position Switcher Bar (Instant Navigation between Offices) -->
    @if (isset($allPositions) && $allPositions->count() > 1)
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap pl-2 pr-1">Switch Office:</span>
                @foreach ($allPositions as $pos)
                    <a 
                        href="{{ route('admin.positions.show', $pos) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition border {{ $pos->id === $position->id ? 'bg-navy-950 text-yellow-400 border-navy-950 shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-charcoal-700 border-slate-200' }}"
                    >
                        <span>#{{ $pos->display_order }}</span>
                        <span>{{ $pos->title }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $pos->id === $position->id ? 'bg-navy-800 text-yellow-400' : 'bg-slate-200 text-slate-600' }}">
                            {{ $pos->candidates_count ?? $pos->candidates()->count() }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Breadcrumb, Title & Top Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('admin.positions.index') }}" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-navy-950 font-bold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back to Positions List</span>
                </a>
                <span>&bull;</span>
                <span class="text-slate-600">Executive Office #{{ $position->display_order }}</span>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1.5">
                <h2 class="text-xl font-bold text-navy-950">{{ $position->title }}</h2>
                @if ($position->is_published)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Ballot Published
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        Hidden / Draft
                    </span>
                @endif
            </div>

            @if ($position->description)
                <p class="text-xs text-slate-600 max-w-2xl mt-1 leading-relaxed">{{ $position->description }}</p>
            @endif
        </div>

        <!-- Distinct Clickable Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a 
                href="{{ route('admin.positions.index') }}" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-xs transition"
            >
                <span>&larr; All Positions</span>
            </a>

            <button 
                type="button" 
                data-modal-open="create-candidate-modal"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 font-bold text-xs transition border border-navy-950 shadow-xs cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Register Candidate</span>
            </button>
        </div>
    </div>

    <!-- Candidate Roster Section Header -->
    <div class="flex items-center justify-between px-1">
        <h3 class="text-sm font-bold text-navy-950 uppercase tracking-wider">
            Accredited Candidates ({{ $candidates->count() }})
        </h3>
        <span class="text-xs text-slate-500">Profiles displayed on the student discovery and ballot sheet</span>
    </div>

    <!-- Candidates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($candidates as $candidate)
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between hover:border-slate-300 transition">
                <div>
                    <!-- Candidate Photo & Badges -->
                    <div class="h-48 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                        @if ($candidate->photo_url)
                            <img 
                                src="{{ $candidate->photo_url }}" 
                                alt="{{ $candidate->name }}" 
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-16 h-16 rounded-full bg-navy-100 text-navy-800 flex items-center justify-center text-xl font-bold font-mono">
                                {{ strtoupper(substr($candidate->name, 0, 2)) }}
                            </div>
                        @endif

                        <div class="absolute top-3 right-3 flex flex-col gap-1.5 items-end">
                            @if ($candidate->is_published)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-700 text-white shadow-xs">
                                    Published
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-700 text-white shadow-xs">
                                    Hidden
                                </span>
                            @endif

                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-navy-950 text-yellow-400 shadow-xs font-mono border border-navy-800">
                                {{ $candidate->votes_count }} {{ Str::plural('vote', $candidate->votes_count) }}
                            </span>
                        </div>
                    </div>

                    <!-- Candidate Details -->
                    <div class="p-5 space-y-3">
                        <div>
                            <h3 class="font-bold text-navy-950 text-base leading-snug">{{ $candidate->name }}</h3>
                            <div class="flex items-center gap-2 mt-1 text-xs text-slate-600">
                                <span class="font-bold text-navy-900">{{ $candidate->academic_level }}</span>
                                @if ($candidate->matric_no)
                                    <span>&bull;</span>
                                    <span class="font-mono text-slate-700 font-semibold">{{ $candidate->matric_no }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-500">
                            <span>Office: <strong class="text-navy-950 font-bold">{{ $position->title }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Actions (Clearly Visible Buttons) -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500 font-mono">ID: #{{ $candidate->id }}</span>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            data-modal-open="edit-candidate-modal-{{ $candidate->id }}"
                            class="inline-flex items-center px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 font-bold text-xs shadow-xs transition cursor-pointer"
                        >
                            Edit
                        </button>

                        <button 
                            type="button" 
                            data-modal-open="delete-candidate-modal-{{ $candidate->id }}"
                            class="inline-flex items-center px-3.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs border border-red-200 shadow-xs transition cursor-pointer"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-500 shadow-xs">
                <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-sm font-bold text-navy-950">No candidates registered for this position.</p>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Register an aspirant to display them on the ballot sheet for {{ $position->title }}.</p>
                <button 
                    type="button" 
                    data-modal-open="create-candidate-modal"
                    class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 font-bold text-xs transition border border-navy-950 shadow-xs cursor-pointer"
                >
                    Register First Candidate
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- ================= CREATE CANDIDATE MODAL ================= -->
<div id="create-candidate-modal" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-navy-950">Add Candidate Profile</h3>
                <p class="text-xs text-slate-600 font-bold">Office: {{ $position->title }}</p>
            </div>
            <button type="button" data-modal-close="create-candidate-modal" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1 cursor-pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.candidates.store', $position) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="create_cand_name" class="block text-xs font-bold text-navy-900 mb-1">Full Candidate Name *</label>
                <input 
                    type="text" 
                    id="create_cand_name" 
                    name="name" 
                    required 
                    placeholder="e.g. Adebayo Ogunlesi" 
                    class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="create_academic_level" class="block text-xs font-bold text-navy-900 mb-1">Academic Level *</label>
                    <select 
                        id="create_academic_level" 
                        name="academic_level" 
                        required 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                    >
                        <option value="500 Level">500 Level</option>
                        <option value="400 Level" selected>400 Level</option>
                        <option value="300 Level">300 Level</option>
                        <option value="200 Level">200 Level</option>
                        <option value="100 Level">100 Level</option>
                    </select>
                </div>

                <div>
                    <label for="create_matric_no" class="block text-xs font-bold text-navy-900 mb-1">Matriculation No. (Optional)</label>
                    <input 
                        type="text" 
                        id="create_matric_no" 
                        name="matric_no" 
                        placeholder="e.g. CPE/2021/045" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                    >
                </div>
            </div>

            <div>
                <label for="create_photo" class="block text-xs font-bold text-navy-900 mb-1">Candidate Portrait Photograph</label>
                <input 
                    type="file" 
                    id="create_photo" 
                    name="photo" 
                    accept="image/png,image/jpeg,image/webp" 
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-navy-950 file:text-yellow-400 hover:file:bg-navy-900 cursor-pointer"
                >
                <span class="text-[10px] text-slate-500 block mt-1">Accepted formats: JPG, PNG, WEBP, JFIF (Max 10MB).</span>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="is_published" 
                        value="1" 
                        checked 
                        class="rounded border-slate-300 text-navy-900 focus:ring-navy-900 w-4 h-4"
                    >
                    <span class="text-xs font-bold text-charcoal-700">Publish candidate immediately</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    data-modal-close="create-candidate-modal"
                    class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 text-xs font-bold border border-navy-950 shadow-xs transition cursor-pointer"
                >
                    Save Candidate
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT & DELETE MODALS ================= -->
@foreach ($candidates as $candidate)
    <!-- Edit Candidate Modal -->
    <div id="edit-candidate-modal-{{ $candidate->id }}" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-navy-950">Edit Profile: {{ $candidate->name }}</h3>
                <button type="button" data-modal-close="edit-candidate-modal-{{ $candidate->id }}" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_cand_name_{{ $candidate->id }}" class="block text-xs font-bold text-navy-900 mb-1">Full Candidate Name *</label>
                    <input 
                        type="text" 
                        id="edit_cand_name_{{ $candidate->id }}" 
                        name="name" 
                        required 
                        value="{{ old('name', $candidate->name) }}" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                    >
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_academic_level_{{ $candidate->id }}" class="block text-xs font-bold text-navy-900 mb-1">Academic Level *</label>
                        <select 
                            id="edit_academic_level_{{ $candidate->id }}" 
                            name="academic_level" 
                            required 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                        >
                            @foreach (['500 Level', '400 Level', '300 Level', '200 Level', '100 Level'] as $lvl)
                                <option value="{{ $lvl }}" {{ old('academic_level', $candidate->academic_level) === $lvl ? 'selected' : '' }}>
                                    {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_matric_no_{{ $candidate->id }}" class="block text-xs font-bold text-navy-900 mb-1">Matriculation No.</label>
                        <input 
                            type="text" 
                            id="edit_matric_no_{{ $candidate->id }}" 
                            name="matric_no" 
                            value="{{ old('matric_no', $candidate->matric_no) }}" 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 shadow-xs px-3 py-2"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-navy-900 mb-1">Update Photograph</label>
                    @if ($candidate->photo_url)
                        <div class="flex items-center gap-3 mb-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                            <img src="{{ $candidate->photo_url }}" alt="Current Photo" class="w-12 h-12 object-cover rounded-md">
                            <span class="text-[11px] text-slate-500">Current photo is loaded. Select a new file below if you wish to replace it.</span>
                        </div>
                    @endif
                    <input 
                        type="file" 
                        name="photo" 
                        accept="image/png,image/jpeg,image/webp" 
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-navy-950 file:text-yellow-400 hover:file:bg-navy-900 cursor-pointer"
                    >
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_published" 
                            value="1" 
                            {{ $candidate->is_published ? 'checked' : '' }} 
                            class="rounded border-slate-300 text-navy-900 focus:ring-navy-900 w-4 h-4"
                        >
                        <span class="text-xs font-bold text-charcoal-700">Published on candidate roster and ballot</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        data-modal-close="edit-candidate-modal-{{ $candidate->id }}"
                        class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 text-xs font-bold border border-navy-950 shadow-xs transition cursor-pointer"
                    >
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Candidate Modal -->
    <div id="delete-candidate-modal-{{ $candidate->id }}" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $candidate->votes_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-950">
                        {{ $candidate->votes_count > 0 ? 'Candidate Deletion Blocked' : 'Remove Candidate' }}
                    </h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $candidate->name }}</p>
                </div>
            </div>

            @if ($candidate->votes_count > 0)
                <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-300 text-xs text-amber-900 leading-relaxed space-y-1">
                    <p class="font-bold">Cannot remove this candidate because {{ $candidate->votes_count }} {{ Str::plural('vote', $candidate->votes_count) }} have been cast for them.</p>
                    <p class="text-amber-800">To maintain ballot integrity, candidate profiles with recorded votes cannot be removed. You can uncheck "Published" to hide them instead.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button 
                        type="button" 
                        data-modal-close="delete-candidate-modal-{{ $candidate->id }}"
                        class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition cursor-pointer"
                    >
                        Dismiss
                    </button>
                </div>
            @else
                <p class="text-xs text-charcoal-700 leading-relaxed">
                    Are you sure you want to permanently remove candidate <strong>"{{ $candidate->name }}"</strong>? This action cannot be reversed.
                </p>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button 
                        type="button" 
                        data-modal-close="delete-candidate-modal-{{ $candidate->id }}"
                        class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                    >
                        Cancel
                    </button>

                    <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold border border-red-700 shadow-xs transition cursor-pointer"
                        >
                            Confirm Removal
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endforeach

@endsection
