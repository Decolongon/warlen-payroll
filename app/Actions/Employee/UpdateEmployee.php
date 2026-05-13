<?php

namespace App\Actions\Employee;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UpdateEmployee
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function update(array $data, Employee $employee): Employee
    {
        $user = $employee->user;

        $userData = [
            'name' => $data['name'],
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

       // $role = Role::firstOrCreate(['name' => 'employee']);
       // $user->assignRole($role);

        //dd($data['employee_status']);

        $startDate = \Carbon\Carbon::parse($data['contract_start_date']);
        $endDate = \Carbon\Carbon::parse($data['contract_end_date']);
        $duration = $startDate->diffInDays($endDate);
        $employee->update([
            'position_id' => $data['position_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'user_id' => $user->id,
            'site_id' => $data['site_id'] ?? null,
            'employee_number' => $data['employee_number'],
            'emp_code' => $data['emp_code'],
            'slug_emp' => Str::slug($data['name'] . ' ' . $data['emp_code']),
            'emergency_contact_number' => $data['emergency_contact_number'],
            'contact_person' => $data['contact_person'] ?? null,
            'contact_person_number' => $data['contact_person_number'] ?? null,
            'skills' => isset($data['skills']) ? (is_array($data['skills']) ? json_encode($data['skills']) : $data['skills']) : null,
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
            'pagibig_number' => $data['pagibig_number'],
            'philhealth_number' => $data['philhealth_number'],
            'pay_frequency' => $data['pay_frequency'] ?? 'monthly',
            'employee_status' => $data['employee_status'] ?? 'active',
        ]);

        // Handle avatar upload – store on Employee, not User
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old avatar if exists
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $employee->avatar = $avatarPath;
        } elseif (isset($data['remove_avatar']) && $data['remove_avatar'] === 'true') {
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $employee->avatar = null;
        }

        $employee->save();

        return $employee;
    }
}
