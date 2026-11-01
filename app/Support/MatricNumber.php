<?php

namespace App\Support;

class MatricNumber
{
    /**
     * Normalize a matriculation number for storage and comparison.
     *
     * Converts separators such as spaces, pipes, and hyphens to slashes so
     * numbers like "cet|cpe|0155|2025" match "CET/CPE/0155/2025".
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $normalized = strtoupper(trim($value));
        $normalized = (string) preg_replace('/[^A-Z0-9]+/', '/', $normalized);
        $normalized = (string) preg_replace('#/+#', '/', $normalized);

        return trim($normalized, '/');
    }
}
