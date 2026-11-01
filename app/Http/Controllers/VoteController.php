<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteSubmitRequest;
use App\Models\Election;
use App\Models\Position;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
    /**
     * Display the student voting interface, ballot desk, or confirmation receipt.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuspended()) {
            abort(403, 'Your student voter account has been suspended by the Electoral Commission.');
        }

        // Retrieve active election
        $election = Election::where('status', 'open')->first();

        if (! $election) {
            $latestElection = Election::latest()->first();

            return view('vote.closed', [
                'election' => $latestElection,
            ]);
        }

        // Fetch published positions with their eligible candidates
        $positions = Position::where('election_id', $election->id)
            ->where('is_published', true)
            ->with(['publishedCandidates'])
            ->orderBy('display_order')
            ->get();

        // Check already cast votes for this student in this election
        $userVotes = Vote::where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->with(['position', 'candidate'])
            ->get()
            ->keyBy('position_id');

        $isCompleted = $positions->isNotEmpty() && $userVotes->count() >= $positions->count();

        return view('vote.index', [
            'election' => $election,
            'positions' => $positions,
            'userVotes' => $userVotes,
            'isCompleted' => $isCompleted,
        ]);
    }

    /**
     * Process and securely record the student's ballot within a database transaction.
     *
     * @throws ValidationException
     */
    public function store(VoteSubmitRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isSuspended()) {
            abort(403, 'Your student voter account has been suspended by the Electoral Commission.');
        }

        $electionId = (int) $request->input('election_id');
        $votes = (array) $request->input('votes');

        DB::transaction(function () use ($user, $electionId, $votes): void {
            // Re-verify election is still open within transaction
            $election = Election::where('id', $electionId)->lockForUpdate()->firstOrFail();

            if (! $election->isOpen()) {
                throw ValidationException::withMessages([
                    'election_id' => 'This election is closed. Ballots can no longer be accepted.',
                ]);
            }

            foreach ($votes as $positionId => $candidateId) {
                // Ensure student hasn't already voted for this position
                $alreadyVoted = Vote::where('election_id', $election->id)
                    ->where('user_id', $user->id)
                    ->where('position_id', $positionId)
                    ->exists();

                if ($alreadyVoted) {
                    $position = Position::find($positionId);
                    $positionName = $position ? $position->title : 'this office';

                    throw ValidationException::withMessages([
                        'votes' => "You have already cast a ballot for {$positionName}. Ballots cannot be resubmitted or modified.",
                    ]);
                }

                Vote::create([
                    'election_id' => $election->id,
                    'user_id' => $user->id,
                    'position_id' => (int) $positionId,
                    'candidate_id' => (int) $candidateId,
                ]);
            }
        });

        return redirect()->route('vote')->with('status', 'Your ballot has been securely submitted and permanently recorded. Your voting receipt is displayed below.');
    }
}
