<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    /**
     * Display the candidates discovery page grouped by contested position.
     */
    public function index(Request $request): View
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        $positions = collect();
        $voteCounts = [];
        $positionTotals = [];

        if ($election) {
            $positions = Position::where('election_id', $election->id)
                ->where('is_published', true)
                ->with(['publishedCandidates'])
                ->orderBy('display_order')
                ->get();

            // Live poll progress: vote shares and tallies are public by default
            foreach ($positions as $position) {
                $total = $position->votes()->count();
                $positionTotals[$position->id] = $total;

                foreach ($position->publishedCandidates as $candidate) {
                    $count = $candidate->votes()->count();
                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    $voteCounts[$candidate->id] = [
                        'count' => $count,
                        'percentage' => $percentage,
                    ];
                }
            }
        }

        return view('candidates.index', [
            'election' => $election,
            'positions' => $positions,
            'resultsPublished' => true,
            'voteCounts' => $voteCounts,
            'positionTotals' => $positionTotals,
        ]);
    }

    /**
     * Display a specific candidate's profile and credentials.
     */
    public function show(Candidate $candidate): View
    {
        abort_unless($candidate->is_published, 404);

        $candidate->load(['position', 'election']);

        return view('candidates.show', [
            'candidate' => $candidate,
            'election' => $candidate->election,
            'position' => $candidate->position,
        ]);
    }
}
