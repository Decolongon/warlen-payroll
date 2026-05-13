<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Position;
use App\Models\Employee;
use App\Models\User;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Generate a unique 11-digit employee number starting with 9
     */
    private function generateEmployeeNumber(): string
    {
        do {
            $number = '9' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique 10-digit SSS number (without hyphens)
     */
    private function generateSssNumber(): string
    {
        do {
            $number = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Employee::where('sss_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique 12-digit Pag-IBIG number
     */
    private function generatePagibigNumber(): string
    {
        do {
            $number = str_pad(rand(0, 9999999999), 12, '0', STR_PAD_LEFT);
        } while (Employee::where('pagibig_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique 12-digit PhilHealth number
     */
    private function generatePhilhealthNumber(): string
    {
        do {
            $number = str_pad(rand(0, 9999999999), 12, '0', STR_PAD_LEFT);
        } while (Employee::where('philhealth_number', $number)->exists());

        return $number;
    }

    /**
     * Get or create branch (Bacolod branch)
     */
    private function getBranch(): Branch
    {
        return Branch::firstOrCreate(
            ['branch_name' => 'Bacolod branch'],
            [
                'branch_slug' => Str::slug('Bacolod branch'),
                'branch_address' => 'Alijis'
            ]
        );
    }

    /**
     * Get or create a position by name
     */
    private function getOrCreatePosition(string $posName): Position
    {
        if (empty($posName)) {
            $posName = 'Regular employee';
        }
        return Position::firstOrCreate(
            ['pos_name' => $posName],
            [
                'pos_slug' => Str::slug($posName),
                'basic_salary' => 550
            ]
        );
    }

    /**
     * Get or create a site by name under the given branch
     */
    private function getOrCreateSite(string $siteName, Branch $branch): Site
    {
        if (empty($siteName)) {
            $siteName = 'Default Site';
        }
        return Site::firstOrCreate(
            ['site_name' => $siteName, 'branch_id' => $branch->id],
            ['branch_id' => $branch->id]
        );
    }

    /**
     * Parse the embedded tab‑separated raw data into an array of employee records.
     * Each record contains user data (name, email, etc.) and employee attributes.
     */
    private function parseEmployeeData(): array
    {
        // The raw data as a heredoc – copy the entire table from your spreadsheet
        // (I've included a small sample; replace with your full data)
        $rawData = <<<RAW
#	START DATE	DURATION	ID Number	NAME	MIDDLE NAME	STATUS	PROJECT SITE	SEX	POSITION	PERMANENT ADDRESS	PRESENT ADDRESS	NUMBER	DATE OF BIRTH	AGE	EMAIL ADDRESS	CONTACT PERSON	CONTACT NUMBER	ADDRESS OF CONTACT PERSON	MOTHERS NAME	FATHERS NAME	PLANTILLA	ASSIGNMENT	SSS#	PHILHEALTH	PAG-IBIG	TIN	DATE END	EDUCATIONAL ATTAINTMENT	CERTIFICATES	SKILLS
1	03/13/2014	12 Years, 2 Months, 0 Days	133	Masgong, Jason	Fernando	Active	Fabrication-Bacolod	M	Welder	Villa Esperanza Tangub Bacolod City	Villa Esperanza Tangub Bacolod City	9109881355	10/09/1988	37 Years, 7 Months, 4 Days					Elna Masgong	Crispolo Masgong	Bacolod	LBC IloIlo		11-050663051-7	9140-6903-1211			TESDA RTC Shield Metal Arc Welding			
2	12/25/2015	10 Years, 4 Months, 18 Days	18	Ruel Herminanda	Infane	Active	Megaworld Belmont-Iloilo	M	Fabrication	Prk. Crossing 8 Brgy. Tangub Bacolod CIty	Prk. Crossing 8 Brgy. Tangub Bacolod CIty		01/31/1985	41 Years, 3 Months, 12 Days		Richard Herminanda	9494889410	Prk Crosing 8 Tangub Bacolod City	Mercedita Herminanda	Romeo Herminanda	Bacolod	Bacolod	00-07-2328659-6	11-050539504-2		947-495-323		Computer Technicin IT (Undergraduate)			
3	5/6/2019	7 Years, 0 Months, 7 Days	135	Matta, Michael Andrew	Adrao	Active	Phil Arforce-Cebu	M	Electrician	Brgy. Tiza Roxas City Capiz	Brgy. Tiza Roxas City Capiz	0946-629-5455	2/23/1996	30 Years, 2 Months, 20 Days					Maria Matta	Ireneo Matta	Bacolod	Crimson		11-025617573-8	1212-6087-0833						
RAW;
        // IMPORTANT: Replace the sample data above with your full dataset (all rows)
        // The seeder will parse every row.

        $lines = explode("\n", $rawData);
        if (count($lines) < 2) {
            return [];
        }

        $header = str_getcsv(array_shift($lines), "\t");
        // Normalize header keys (lowercase, trim)
        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        $employees = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            $row = str_getcsv($line, "\t");
            // Ensure row has enough columns
            if (count($row) < count($header)) continue;

            $data = array_combine($header, $row);

            $name = trim($data['name'] ?? '');
            if (empty($name)) continue;

            $email = !empty($data['email address']) ? trim($data['email address']) : null;
            if (!$email) {
                // Generate a simple email from the name (remove commas, replace spaces with dots)
                $emailName = strtolower(str_replace([' ', ','], ['.', ''], $name));
                $email = $emailName . '@example.com';
            }

            // Prepare employee attributes
            $employees[] = [
                'name'                  => $name,
                'email'                 => $email,
                'emp_code'              => !empty($data['id number']) ? (int) $data['id number'] : null,
                'duration'              => $data['duration'] ?? null,
                'employee_status'       => strtolower($data['status'] ?? 'active'),
                'gender'                => $data['sex'] ?? null,
                'position_name'         => $data['position'] ?? null,
                'permanent_address'     => $data['permanent address'] ?? null,
                'present_address'       => $data['present address'] ?? null,
                'contact_number'        => $data['number'] ?? null,
                'dob'                   => !empty($data['date of birth']) ? Carbon::parse($data['date of birth'])->format('Y-m-d') : null,
                'age'                   => !empty($data['age']) ? trim($data['age']) : null,
                'contact_person'        => $data['contact person'] ?? null,
                'contact_person_number' => $data['contact number'] ?? null,
                'mother_name'           => $data['mothers name'] ?? null,
                'father_name'           => $data['fathers name'] ?? null,
                'sss_number'            => $data['sss#'] ?? null,
                'philhealth_number'     => $data['philhealth'] ?? null,
                'pagibig_number'        => $data['pag-ibig'] ?? null,
                'contract_end_date'     => !empty($data['date end']) ? Carbon::parse($data['date end'])->format('Y-m-d') : null,
                'educ_attainment'       => $data['educational atainment'] ?? $data['educational attaintment'] ?? null,
                'certificate'           => $data['certificates'] ?? null,
                'skills'                => $data['skills'] ?? null,
                'site_name'             => $data['project site'] ?? null,
                'tin_number'            => $data['tin'] ?? null,
            ];
        }

        return $employees;
    }

    /**
     * Get all users data (static admin/HR + dynamic employees from spreadsheet)
     */
    private function getUsersData(): array
    {
        // ========== STATIC ADMIN / HR USERS (KEEP AS IS) ==========
        $staticUsers = [
            // Admin Users
            ['name' => 'Warlito', 'email' => 'warlito@gmail.com', 'emp_id' => 1000, 'role' => 'admin'],
            ['name' => 'Elena', 'email' => 'elena@gmail.com', 'emp_id' => 1001, 'role' => 'admin'],
            
            // HR User
            ['name' => 'Jona', 'email' => 'jona@gmail.com', 'emp_id' => 2222, 'role' => 'hr_head'],
            ['name' => 'Rica', 'email' => 'rica@gmail.com', 'emp_id' => 3333, 'role' => 'hr_head'],
        ];
        
        // ========== OLD HARDCODED EMPLOYEE ENTRIES (COMMENTED OUT) ==========
        // ... (all old employee entries are removed / commented)
        
        // ========== DYNAMIC EMPLOYEE DATA FROM SPREADSHEET ==========
        $spreadsheetEmployees = $this->parseEmployeeData();
        
        $dynamicUsers = [];
        foreach ($spreadsheetEmployees as $emp) {
            $dynamicUsers[] = [
                'name'     => $emp['name'],
                'email'    => $emp['email'],
                'emp_id'   => $emp['emp_code'],
                'role'     => 'employee',
                // Store additional employee data temporarily to use when creating Employee record
                'employee_attrs' => $emp,
            ];
        }
        
        // Merge static admins/HR with dynamic employees
        return array_merge($staticUsers, $dynamicUsers);
    }

    /**
     * Create or get user with role
     */
    private function getOrCreateUser(array $userData, array $roles): User
    {
        $user = User::where('email', $userData['email'])->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);
            
            $user->assignRole($roles[$userData['role']]);
        } elseif (!$user->hasRole($userData['role'])) {
            $user->assignRole($userData['role']);
        }
        
        return $user;
    }

    /**
     * Create employee record (exclude admin users)
     */
    private function createEmployeeRecord(Position $position, User $user, Branch $branch, int $empId, ?Site $site = null, array $extraAttrs = []): void
    {
        // Skip if user is admin
        if ($user->hasRole('admin')) {
            $this->command->info("Skipping employee record for admin: {$user->name} ({$user->email})");
            return;
        }
        
        // Prepare employee data
        $employeeData = [
            'position_id'               => $position->id,
            'branch_id'                 => $branch->id,
            'user_id'                   => $user->id,
            'site_id'                   => $site?->id,
            'slug_emp'                  => Str::slug($user->name . '-' . $empId),
            'emp_code'                  => $empId,
            'employee_number'           => $this->generateEmployeeNumber(),
            'sss_number'                => $extraAttrs['sss_number'] ?? $this->generateSssNumber(),
            'pagibig_number'            => $extraAttrs['pagibig_number'] ?? $this->generatePagibigNumber(),
            'philhealth_number'         => $extraAttrs['philhealth_number'] ?? $this->generatePhilhealthNumber(),
            'contract_start_date'       => now(),
            'contract_end_date'         => $extraAttrs['contract_end_date'] ?? now()->addYear(),
            'emergency_contact_number'  => $extraAttrs['contact_person_number'] ?? '09123456789',
            'pay_frequency'             => 'weekender',
            'employee_status'           => $extraAttrs['employee_status'] ?? 'active',
            'contact_person'            => $extraAttrs['contact_person'] ?? null,
            'contact_person_number'     => $extraAttrs['contact_person_number'] ?? null,
            'skills'                    => $extraAttrs['skills'] ? explode(',', $extraAttrs['skills']) : null,
            'age'                       => $extraAttrs['age'] ?? null,
            'gender'                    => $extraAttrs['gender'] ?? null,
            'dob'                       => $extraAttrs['dob'] ?? null,
            'mother_name'               => $extraAttrs['mother_name'] ?? null,
            'father_name'               => $extraAttrs['father_name'] ?? null,
            'educ_attainment'           => $extraAttrs['educ_attainment'] ?? null,
            'certificate'               => $extraAttrs['certificate'] ?? null,
            'permanent_address'         => $extraAttrs['permanent_address'] ?? null,
            'present_address'           => $extraAttrs['present_address'] ?? null,
            'duration'                  => $extraAttrs['duration'] ?? null,
        ];
        
        Employee::firstOrCreate(
            ['user_id' => $user->id],
            $employeeData
        );
    }

    /**
     * Display statistics
     */
    private function displayStatistics(): void
    {
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('Employee Seeder completed successfully!');
        $this->command->info('========================================');
        $this->command->info("Admin users: " . User::role('admin')->count());
        $this->command->info("HR users: " . User::role('hr_head')->count());
        $this->command->info("Employee users: " . User::role('employee')->count());
        $this->command->info("Total employee records: " . Employee::count());
        $this->command->info('========================================');
        
        $this->command->info("\nAdmin Users (No employee records):");
        foreach (User::role('admin')->get() as $admin) {
            $employeeRecord = Employee::where('user_id', $admin->id)->first();
            $hasEmployeeRecord = $employeeRecord ? '⚠️ Has employee record' : '✓ No employee record';
            $this->command->info("  - {$admin->name} ({$admin->email}) - {$hasEmployeeRecord}");
        }
        
        $this->command->info("\nHR Users (With employee records):");
        foreach (User::role('hr_head')->get() as $hr) {
            $employeeRecord = Employee::where('user_id', $hr->id)->first();
            $hasEmployeeRecord = $employeeRecord ? '✓ Has employee record' : '✗ Missing employee record';
            $this->command->info("  - {$hr->name} ({$hr->email}) - {$hasEmployeeRecord}");
        }
        
        $this->command->info("\nEmployee Users (first 5):");
        foreach (User::role('employee')->limit(5)->get() as $employee) {
            $employeeRecord = Employee::where('user_id', $employee->id)->first();
            $hasEmployeeRecord = $employeeRecord ? '✓' : '✗';
            $this->command->info("  - {$employee->name} ({$employee->email}) - Employee Record: {$hasEmployeeRecord}");
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'admin' => Role::firstOrCreate(['name' => 'admin']),
            'employee' => Role::firstOrCreate(['name' => 'employee']),
            'hr_head' => Role::firstOrCreate(['name' => 'hr_head']),
        ];
        
        $roleMap = [
            'admin' => $roles['admin'],
            'hr_head' => $roles['hr_head'],
            'employee' => $roles['employee'],
        ];

        // Create branch, default position, and default site
        $branch = $this->getBranch();
        $defaultPosition = $this->getOrCreatePosition('Regular employee');
        $defaultSite = $this->getOrCreateSite('Default Site', $branch);

        $usersData = $this->getUsersData();
        $totalUsers = count($usersData);
        
        $this->command->info('Starting Employee Seeder...');
        $this->command->info("Total users to process: {$totalUsers}");
        $progressBar = $this->command->getOutput()->createProgressBar($totalUsers);
        $progressBar->start();

        $processedUsers = [];

        foreach ($usersData as $data) {
            // For employee rows from spreadsheet, we have extra attributes
            $extraAttrs = $data['employee_attrs'] ?? [];
            $user = $this->getOrCreateUser($data, $roleMap);
            
            // Determine position and site from extraAttrs if available
            $position = $defaultPosition;
            $site = $defaultSite;
            if (!empty($extraAttrs['position_name'])) {
                $position = $this->getOrCreatePosition($extraAttrs['position_name']);
            }
            if (!empty($extraAttrs['site_name'])) {
                $site = $this->getOrCreateSite($extraAttrs['site_name'], $branch);
            }
            
            $processedUsers[] = [
                'user' => $user,
                'emp_id' => $data['emp_id'],
                'role' => $data['role'],
                'position' => $position,
                'site' => $site,
                'extra_attrs' => $extraAttrs,
            ];
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        
        // Create employee records (exclude admin users)
        $this->command->info('Creating employee records (excluding admin users)...');
        
        $nonAdminUsers = array_filter($processedUsers, function($item) {
            return $item['role'] !== 'admin';
        });
        
        $nonAdminCount = count($nonAdminUsers);
        $this->command->info("Creating employee records for {$nonAdminCount} non-admin users...");
        
        $employeeProgressBar = $this->command->getOutput()->createProgressBar($nonAdminCount);
        $employeeProgressBar->start();

        foreach ($processedUsers as $item) {
            $this->createEmployeeRecord(
                $item['position'],
                $item['user'],
                $branch,
                $item['emp_id'],
                $item['site'],
                $item['extra_attrs']
            );
            if ($item['role'] !== 'admin') {
                $employeeProgressBar->advance();
            }
        }
        
        $employeeProgressBar->finish();
        
        // Display statistics
        $this->displayStatistics();
    }
}