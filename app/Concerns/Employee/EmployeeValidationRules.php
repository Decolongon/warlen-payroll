<?php

namespace App\Concerns\Employee;

use App\Models\Employee;
use App\Rules\UniqueEncrypted;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait EmployeeValidationRules
{
    protected function empRules(): array
    {
        return [
            // ── Contact ──────────────────────────────────────────────────────
            'emergency_contact_number' => [
                'required',
                'string',
                'min:11',
                'regex:/^(\+63|0)?9\d{9}$/',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],

            'contact_person_number' => [
                'nullable',
                'string',
                'min:11',
                'regex:/^(\+63|0)?9\d{9}$/',
            ],

            // ── User account ─────────────────────────────────────────────────
            'name' => [
                'required',
                'string',
                'max:80',
                'min:3',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],

            'email' => [
                'required',
                'email',
                'max:80',
                Rule::unique('users', 'email')
                    ->ignore($this->route('employee')?->user_id),
            ],

            // password rule is overridden in each Request class
            'password' => [
                'nullable',
                'string',
                Password::default(),
            ],

            'employee_number' => [
                'required',
                'string',
                'min:11',
                'regex:/^(\+63|0)?9\d{9}$/',
                Rule::unique('employees', 'employee_number')
                    ->ignore($this->route('employee')?->id),
            ],

            'emp_code' => [
                'required',
                'integer',
                Rule::unique('employees', 'emp_code')
                    ->ignore($this->route('employee')?->id),
            ],

            // ── Personal ─────────────────────────────────────────────────────
            'age' => [
                'nullable',
                'integer',
                'min:18',
                'max:100',
            ],

            'gender' => [
                'nullable',
                'string',
                Rule::in(['male', 'female']),
            ],

            'dob' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],

            'father_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\'\-]+$/',
            ],

            'educ_attainment' => [
                'nullable',
                'string',
                'max:100',
                Rule::in([
                    'Elementary Graduate',
                    'High School Graduate',
                    'Senior High School Graduate',
                    'Vocational',
                    'Associate Degree',
                    "Bachelor's Degree",
                    "Master's Degree",
                    'Doctorate',
                    'No Formal Education',
                ]),
            ],

            'certificate' => [
                'nullable',
                'string',
                'max:255',
            ],

            'permanent_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'present_address' => [
                'nullable',
                'string',
                'max:500',
            ],

            // ── Skills ───────────────────────────────────────────────────────
            // The form sends skills as a JSON string; prepareForValidation()
            // in each Request class decodes it to an array before these rules run.
            'skills' => [
                'nullable',
                'array',
                'max:20',   // max 20 skill entries
            ],

            'skills.*' => [
                'string',
                'max:50',
                'distinct',
            ],

            // ── Contract ─────────────────────────────────────────────────────
            'contract_start_date' => [
                'required',
                'date',
            ],

            'contract_end_date' => [
                'required',
                'date',
                'after_or_equal:contract_start_date',
            ],

            'duration' => [
                'nullable',
                'integer',
                'min:1',
            ],

            // ── Government numbers ───────────────────────────────────────────
            'sss_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[\d\-]+$/',
                new UniqueEncrypted(Employee::class, 'sss_number', $this->route('employee')?->id),
            ],

            'pagibig_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[\d\-]+$/',
                new UniqueEncrypted(Employee::class, 'pagibig_number', $this->route('employee')?->id),
            ],

            'philhealth_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[\d\-]+$/',
                new UniqueEncrypted(Employee::class, 'philhealth_number', $this->route('employee')?->id),
            ],

            // ── Employment ───────────────────────────────────────────────────
            'employee_status' => [
                'required',
                Rule::in(['active', 'end_of_contract', 'awol', 'terminated', 'resigned', 'newly_hired']),
            ],

            'position_id' => [
                'nullable',
                'sometimes',
                'exists:positions,id',
            ],

            'branch_id' => [
                'required',
                'exists:branches,id',
            ],

            'pay_frequency' => [
                'required',
                Rule::in(['weekender', 'monthly', 'semi_monthly']),
            ],

            'site_id' => [
                'nullable',
                'exists:sites,id',
            ],

            // ── Avatar ───────────────────────────────────────────────────────
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'string', Rule::in(['true', 'false'])],
        ];
    }

    protected function empMessages(): array
    {
        return [
            // Avatar
            'avatar.image' => 'The avatar must be an image.',
            'avatar.mimes' => 'The avatar must be a valid image file (jpeg, png, jpg, gif, webp).',
            'avatar.max'   => 'The avatar must not exceed 2MB in size.',

            // Emergency contact
            'emergency_contact_number.required' => 'The emergency contact number is required.',
            'emergency_contact_number.min'      => 'The emergency contact number must be at least 11 digits.',
            'emergency_contact_number.regex'    => 'The emergency contact number must be a valid Philippine mobile number (e.g., 09123456789 or +639123456789).',

            // Contact person
            'contact_person.max'              => 'The contact person name cannot exceed 100 characters.',
            'contact_person.regex'            => 'The contact person name may only contain letters, spaces, apostrophes, and hyphens.',
            'contact_person_number.min'       => 'The contact person number must be at least 11 digits.',
            'contact_person_number.regex'     => 'The contact person number must be a valid Philippine mobile number.',

            // Name
            'name.required' => 'The employee name is required.',
            'name.max'      => 'The employee name cannot exceed 80 characters.',
            'name.min'      => 'The employee name must be at least 3 characters.',
            'name.regex'    => 'The employee name may only contain letters, spaces, apostrophes, and hyphens.',

            // Email
            'email.required' => 'The email address is required.',
            'email.email'    => 'Please enter a valid email address.',
            'email.max'      => 'The email address cannot exceed 80 characters.',
            'email.unique'   => 'This email address is already registered.',

            // Password
            'password.required' => 'A password is required.',
            'password.min'      => 'The password must be at least :min characters.',

            // Employee number
            'employee_number.required' => 'The employee mobile number is required.',
            'employee_number.min'      => 'The employee mobile number must be at least 11 digits.',
            'employee_number.regex'    => 'The employee mobile number must be a valid Philippine mobile number.',
            'employee_number.unique'   => 'This employee mobile number is already registered.',

            // Employee code
            'emp_code.required' => 'The employee code is required.',
            'emp_code.integer'  => 'The employee code must be a number.',
            'emp_code.unique'   => 'This employee code is already taken.',

            // Personal
            'age.integer' => 'Age must be a valid number.',
            'age.min'     => 'Employee must be at least 18 years old.',
            'age.max'     => 'Age cannot exceed 100 years.',

            'gender.in' => 'Gender must be either male or female.',

            'dob.date'   => 'Please enter a valid date of birth.',
            'dob.before' => 'Date of birth must be before today.',
            'dob.after'  => 'Date of birth must be after 1900.',

            'mother_name.regex' => "Mother's name may only contain letters, spaces, apostrophes, and hyphens.",
            'father_name.regex' => "Father's name may only contain letters, spaces, apostrophes, and hyphens.",

            'educ_attainment.in' => 'Please select a valid educational attainment level.',
            'permanent_address.max' => 'Permanent address cannot exceed 500 characters.',
            'present_address.max'   => 'Present address cannot exceed 500 characters.',

            // Skills
            'skills.array'    => 'Skills must be provided as a list.',
            'skills.max'      => 'You cannot add more than 20 skills.',
            'skills.*.string' => 'Each skill must be valid text.',
            'skills.*.max'    => 'Each skill cannot exceed 50 characters.',
            'skills.*.distinct' => 'Duplicate skills are not allowed.',

            // Contract
            'contract_start_date.required'          => 'The contract start date is required.',
            'contract_start_date.date'               => 'Please enter a valid contract start date.',
            'contract_end_date.required'             => 'The contract end date is required.',
            'contract_end_date.date'                 => 'Please enter a valid contract end date.',
            'contract_end_date.after_or_equal'       => 'The contract end date must be after or equal to the start date.',
            'duration.integer' => 'Duration must be a valid number.',
            'duration.min'     => 'Duration must be at least 1 day.',

            // Government numbers
            'sss_number.required'       => 'The SSS number is required.',
            'sss_number.max'            => 'The SSS number cannot exceed 15 characters.',
            'sss_number.regex'          => 'The SSS number must contain only numbers and hyphens.',
            'pagibig_number.required'   => 'The Pag-IBIG number is required.',
            'pagibig_number.max'        => 'The Pag-IBIG number cannot exceed 15 characters.',
            'pagibig_number.regex'      => 'The Pag-IBIG number must contain only numbers and hyphens.',
            'philhealth_number.required' => 'The PhilHealth number is required.',
            'philhealth_number.max'      => 'The PhilHealth number cannot exceed 15 characters.',
            'philhealth_number.regex'    => 'The PhilHealth number must contain only numbers and hyphens.',

            // Employment
            'employee_status.required' => 'Please select an employee status.',
            'employee_status.in'       => 'Please select a valid employee status.',
            'position_id.exists'       => 'The selected position does not exist.',
            'branch_id.required'       => 'Please select a branch.',
            'branch_id.exists'         => 'The selected branch does not exist.',
            'pay_frequency.required'   => 'Please select a pay frequency.',
            'pay_frequency.in'         => 'Please select a valid pay frequency.',
            'site_id.exists'           => 'The selected site does not exist or does not belong to the selected branch.',
        ];
    }
}