@extends('layouts.app')

@section('title', 'Set New Password — NACOMES Elections')
@section('description', 'Choose a new password for your NACOMES student voting account.')

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
                Set New Password
            </h1>
            <p class="mt-2 text-center text-xs sm:text-sm text-charcoal-600 max-w-sm mx-auto">
                Enter your new secure password to restore access to your account.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-10 rounded-xl border border-slate-200 shadow-xs space-y-6">
                <!-- Errors Alert -->
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

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5" novalidate>
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            Student Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $email) }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('email') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('password') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Minimum 8 characters.</p>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="w-full px-3.5 py-2.5 rounded-md border border-slate-300 text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs"
                        >
                            Update Password &amp; Sign In
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-royal-700 hover:text-royal-800">
                        &larr; Return to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
