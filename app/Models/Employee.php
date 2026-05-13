<?php

namespace App\Models;


use App\Concerns\LogsActivityTrait;
use App\Models\Branch;
use App\Models\EmployeeContributionSetting;
use App\Models\Position;
use App\Models\User;
use App\Policies\EmployeePolicy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[UsePolicy(EmployeePolicy::class)]
class Employee extends Model
{
    use HasRoles, HasFactory, SoftDeletes,  LogsActivity, Notifiable;

    use LogsActivityTrait;

    protected $table = 'employees';


    protected $fillable = [
        'position_id',
        'branch_id',
        'user_id',
        'site_id',
        'slug_emp',
        'avatar',
        'emp_code',
        'employee_number',
        'contract_start_date',
        'contract_end_date',
        'sss_number',
        'pagibig_number',
        'philhealth_number',
        'emergency_contact_number',
        'pay_frequency',
        'employee_status',
        'contact_person',
        'contact_person_number',
        'skills',
        'age',
        'gender',
        'dob',
        'mother_name',
        'father_name',
        'educ_attainment',
        'certificate',
        'permanent_address',
        'present_address',
        'duration',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sss_number' => 'encrypted',
        'pagibig_number' => 'encrypted',
        'philhealth_number' => 'encrypted',
        'skills' => 'array',
        'duration' => 'integer',
        'dob' => 'date',
        'age' => 'integer',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'position.pos_name',
                'branch.branch_name',
                'site.site_name',
                'user.name',
                'user.email',
                'emp_code',
                'employee_number',
                'contract_start_date',
                'contract_end_date',
                'sss_number',
                'pagibig_number',
                'philhealth_number',
                'emergency_contact_number',
                'pay_frequency',
                'employee_status',
                'present_address',
                'duration',
                // ----- add these -----
                'slug_emp',
                'avatar',
                'contact_person',
                'contact_person_number',
                'skills',
                'age',
                'gender',
                'dob',
                'mother_name',
                'father_name',
                'educ_attainment',
                'certificate',
                'permanent_address',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function contributionSettings(): HasMany
    {
        return $this->hasMany(EmployeeContributionSetting::class, 'employee_id');
    }

    protected function getActivityDisplayNames(): array
    {
        return [
            'position.pos_name' => 'Position',
            'branch.branch_name' => 'Branch',
            'site.site_name' => 'Site',
            'user.name' => 'Employee Name',
            'user.email' => 'Email',
            'emp_code' => 'Employee Code',
            'employee_number' => 'Employee Number',
            'contract_start_date' => 'Contract Start Date',
            'contract_end_date' => 'Contract End Date',
            'sss_number' => 'SSS Number',
            'pagibig_number' => 'Pag-Ibig Membership ID',
            'philhealth_number' => 'PhilHealth Identification Number', // fixed key
            'emergency_contact_number' => 'Emergency Contact Number',
            'pay_frequency' => 'Pay Frequency',
            'employee_status' => 'Employee Status',
            // ----- add these -----
            'present_address' => 'Present Address',
            'duration' => 'Duration',
            'slug_emp' => 'Employee Slug',
            'avatar' => 'Avatar',
            'contact_person' => 'Contact Person',
            'contact_person_number' => 'Contact Person Number',
            'skills' => 'Skills',
            'age' => 'Age',
            'gender' => 'Gender',
            'dob' => 'Date of Birth',
            'mother_name' => "Mother's Name",
            'father_name' => "Father's Name",
            'educ_attainment' => 'Educational Attainment',
            'certificate' => 'Certificate',
            'permanent_address' => 'Permanent Address',
        ];
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return $this->employee_status === 'active' ? 'green' : 'gray';
    }

    /**
     * Scope active employees
     */
    public function scopeActive($query)
    {
        return $query->where('employee_status', 'active');
    }

    /**
     * Scope inactive employees
     */
    public function scopeInactive($query)
    {
        return $query->where('employee_status', 'inactive');
    }

    /**
     * Check if employee is active
     */
    public function isActive(): bool
    {
        return $this->employee_status === 'active';
    }

    /**
     * Check if employee is inactive
     */
    public function isInactive(): bool
    {
        return $this->employee_status === 'inactive';
    }

    /**
     * Manually check if employee should be active based on current date
     */
    public function shouldBeActive(): bool
    {
        $today = Carbon::today();

        if (!$this->contract_start_date || !$this->contract_end_date) {
            return false;
        }

        return $today->gte($this->contract_start_date) &&
            $today->lte($this->contract_end_date) &&
            $this->contract_start_date->lte($this->contract_end_date);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ApplicationLeave::class, 'employee_id');
    }

    public function sites(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    // public function attendances(): HasMany
    // {
    //     return $this->hasMany(Attendance::class, 'employee_id');
    // }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function employeeIncentives()
    {
        return $this->belongsToMany(Employee::class, 'employee_incentives', 'employee_id', 'incentive_id');
    }



    // Accessors and Mutators
    protected function employeeStatus(): Attribute
    {
        return Attribute::make(
            //get: fn($value) => Str::title($value),
            set: fn($value) => strtolower(trim(strip_tags($value))),
        );
    }

    protected function employeeNumber(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    protected function emergencyContactNumber(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    protected function payFrequency(): Attribute
    {
        return Attribute::make(
            // get: fn($value) => preg_replace('/[^a-zA-Z0-9\s]/', '-', Str::title($value)),
            set: fn($value) => strtolower(trim(strip_tags($value))),
        );
    }

    // Contact person number
    protected function contactPersonNumber(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Gender
    protected function gender(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Mother's name
    protected function motherName(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Father's name
    protected function fatherName(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Educational attainment
    protected function educAttainment(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Certificate (filename or description)
    protected function certificate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Permanent address
    protected function permanentAddress(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    // Present address
    protected function presentAddress(): Attribute
    {
        return Attribute::make(
            set: fn($value) => trim(strip_tags($value)),
        );
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function getRouteKeyName()
    {
        return 'slug_emp';
    }
}
