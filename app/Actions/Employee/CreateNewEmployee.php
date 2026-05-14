<?php

namespace App\Actions\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateNewEmployee
{
    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'password' => Hash::make($data['password'] ?? 'password'),
            ]);

            $role = Role::firstOrCreate(['name' => 'employee']);
            $user->assignRole($role);

            $startDate = \Carbon\Carbon::parse($data['contract_start_date']);
            $endDate = \Carbon\Carbon::parse($data['contract_end_date']);
            $duration = $startDate->diffInDays($endDate);

            $employeeData = [
                'position_id' => $data['position_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'user_id' => $user->id,
                'site_id' => $data['site_id'] ?? null,
                'slug_emp' => Str::slug($data['name'] . ' ' . $data['emp_code']),
                'employee_number' => $data['employee_number'],
                'emp_code' => $data['emp_code'],
                'emergency_contact_number' => $data['emergency_contact_number'],
                'contact_person' => $data['contact_person'] ?? null,
                'contact_person_number' => $data['contact_person_number'] ?? null,
                'skills' => isset($data['skills']) ? json_encode($data['skills']) : null,
                'age' => $data['age'] ?? null,
                'gender' => $data['gender'] ?? 'male',
                'dob' => $data['dob'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,
                'father_name' => $data['father_name'] ?? null,
                'educ_attainment' => $data['educ_attainment'] ?? null,
                'certificate' => $data['certificate'] ?? null,
                'permanent_address' => $data['permanent_address'] ?? null,
                'present_address' => $data['present_address'] ?? null,
                'contract_start_date' => $data['contract_start_date'],
                'contract_end_date' => $data['contract_end_date'],
                'duration' => $duration,
                'sss_number' => $data['sss_number'],
                'philhealth_number' => $data['philhealth_number'],
                'pagibig_number' => $data['pagibig_number'],
                'tin_number' => $data['tin_number'],
                'pay_frequency' => $data['pay_frequency'] ?? 'monthly',
                'employee_status' => $data['employee_status'] ?? 'newly_hired',
            ];

            // Handle avatar upload – store on Employee, not User
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
                $employeeData['avatar'] = $avatarPath;
            } elseif (isset($data['remove_avatar']) && $data['remove_avatar'] === 'true') {
                $employeeData['avatar'] = null;
            }

            $employee = Employee::create($employeeData);

            return $employee;
        });
    }
}
