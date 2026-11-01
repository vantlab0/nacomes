@extends('layouts.app')

@section('title', 'Student Registration — NACOMES Elections')
@section('description', 'Register for the NACOMES departmental election portal to verify your student credentials and prepare for voting.')

@section('content')
    <div class="min-h-[calc(100vh-14rem)] py-12 sm:py-16 bg-slate-50 flex flex-col justify-center sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-xl">
            @php
                $siteLogo = \App\Models\Setting::get('site_logo');
            @endphp
            <!-- Brand Mark -->
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
                Student Voter Registration
            </h1>
            <p class="mt-2 text-center text-xs sm:text-sm text-charcoal-600 max-w-md mx-auto">
                Accredited student portal registration for the Department of Computer Engineering.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4">
            <div class="bg-white py-8 px-6 sm:px-10 rounded-xl border border-slate-200 shadow-xs space-y-6">
                <!-- Accreditation Notice -->
                <div class="p-3.5 rounded-lg bg-blue-50 border border-blue-200 text-xs text-royal-950 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-royal-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Registration is strictly for undergraduate students of Computer Engineering. Please supply your exact departmental matriculation number and official institutional email address.
                    </span>
                </div>

                <!-- Global Validation Summary -->
                @if ($errors->any())
                    <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-800 space-y-1.5" role="alert">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2" />
                                <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" />
                                <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" />
                            </svg>
                            <span>Please correct the errors below to continue:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-red-700 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            Full Name (Surname First) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="e.g. Adebayo Chukwuma Ibrahim"
                                required
                                autofocus
                                autocomplete="name"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('name') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 transition"
                            />
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Matriculation Number -->
                    <div>
                        <label for="matric_no" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                            Matric Number <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="matric_no"
                                name="matric_no"
                                type="text"
                                value="{{ old('matric_no') }}"
                                placeholder="e.g. COET/10970/2023"
                                required
                                autocomplete="off"
                                class="w-full px-3.5 py-2.5 rounded-md border @error('matric_no') border-red-500 bg-red-50/20 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-royal-600 focus:border-royal-600 uppercase transition"
                            />
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Enter the exact matriculation number on your department record (e.g. COET/10970/2023 or CET/CPE/0155/2025). Students not in the Computer Engineering register cannot register.</p>
                        @error('matric_no')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password & Confirmation (2 columns on tablet+) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                                Password <span class="text-red-500">*</span>
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
                                Confirm Password <span class="text-red-500">*</span>
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
                    </div>

                    <!-- Acceptance of Election and Privacy Terms -->
                    <div class="pt-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                id="terms"
                                name="terms"
                                type="checkbox"
                                value="1"
                                {{ old('terms') ? 'checked' : '' }}
                                required
                                class="w-4 h-4 mt-0.5 rounded text-royal-700 border-slate-300 focus:ring-royal-600"
                            />
                            <span class="text-xs text-charcoal-700 leading-relaxed">
                                I confirm that I am a matriculated undergraduate student of the Department of Computer Engineering. I agree to the <a href="{{ route('home') }}#guidelines" target="_blank" class="text-royal-700 hover:text-royal-800 font-semibold underline">NACOMES Electoral Code of Conduct</a> and accept single-ballot verification terms. <span class="text-red-500">*</span>
                            </span>
                        </label>
                        @error('terms')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            class="w-full py-3.5 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs"
                        >
                            Complete Registration &amp; Verify Email
                        </button>
                    </div>
                </form>

                <!-- Link to Login -->
                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-charcoal-600">
                        Already have an accredited student account?
                        <a href="{{ route('login') }}" class="font-semibold text-royal-700 hover:text-royal-800 ml-1">
                            Sign In to Portal &rarr;
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
