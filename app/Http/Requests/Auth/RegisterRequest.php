<?php

namespace App\Http\Requests\Auth;

use App\Models\DepartmentStudent;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'matric_no' => User::normalizeMatricNo($this->input('matric_no')),
            'email' => is_string($this->input('email')) ? strtolower(trim($this->input('email'))) : $this->input('email'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'matric_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'matric_no'),
                Rule::exists('department_students', 'matric_no')
                    ->where('department', DepartmentStudent::DEPARTMENT),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'matric_no' => 'matriculation number',
            'terms' => 'election and privacy terms',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'matric_no.unique' => 'A student with this matriculation number is already registered.',
            'matric_no.exists' => 'You are not in this department. Your matriculation number was not found in the Department of Computer Engineering student records.',
            'email.unique' => 'This email address is already associated with a student account.',
            'terms.accepted' => 'You must accept the NACOMES election and privacy guidelines to register.',
        ];
    }
}
