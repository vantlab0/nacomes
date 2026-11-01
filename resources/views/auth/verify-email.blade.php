@extends('layouts.app')

@section('title', 'Verify Student Email — NACOMES Elections')
@section('description', 'Provide your student email and enter your 6-digit verification code to complete voter accreditation for NACOMES departmental elections.')

@section('content')
    <div class="min-h-[calc(100vh-14rem)] py-12 sm:py-16 bg-slate-50 flex flex-col justify-center sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            @php
                $siteLogo = \App\Models\Setting::get('site_logo');
                $isChanging = request()->has('change') || empty($email);
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
                {{ $isChanging ? 'Provide Student Email' : 'Enter Verification Code' }}
            </h1>
            <p class="mt-2 text-center text-xs sm:text-sm text-charcoal-600 max-w-sm mx-auto">
                {{ $isChanging ? 'Enter your official student email to receive your 6-digit accreditation code.' : 'Enter the 6-digit accreditation code sent to your student email.' }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white py-8 px-6 sm:px-10 rounded-xl border border-slate-200 shadow-xs space-y-6">
                <!-- Status Notice if Resent -->
                @if (session('status') === 'verification-code-sent' || session('status') === 'verification-link-sent')
                    <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2" role="status">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>A fresh 6-digit verification code has been dispatched to your email address.</span>
                    </div>
                @endif

                @if ($isChanging)
                    <!-- Form 1: Provide or Update Email -->
                    <form method="POST" action="{{ route('verification.update_email') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700 mb-1.5">
                                Official Student Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $email) }}"
                                placeholder="e.g. student@university.edu.ng"
                                required
                                autofocus
                                class="w-full px-3.5 py-2.5 rounded-md border @error('email') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                            <p class="mt-1 text-[11px] text-slate-500">Your 6-digit one-time voting accreditation code will be delivered here.</p>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs cursor-pointer"
                        >
                            Send Verification Code &rarr;
                        </button>

                        @if (!empty($email))
                            <div class="text-center pt-2">
                                <a href="{{ route('verification.notice') }}" class="text-xs text-royal-700 hover:underline">
                                    Cancel and return to code input
                                </a>
                            </div>
                        @endif
                    </form>
                @else
                    <!-- Form 2: Enter 6-digit code -->
                    <div class="space-y-2 text-xs sm:text-sm text-charcoal-700 leading-relaxed text-center">
                        <p class="text-xs text-slate-500">Accreditation code sent to:</p>
                        <p class="font-bold text-navy-950 bg-slate-100 py-2 px-3 rounded-lg border border-slate-200 break-all text-xs font-mono inline-block">
                            {{ $email }}
                        </p>
                        <div>
                            <a href="{{ route('verification.notice', ['change' => 1]) }}" class="text-[11px] text-royal-700 hover:underline">
                                (Change email address)
                            </a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('verification.code') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="code" class="block text-center text-xs font-bold uppercase tracking-wider text-navy-900 mb-2">
                                6-Digit Accreditation Code
                            </label>
                            <input
                                type="text"
                                name="code"
                                id="code"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required
                                autofocus
                                placeholder="000000"
                                value="{{ old('code') }}"
                                class="w-full text-center text-2xl font-mono font-bold tracking-[0.5em] py-3.5 px-4 rounded-xl border-2 {{ $errors->has('code') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-navy-950 focus:ring-navy-900/10' }} shadow-2xs transition"
                            />
                            @error('code')
                                <p class="mt-2 text-center text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3.5 px-4 rounded-lg font-bold text-xs sm:text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs cursor-pointer"
                        >
                            Verify Code &amp; Proceed to Ballot
                        </button>
                    </form>

                    <!-- Resend Verification Code Form -->
                    <div class="pt-2 text-center">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs font-semibold text-royal hover:text-navy-950 hover:underline transition cursor-pointer"
                            >
                                Didn't receive the code? Resend Code
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Logout Form -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Accredited as: <strong class="text-navy-900">{{ auth()->user()->matric_no }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700 transition cursor-pointer">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
