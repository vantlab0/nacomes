<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice and code input interface.
     */
    public function notice(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('vote');
        }

        // If user already has an email and no active code, send a fresh one
        if ($user && $user->email && (! $user->verification_code || ! $user->verification_code_expires_at || $user->verification_code_expires_at->isPast())) {
            $user->sendEmailVerificationNotification();
        }

        return view('auth.verify-email', [
            'hasEmail' => ! empty($user?->email),
            'email' => $user?->email,
        ]);
    }

    /**
     * Store or update the student's email address and dispatch a verification code.
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('vote');
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'email.required' => 'Please provide your official student email address.',
            'email.unique' => 'This email address is already associated with another student account.',
        ]);

        $user->forceFill([
            'email' => strtolower(trim($validated['email'])),
        ])->save();

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-code-sent');
    }

    /**
     * Verify the student's email using the submitted 6-digit verification code.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('vote');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ], [
            'code.required' => 'Please enter the 6-digit verification code sent to your email.',
            'code.digits' => 'The verification code must be exactly 6 digits.',
        ]);

        if ($user->verification_code !== $validated['code'] || ! $user->verification_code_expires_at || $user->verification_code_expires_at->isPast()) {
            return back()->withErrors([
                'code' => 'The verification code entered is invalid or has expired. Please click "Resend Code" to obtain a new code.',
            ]);
        }

        $user->forceFill([
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('vote')->with('status', 'Your institutional student email has been verified successfully. You may now cast your ballot.');
    }

    /**
     * Fallback for signed URL verification.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('vote');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('vote')->with('status', 'Your institutional student email has been verified successfully. You may now cast your ballot.');
    }

    /**
     * Generate and dispatch a new 6-digit email verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('vote');
        }

        if (! $user->email) {
            return back()->withErrors(['email' => 'Please enter your student email first.']);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-code-sent');
    }
}
