<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function create(): View|RedirectResponse
    {
        if (! Setting::registrationIsOpen()) {
            return redirect()->route('home')->with('error', 'Student registration is currently closed. Please check back later.');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        if (! Setting::registrationIsOpen()) {
            return redirect()->route('home')->with('error', 'Student registration is currently closed.');
        }

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'matric_no' => $validated['matric_no'],
            'password' => $validated['password'],
            'terms_accepted_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
