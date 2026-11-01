@extends('layouts.app')

@section('title', 'Forgot Password — NACOMES Elections')
@section('description', 'Request a password reset link for your NACOMES student voting account.')

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
                Reset Account Password
            </h1>
            <p class="mt-2 text-center text-xs sm:text-sm text-charcoal-600 max-w-sm mx-auto">
                Enter your registered student email address to receive a secure password reset link.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-10 rounded-xl border border-slate-200 shadow-xs space-y-6">
                <!-- Status Alert -->
                @if (session('status'))
                    <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
                    @csrf

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
                                value="{{ old('email') }}"
                                placeholder="student@university.edu.ng"
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

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs"
                        >
                            Email Password Reset Link
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-charcoal-600">
                        Remembered your password?
                        <a href="{{ route('login') }}" class="font-semibold text-royal-700 hover:text-royal-800 ml-1">
                            Return to Sign In &rarr;
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
