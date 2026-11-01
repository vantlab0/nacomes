<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Portal Login — NACOMES Elections</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-charcoal-900 bg-navy-950">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        @php
            $siteLogo = \App\Models\Setting::get('site_logo');
        @endphp
        <div class="flex justify-center">
            @if ($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                <a href="{{ route('home') }}" class="inline-block focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-400 rounded-lg p-1">
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="NACOMES Logo" class="h-16 w-auto max-w-[220px] object-contain drop-shadow-sm" />
                </a>
            @else
                <div class="w-14 h-14 rounded-2xl bg-navy-900 border border-yellow-400/40 text-yellow-400 flex items-center justify-center font-bold text-xl mx-auto shadow-inner">
                    NA
                </div>
            @endif
        </div>
        <h1 class="mt-4 text-2xl font-extrabold text-white tracking-tight">
            Electoral Administration Portal
        </h1>
        <p class="mt-1 text-xs text-slate-400">
            Authorized electoral officers and departmental administrators only.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 rounded-2xl border border-slate-200 shadow-xl space-y-6">
            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Errors Alert -->
            @if ($errors->any())
                <div class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-xs text-red-900 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5">
                            <span class="text-red-600 font-bold">•</span>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-charcoal-700">
                        Admin Email or Staff ID <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="login"
                            name="login"
                            type="text"
                            value="{{ old('login') }}"
                            placeholder="admin@department.edu.ng"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-3.5 py-2.5 rounded-md border @error('login') border-red-500 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-900 focus:border-navy-900 transition"
                        />
                    </div>
                </div>

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
                            autocomplete="current-password"
                            class="w-full px-3.5 py-2.5 rounded-md border @error('password') border-red-500 @else border-slate-300 @enderror text-sm text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-900 focus:border-navy-900 transition"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-navy-900 border-slate-300 focus:ring-navy-900" />
                        <span>Remember session</span>
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full py-3 px-4 rounded-md font-semibold text-sm text-navy-950 bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors shadow-2xs"
                    >
                        Authenticate Administrator
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('home') }}" class="text-xs font-medium text-slate-500 hover:text-navy-950 transition">
                    &larr; Return to Student Public Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
