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
    private function generateSlug(string $name, int $empCode): string
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