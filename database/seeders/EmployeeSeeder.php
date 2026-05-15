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
     * Create or get branch
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
     * Create or get position (Regular employee)
     */
    private function getPosition(): Position
    {
        return Position::firstOrCreate(
            ['pos_name' => 'Regular employee'],
            [
                'pos_slug' => Str::slug('Regular employee'),
                'basic_salary' => 550
            ]
        );
    }

    /**
     * Create or get site
     */
    private function getSite(Branch $branch): Site
    {
        return Site::firstOrCreate(
            ['site_name' => 'UPHD', 'branch_id' => $branch->id],
            ['branch_id' => $branch->id]
        );
    }

    /**
     * Get all users data (original + new employees)
     * All new employees have role 'employee', position 'Regular employee',
     * and pay_frequency 'semi_monthly'
     */
    private function getUsersData(): array
    {
        // Original users (unchanged) – these keep pay_frequency = 'weekender' (default)
        $originalUsers = [
            // Admin Users
            ['name' => 'Warlito', 'email' => 'warlito@gmail.com', 'emp_id' => 1000, 'role' => 'admin'],
            ['name' => 'Elena', 'email' => 'elena@gmail.com', 'emp_id' => 1001, 'role' => 'admin'],
            
            // HR User
            ['name' => 'Jona', 'email' => 'jona@gmail.com', 'emp_id' => 2222, 'role' => 'hr_head'],
            ['name' => 'Rica', 'email' => 'rica@gmail.com', 'emp_id' => 3333, 'role' => 'hr_head'],
            
            // from uphd
            ['name' => 'Jolisa', 'email' => 'jolisa@example.com', 'emp_id' => 1100, 'role' => 'employee'],
            ['name' => 'John Eric Dumala', 'email' => 'john.eric@example.com', 'emp_id' => 244, 'role' => 'employee'],
            ['name' => 'Arman', 'email' => 'arman@example.com', 'emp_id' => 1080, 'role' => 'employee'],
            ['name' => 'Lloyd', 'email' => 'lloyd@example.com', 'emp_id' => 1077, 'role' => 'employee'],
            ['name' => 'Allan', 'email' => 'allan@example.com', 'emp_id' => 1079, 'role' => 'employee'],
            ['name' => 'Marlon', 'email' => 'marlon@example.com', 'emp_id' => 1031, 'role' => 'employee'],
            ['name' => 'Joven', 'email' => 'joven@example.com', 'emp_id' => 1028, 'role' => 'employee'],
            ['name' => 'Francis', 'email' => 'francis@example.com', 'emp_id' => 309, 'role' => 'employee'],
            ['name' => 'Ana', 'email' => 'ana@example.com', 'emp_id' => 670, 'role' => 'employee'],
            ['name' => 'Rey', 'email' => 'rey@example.com', 'emp_id' => 1211, 'role' => 'employee'],
            ['name' => 'Jonas', 'email' => 'jonas@example.com', 'emp_id' => 1082, 'role' => 'employee'],
            ['name' => 'Eugenio', 'email' => 'eugenio@example.com', 'emp_id' => 1209, 'role' => 'employee'],
            ['name' => 'Ferdinand', 'email' => 'ferdinand@example.com', 'emp_id' => 1210, 'role' => 'employee'],
            ['name' => 'Jury', 'email' => 'jury@example.com', 'emp_id' => 1174, 'role' => 'employee'],
            ['name' => 'Ruth', 'email' => 'ruth@example.com', 'emp_id' => 1234, 'role' => 'employee'],
            ['name' => 'Ryan', 'email' => 'ryan@example.com', 'emp_id' => 1212, 'role' => 'employee'],
            ['name' => 'Aubrey', 'email' => 'aubrey@example.com', 'emp_id' => 1047, 'role' => 'employee'],
            ['name' => 'Arnel', 'email' => 'arnel@example.com', 'emp_id' => 787, 'role' => 'employee'],
            ['name' => 'Christoval', 'email' => 'christoval@example.com', 'emp_id' => 1297, 'role' => 'employee'],
            ['name' => 'Philippe', 'email' => 'philippe@example.com', 'emp_id' => 1120, 'role' => 'employee'],
            ['name' => 'Harold', 'email' => 'harold@example.com', 'emp_id' => 789, 'role' => 'employee'],
            
            // from ridge (all employees)
            ['name' => 'AllanB', 'email' => 'allanb@example.com', 'emp_id' => 1444, 'role' => 'employee'],
            ['name' => 'BenedictoG', 'email' => 'benedictog@example.com', 'emp_id' => 1333, 'role' => 'employee'],
            ['name' => 'ErenioC', 'email' => 'erenioc@example.com', 'emp_id' => 1222, 'role' => 'employee'],
            ['name' => 'RyanJayT', 'email' => 'ryanjayt@example.com', 'emp_id' => 1111, 'role' => 'employee'],
            ['name' => 'DanteM', 'email' => 'dantem@example.com', 'emp_id' => 1191, 'role' => 'employee'],
            ['name' => 'EdwinJ', 'email' => 'edwinj@example.com', 'emp_id' => 1262, 'role' => 'employee'],
            ['name' => 'PauloJ', 'email' => 'pauloj@example.com', 'emp_id' => 1269, 'role' => 'employee'],
            ['name' => 'JohRobertL', 'email' => 'johrobertl@example.com', 'emp_id' => 1266, 'role' => 'employee'],
            ['name' => 'RichardG', 'email' => 'richardg@example.com', 'emp_id' => 1284, 'role' => 'employee'],
            ['name' => 'DaniloG', 'email' => 'danilog@example.com', 'emp_id' => 1034, 'role' => 'employee'],
            ['name' => 'JoebertD', 'email' => 'joebertd@example.com', 'emp_id' => 857, 'role' => 'employee'],
            ['name' => 'JuluwieV', 'email' => 'juluwiev@example.com', 'emp_id' => 754, 'role' => 'employee'],
            ['name' => 'LhenieJaneS', 'email' => 'lheniejanes@example.com', 'emp_id' => 1272, 'role' => 'employee'],
            ['name' => 'IrishSandraB', 'email' => 'irishsandrab@example.com', 'emp_id' => 1264, 'role' => 'employee'],
        ];

        // New employees (only name and emp_id) – all will have pay_frequency = 'semi_monthly'
        $rawNewEmployees = [
            [751, 'JohnLouel'], [713, 'RachelleAnn'], [885, 'MarkTaryao'], [6, 'WelbertDatoon'],
            [115, 'JonaDelicana'], [646, 'MarinyDelrosario'], [220, 'ShanineNatalio'], [1, 'AileenEsplaguera'],
            [450, 'JuleeSagal'], [131, 'RecajeanDesacula'], [12, 'LeonelFermiza'], [54, 'JerwinNierves'],
            [671, 'RossethFormanes'], [214, 'NicaGonzale'], [659, 'Jay   LAsola'], [167, 'KarlynnNuneza'],
            [163, 'MarneAlmarza'], [887, 'Manilyn Estorco'], [9, 'Gilbert Saluta'], [273, 'Casipong Antonio'],
            [882, 'Christian Mariano'], [66, 'BENNYALLADIN'], [71, 'Primitiva Alladin'], [683, 'Marjorie Galario'],
            [867, 'NoliRodriguez'], [651, 'HandellRonquillo'], [916, 'Angela Canaya'], [746, 'Lorenz Monton'],
            [215, 'Russel John Tuya'], [909, 'LULENORALFORPUE'], [915, 'LEO GESTER CANETE'], [890, 'JOHN VINCENT SALINAS'],
            [18, 'RUEL HERMINANDA'], [734, 'ELLA PAGLOMUTAN'], [888, 'ERROL BIONA'], [728, 'JOHN CHRISTIAN TALAMAN'],
            [97, 'DEMMY CORDOVA'], [914, 'Jason Cuberos'], [897, 'John Rhyan Cancian'], [364, 'Joseph Amant'],
            [206, 'Diosdado Gonzaga Ii'], [222, 'BILLY JOHN BARSANAS'], [172, 'JOME GRUZO'], [174, 'JOEL SEDERIO'],
            [736, 'MELODY SENTOY'], [912, 'JAMBIE REYES'], [816, 'ALISASIS ELFRED'], [111, 'ERICKSON DELOGAR'],
            [917, 'BRENT MISPENAS'], [898, 'BREY BRYAN DAYDAY'], [799, 'JUAN CADUNGON'], [695, 'HELBERT ALMANOCHE'],
            [831, 'RONDEL EDANGGA'], [880, 'CHRISTIAN MATA'], [710, 'RODNEY PARCON'], [112, 'RYAN SAMUEL SUSADA'],
            [588, 'VITO MACARIO'], [30, 'JOHN LE  MONTANO'], [779, 'RYAN BENGUELO'], [668, 'PAUL JOHN DENAGA'],
            [418, 'JERIC DESLATE'], [47, 'RENz ESPINOSA'], [737, 'RONEL TULIO'], [884, 'JOSEPH JOHN LAURE'],
            [46, 'JUNEDY GALLETO III'], [910, 'SHARMINE BAYOT'], [942, 'HILARY FICA'], [654, 'BOBBY LABRADOR'],
            [953, 'MA JESSA BALIBALOS'], [625, 'ARLAN IGMEDIO'], [124, 'ELSIE AQUINO'], [960, 'NORMAN LAUSA'],
            [877, 'NONATO EDISON'], [878, 'ABELLANO JES MARTIN'], [626, 'GERALD GENTEROLES'], [961, 'CREZELLE ANN VILLANUEVA'],
            [678, 'JOHN  LOVELL ZERRUDO'], [963, 'ANDREW HUERVA'], [268, 'Jerone Vista'], [968, 'REMY QUIJANO'],
            [972, 'Jame Kyle Penequito'], [973, 'De~Ann Manocan'], [974, 'WENNAH OBEDIENTE'], [299, 'RYAN COSEBA'],
            [313, 'GLYN TINGSON'], [976, 'JUSTINE CONSTANTINO'], [977, 'WILMAR VILLEGAS'], [979, 'REDEN ALIMANTE'],
            [978, 'EDWARD ZASPA'], [980, 'JASSEN PADILLA'], [984, 'MARAH ZARAGOSA'], [970, 'Ace Andrian Barredo'],
            [971, 'Xhar Jhon Suarez'], [883, 'Timothy Montero'], [845, 'ROMME PAHILANGA'], [992, 'BELLA PORNALES'],
            [1017, 'KIRB GASCON'], [1019, 'JOHNEL DOROIN'], [1021, 'JOHN NEIL BACHOCO'], [1025, 'MAMERTO VILLAREAL'],
            [1026, 'ADONIS LADDARAN'], [600, 'JERNELL GARCIA~'], [808, 'KLEO SANDY'], [183, 'JASON LOZADA'],
            [539, 'ALIXANDER NONO'], [735, 'ERICA HERMOSURA'], [168, 'BREX DATOON'], [8, 'CHRISTINE NOBLEZA'],
            [1042, 'LOUIE JAY AGUILLON'], [1045, 'ARGIE LAGATIMAN'], [1044, 'ALEJO LAMPASO'], [1057, 'KENT ALISON'],
            [1073, 'ERAIZA FEJER'], [1074, 'ALPHA OMEGA OLAYBAR'], [1093, 'ELIJAH MONTON'], [1094, 'LAARNE LUMOGDANG'],
            [1095, 'CRISTA MAE   LIZA'], [1099, 'FUENTEBELLA JOHN EARL'], [581, 'BASAS JOFFREY'], [425, 'MELFRED PAGLINAWAN'],
            [1121, 'JAMES DURAY'], [332, 'VICTORIO HENDEN'], [1102, 'EDMUNDO Laurencio'], [1039, 'REY CABUNGCAG'],
            [995, 'EPIPANIO BALIJADO'], [1023, 'RALPH ARIBATO'], [1040, 'JOE PIT SAZON'], [786, 'CHRISTIAN RICO'],
            [1041, 'MARK GOMEZ'], [1134, 'MEG JULIANNE VILLACASTI'], [1143, 'LEONELFERMIZA'], [1148, 'IRISH GRACE TICAR'],
            [1164, 'CANTOR RHYNEXELLE'], [1165, 'COJA HANA JANELLE'], [1169, 'EVAN  JAMES BARBON'], [1175, 'JIMENA MACANDILI'],
            [1178, 'JOSEPH PENAFIEL'], [1184, 'Jenelyn Viajedor'], [1213, 'DOMINADOR ALCON JR'], [1215, 'CEJIE ALTAS'],
            [1214, 'ELEUBERT MERABE'], [1216, 'MARIE ARENDON'], [1130, 'GERALD DUAZO'], [1222, 'CINDY JOY TAGLE'],
            [1218, 'ANNE IWAYAN'], [891, 'MARK ANTHONY ORENCIO'], [1231, 'LOUISE AGNAS'], [1232, 'BEATRICE DEOCAMPO'],
            [1233, 'ALISTER ARROS'], [1037, 'JOSHUA SONIER'], [1228, 'JOHN MICHAELBULOS'], [1217, 'BRYAN BALBOA'],
            [1219, 'JETHRO PECHAYCO'], [1220, 'GLENN GASAPO'], [1235, 'MARY JOY JAVOC'], [1236, 'FRITZIE GRACE FRAYCO'],
            [1237, 'MARY  JOY  BALOYO'], [1258, 'FRANCIS COLMENARES'], [1260, 'DOMINIC LASPONIA'], [1275, 'GEREBELO ANGCO'],
            [1276, 'YDRYAN CABALINAN'], [1277, 'RIO JHOY DELOS SANTOS'], [1278, 'NICHOLE MATI~ONG'], [1279, 'RAYSELLE CABAN'],
            [1280, 'JOHN GODWIN MAMIGO'], [1287, 'JASMIN GENOVIA'], [1298, 'ANGELO TORRECAMPO'], [893, 'BRIGEDO CARASAQUIT'],
            [210, 'MARLO BALOLOT'], [642, 'MARK MENGULLO'], [1348, 'AGUIDA SANTILLAN'], [1349, 'SAYRON BERMEO'],
            [1352, 'CASELYN BANHAO'], [1381, 'JUNALYN COMBESTRA'], [1402, 'GENARA DUCASI'], [1432, 'BRIAN JIMENA'],
            [1431, 'RAFAEL PERLADO'], [962, 'JHON ERNIE SERANA'], [1441, 'MAE ANNECHELL SUSTIOSO'], [1442, 'CLARIS ANN DEQUILLA'],
            [1444, 'GODFREY CORTEZA'], [1447, 'RONIMAE KATE VICENTE'], [1450, 'GILBERT TACUYAN'], [1460, 'YVONNE LOBATON'],
            [1457, 'MICHAEL GABAN'], [1458, 'ANN JELO JOSH AGBAY'], [1464, 'KENT GERRY DIAPEN'], [1465, 'JERICHO FRANCIS BABA'],
            [1470, 'BIEN PAHILANGA'], [1471, 'KLYNT SALGADO'], [1482, 'CRISPHER PENAROYO'],
        ];

        // Collect existing emp_ids to avoid duplicates (e.g., duplicate 1034)
        $existingEmpIds = array_column($originalUsers, 'emp_id');

        $newUsers = [];
        foreach ($rawNewEmployees as [$empId, $name]) {
            if (in_array($empId, $existingEmpIds)) {
                continue; // skip duplicate emp_id
            }
            // Generate a clean email from the name (same style as ridge employees)
            $emailName = strtolower(preg_replace('/\s+/', '.', trim($name)));
            $emailName = preg_replace('/[^a-z0-9.]/', '', $emailName);
            $email = $emailName . '@example.com';

            $newUsers[] = [
                'name'           => $name,
                'email'          => $email,
                'emp_id'         => $empId,
                'role'           => 'employee',
                'pay_frequency'  => 'semi_monthly',   // set for all new employees
            ];
        }

        return array_merge($originalUsers, $newUsers);
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
     * Create employee record (exclude only admin users)
     * Now accepts an optional $payFrequency parameter (default 'weekender')
     */
    private function createEmployeeRecord(Position $position, User $user, Branch $branch, int $empId, ?Site $site = null, string $payFrequency = 'weekender'): void
    {
        // Only skip if user is admin - HR and employees will get employee records
        if ($user->hasRole('admin')) {
            $this->command->info("Skipping employee record for admin: {$user->name} ({$user->email})");
            return;
        }
        
        Employee::firstOrCreate(
            ['user_id' => $user->id],
            [
                'position_id' => $position->id,
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'site_id' => $site?->id,
                'slug_emp' => Str::slug($user->name . '-' . $empId),
                'emp_code' => $empId,
                'employee_number' => $this->generateEmployeeNumber(),
                'sss_number' => $this->generateSssNumber(),
                'pagibig_number' => $this->generatePagibigNumber(),
                'philhealth_number' => $this->generatePhilhealthNumber(),
                'contract_start_date' => now(),
                'contract_end_date' => now()->addYear(),
                'emergency_contact_number' => '09123456789',
                'pay_frequency' => $payFrequency,   // dynamic based on caller
                'employee_status' => 'active',
            ]
        );
    }

    /**
     * Display statistics (unchanged)
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
        
        // List users by role
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
        
        $this->command->info("\nSkipped Users (Admins only):");
        $admins = User::role('admin')->get();
        if ($admins->count() > 0) {
            foreach ($admins as $admin) {
                $this->command->info("  - {$admin->name} ({$admin->email}) - Admin (No employee record created)");
            }
        } else {
            $this->command->info("  No admin users found");
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

        // Create branch, position, and site
        $branch = $this->getBranch();
        $position = $this->getPosition(); // 'Regular employee'
        $site = $this->getSite($branch);

        $usersData = $this->getUsersData();
        $totalUsers = count($usersData);
        
        $this->command->info('Starting Employee Seeder...');
        $this->command->info("Total users to process: {$totalUsers}");
        $progressBar = $this->command->getOutput()->createProgressBar($totalUsers);
        $progressBar->start();

        $processedUsers = [];

        foreach ($usersData as $data) {
            $user = $this->getOrCreateUser($data, $roleMap);
            // Store the pay_frequency if present; default to 'weekender' for original users
            $payFreq = $data['pay_frequency'] ?? 'weekender';
            $processedUsers[] = [
                'user'           => $user,
                'emp_id'         => $data['emp_id'],
                'role'           => $data['role'],
                'pay_frequency'  => $payFreq,
            ];
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        
        // Create employee records (exclude only admin users)
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
                $position,
                $item['user'],
                $branch,
                $item['emp_id'],
                $site,
                $item['pay_frequency']   // pass the stored pay_frequency
            );
            if ($item['role'] !== 'admin') {
                $employeeProgressBar->advance();
            }
        }
        
        $employeeProgressBar->finish();
        
        $this->displayStatistics();
    }
}