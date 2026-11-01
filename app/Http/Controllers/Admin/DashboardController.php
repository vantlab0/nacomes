<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the primary administrative metrics and activity dashboard.
     */
    public function index(): View
    {
        $election = Election::where('status', 'open')->first()
            ?? Election::latest()->first();

        $totalStudents = User::where('is_admin', false)->count();
        $verifiedStudents = User::where('is_admin', false)->whereNotNull('email_verified_at')->count();
        $suspendedStudents = User::where('is_admin', false)->where('is_suspended', true)->count();
        $totalPositions = Position::count();
        $publishedCandidates = Candidate::where('is_published', true)->count();

        // Calculate turnout
        $distinctVotersCount = 0;
        $turnoutPercentage = 0.0;

        if ($election) {
            $distinctVotersCount = Vote::where('election_id', $election->id)
                ->distinct('user_id')
                ->count('user_id');

            $turnoutPercentage = $verifiedStudents > 0
                ? round(($distinctVotersCount / $verifiedStudents) * 100, 1)
                : 0.0;
        }

        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'election' => $election,
            'totalStudents' => $totalStudents,
            'verifiedStudents' => $verifiedStudents,
            'suspendedStudents' => $suspendedStudents,
            'totalPositions' => $totalPositions,
            'publishedCandidates' => $publishedCandidates,
            'distinctVotersCount' => $distinctVotersCount,
            'turnoutPercentage' => $turnoutPercentage,
            'recentLogs' => $recentLogs,
        ]);
    }
}
