@extends('layouts.admin')

@section('title', 'Positions Management — NACOMES Admin')
@section('page-title', 'Executive Positions Management')

@section('content')
<div class="space-y-6">
    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-xs">
        <div>
            <h2 class="text-base font-bold text-navy-950">Active Executive Offices</h2>
            <p class="text-xs text-slate-500 mt-0.5">Click any position to view and manage its accredited candidate roster.</p>
        </div>

        <button 
            type="button" 
            data-modal-open="create-position-modal"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 font-bold text-xs transition border border-navy-900 shadow-xs cursor-pointer shrink-0"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create New Position</span>
        </button>
    </div>

    <!-- Positions Listing Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-charcoal-700">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 w-16 text-center">Order</th>
                        <th class="py-3.5 px-4">Executive Position</th>
                        <th class="py-3.5 px-4">Description / Scope</th>
                        <th class="py-3.5 px-4 text-center">Roster Size</th>
                        <th class="py-3.5 px-4 text-center">Votes Recorded</th>
                        <th class="py-3.5 px-4 text-center">Ballot Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($positions as $position)
                        <tr class="hover:bg-slate-50 transition">
                            <!-- Display Order -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-2 py-1 rounded bg-slate-100 text-navy-950 font-mono font-bold text-xs border border-slate-200">
                                    #{{ $position->display_order }}
                                </span>
                            </td>

                            <!-- Clickable Title & Details -->
                            <td class="py-4 px-4">
                                <a 
                                    href="{{ route('admin.positions.show', $position) }}" 
                                    class="group inline-block"
                                    title="Click to view and manage candidates for {{ $position->title }}"
                                >
                                    <div class="font-bold text-navy-950 text-sm group-hover:text-royal-700 group-hover:underline flex items-center gap-1.5">
                                        <span>{{ $position->title }}</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-royal-700 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">Click to open candidate management &rarr;</span>
                                </a>
                            </td>

                            <!-- Description -->
                            <td class="py-4 px-4 text-slate-600 max-w-xs truncate" title="{{ $position->description }}">
                                {{ $position->description ?? 'No specific duties configured.' }}
                            </td>

                            <!-- Candidate count badge -->
                            <td class="py-4 px-4 text-center">
                                <a 
                                    href="{{ route('admin.positions.show', $position) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-navy-50 text-navy-900 border border-navy-200 hover:bg-navy-100 transition"
                                >
                                    <span>{{ $position->candidates_count }}</span>
                                    <span>{{ Str::plural('Aspirant', $position->candidates_count) }}</span>
                                </a>
                            </td>

                            <!-- Votes Cast -->
                            <td class="py-4 px-4 text-center font-mono font-bold text-slate-800 text-xs">
                                {{ number_format($position->votes_count) }}
                            </td>

                            <!-- Publication Status -->
                            <td class="py-4 px-4 text-center">
                                @if ($position->is_published)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        Hidden
                                    </span>
                                @endif
                            </td>

                            <!-- Clearly Visible Action Buttons -->
                            <td class="py-4 px-4 text-right space-x-2 whitespace-nowrap">
                                <a 
                                    href="{{ route('admin.positions.show', $position) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 font-bold text-xs border border-navy-900 shadow-xs transition"
                                    title="Manage candidates for this position"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>Manage</span>
                                </a>

                                <button 
                                    type="button" 
                                    data-modal-open="edit-position-modal-{{ $position->id }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-slate-800 font-bold text-xs border border-slate-300 shadow-xs transition cursor-pointer"
                                >
                                    Edit
                                </button>

                                <button 
                                    type="button" 
                                    data-modal-open="delete-position-modal-{{ $position->id }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs border border-red-200 shadow-xs transition cursor-pointer"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <p class="text-sm font-bold text-navy-950">No executive positions created yet.</p>
                                <p class="text-xs text-slate-500 mt-1">Click "Create New Position" above to initialize your election positions.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= CREATE POSITION MODAL ================= -->
