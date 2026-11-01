<?php

namespace App\Models;

use App\Support\MatricNumber;
use Database\Factories\DepartmentStudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'matric_no', 'department', 'level'])]
class DepartmentStudent extends Model
{
    /** @use HasFactory<DepartmentStudentFactory> */
    use HasFactory;

    /**
     * The department covered by this eligibility register.
     */
    public const DEPARTMENT = 'Computer Engineering';

    /**
     * Interact with the student's matriculation number.
     *
     * @return Attribute<string, string>
     */
    protected function matricNo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => (string) $value,
            set: fn (?string $value): string => MatricNumber::normalize($value),
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }
}
