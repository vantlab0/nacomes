<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of all executive election positions.
     */
    public function index(): View
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        $positions = collect();

        if ($election) {
            $positions = Position::where('election_id', $election->id)
                ->withCount(['candidates', 'votes'])
                ->orderBy('display_order')
                ->get();
        }

        return view('admin.positions.index', [
            'election' => $election,
            'positions' => $positions,
        ]);
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'display_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        if (! $election) {
            $election = Election::create([
                'title' => 'NACOMES Departmental Executive Elections',
                'status' => 'open',
            ]);
        }

        $position = Position::create([
            'election_id' => $election->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'display_order' => (int) $validated['display_order'],
            'is_published' => $request->boolean('is_published', true),
        ]);

        AuditLog::record('position_created', "Created new executive position: '{$position->title}' in election '{$election->title}'.");

        return back()->with('status', "Position '{$position->title}' created successfully.");
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'display_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $position->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'display_order' => (int) $validated['display_order'],
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditLog::record('position_updated', "Updated position details for '{$position->title}'.");

        return back()->with('status', "Position '{$position->title}' updated successfully.");
    }

    /**
     * Remove the specified position from storage when safe.
     */
    public function destroy(Position $position): RedirectResponse
    {
        // Safety check: Prevent deletion if recorded votes depend on this position
        if ($position->votes()->exists()) {
            return back()->withErrors([
                'error' => "Cannot delete position '{$position->title}' because recorded student ballots already depend on it. You can set it to unpublished to hide it without destroying vote records.",
            ]);
        }

        $title = $position->title;
        $position->delete();

        AuditLog::record('position_deleted', "Deleted position '{$title}' (Zero votes cast).");

        return redirect()->route('admin.positions.index')->with('status', "Position '{$title}' deleted successfully.");
    }
}
