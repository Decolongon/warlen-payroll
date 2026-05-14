<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    private function getEmployeeData(): array
    {
        return [
            ['name' => 'John Louel', 'emp_code' => 751],
            ['name' => 'Rachelle Ann', 'emp_code' => 713],
            ['name' => 'Mark Taryao', 'emp_code' => 885],
            ['name' => 'Welbert Datoon', 'emp_code' => 6],
            ['name' => 'Jona Delicana', 'emp_code' => 115],
            ['name' => 'Mariny Delrosario', 'emp_code' => 646],
            ['name' => 'Shanine Natalio', 'emp_code' => 220],
            ['name' => 'Aileen Esplaguera', 'emp_code' => 1],
            ['name' => 'Julee Sagal', 'emp_code' => 450],
            ['name' => 'Recajean Desacula', 'emp_code' => 131],
            ['name' => 'Leonel Fermiza', 'emp_code' => 12],
            ['name' => 'Jerwin Nierves', 'emp_code' => 54],
            ['name' => 'Rosseth Formanes', 'emp_code' => 671],
            ['name' => 'Nica Gonzale', 'emp_code' => 214],
            ['name' => 'Jay L Asola', 'emp_code' => 659],
            ['name' => 'Karlynn Nuneza', 'emp_code' => 167],
            ['name' => 'Marne Almarza', 'emp_code' => 163],
            ['name' => 'Manilyn Estorco', 'emp_code' => 887],
            ['name' => 'Gilbert Saluta', 'emp_code' => 9],
            ['name' => 'Casipong Antonio', 'emp_code' => 273],
            ['name' => 'Christian Mariano', 'emp_code' => 882],
            ['name' => 'Benny Alladin', 'emp_code' => 66],
            ['name' => 'Primitiva Alladin', 'emp_code' => 71],
            ['name' => 'Marjorie Galario', 'emp_code' => 683],
            ['name' => 'Noli Rodriguez', 'emp_code' => 867],
            ['name' => 'Handell Ronquillo', 'emp_code' => 651],
            ['name' => 'Angela Canaya', 'emp_code' => 916],
            ['name' => 'Lorenz Monton', 'emp_code' => 746],
            ['name' => 'Russel John Tuya', 'emp_code' => 215],
            ['name' => 'Lulenoral Forpue', 'emp_code' => 909],
            ['name' => 'Leo Gester Canete', 'emp_code' => 915],
            ['name' => 'John Vincent Salinas', 'emp_code' => 890],
            ['name' => 'Ruel Herminanda', 'emp_code' => 18],
            ['name' => 'Ella Paglomutan', 'emp_code' => 734],
            ['name' => 'Errol Biona', 'emp_code' => 888],
            ['name' => 'John Christian Talaman', 'emp_code' => 728],
            ['name' => 'Demmy Cordova', 'emp_code' => 97],
            ['name' => 'Jason Cuberos', 'emp_code' => 914],
            ['name' => 'John Rhyan Cancian', 'emp_code' => 897],
            ['name' => 'Joseph Amant', 'emp_code' => 364],
            ['name' => 'Diosdado Gonzaga Ii', 'emp_code' => 206],
            ['name' => 'Billy John Barsanas', 'emp_code' => 222],
            ['name' => 'Jome Gruzo', 'emp_code' => 172],
            ['name' => 'Joel Sederio', 'emp_code' => 174],
            ['name' => 'Melody Sentoy', 'emp_code' => 736],
            ['name' => 'Jambie Reyes', 'emp_code' => 912],
            ['name' => 'Alisasis Elfired', 'emp_code' => 816],
            ['name' => 'Erickson Delogar', 'emp_code' => 111],
            ['name' => 'Brent Mispenas', 'emp_code' => 917],
            ['name' => 'Brey Bryan Dayday', 'emp_code' => 898],
            ['name' => 'Juan Cadungon', 'emp_code' => 799],
            ['name' => 'Helbert Almanoche', 'emp_code' => 695],
            ['name' => 'Rondel Edangga', 'emp_code' => 831],
            ['name' => 'Christian Mata', 'emp_code' => 880],
            ['name' => 'Rodney Parcon', 'emp_code' => 710],
            ['name' => 'Ryan Samuel Susada', 'emp_code' => 112],
            ['name' => 'Vito Macario', 'emp_code' => 588],
            ['name' => 'John Le Montano', 'emp_code' => 30],
            ['name' => 'Ryan Benguelo', 'emp_code' => 779],
            ['name' => 'Paul John Denaga', 'emp_code' => 668],
            ['name' => 'Jeric Deslate', 'emp_code' => 418],
            ['name' => 'Renz Espinosa', 'emp_code' => 47],
            ['name' => 'Ronel Tulio', 'emp_code' => 737],
            ['name' => 'Joseph John Laure', 'emp_code' => 884],
            ['name' => 'Junedy Galletto Iii', 'emp_code' => 46],
            ['name' => 'Sharmine Bayot', 'emp_code' => 910],
            ['name' => 'Hilary Fica', 'emp_code' => 942],
            ['name' => 'Bobby Labrador', 'emp_code' => 654],
            ['name' => 'Ma Jessa Balibalos', 'emp_code' => 953],
            ['name' => 'Arlan Igmedio', 'emp_code' => 625],
            ['name' => 'Elsie Aquino', 'emp_code' => 124],
            ['name' => 'Norman Lausa', 'emp_code' => 960],
            ['name' => 'Nonato Edison', 'emp_code' => 877],
            ['name' => 'Abellano Jes Martin', 'emp_code' => 878],
            ['name' => 'Gerald Genteroles', 'emp_code' => 626],
            ['name' => 'Crezelle Ann Villanueva', 'emp_code' => 961],
            ['name' => 'John Lovell Zerrudo', 'emp_code' => 678],
            ['name' => 'Andrew Huerva', 'emp_code' => 963],
            ['name' => 'Jerone Vista', 'emp_code' => 268],
            ['name' => 'Remy Quijano', 'emp_code' => 968],
            ['name' => 'Jame Kyle Penequito', 'emp_code' => 972],
            ['name' => 'De Ann Manocan', 'emp_code' => 973],
            ['name' => 'Wennah Obediente', 'emp_code' => 974],
            ['name' => 'Ryan Coseba', 'emp_code' => 299],
            ['name' => 'Glyn Tingson', 'emp_code' => 313],
            ['name' => 'Justine Constantino', 'emp_code' => 976],
            ['name' => 'Wilmar Villegas', 'emp_code' => 977],
            ['name' => 'Reden Alimante', 'emp_code' => 979],
            ['name' => 'Edward Zaspa', 'emp_code' => 978],
            ['name' => 'Jassen Padilla', 'emp_code' => 980],
            ['name' => 'Marah Zaragosa', 'emp_code' => 984],
            ['name' => 'Ace Andrian Barredo', 'emp_code' => 970],
            ['name' => 'Xhar Jhon Suarez', 'emp_code' => 971],
            ['name' => 'Timothy Montero', 'emp_code' => 883],
            ['name' => 'Romme Pahilanga', 'emp_code' => 845],
            ['name' => 'Bella Pornales', 'emp_code' => 992],
            ['name' => 'Kirb Gascon', 'emp_code' => 1017],
            ['name' => 'Johnel Doroin', 'emp_code' => 1019],
            ['name' => 'John Neil Bachoco', 'emp_code' => 1021],
            ['name' => 'Mamerto Villareal', 'emp_code' => 1025],
            ['name' => 'Adonis Laddaran', 'emp_code' => 1026],
            ['name' => 'Jernell Garcia', 'emp_code' => 600],
            ['name' => 'Kleo Sandy', 'emp_code' => 808],
            ['name' => 'Jason Lozada', 'emp_code' => 183],
            ['name' => 'Alixander Nono', 'emp_code' => 539],
            ['name' => 'Erica Hermosura', 'emp_code' => 735],
            ['name' => 'Brex Datoun', 'emp_code' => 168],
            ['name' => 'Christine Nobleza', 'emp_code' => 8],
            ['name' => 'Louie Jay Aguillon', 'emp_code' => 1042],
            ['name' => 'Argie Lagatiman', 'emp_code' => 1045],
            ['name' => 'Alejo Lampaso', 'emp_code' => 1044],
            ['name' => 'Kent Alison', 'emp_code' => 1057],
            ['name' => 'Eraiza Fejer', 'emp_code' => 1073],
            ['name' => 'Alpha Omega Olaybar', 'emp_code' => 1074],
            ['name' => 'Elijah Monton', 'emp_code' => 1093],
            ['name' => 'Laarne Lumogdang', 'emp_code' => 1094],
            ['name' => 'Crista Mae Liza', 'emp_code' => 1095],
            ['name' => 'Fuentebella John Earl', 'emp_code' => 1099],
            ['name' => 'Basas Joffrey', 'emp_code' => 581],
            ['name' => 'Melfred Paglinawan', 'emp_code' => 425],
            ['name' => 'James Duray', 'emp_code' => 1121],
            ['name' => 'Victorio Henden', 'emp_code' => 332],
            ['name' => 'Edmundo Laurencio', 'emp_code' => 1102],
            ['name' => 'Rey Cabungcag', 'emp_code' => 1039],
            ['name' => 'Danilo Gayoba', 'emp_code' => 1034],
            ['name' => 'Epipanio Balijado', 'emp_code' => 995],
            ['name' => 'Ralph Aribato', 'emp_code' => 1023],
            ['name' => 'Joe Pit Sazon', 'emp_code' => 1040],
            ['name' => 'Christian Rico', 'emp_code' => 786],
            ['name' => 'Mark Gomez', 'emp_code' => 1041],
            ['name' => 'Meg Julianne Villacasti', 'emp_code' => 1134],
            ['name' => 'Leonel Fermiza', 'emp_code' => 1143],
            ['name' => 'Irish Grace Ticar', 'emp_code' => 1148],
            ['name' => 'Cantor Rhynexelle', 'emp_code' => 1164],
            ['name' => 'Coja Hana Janelle', 'emp_code' => 1165],
            ['name' => 'Evan James Barbon', 'emp_code' => 1169],
            ['name' => 'Jimena Macandili', 'emp_code' => 1175],
            ['name' => 'Joseph Penafiel', 'emp_code' => 1178],
            ['name' => 'Jenelyn Viajedor', 'emp_code' => 1184],
            ['name' => 'Dominador Alcon Jr', 'emp_code' => 1213],
            ['name' => 'Cejie Altas', 'emp_code' => 1215],
            ['name' => 'Eleubert Merabe', 'emp_code' => 1214],
            ['name' => 'James Seva', 'emp_code' => 1209],
            ['name' => 'Marie Arendon', 'emp_code' => 1216],
            ['name' => 'Gerald Duazo', 'emp_code' => 1130],
            ['name' => 'Cindy Joy Tagle', 'emp_code' => 1222],
            ['name' => 'Anne Iwayan', 'emp_code' => 1218],
            ['name' => 'Mark Anthony Orencio', 'emp_code' => 891],
            ['name' => 'Louise Agnas', 'emp_code' => 1231],
            ['name' => 'Beatrice Deocampo', 'emp_code' => 1232],
            ['name' => 'Alister Arros', 'emp_code' => 1233],
            ['name' => 'Joshua Sonier', 'emp_code' => 1037],
            ['name' => 'John Michael Bulos', 'emp_code' => 1228],
            ['name' => 'Bryan Balboa', 'emp_code' => 1217],
            ['name' => 'Jethro Pechayco', 'emp_code' => 1219],
            ['name' => 'Glenn Gasapo', 'emp_code' => 1220],
            ['name' => 'Mary Joy Javoc', 'emp_code' => 1235],
            ['name' => 'Fritzie Grace Frayco', 'emp_code' => 1236],
            ['name' => 'Mary Joy Baloyo', 'emp_code' => 1237],
            ['name' => 'Francis Colmenares', 'emp_code' => 1258],
            ['name' => 'Dominic Lasponia', 'emp_code' => 1260],
            ['name' => 'Gerebelo Angco', 'emp_code' => 1275],
            ['name' => 'Ydryan Cabalinan', 'emp_code' => 1276],
            ['name' => 'Rio Jhoy Delos Santos', 'emp_code' => 1277],
            ['name' => 'Nichole Mationg', 'emp_code' => 1278],
            ['name' => 'Rayselle Caban', 'emp_code' => 1279],
            ['name' => 'John Godwin Mamigo', 'emp_code' => 1280],
            ['name' => 'Jasmin Genovia', 'emp_code' => 1287],
            ['name' => 'Angelo Torrecampo', 'emp_code' => 1298],
            ['name' => 'Brigedo Carasaquit', 'emp_code' => 893],
            ['name' => 'Marlo Balolot', 'emp_code' => 210],
            ['name' => 'Mark Mengullo', 'emp_code' => 642],
            ['name' => 'Agueda Santillan', 'emp_code' => 1348],
            ['name' => 'Sayron Bermeo', 'emp_code' => 1349],
            ['name' => 'Caselyn Banhao', 'emp_code' => 1352],
            ['name' => 'Junalyn Combestra', 'emp_code' => 1381],
            ['name' => 'Genara Ducasi', 'emp_code' => 1402],
            ['name' => 'Brian Jimena', 'emp_code' => 1432],
            ['name' => 'Rafael Perlado', 'emp_code' => 1431],
            ['name' => 'Jhon Ernie Serana', 'emp_code' => 962],
            ['name' => 'Mae Annechell Sustioso', 'emp_code' => 1441],
            ['name' => 'Claris Ann Dequilla', 'emp_code' => 1442],
            ['name' => 'Godfrey Corteza', 'emp_code' => 1444],
            ['name' => 'Ronimae Kate Vicente', 'emp_code' => 1447],
            ['name' => 'Gilbert Tacuyan', 'emp_code' => 1450],
            ['name' => 'Yvonne Lobaton', 'emp_code' => 1460],
            ['name' => 'Michael Gaban', 'emp_code' => 1457],
            ['name' => 'Ann Jelo Josh Agbay', 'emp_code' => 1458],
            ['name' => 'Kent Gerry Diapen', 'emp_code' => 1464],
            ['name' => 'Jericho Francis Baba', 'emp_code' => 1465],
            ['name' => 'Bien Pahilanga', 'emp_code' => 1470],
            ['name' => 'Klynt Salgado', 'emp_code' => 1471],
            ['name' => 'Crispher Penaroyo', 'emp_code' => 1482],
        ];
    }

    private function getAdminAndHRData(): array
    {
        return [
            ['name' => 'Warlito', 'email' => 'warlito@gmail.com', 'role' => 'admin', 'emp_code' => 1000],
            ['name' => 'Elena', 'email' => 'elena@gmail.com', 'role' => 'admin', 'emp_code' => 1001],
            ['name' => 'Jona', 'email' => 'jona@gmail.com', 'role' => 'hr_head', 'emp_code' => 2222],
            ['name' => 'Rica', 'email' => 'rica@gmail.com', 'role' => 'hr_head', 'emp_code' => 3333],
        ];
    }

    /**
     * Generate a unique employee number
     */
    private function generateEmployeeNumber(): string
    {
        do {
            $number = '9' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Employee::where('employee_number', $number)->exists());
        return $number;
    }

    /**
     * Generate slug for employee
     */
    private function generateSlug(string $name, int $empCode): string
    {
        return Str::slug($name . '-' . $empCode);
    }

    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $hrRole = Role::firstOrCreate(['name' => 'hr_head']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Create Admin and HR users first
        $this->command->info('Creating Admin and HR users...');
        $adminAndHR = $this->getAdminAndHRData();
        
        foreach ($adminAndHR as $data) {
            // Check if user already exists
            $user = User::where('email', $data['email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ]);
                
                // Assign role
                if ($data['role'] === 'admin') {
                    $user->assignRole($adminRole);
                } elseif ($data['role'] === 'hr_head') {
                    $user->assignRole($hrRole);
                }
                
                // Create employee record for admin/HR
                if (!Employee::where('emp_code', $data['emp_code'])->exists()) {
                    Employee::create([
                        'user_id' => $user->id,
                        'emp_code' => $data['emp_code'],
                        'employee_number' => $this->generateEmployeeNumber(),
                        'slug_emp' => $this->generateSlug($data['name'], $data['emp_code']),
                        'emergency_contact_number' => '09123456789',
                        'contract_start_date' => now(),
                        'pay_frequency' => 'weekender',
                        'employee_status' => 'active',
                    ]);
                }
                
                $this->command->info("Created {$data['role']}: {$data['name']}");
            } else {
                $this->command->warn("User already exists: {$data['email']}");
            }
        }
        
        // Create regular employees
        $employees = $this->getEmployeeData();
        $total = count($employees);
        
        $this->command->newLine();
        $this->command->info('Creating regular employees...');
        $this->command->info("Total employees to create: {$total}");
        
        $progressBar = $this->command->getOutput()->createProgressBar($total);
        $progressBar->start();
        
        $created = 0;
        $skipped = 0;
        
        foreach ($employees as $emp) {
            // Check if employee with this emp_code already exists
            if (Employee::where('emp_code', $emp['emp_code'])->exists()) {
                $skipped++;
                $progressBar->advance();
                continue;
            }
            
            // Create email from name
            $emailBase = strtolower(str_replace([' ', ',', '.', "'"], ['.', '', '', ''], $emp['name']));
            $email = $emailBase . '@example.com';
            
            // Check if user already exists (to avoid duplicates)
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $emp['name'],
                    'email' => $email,
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ]);
            }
            
            // Assign employee role
            if (!$user->hasRole('employee')) {
                $user->assignRole($employeeRole);
            }
            
            // Create employee record
            Employee::create([
                'user_id' => $user->id,
                'emp_code' => $emp['emp_code'],
                'employee_number' => $this->generateEmployeeNumber(),
                'slug_emp' => $this->generateSlug($emp['name'], $emp['emp_code']),
                'emergency_contact_number' => '09123456789',
                'contract_start_date' => now(),
                'pay_frequency' => 'weekender',
                'employee_status' => 'active',
            ]);
            
            $created++;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info('========================================');
        $this->command->info('Employee Seeder completed successfully!');
        $this->command->info('========================================');
        $this->command->info("Admin users created: " . User::role('admin')->count());
        $this->command->info("HR users created: " . User::role('hr_head')->count());
        $this->command->info("Employee users created: " . User::role('employee')->count());
        $this->command->info("Regular employees created: {$created}");
        $this->command->info("Regular employees skipped (duplicate emp_code): {$skipped}");
        $this->command->info("Total employees in database: " . Employee::count());
        $this->command->info('========================================');
    }
}