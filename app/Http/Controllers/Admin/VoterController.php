<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    /**
     * Display a searchable, filterable list of registered students.
     */
    public function index(Request $request): View
    {
        $query = User::where('is_admin', false);

        // Search by name, matric_no, or email
        if ($search = $request->input('q')) {
            $normalizedSearch = User::normalizeMatricNo($search);

            $query->where(function ($q) use ($search, $normalizedSearch): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$normalizedSearch}%");
            });
        }

        // Filter by verification status
        if ($status = $request->input('status')) {
            if ($status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($status === 'suspended') {
                $query->where('is_suspended', true);
            }
        }

        $voters = $query->latest()->paginate(15)->withQueryString();

        return view('admin.voters.index', [
            'voters' => $voters,
            'search' => $request->input('q', ''),
            'statusFilter' => $request->input('status', 'all'),
        ]);
    }

    /**
     * Toggle the active suspension status of a student voter.
     */
    public function toggleSuspension(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin() || $user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot modify the suspension status of an administrative account.']);
        }

        $wasSuspended = $user->isSuspended();
        $newSuspensionState = ! $wasSuspended;

        $user->update([
            'is_suspended' => $newSuspensionState,
            'suspended_at' => $newSuspensionState ? now() : null,
        ]);

        $actionType = $newSuspensionState ? 'voter_suspended' : 'voter_reactivated';
        $actionDesc = $newSuspensionState
            ? "Suspended student voter access for {$user->name} ({$user->matric_no}). Existing votes preserved."
            : "Reactivated student voter access for {$user->name} ({$user->matric_no}).";

        AuditLog::record($actionType, $actionDesc);

        $statusMessage = $newSuspensionState
            ? "Student {$user->name} ({$user->matric_no}) has been suspended. Note: Any previously recorded votes are safely preserved in the ledger."
            : "Student {$user->name} ({$user->matric_no}) has been reactivated successfully.";

        return back()->with('status', $statusMessage);
    }
}
