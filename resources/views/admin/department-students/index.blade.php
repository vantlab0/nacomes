@extends('layouts.admin')

@section('title', 'Eligible Matrics — NACOMES Elections')
@section('page-title', 'Department Matric Eligibility Register')

@section('content')
    @php($importErrors = session('import_errors', []))

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-2xs space-y-5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-royal-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-navy-950">Upload Matric Numbers</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Add or update the students who are eligible to register for {{ $department }}.
                    </p>
                </div>
            </div>

            @if (! empty($importErrors))
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1.5" role="alert">
                    <strong class="font-bold block">Some rows were not imported:</strong>
                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        @foreach ($importErrors as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.department-students.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="file" class="block text-xs font-semibold text-slate-700 mb-1.5">CSV or TXT file</label>
                    <input
                        id="file"
                        name="file"
                        type="file"
                        accept=".csv,.txt,text/csv,text/plain"
                        required
                        class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-navy-950 file:text-white hover:file:bg-navy-900 file:cursor-pointer border border-slate-300 rounded-md bg-slate-50"
                    />
                    @error('file')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-[11px] text-slate-600 space-y-2">
                    <p class="font-bold text-slate-800">Accepted formats</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>CSV:</strong> include a <code class="font-mono text-royal-700">matric_no</code> column. Optional columns are <code class="font-mono text-royal-700">name</code> and <code class="font-mono text-royal-700">level</code>.</li>
                        <li><strong>TXT:</strong> one matriculation number per line.</li>
                        <li>Existing matric numbers are updated; new ones are added.</li>
                        <li>All imported students are assigned to {{ $department }}.</li>
                        <li>Blank name or level values will not erase existing details.</li>
                        <li>Maximum 2 MB and 5,000 rows per upload.</li>
                    </ul>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <a
                        href="{{ route('admin.department-students.template') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-royal-700 hover:text-royal-900 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download CSV template
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-md text-xs font-semibold text-white bg-navy-950 hover:bg-navy-900 transition shadow-xs"
                    >
                        Import Matric Numbers
                    </button>
                </div>
            </form>
        </div>

        <!-- Register Summary -->
        <div class="bg-navy-950 p-6 rounded-xl border border-navy-800 shadow-2xs text-white flex flex-col justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Eligibility Register</p>
                <p class="mt-3 text-4xl font-bold text-yellow-400">{{ number_format($total) }}</p>
                <p class="text-xs text-slate-300 mt-1">eligible matric numbers on record</p>
            </div>

            <div class="mt-6 pt-4 border-t border-navy-800 space-y-2.5 text-xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-slate-400">Department</span>
                    <span class="font-semibold text-right">{{ $department }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-slate-400">Levels represented</span>
                    <span class="font-semibold">{{ $levels->count() }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-slate-400">Current results</span>
                    <span class="font-semibold">{{ $students->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs space-y-4">
        <form method="GET" action="{{ route('admin.department-students.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search by student name or matriculation number..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-md border border-slate-300 text-xs text-charcoal-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-900 transition"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2" />
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <select
                    name="level"
                    class="px-3 py-2.5 rounded-md border border-slate-300 text-xs text-charcoal-900 focus:outline-none focus:ring-2 focus:ring-navy-900 transition bg-white"
                >
                    <option value="all" {{ $levelFilter === 'all' ? 'selected' : '' }}>All Levels</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level }}" {{ (string) $levelFilter === (string) $level ? 'selected' : '' }}>
                            {{ $level }} Level
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-md text-xs font-semibold text-white bg-navy-950 hover:bg-navy-900 transition shrink-0"
                >
                    Apply Filter
                </button>

                @if ($search || $levelFilter !== 'all')
                    <a
                        href="{{ route('admin.department-students.index') }}"
                        class="px-3 py-2.5 rounded-md text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition shrink-0"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Eligible Students Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
            <h3 class="text-sm font-bold text-navy-950">
                Registered Matric Numbers ({{ $students->total() }})
            </h3>
            <span class="text-xs text-slate-400 whitespace-nowrap">
                Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
            </span>
        </div>

        @if ($students->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400 space-y-1">
                <p class="font-medium text-slate-600 text-sm">No eligible matric numbers found.</p>
                <p>Upload a CSV or TXT file above, or refine your search and filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5">Matriculation No</th>
                            <th class="px-5 py-3.5">Student Name</th>
                            <th class="px-5 py-3.5">Level</th>
                            <th class="px-5 py-3.5">Department</th>
                            <th class="px-5 py-3.5">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-charcoal-700">
                        @foreach ($students as $student)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-royal-700 font-medium">
                                    {{ $student->matric_no }}
                                </td>
                                <td class="px-5 py-3.5 font-bold text-navy-950">
                                    {{ $student->name ?: '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($student->level)
                                        <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $student->level }} Level
                                        </span>
                                    @else
                                        <span class="text-slate-400">Not set</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{ $student->department }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $student->updated_at?->format('M d, Y') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
@endsection
