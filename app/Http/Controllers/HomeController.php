<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Position;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Display the NACOMES election landing page.
     */
    public function index(): View
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        $offices = [];

        if ($election) {
            $positions = Position::where('election_id', $election->id)
                ->where('is_published', true)
                ->withCount('publishedCandidates')
                ->orderBy('display_order')
                ->get();

            foreach ($positions as $position) {
                $offices[] = [
                    'title' => $position->title,
                    'description' => $position->description,
                    'candidates_count' => $position->published_candidates_count,
                ];
            }
        }

        return view('home', [
            'election' => $election,
            'offices' => $offices,
        ]);
    }
}
