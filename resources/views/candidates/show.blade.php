@extends('layouts.app')

@section('title', "{$candidate->name} — Candidate Profile | NACOMES Elections")
@section('description', "Review the official candidacy profile of {$candidate->name} for {$position->title} in the NACOMES departmental election.")

@section('content')
    <!-- Page Header -->
    <section class="bg-navy-950 text-white py-12 sm:py-16 border-b border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-yellow-400 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('candidates') }}" class="hover:text-yellow-400 transition-colors">Candidates</a>
                <span>/</span>
                <span class="text-slate-200">{{ $candidate->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded bg-yellow-400 text-navy-950 mb-2">
                        Aspirant for {{ $position->title }}
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                        {{ $candidate->name }}
                    </h1>
                    <p class="text-sm sm:text-base text-slate-300 mt-2">
                        Department of Computer Engineering • {{ $candidate->academic_level }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('candidates') }}" class="px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-navy-900 hover:bg-navy-850 border border-slate-600 transition shadow-xs">
                        &larr; Back to Candidates
                    </a>
                    <a href="{{ route('vote') }}" class="px-5 py-2.5 rounded-lg text-xs font-bold text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 transition shadow-xs">
                        Proceed to Vote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-14 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Left Column: Portrait & Key Credentials Card -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-navy-950 rounded-xl border border-yellow-500/30 overflow-hidden shadow-sm">
                        <div class="h-80 w-full overflow-hidden bg-navy-900">
                            <img
                                src="{{ $candidate->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80' }}"
                                alt="Portrait of {{ $candidate->name }}"
                                class="w-full h-full object-cover object-top"
                            />
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <h2 class="text-lg font-bold text-white">{{ $candidate->name }}</h2>
                                <p class="text-xs text-yellow-400 font-medium">{{ $position->title }}</p>
                            </div>

                            <div class="space-y-3 pt-3 border-t border-navy-800 text-xs">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="text-slate-400">Academic Level</span>
                                    <span class="font-semibold text-white">{{ $candidate->academic_level }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="text-slate-400">Matriculation</span>
                                    <span class="font-mono text-slate-200">{{ $candidate->matric_no ?: 'CPE Verified' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="text-slate-400">Accreditation</span>
                                    <span class="text-emerald-400 font-semibold">Certified Eligible</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="text-slate-400">Election</span>
                                    <span class="text-slate-200">{{ $election ? $election->title : 'NACOMES Elections' }}</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-navy-800">
                                <a href="{{ route('vote') }}" class="w-full block text-center py-3 px-4 rounded-lg font-bold text-xs text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 transition shadow-xs">
                                    Vote for {{ $candidate->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Candidacy Information & Guidelines -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <span class="text-xs font-bold tracking-widest text-royal-700 uppercase">Electoral Certification</span>
                            <h2 class="text-2xl font-bold text-navy-950 mt-1">Candidate Profile &amp; Office Details</h2>
                            <p class="text-xs text-charcoal-600 mt-1">Official candidate credentials verified by the NACOMES Electoral Commission.</p>
                        </div>

                        <div class="space-y-4 text-xs sm:text-sm text-charcoal-700 leading-relaxed">
                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                <h3 class="font-bold text-navy-950 text-sm">Contested Office: {{ $position->title }}</h3>
                                <p class="text-xs text-slate-600">
                                    {{ $position->description ?: 'This executive office serves the student body of the Department of Computer Engineering for the upcoming academic session.' }}
                                </p>
                            </div>

                            <div class="space-y-3">
                                <h4 class="font-bold text-navy-950">Accredited Eligibility Requirements Met:</h4>
                                <ul class="space-y-2 list-disc list-inside text-xs text-charcoal-700">
                                    <li>Enrolled student in good academic standing within the Department of Computer Engineering.</li>
                                    <li>Official candidacy registration certified by the NACOMES Electoral Commission.</li>
                                    <li>Cleared for participation on the digital ballot sheet.</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-lg bg-navy-50 border border-navy-100 space-y-1.5">
                                <span class="font-bold text-navy-950 text-xs block">Voting Information:</span>
                                <p class="text-xs text-slate-600">
                                    To vote for this aspirant, log in with your verified student account and navigate to the voting desk. Each student voter may cast one ballot per executive office.
                                </p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <a href="{{ route('candidates') }}" class="text-xs font-semibold text-royal-700 hover:text-royal-800 inline-flex items-center gap-1.5">
                                &larr; Browse All Contested Positions
                            </a>
                            <a href="{{ route('vote') }}" class="px-6 py-2.5 rounded-lg font-bold text-xs text-navy-950 bg-yellow-400 hover:bg-yellow-300 border border-yellow-400 transition shadow-xs text-center">
                                Proceed to Digital Ballot
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
