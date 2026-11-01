<?php

namespace Database\Seeders;

use App\Models\DepartmentStudent;
use App\Support\MatricNumber;
use Illuminate\Database\Seeder;
use RuntimeException;

class DepartmentStudentSeeder extends Seeder
{
    /**
     * Import the department's eligible student register from CSV.
     */
    public function run(): void
    {
        $path = database_path('data/computer_engineering_students.csv');

        if (! is_file($path)) {
            throw new RuntimeException("Department student register not found at [{$path}].");
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Unable to open department student register at [{$path}].");
        }

        try {
            $headers = fgetcsv($handle, null, ',', '"', '');

            if ($headers === false) {
                throw new RuntimeException('Department student register is empty.');
            }

            $headers = array_map(
                fn (mixed $header): string => trim((string) $header),
                $headers
            );

            $records = [];
            $timestamp = now();

            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                if ($row === [null] || count($row) !== count($headers)) {
                    continue;
                }

                $data = array_combine($headers, array_map(
                    fn (mixed $value): string => trim((string) $value),
                    $row
                ));

                if ($data === false) {
                    continue;
                }

                $matricNo = MatricNumber::normalize($data['matric_no'] ?? null);

                if ($matricNo === '') {
                    continue;
                }

                $name = $data['name'] ?? '';
                $level = $data['level'] ?? '';

                $records[$matricNo] = [
                    'name' => $name !== '' ? $name : null,
                    'matric_no' => $matricNo,
                    'department' => ($data['department'] ?? '') !== ''
                        ? $data['department']
                        : DepartmentStudent::DEPARTMENT,
                    'level' => is_numeric($level) ? (int) $level : null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        } finally {
            fclose($handle);
        }

        foreach (array_chunk(array_values($records), 100) as $chunk) {
            DepartmentStudent::upsert(
                $chunk,
                ['matric_no'],
                ['name', 'department', 'level', 'updated_at']
            );
        }
    }
}