<div id="create-position-modal" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-navy-950">Add Executive Position</h3>
            <button type="button" data-modal-close="create-position-modal" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1 cursor-pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.positions.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="create_title" class="block text-xs font-bold text-navy-900 mb-1">Position Title *</label>
                <input 
                    type="text" 
                    id="create_title" 
                    name="title" 
                    required 
                    placeholder="e.g. President, Vice President, General Secretary" 
                    class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                >
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="create_display_order" class="block text-xs font-bold text-navy-900 mb-1">Display Order *</label>
                    <input 
                        type="number" 
                        id="create_display_order" 
                        name="display_order" 
                        required 
                        min="1" 
                        value="{{ ($positions->max('display_order') ?? 0) + 1 }}" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                    >
                    <span class="text-[10px] text-slate-500 block mt-0.5">Determines ballot appearance order.</span>
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_published" 
                            value="1" 
                            checked 
                            class="rounded border-slate-300 text-navy-900 focus:ring-navy-900 w-4 h-4"
                        >
                        <span class="text-xs font-bold text-charcoal-700">Publish immediately</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="create_description" class="block text-xs font-bold text-navy-900 mb-1">Description / Guidelines (Optional)</label>
                <textarea 
                    id="create_description" 
                    name="description" 
                    rows="3" 
                    placeholder="Describe duties and eligibility for this executive office..." 
                    class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                ></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    data-modal-close="create-position-modal"
                    class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 text-xs font-bold border border-navy-950 shadow-xs transition cursor-pointer"
                >
                    Save Position
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT & DELETE MODALS ================= -->
@foreach ($positions as $position)
    <!-- Edit Modal -->
    <div id="edit-position-modal-{{ $position->id }}" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-5 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-navy-950">Edit Position: {{ $position->title }}</h3>
                <button type="button" data-modal-close="edit-position-modal-{{ $position->id }}" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.positions.update', $position) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_title_{{ $position->id }}" class="block text-xs font-bold text-navy-900 mb-1">Position Title *</label>
                    <input 
                        type="text" 
                        id="edit_title_{{ $position->id }}" 
                        name="title" 
                        required 
                        value="{{ old('title', $position->title) }}" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                    >
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_display_order_{{ $position->id }}" class="block text-xs font-bold text-navy-900 mb-1">Display Order *</label>
                        <input 
                            type="number" 
                            id="edit_display_order_{{ $position->id }}" 
                            name="display_order" 
                            required 
                            min="1" 
                            value="{{ old('display_order', $position->display_order) }}" 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                        >
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_published" 
                                value="1" 
                                {{ $position->is_published ? 'checked' : '' }} 
                                class="rounded border-slate-300 text-navy-900 focus:ring-navy-900 w-4 h-4"
                            >
                            <span class="text-xs font-bold text-charcoal-700">Published on ballot</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="edit_description_{{ $position->id }}" class="block text-xs font-bold text-navy-900 mb-1">Description / Guidelines</label>
                    <textarea 
                        id="edit_description_{{ $position->id }}" 
                        name="description" 
                        rows="3" 
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-900 focus:ring-navy-900 px-3 py-2 shadow-xs"
                    >{{ old('description', $position->description) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        data-modal-close="edit-position-modal-{{ $position->id }}"
                        class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 rounded-lg bg-navy-950 hover:bg-navy-900 text-yellow-400 text-xs font-bold border border-navy-950 shadow-xs transition cursor-pointer"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-position-modal-{{ $position->id }}" class="admin-modal fixed inset-0 z-50 hidden bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $position->votes_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-950">
                        {{ $position->votes_count > 0 ? 'Deletion Blocked' : 'Delete Position' }}
                    </h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $position->title }}</p>
                </div>
            </div>

            @if ($position->votes_count > 0)
                <div class="p-3.5 rounded-lg bg-amber-50 border border-amber-300 text-xs text-amber-900 leading-relaxed space-y-1">
                    <p class="font-bold">Cannot delete this position because {{ $position->votes_count }} {{ Str::plural('vote', $position->votes_count) }} have already been recorded.</p>
                    <p class="text-amber-800">To maintain audit integrity, positions with cast ballots cannot be deleted. You may unpublish this position instead.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button 
                        type="button" 
                        data-modal-close="delete-position-modal-{{ $position->id }}"
                        class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition cursor-pointer"
                    >
                        Dismiss
                    </button>
                </div>
            @else
                <p class="text-xs text-charcoal-700 leading-relaxed">
                    Are you sure you want to permanently delete the position <strong>"{{ $position->title }}"</strong>? This action cannot be undone.
                </p>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button 
                        type="button" 
                        data-modal-close="delete-position-modal-{{ $position->id }}"
                        class="px-4 py-2 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer"
                    >
                        Cancel
                    </button>

                    <form method="POST" action="{{ route('admin.positions.destroy', $position) }}">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold border border-red-700 shadow-xs transition cursor-pointer"
                        >
                            Confirm Deletion
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endforeach

@endsection
