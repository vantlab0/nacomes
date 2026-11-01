<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\Position;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    /**
     * Display election governance, configuration, status, and certified results.
     */
    public function index(): View
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        if (! $election) {
            $election = Election::create([
                'title' => 'NACOMES Departmental Executive Elections',
                'description' => 'General elections for the executive leadership of the National Association of Computer Engineering Students.',
                'status' => 'draft',
                'results_published' => false,
            ]);
        }

        $verifiedStudentsCount = User::where('is_admin', false)->whereNotNull('email_verified_at')->count();
        $distinctVotersCount = Vote::where('election_id', $election->id)->distinct('user_id')->count('user_id');
        $turnoutPercentage = $verifiedStudentsCount > 0 ? round(($distinctVotersCount / $verifiedStudentsCount) * 100, 1) : 0;

        // Results compilation per position
        $positions = Position::where('election_id', $election->id)
            ->with(['candidates'])
            ->orderBy('display_order')
            ->get();

        $results = [];

        foreach ($positions as $position) {
            $totalVotes = $position->votes()->count();
            $candidateBreakdown = [];

            foreach ($position->candidates as $candidate) {
                $count = $candidate->votes()->count();
                $percentage = $totalVotes > 0 ? round(($count / $totalVotes) * 100, 1) : 0;

                $candidateBreakdown[] = [
                    'candidate' => $candidate,
                    'votes' => $count,
                    'percentage' => $percentage,
                ];
            }

            usort($candidateBreakdown, fn ($a, $b) => $b['votes'] <=> $a['votes']);

            $results[$position->id] = [
                'position' => $position,
                'total_votes' => $totalVotes,
                'candidates' => $candidateBreakdown,
            ];
        }

        return view('admin.election.index', [
            'election' => $election,
            'verifiedStudentsCount' => $verifiedStudentsCount,
            'distinctVotersCount' => $distinctVotersCount,
            'turnoutPercentage' => $turnoutPercentage,
            'results' => $results,
        ]);
    }

    /**
     * Update election details and scheduling times.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->firstOrFail();

        $election->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'] ? Carbon::parse($validated['starts_at']) : null,
            'ends_at' => $validated['ends_at'] ? Carbon::parse($validated['ends_at']) : null,
        ]);

        AuditLog::record('election_updated', "Updated election settings for '{$election->title}'.");

        return back()->with('status', 'Election details updated successfully.');
    }

    /**
     * Open or close balloting for the active election.
     */
    public function toggleStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,open,closed'],
        ]);

        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->firstOrFail();

        $oldStatus = $election->status;
        $election->update([
            'status' => $validated['status'],
        ]);

        AuditLog::record('election_status_changed', "Changed election status from '{$oldStatus}' to '{$validated['status']}'.");

        return back()->with('status', "Election status changed to '".ucfirst($validated['status'])."'.");
    }

    /**
     * Configure public result visibility and authorize official results publication.
     */
    public function toggleResults(Request $request): RedirectResponse
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->firstOrFail();

        $newResultsState = $request->boolean('results_published');

        $election->update([
            'results_published' => $newResultsState,
        ]);

        $actionDesc = $newResultsState
            ? 'Authorized official election results for public candidate page visibility.'
            : 'De-authorized public results visibility (Results restricted to administration).';

        AuditLog::record('results_visibility_changed', $actionDesc);

        $statusMessage = $newResultsState
            ? 'Official election results have been authorized for public display.'
            : 'Public results display has been hidden.';

        return back()->with('status', $statusMessage);
    }
}
