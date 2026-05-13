<?php

namespace App\Concerns\Employee;

trait EmployeeAttributes
{
    /**
     * Returns an associative array of employee attributes where the key is the attribute name
     * and the value is a human-readable description of the attribute.
     *
     * @return array
     */
    protected function empAttributes(): array
    {
        return [
            'employee_number' => 'employee number',
            'emergency_contact_number' => 'emergency contact number',
            'contact_person' => 'contact person',
            'contact_person_number' => 'contact person number',
            'emp_code' => 'employee code',
            'skills' => 'skills',
            'age' => 'age',
            'gender' => 'gender',
            'dob' => 'date of birth',
            'mother_name' => 'mother\'s name',
            'father_name' => 'father\'s name',
            'educ_attainment' => 'educational attainment',
            'certificate' => 'certificate',
            'permanent_address' => 'permanent address',
            'present_address' => 'present address',
            'pay_frequency' => 'pay frequency',
            'contract_start_date' => 'contract start date',
            'contract_end_date' => 'contract end date',
            'duration' => 'contract duration',
            'sss_number' => 'SSS number',
            'pagibig_number' => 'Pag-IBIG number',
            'philhealth_number' => 'PhilHealth number',
            'employee_status' => 'employee status',
            'position_id' => 'position',
            'branch_id' => 'branch',
            'user_id' => 'user',
            'site_id' => 'site',
            'slug_emp' => 'employee slug',
        ];
    }
}
