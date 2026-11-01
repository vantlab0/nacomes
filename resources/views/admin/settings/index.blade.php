@extends('layouts.admin')

@section('title', 'System Settings — NACOMES Admin')
@section('page-title', 'System Settings')

@section('content')
<div class="space-y-8 max-w-5xl">
    <!-- Header Summary Card -->
    <div class="bg-navy-950 text-white rounded-xl p-6 border border-navy-800 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-yellow-400 font-mono">Platform Configuration</span>
                <h2 class="text-xl font-bold tracking-tight text-white mt-1">Branding &amp; SMTP Email Dispatch</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Upload official NACOMES departmental logos, configure browser favicon icons, and manage institutional SMTP mail delivery credentials for student accreditation codes.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- SECTION 0: Student Registration -->
        @php
            $registrationOpen = \App\Models\Setting::registrationIsOpen();
        @endphp
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-navy-950">Student Registration</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Control whether new students can create an account and register for departmental elections.</p>
                </div>
                <div>
                    @if ($registrationOpen)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            OPEN
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 font-mono">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            CLOSED
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 p-4 rounded-lg border border-slate-200 bg-slate-50/50">
                    <input type="hidden" name="registration_open" value="0">
                    <input
                        type="checkbox"
                        id="registration_open"
                        name="registration_open"
                        value="1"
                        {{ old('registration_open', $registrationOpen ? '1' : '0') === '1' ? 'checked' : '' }}
                        class="mt-0.5 rounded border-slate-300 text-navy-950 focus:ring-navy-900 cursor-pointer"
                    >
                    <div>
                        <label for="registration_open" class="text-xs font-bold text-navy-950 cursor-pointer">
                            Allow new student registrations
                        </label>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            When unchecked, the public Register page is disabled and blocked; existing students can still log in and vote.
                        </p>
                    </div>
                </div>
                @error('registration_open')
                    <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SECTION 1: Portal Branding (Logo & Favicon) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-bold text-navy-950">Website Branding &amp; Visual Identity</h3>
                <p class="text-xs text-slate-500 mt-0.5">Customize the official emblem and browser tab icon displayed across public and administrative interfaces.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Website Logo Upload -->
                    <div class="p-5 rounded-lg border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <label for="site_logo" class="block text-xs font-bold text-navy-950 uppercase tracking-wider">Website Logo</label>
                                <span class="text-[11px] text-slate-500">Formats: PNG, JPG, SVG, WebP (Max: 3MB)</span>
                            </div>
                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-slate-200 text-slate-700">Navbar Brand</span>
                        </div>

                        <!-- Current Logo Preview -->
                        <div class="p-4 rounded-lg bg-navy-950 border border-navy-800 flex items-center justify-center min-h-[90px]">
                            @if (!empty($settings['site_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['site_logo']))
                                <div class="text-center space-y-2">
                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Current Website Logo" class="max-h-14 max-w-[200px] object-contain mx-auto" />
                                    <span class="text-[10px] text-yellow-400 font-mono block">Current Active Logo</span>
                                </div>
                            @else
                                <div class="text-center text-slate-400 space-y-1">
                                    <div class="w-8 h-8 rounded bg-navy-900 border border-yellow-400/40 text-yellow-400 flex items-center justify-center font-bold text-xs mx-auto">
                                        NC
                                    </div>
                                    <span class="text-[11px] text-slate-400 block font-sans">Default Institutional Monogram Active</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <input 
                                type="file" 
                                id="site_logo" 
                                name="site_logo" 
                                accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-navy-950 file:text-yellow-400 hover:file:bg-navy-900 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg p-1 bg-white"
                            />
                            @error('site_logo')
                                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (!empty($settings['site_logo']))
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" id="remove_site_logo" name="remove_site_logo" value="1" class="rounded border-slate-300 text-navy-950 focus:ring-navy-900">
                                <label for="remove_site_logo" class="text-xs text-red-600 font-medium cursor-pointer">Remove custom logo (revert to default)</label>
                            </div>
                        @endif
                    </div>

                    <!-- Website Favicon / Icon Upload -->
                    <div class="p-5 rounded-lg border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <label for="site_icon" class="block text-xs font-bold text-navy-950 uppercase tracking-wider">Website Favicon / Icon</label>
                                <span class="text-[11px] text-slate-500">Formats: ICO, PNG, SVG (Max: 1MB)</span>
                            </div>
                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-slate-200 text-slate-700">Browser Tab</span>
                        </div>

                        <!-- Current Icon Preview -->
                        <div class="p-4 rounded-lg bg-navy-950 border border-navy-800 flex items-center justify-center min-h-[90px]">
                            @if (!empty($settings['site_icon']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['site_icon']))
                                <div class="text-center space-y-2">
                                    <img src="{{ asset('storage/' . $settings['site_icon']) }}" alt="Current Favicon" class="w-8 h-8 object-contain mx-auto" />
                                    <span class="text-[10px] text-yellow-400 font-mono block">Current Active Favicon</span>
                                </div>
                            @else
                                <div class="text-center text-slate-400 space-y-1">
                                    <div class="w-8 h-8 rounded bg-navy-900 border border-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs mx-auto">
                                        ICO
                                    </div>
                                    <span class="text-[11px] text-slate-400 block font-sans">Default NACOMES Tab Icon Active</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <input 
                                type="file" 
                                id="site_icon" 
                                name="site_icon" 
                                accept="image/*,.ico"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-navy-950 file:text-yellow-400 hover:file:bg-navy-900 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg p-1 bg-white"
                            />
                            @error('site_icon')
                                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (!empty($settings['site_icon']))
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" id="remove_site_icon" name="remove_site_icon" value="1" class="rounded border-slate-300 text-navy-950 focus:ring-navy-900">
                                <label for="remove_site_icon" class="text-xs text-red-600 font-medium cursor-pointer">Remove custom icon (revert to default)</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: SMTP Email Settings -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-navy-950">SMTP Email Server Configuration</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Used for dispatching 6-digit accreditation codes and institutional election announcements.</p>
                </div>
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">
                    SMTP Protocol
                </span>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <!-- SMTP Host -->
                    <div class="sm:col-span-2">
                        <label for="mail_host" class="block text-xs font-bold text-navy-900 mb-1">SMTP Host / Server *</label>
                        <input 
                            type="text" 
                            id="mail_host" 
                            name="mail_host" 
                            placeholder="e.g. smtp.gmail.com or mail.nacomes.edu.ng"
                            value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                        @error('mail_host')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SMTP Port -->
                    <div>
                        <label for="mail_port" class="block text-xs font-bold text-navy-900 mb-1">SMTP Port *</label>
                        <input 
                            type="number" 
                            id="mail_port" 
                            name="mail_port" 
                            placeholder="587"
                            value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                        @error('mail_port')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SMTP Username -->
                    <div>
                        <label for="mail_username" class="block text-xs font-bold text-navy-900 mb-1">SMTP Username</label>
                        <input 
                            type="text" 
                            id="mail_username" 
                            name="mail_username" 
                            placeholder="e.g. elections@nacomes.edu.ng"
                            value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                        @error('mail_username')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SMTP Password -->
                    <div>
                        <label for="mail_password" class="block text-xs font-bold text-navy-900 mb-1">
                            SMTP Password
                            @if (!empty($settings['mail_password']))
                                <span class="text-[10px] text-emerald-600 font-semibold font-sans">(Saved)</span>
                            @endif
                        </label>
                        <input 
                            type="password" 
                            id="mail_password" 
                            name="mail_password" 
                            placeholder="{{ !empty($settings['mail_password']) ? '••••••••••••' : 'Enter SMTP password' }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Leave empty to keep existing password</span>
                        @error('mail_password')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SMTP Encryption -->
                    <div>
                        <label for="mail_encryption" class="block text-xs font-bold text-navy-900 mb-1">Encryption Protocol</label>
                        <select 
                            id="mail_encryption" 
                            name="mail_encryption" 
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                            <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                            <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="none" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None (Unencrypted)</option>
                        </select>
                        @error('mail_encryption')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender Email Address -->
                    <div>
                        <label for="mail_from_address" class="block text-xs font-bold text-navy-900 mb-1">From Email Address *</label>
                        <input 
                            type="email" 
                            id="mail_from_address" 
                            name="mail_from_address" 
                            placeholder="elections@nacomes.edu.ng"
                            value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'elections@nacomes.edu.ng') }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                        >
                        @error('mail_from_address')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sender From Name -->
                    <div class="sm:col-span-2">
                        <label for="mail_from_name" class="block text-xs font-bold text-navy-900 mb-1">From Sender Name *</label>
                        <input 
                            type="text" 
                            id="mail_from_name" 
                            name="mail_from_name" 
                            placeholder="NACOMES Electoral Commission"
                            value="{{ old('mail_from_name', $settings['mail_from_name'] ?? 'NACOMES Electoral Commission') }}"
                            class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5"
                        >
                        @error('mail_from_name')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <button 
                type="submit" 
                class="px-6 py-3 rounded-lg bg-yellow-400 hover:bg-yellow-300 text-navy-950 font-bold text-xs border border-yellow-400 transition shadow-xs cursor-pointer"
            >
                Save Settings
            </button>
        </div>
    </form>

    <!-- SECTION 3: SMTP Test Dispatch Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-navy-950">Send Test Verification Code Email</h3>
                <p class="text-xs text-slate-500 mt-0.5">Test your SMTP server connectivity by sending a sample 6-digit verification code email.</p>
            </div>
            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                Diagnostic Tool
            </span>
        </div>

        <div class="p-6">
            @if ($errors->has('test_email'))
                <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-800 mb-4 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ $errors->first('test_email') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.test-email') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @csrf
                <div class="flex-1">
                    <label for="test_recipient" class="sr-only">Recipient Email</label>
                    <input 
                        type="email" 
                        id="test_recipient" 
                        name="test_recipient" 
                        required 
                        placeholder="Enter recipient email (e.g. admin@school.edu.ng)"
                        value="{{ old('test_recipient', auth()->user()->email) }}"
                        class="w-full text-xs rounded-lg border-slate-300 focus:border-navy-950 focus:ring-navy-950 shadow-2xs px-3.5 py-2.5 font-mono"
                    >
                </div>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-lg bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs border border-slate-700 transition shadow-2xs shrink-0 cursor-pointer"
                >
                    Send Test Email &rarr;
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
