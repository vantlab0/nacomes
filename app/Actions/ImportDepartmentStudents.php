<?php

namespace App\Actions;

use App\Models\DepartmentStudent;
use App\Support\MatricNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportDepartmentStudents
{
    /**
     * The maximum number of rows accepted in a single upload.
     */
    private const MAX_ROWS = 5000;

    /**
     * Import matric numbers from a CSV or plain-text file.
     *
     * @return array{created: int, updated: int, skipped: int, duplicates: int, errors: array<int, string>}
     */
    public function handle(string $path): array
    {
        $rows = $this->readRows($path);
        [$header, $rows] = $this->extractHeader($rows);

        if ($header !== null && $header['matric_no'] === null) {
            throw ValidationException::withMessages([
                'file' => 'The CSV must contain a column for the matric number, such as "matric_no".',
            ]);
        }

        $records = [];
        $errors = [];
        $skipped = 0;
        $duplicates = 0;
        $line = $header !== null ? 2 : 1;

        foreach ($rows as $row) {
            $matricNo = MatricNumber::normalize(
                $this->cell($row, $header['matric_no'] ?? 0)
            );

            if ($matricNo === '') {
                $errors[] = "Row {$line}: missing matric number.";
                $skipped++;
                $line++;

                continue;
            }

            if (mb_strlen($matricNo) > 50) {
                $errors[] = "Row {$line}: matric number exceeds 50 characters.";
                $skipped++;
                $line++;

                continue;
            }

            $name = $this->cell($row, $header['name'] ?? null);
            $level = $this->parseLevel($this->cell($row, $header['level'] ?? null));

            if (mb_strlen($name) > 255) {
                $errors[] = "Row {$line}: name exceeds 255 characters.";
                $skipped++;
                $line++;

                continue;
            }

            if (isset($records[$matricNo])) {
                $duplicates++;
            }

            $records[$matricNo] = [
                'name' => $name !== '' ? $name : null,
                'matric_no' => $matricNo,
                'department' => DepartmentStudent::DEPARTMENT,
                'level' => $level,
            ];

            $line++;
        }

        if ($records === []) {
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => $skipped,
                'duplicates' => $duplicates,
                'errors' => array_slice($errors, 0, 20),
            ];
        }

        $timestamp = now();
        $payload = array_map(
            fn (array $record): array => $record + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            array_values($records)
        );
        $existingCount = 0;

        DB::transaction(function () use ($records, $payload, &$existingCount): void {
            $existing = [];

            foreach (array_chunk(array_keys($records), 500) as $chunk) {
                foreach (DepartmentStudent::query()
                    ->whereIn('matric_no', $chunk)
                    ->get(['matric_no', 'name', 'level']) as $student) {
                    $existing[$student->matric_no] = $student;
                }
            }

            $existingCount = count($existing);

            $payload = array_map(function (array $record) use ($existing): array {
                $current = $existing[$record['matric_no']] ?? null;

                if ($record['name'] === null && $current?->name !== null) {
                    $record['name'] = $current->name;
                }

                if ($record['level'] === null && $current?->level !== null) {
                    $record['level'] = $current->level;
                }

                return $record;
            }, $payload);

            foreach (array_chunk($payload, 100) as $chunk) {
                DepartmentStudent::upsert(
                    $chunk,
                    ['matric_no'],
                    ['name', 'department', 'level', 'updated_at']
                );
            }
        });

        return [
            'created' => count($records) - $existingCount,
            'updated' => $existingCount,
            'skipped' => $skipped,
            'duplicates' => $duplicates,
            'errors' => array_slice($errors, 0, 20),
        ];
    }

    /**
     * Read non-empty rows from the uploaded file.
     *
     * @return array<int, array<int, string>>
     */
    private function readRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'Unable to read the uploaded file.',
            ]);
        }

        $rows = [];
        $delimiter = $this->detectDelimiter($handle);

        try {
            while (($row = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
                if ($row === [null]) {
                    continue;
                }

                $row = array_map(
                    fn (mixed $value): string => trim((string) $value),
                    $row
                );

                if (implode('', $row) === '') {
                    continue;
                }

                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]) ?? $row[0];
                $rows[] = $row;

                if (count($rows) > self::MAX_ROWS + 1) {
                    throw ValidationException::withMessages([
                        'file' => 'The file contains too many rows. The maximum is 5,000.',
                    ]);
                }
            }
        } finally {
            fclose($handle);
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file is empty.',
            ]);
        }

        return $rows;
    }

    /**
     * Detect the delimiter used by the uploaded file.
     *
     * Supports comma-separated CSV, semicolon-separated CSV, and tab-separated text.
     *
     * @param  resource  $handle
     */
    private function detectDelimiter($handle): string
    {
        rewind($handle);
        $firstLine = fgets($handle);
        rewind($handle);

        if ($firstLine === false) {
            return ',';
        }

        $delimiter = ',';
        $highestCount = substr_count($firstLine, ',');

        foreach ([';', "\t"] as $candidate) {
            $count = substr_count($firstLine, $candidate);

            if ($count > $highestCount) {
                $delimiter = $candidate;
                $highestCount = $count;
            }
        }

        return $highestCount > 0 ? $delimiter : ',';
    }

    /**
     * Detect and map a header row when one is present.
     *
     * @param  array<int, array<int, string>>  $rows
     * @return array{0: array{matric_no: int|null, name: int|null, level: int|null}|null, 1: array<int, array<int, string>>}
     */
    private function extractHeader(array $rows): array
    {
        $firstRow = $rows[0] ?? [];
        $mapped = [
            'matric_no' => null,
            'name' => null,
            'level' => null,
        ];
        $isHeader = false;

        foreach ($firstRow as $index => $cell) {
            $normalized = strtolower($cell);
            $normalized = (string) preg_replace('/[^a-z0-9]+/', ' ', $normalized);

            if (str_contains($normalized, 'matric')) {
                $mapped['matric_no'] = $index;
                $isHeader = true;
            } elseif (str_contains($normalized, 'name')) {
                $mapped['name'] = $index;
                $isHeader = true;
            } elseif (str_contains($normalized, 'department')) {
                $isHeader = true;
            } elseif (str_contains($normalized, 'level')) {
                $mapped['level'] = $index;
                $isHeader = true;
            }
        }

        if (! $isHeader) {
            return [null, $rows];
        }

        array_shift($rows);

        return [$mapped, $rows];
    }

    /**
     * Read a cell from a row by its column index.
     *
     * @param  array<int, string>  $row
     */
    private function cell(array $row, ?int $index): string
    {
        if ($index === null) {
            return '';
        }

        return trim((string) ($row[$index] ?? ''));
    }

    /**
     * Convert labels such as "100 Level" into a numeric level.
     */
    private function parseLevel(string $value): ?int
    {
        if ($value === '' || ! preg_match('/\d+/', $value, $matches)) {
            return null;
        }

        $level = (int) $matches[0];

        return $level >= 100 && $level <= 500 ? $level : null;
    }
}
