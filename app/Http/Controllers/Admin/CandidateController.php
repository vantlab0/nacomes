<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Display position information and associated candidates for management.
     */
    public function show(Position $position): View
    {
        $position->load(['election']);
        $candidates = $position->candidates()
            ->withCount('votes')
            ->latest()
            ->get();

        $allPositions = Position::where('election_id', $position->election_id)
            ->withCount('candidates')
            ->orderBy('display_order')
            ->get();

        return view('admin.positions.show', [
            'position' => $position,
            'candidates' => $candidates,
            'election' => $position->election,
            'allPositions' => $allPositions,
        ]);
    }

    /**
     * Store a new candidate for the given position with photo upload handling.
     */
    public function store(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['nullable', 'string', 'max:50'],
            'academic_level' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,jfif', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $photoUrl = null;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('candidates', 'public');
            $photoUrl = Storage::url($path);
        }

        $candidate = Candidate::create([
            'election_id' => $position->election_id,
            'position_id' => $position->id,
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'] ?? null,
            'academic_level' => $validated['academic_level'],
            'photo_url' => $photoUrl,
            'is_published' => $request->boolean('is_published', true),
        ]);

        AuditLog::record('candidate_added', "Added aspirant '{$candidate->name}' to position '{$position->title}'.");

        return back()->with('status', "Candidate '{$candidate->name}' added successfully.");
    }

    /**
     * Update an existing candidate's information and photo.
     */
    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => ['nullable', 'string', 'max:50'],
            'academic_level' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,jfif', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $photoUrl = $candidate->photo_url;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('candidates', 'public');
            $photoUrl = Storage::url($path);
        }

        $candidate->update([
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'] ?? null,
            'academic_level' => $validated['academic_level'],
            'photo_url' => $photoUrl,
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditLog::record('candidate_updated', "Updated candidate '{$candidate->name}' details.");

        return back()->with('status', "Candidate '{$candidate->name}' updated successfully.");
    }

    /**
     * Remove the specified candidate from storage when safe.
     */
    public function destroy(Candidate $candidate): RedirectResponse
    {
        // Safety check: Prevent deletion if recorded votes depend on this candidate
        if ($candidate->votes()->exists()) {
            return back()->withErrors([
                'error' => "Cannot delete candidate '{$candidate->name}' because recorded votes already depend on this profile. You may set their status to unpublished instead.",
            ]);
        }

        $name = $candidate->name;
        $candidate->delete();

        AuditLog::record('candidate_removed', "Removed candidate '{$name}' (Zero votes recorded).");

        return back()->with('status', "Candidate '{$name}' removed successfully.");
    }
}
