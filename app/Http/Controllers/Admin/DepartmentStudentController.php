<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ImportDepartmentStudents;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DepartmentStudent;
use App\Support\MatricNumber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DepartmentStudentController extends Controller
{
    /**
     * Display the eligible matric register and upload form.
     */
    public function index(Request $request): View
    {
        $query = DepartmentStudent::query();
        $search = $request->input('q');
        $search = is_string($search) ? trim($search) : '';

        if ($search !== '') {
            $normalizedSearch = MatricNumber::normalize($search);

            $query->where(function ($query) use ($search, $normalizedSearch): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$normalizedSearch}%");
            });
        }

        $level = $request->input('level');
        $level = is_string($level) ? $level : 'all';

        if ($level !== 'all' && ctype_digit($level) && (int) $level >= 100 && (int) $level <= 500) {
            $query->where('level', (int) $level);
        } else {
            $level = 'all';
        }

        return view('admin.department-students.index', [
            'students' => $query->orderBy('level')->orderBy('name')->paginate(25)->withQueryString(),
            'search' => $search,
            'levelFilter' => $level,
            'levels' => DepartmentStudent::query()
                ->whereNotNull('level')
                ->distinct()
                ->orderBy('level')
                ->pluck('level'),
            'total' => DepartmentStudent::count(),
            'department' => DepartmentStudent::DEPARTMENT,
        ]);
    }

    /**
     * Import matric numbers from an uploaded CSV or text file.
     */
    public function import(Request $request, ImportDepartmentStudents $importer): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'extensions:csv,txt', 'max:2048'],
        ], [
            'file.required' => 'Choose a CSV or TXT file to upload.',
            'file.extensions' => 'The file must have a .csv or .txt extension.',
            'file.max' => 'The file may not be larger than 2 MB.',
        ]);

        $result = $importer->handle($validated['file']->getRealPath());
        $imported = $result['created'] + $result['updated'];

        if ($imported === 0) {
            return back()
                ->withErrors(['file' => 'No valid matric numbers were found in the uploaded file.'])
                ->with('import_errors', $result['errors']);
        }

        AuditLog::record(
            'import_department_students',
            "Imported department matric register: {$result['created']} added, {$result['updated']} updated, {$result['skipped']} skipped.",
            $request->user()?->id,
            $request->ip()
        );

        $status = "Import complete: {$result['created']} added and {$result['updated']} updated.";

        if ($result['skipped'] > 0) {
            $status .= " {$result['skipped']} row(s) skipped.";
        }

        if ($result['duplicates'] > 0) {
            $status .= " {$result['duplicates']} duplicate row(s) overwritten.";
        }

        return back()
            ->with('status', $status)
            ->with('import_errors', $result['errors']);
    }

    /**
     * Download a sample CSV with the accepted columns.
     */
    public function template(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['matric_no', 'name', 'level'], ',', '"', '');

            fclose($handle);
        }, 'department-students-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
