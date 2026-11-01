@extends('layouts.app')

@section('title', 'Student Login — NACOMES Elections')
@section('description', 'Sign in to the NACOMES departmental election portal using your matriculation number or university email to access the voting booth.')

@section('content')
    <div class="min-h-[calc(100vh-14rem)] py-12 sm:py-16 bg-slate-50 flex flex-col justify-center sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            @php
                $siteLogo = \App\Models\Setting::get('site_logo');
            @endphp
            <!-- Brand Badge -->
            <div class="flex justify-center">
                @if ($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                    <a href="{{ route('home') }}" class="inline-block focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-400 rounded-lg p-1">
                        <img src="{{ asset('storage/' . $siteLogo) }}" alt="NACOMES Logo" class="h-16 w-auto max-w-[220px] object-contain drop-shadow-sm" />
                    </a>
                @else
                    <div class="w-13 h-13 rounded-xl bg-navy-950 border border-navy-800 flex items-center justify-center text-yellow-400 shadow-sm font-bold text-base">
                        NC
                    </div>
                @endif
            </div>

            <h1 class="mt-4 text-center text-2xl sm:text-3xl font-extrabold text-navy-950 tracking-tight">
                Student Voter Portal
            </h1>
            <p class="mt-2 text-center text-xs sm:text-sm text-charcoal-600 max-w-sm mx-auto">
                Sign in with your matriculation number or student email address.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-10 rounded-xl border border-slate-200 shadow-xs space-y-6">
                <!-- Status Alert (e.g. Password reset success, email verified, etc.) -->
                @if (session('status'))
                    <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-xs text-red-800 space-y-1" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                                    <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" />
                                </svg>
                                <span>{{ $error }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
                    @csrf

                    <!-- Login Identifier (Matriculation Number OR Email) -->
                    <div>
                        <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            Matric No or Student Email <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="login"
                                name="login"
                                type="text"
                                value="{{ old('login') }}"
                                placeholder="e.g. CPE/2021/1042 or student@university.edu.ng"
                                required
                                autofocus
                                autocomplete="username"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('login') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Supports both departmental matric number and student email.</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-royal-700 hover:text-royal-800 font-medium">
                                Forgot password?
                            </a>
                        </div>
                        <div class="mt-1.5">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('password') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-xs text-slate-600">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-royal-700 border-slate-300 focus:ring-royal-600" />
                            <span>Remember this device</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs"
                        >
                            Sign In to Voting Booth
                        </button>
                    </div>
                </form>

                <!-- Link to Registration -->
                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-charcoal-600">
                        Don't have a voting account yet?
                        <a href="{{ route('register') }}" class="font-semibold text-royal-700 hover:text-royal-800 ml-1">
                            Register as a Student &rarr;
                        </a>
                    </p>
                </div>
            </div>

            <!-- Return to Home -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-navy-900 transition">
                    &larr; Return to Homepage
                </a>
            </div>
        </div>
    </div>
@endsection
