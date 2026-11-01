<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Display the dedicated administrative login interface.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin() && ! Auth::user()->isSuspended()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Authenticate an administrative session.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower((string) $request->input('login')).'|'.$request->ip().'|admin');

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $loginInput = (string) $request->input('login');
        $password = (string) $request->input('password');
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL) !== false;

        $credentials = ['password' => $password];
        if ($isEmail) {
            $credentials['email'] = strtolower(trim($loginInput));
        } else {
            $credentials['matric_no'] = User::normalizeMatricNo($loginInput);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'login' => 'These credentials do not match our administrative records.',
            ]);
        }

        $user = Auth::user();

        // Strict role verification: Reject students attempting to log in via admin portal
        if (! $user || ! $user->isAdmin()) {
            Auth::logout();
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'login' => 'Access denied. You do not possess authorized administrative privileges.',
            ]);
        }

        if ($user->isSuspended()) {
            Auth::logout();
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'login' => 'This administrative account has been suspended.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        AuditLog::record('admin_login', "Administrator ({$user->email}) logged into the administration console.", $user->id, $request->ip());

        return redirect()->route('admin.dashboard');
    }

    /**
     * Terminate an administrative session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::record('admin_logout', "Administrator ({$user->email}) signed out.", $user->id, $request->ip());
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Administrative session terminated successfully.');
    }
}
