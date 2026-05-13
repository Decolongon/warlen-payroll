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
     */
    private function parseEmployeeData(): array
    {
        // Full raw data as heredoc – copy exactly from your spreadsheet
        $rawData = <<<RAW
#	START DATE	DURATION	ID Number	NAME	MIDDLE NAME	STATUS	PROJECT SITE	SEX	POSITION	PERMANENT ADDRESS	PRESENT ADDRESS	NUMBER	DATE OF BIRTH	AGE	EMAIL ADDRESS	CONTACT PERSON	CONTACT NUMBER	ADDRESS OF CONTACT PERSON	MOTHERS NAME	FATHERS NAME	PLANTILLA	ASSIGNMENT	SSS#	PHILHEALTH	PAG-IBIG	TIN	DATE END	EDUCATIONAL ATTAINTMENT	CERTIFICATES	SKILLS
1	03/13/2014	12 Years, 2 Months, 0 Days	133	Masgong, Jason	Fernando	Active	Fabrication-Bacolod	M	Welder	Villa Esperanza Tangub Bacolod City	Villa Esperanza Tangub Bacolod City	9109881355	10/09/1988	37 Years, 7 Months, 4 Days					Elna Masgong	Crispolo Masgong	Bacolod	LBC IloIlo		11-050663051-7	9140-6903-1211			TESDA RTC Shield Metal Arc Welding			
2	12/25/2015	10 Years, 4 Months, 18 Days	18	Ruel Herminanda	Infane	Active	Megaworld Belmont-Iloilo	M	Fabrication	Prk. Crossing 8 Brgy. Tangub Bacolod CIty	Prk. Crossing 8 Brgy. Tangub Bacolod CIty		01/31/1985	41 Years, 3 Months, 12 Days		Richard Herminanda	9494889410	Prk Crosing 8 Tangub Bacolod City	Mercedita Herminanda	Romeo Herminanda	Bacolod	Bacolod	00-07-2328659-6	11-050539504-2		947-495-323		Computer Technicin IT (Undergraduate)			
3	5/6/2019	7 Years, 0 Months, 7 Days	135	Matta, Michael Andrew	Adrao	Active	Phil Arforce-Cebu	M	Electrician	Brgy. Tiza Roxas City Capiz	Brgy. Tiza Roxas City Capiz	0946-629-5455	2/23/1996	30 Years, 2 Months, 20 Days					Maria Matta	Ireneo Matta	Bacolod	Crimson		11-025617573-8	1212-6087-0833						
4	1/21/2023	3 Years, 3 Months, 22 Days	263	Villamia Juluwie	Gumagda	Active	Ridge 5-Tagaytay	M	Plumber/Electrician	Prk Mahidaiton Brgy 39 Bacolod City Negros Occ	Prk Mahidaiton Brgy 39 Bacolod City Negros Occ	9271443102	5/17/1984	41 Years, 11 Months, 26 Days					Lydia Gumagda	Winy Villarmia	Pasudeco	Ridge 5	07-3636422-7	11-025490790-1	1212-9207-2218			High School Graduate	NC II Plumbing	Welding,Wiring	
5	8/13/2022	3 Years, 9 Months, 0 Days	210	Balolot Marlo	Arevalo	Active	Megaworld Belmont-Iloilo	M	Welder	Villa Barbas II Cadiz City Negros Occ	Villa Barbas II Cadiz City Negros Occ	9071209928	8/13/1981	44 Years, 9 Months, 0 Days					Elma Balolot	Marcelino Balolot	Bacolod	Belmont	07-3460885-9	11-025507565-9	1211-5488-0091	475-385-777		Undergraduate of BS Criminology	NC I	Welding	
6	8/12/2022	3 Years, 9 Months, 1 Days	206	Gonzaga Diosdado		Active	VYKIM-Bacolod	M	Welder	Prk Totong 1 Brgy Felisa Bacolod City	Prk Totong 1 Brgy Felisa Bacolod City	9383767130	8/19/1998	27 Years, 8 Months, 24 Days	19jeegonzaga@gmail.com				Nosomie Gonzaga	Diosdado Gonzaga	Bacolod	Smile Res	07-4085368-5	1.12515E+11	1213-0872-4292			Senior High School Graduate	NC II	Welding	
7	10/21/2022	3 Years, 6 Months, 22 Days	21	Victorio Henden	Diaz	Active	Fabrication-Bacolod	M	Warehouseman	Brgy Efigenio Lizares Talisay City Neg Occ	Brgy Efigenio Lizares Talisay City Neg Occ	9637593630	4/6/1985	41 Years, 1 Months, 7 Days		Aiza Esquillo-0963 759 3630			Sonia Victorio	Holden Victorio	Bacolod	Upper East	33-8369030-9	01-050472330-0	1210-2900-1213			High School Graduate			
8	11/17/2022	3 Years, 5 Months, 26 Days	54	Nierves Jerwin	Granzo	Active	Fabrication-Bacolod	M	Electrical Engr	Block 18 Lot 1 NGO Villlage Brgy Handumanan	Block 18 Lot 1 NGO Villlage Brgy Handumanan	9533648930	4/18/1997	29 Years, 0 Months, 25 Days		Mercy Nierves-09677769628		Prk 2 Brgy Culipapa Hinaboan Negros Occidental	Mercy Granzo Nierves	Norberto Nierves	Bacolod	Bacolod	35-1677830-2	11-025776322-6	1212-9470-2064	718-276-439		Graduate of Bachelor of Science Electrical Engineer			
9	11/17/2022	3 Years, 5 Months, 26 Days	53	Amante Joseph	Gomez	Active	Fabrication-Bacolod	M	Fabricator/Welder	Totong 1 Brgy Felisa Bcolod City	Totong 1 Brgy Felisa Bcolod City	9701992839	9/16/1981	44 Years, 7 Months, 27 Days					Teresita Gomez	Roberto Amante	Bacolod	Bacolod	07-1799414-0	11-050263746-0	1590-0066-1703			High School Graduate			
10	1/20/2023	3 Years, 3 Months, 23 Days		Francis Aranguin	Aranguin	Active	UPHD-Las Piñas	M	Electrician	Prk. Masinadyahon Brgy. Atipuluan Bago City	Prk. Masinadyahon Brgy. Atipuluan Bago City	0950-293-3456	3/17/1997	29 Years, 1 Months, 26 Days							Bacolod	Uphd	07-3516810-7	11-050740237-2	1211-6705-5499			Tesda Graduate(Electrcical)	NC II		
11	1/26/2023	3 Years, 3 Months, 17 Days	172	Gruzo Jome	Salomon	Active	VYKIM-Bacolod	M	Fabricator/Welder	Prk Malapitan Brgy Alijis Bacolod City	Prk Malapitan Brgy Alijis Bacolod City	9121404263	7/22/1996	29 Years, 9 Months, 21 Days	jhayem31@yahoo.com				Marivic P. Salomon	Ireneo V. Gruzo	Bacolod	Smile Res	34-8992870-0	1102-5751079-4	1212-7283-3641			Tesda Graduate of Shielded Metal Arch Welding	NCII	Welding	
12	2/1/2023	3 Years, 3 Months, 12 Days	227	Nono Alixander	Naria	Active	Megaworld Belmont-Iloilo	M	Fabricator/Welder	Brgy Bolho Miagao Iloilo City	Brgy Bolho Miagao Iloilo City	9636639990	11/7/1991	34 Years, 6 Months, 6 Days		Anie Nono-0963639990			Azucena Nono	Wilfredo Nono	Bacolod	Belmont	07-4165382-6	11-202217750-0	1213-1924717-5			High School Graduate			
13	2/3/2023	3 Years, 3 Months, 10 Days	616	Melrose Jazzlyn Sicat	Figueroa	AWOL	Megaworld Pasudeco-Pampanga 	F	Project Civil Engineer	San Fernando Pampanga	Brgy. Del Rosario San Fernando Pampanga	9271330235	5/11/1998	28 Years, 0 Months, 2 Days	engrmjfs@gmail.com				Jennifer Figueroa	Rodolfo Sicat Jr,	Bacolod	Pampanga		07-251061952-9	1213-1179-0556			Bachelor of Science in Civil Engineering			
14	6/21/2023	2 Years, 10 Months, 22 Days	659	Jay Louie Lasola		Active	Megaworld Upper East-Bacolod	M	Safety Officer	Golf Area Vmc Victorias City Negros Occidental	Golf Area Vmc Victorias City Negros Occidental	09687713005/092840444651	5/23/1985	40 Years, 11 Months, 20 Days	Jaylouielasola@gmail.com						Bacolod	Upper East	07-3144408-7	11-050287480-2	1210-7121-2635			Colllege Undergrduate	BOSH,FIRST AID TRAINING,LCMC.		
15	6/26/2023	2 Years, 10 Months, 17 Days	668	Paul John Denaga	Idiosolo	Resigned	Fabrication-Bacolod	M	Fabricator/Welder	Prk Kasilingan, Tangub Bacolod City	Prk Kasilingan, Tangub Bacolod City	9060579512	7/13/1996	29 Years, 10 Months, 0 Days	pjdenaga28@gmail.com	John Denaga-09260458441			Mary Grace Denaga	John Denaga	Bacolod	Smile Res	07-3466951-3	11-253381644-7	1211-6207-7457			Graduate of Vocational School	NC II	Welding	
16	7/5/2023	2 Years, 10 Months, 8 Days	670	Ana Delia Pauline  Felicisimo	Prudente	Resigned	Wilcon-Pila Laguna	F	Electrical Engineer	Brgy. Nanungan Proper Hinigaran, Negros Occidental	Brgy. Nanungan Proper Hinigaran, Negros Occidental	9205407568/9810535733	08/07/1999	26 Years, 9 Months, 6 Days	anadeliapauline07@gmail.com				Arcelita Prudente	Paulino Felicisimo	Bacolod	Wilcon Quirino	07-37-15379-6	11-025806391-0	1213- 2063-4233	628-910-326-00000		Bachelor of Science in Elecrical Engineering			
17	11/6/2023	2 Years, 6 Months, 7 Days	735	Hermosura Erica Marie	Jimenez	Active	Megaworld Belmont-Iloilo	F	Document Controller	#11 Bonifacio Street Prk Mainuswagon,Brgy II Silay City Negros Occidental	#11 Bonifacio Street Prk Mainuswagon,Brgy II Silay City Negros Occidental	9630077184	4/6/2000	26 Years, 1 Months, 7 Days	ericamarieheremusora@gmail.com				Ma.Lourdes Hermosura	Edgardo Hermosura	Bacolod	Belmont	07-4048000-3	01-251652848-4	1213-3274-9210			Graduate of BS. In Electro-Mechanical			
18	11/6/2023	2 Years, 6 Months, 7 Days	736	Sentoy Melody	Arsaga	Active	Wilcon-Pila Laguna	F	Document Controller	kenon Street,Brgy 9,Isabela Negros Occidental	kenon Street,Brgy 9,Isabela Negros Occidental	9949886058	5/6/2000	26 Years, 0 Months, 7 Days	Sentoy.melody.a@gmail.com				Mary Rose Sentoy	Lemuel Sentoy	Bacolod	Wilcon Quirino	07-4002198-3	11-025837610-2	1213-3276-0569			Graduate of BS. In Electronics			
19	12/20/2023	2 Years, 4 Months, 23 Days	217	Ryan Lopez	Tingson	AWOL	Megaworld Belmont-Iloilo	M	Mechanical Engineer	Talisay City, Negros Occidental	Talisay City, Negros Occidental	9613235661	4/4/2000	26 Years, 1 Months, 9 Days	lopez444456@gmal.com	Randy Lopez	9993522707		Lorelyn Lopez	Randy Lopez	Bacolod	Upper East	07-4160265-7	08-251097010-8				Bachelor of Science in Mechanical Engineering			
20	3/20/2024	2 Years, 1 Months, 23 Days	845	Pahilanga Rommel John	Diabor	Active	Fabrication-Bacolod	M	Auto Cad Operator	Sitio Pulo Barangay.Bubog Talisay City Negros Occidental	Sitio Pulo Barangay.Bubog Talisay City Negros Occidental	9296693233	11/9/1990	35 Years, 6 Months, 4 Days	Gersan31elad@gmail.com				Ermilo Pahilanga	Leilang Diabor	Bacolod	Smile Res	07-2983639-3	11-050665191-3	9141-7902-5485			Graduate of BS.Major In Architechtural Drafting			
21	4/1/2024	2 Years, 1 Months, 12 Days	868	Gersan Dale Memis		Resigned	Phil Arforce-Cebu	M	Civil Engineer	Jesusa 1,Alegria,Murcia,Negros Occidental	Jesusa 1,Alegria,Murcia,Negros Occidental	9959367223	4/20/1999	27 Years, 0 Months, 23 Days					Susana Solis	Rogelio Memis	Bacolod	Crimson	07-4081465-3	11-025808746-1	1213-0771-9962			Graduate of Bachelor of Civil Engineer	Civil Engineer Passer 2023		
22	4/11/2024	2 Years, 1 Months, 2 Days	875	Richard Seroco	Mandawe	Resigned	Megaworld Upper East-Bacolod	M	Auto Cad Operator	Prk. Datiles, Ph. 4, Brgy. Handumanan, Bacolod City	Prk. Datiles, Ph. 4, Brgy. Handumanan, Bacolod City	9464920816	7/17/1999	26 Years, 9 Months, 26 Days	richardseroco@gmail.com				Gloria Seroco	Jesus Seroco	Bacolod	Upper East BCD	07-4414070-5	11-251634280-6		637-783-251		Graduate of BS Industrial Technology - Architectural Drafting		Drafting	
23	9/10/2024	1 Years, 8 Months, 3 Days	972	James Kyle Penequito	Tizo	Active	Fabrication-Bacolod	M	Mechanical Engineer	Prk. Kawayanan Brgy. Taloc Bago City	Prk. Kawayanan Brgy. Taloc Bago City	9193649151	12/20/1999	26 Years, 4 Months, 23 Days	riahinkyle@gmail.com				Cecille Tiso	Jimmy Penequito	Bacolod	Bacolod	07-3960489-0	11-251725784-5	1213-5062-2575	765-604-916		Bachelor of Science in Mechanical Engineering			
24	1/9/2025	1 Years, 4 Months, 4 Days	1096	Maga Wyne	Cabarles	Active	VYKIM-Bacolod	M	Safety Officer	Zone 11 Talisay Negros Occidental	Zone 11 Talisay Negros Occidental	9955724331	4/1/2000	26 Years, 1 Months, 12 Days	magawyne@gmail.com				Maria Cecil Maga	Willy Maga	Bacolod	Smile Res	07-427523-9	11-025899721-2	1213-3410-9270	665-722-134		Graduate of Bachelor of Science Major in Mechanical Engineer			
25	5/30/2025	0 Years, 11 Months, 13 Days	1207	Gonzaga Marvin	Sagrado	Active	Megaworld Belmont-Iloilo	M	Project In Charge	Barangay Bata,Bacolod City	Barangay Bata,Bacolod City	9185520573	11/15/2000	25 Years, 5 Months, 28 Days	gonzaga.marvinsagrado38215@gmail.com				Arcili Sagrado	Gargar Gonzaga	Bacolod	Smile Res	35-0029128-0	11-025837051-1				Graduate of BS.Major In Mechanical Engineer	PRC License	DOST-SEI Scholar Graduate,Problem Solving,Basic Autocad,Sketch up	
26	6/1/2025	0 Years, 11 Months, 12 Days	1217	Bryan Balboa	Boglosa	Active	Fabrication-Bacolod	M	Fabrication	Santiago Barotac Viejo Iloilo	Tapulanga, Taculing Bacolod CIty	9663948486	10/13/1994	31 Years, 7 Months, 0 Days	bryanbalbao@gmaill.com	Marycris Balboa	9271390639	Tapulanga Taculing Bacolod City	Letecia Boglosa	Violeta Balboa	Bacolod	Bacolod	07-3570076-9	11-050752427-3	1211-7890-0969	330-573-083-0000		Bachelor of Science in Elecrical Engineering (Undergraduate)	NCII		
27	6/27/2025	0 Years, 10 Months, 16 Days	1310	Vidal Geraldine		Resigned	Fabrication-Bacolod	F	Document Controller	Purok Binuldusan Brgy Isidro	Purok Binuldusan Brgy Isidro	9617146374	7/2/2002	23 Years, 10 Months, 11 Days	vidalgeraldine15@gmail.com					Ray Ocampo Escanan	Bacolod	Smile Res	07-4308430-9	11-203880375-4	1213-5523-7099			Graduate of BS.Major In Manufacturing Engineering			
28	6/16/2025	0 Years, 10 Months, 27 Days	1282	Genaca Victor Nino	Peneiro	Active	Megaworld Belmont-Iloilo	M	Junior Mechanical Engineer 1	Providence Balabag Pavia Iloilo Philippines	Providence Balabag Pavia Iloilo Philippines	9774948976	10/19/2001	24 Years, 6 Months, 24 Days	vicninogenaca@gmail.com				Rosalie Peniero	Victor Genaca	Bacolod	Belmont				677-885-865		Graduate of BS.Major In Mechanical Engineer	PRC License		
29	6/24/2025	0 Years, 10 Months, 19 Days	1232	Deocampo Beatrice	Arroyo	Resigned	Megaworld Belmont-Iloilo	F	Document Controller 1	Blk 13 Lot 19,Daffodil St. Easthome 3 Barangay Estefania	Blk 13 Lot 19,Daffodil St. Easthome 3 Barangay Estefania	9085482593	8/24/2000	25 Years, 8 Months, 19 Days	Beadeocampo2400@gmail.com				Cherryl Arroyo	Jeffery Deocampo	Bacolod	Belmont	07-4569636-8	11-025820293-7	1213-6709-6168	678-632-898		Graduate of BS.Civil Engineer	Basic Autocad	Authocad,Construction Management,Civil Engeering Estimates	
30	6/26/2025	0 Years, 10 Months, 17 Days	1233	Arroz Alister	Moralidad	Resigned	Crimson Hotel-Cebu	M	Project In Charge	Tubod Street Inayuan Cauyan	Tubod Street Inayuan Cauyan	9859750163	9/24/2000	25 Years, 7 Months, 19 Days	arrozalister@gmail.com				Thelma Arroz	Abner Arroz	Bacolod	Crimson	07-4408413-3	11-254061-5104	1213-6707-5742			Graduate of BS.Major In Electrical Engineer	PRC License		
31	7/1/2025	0 Years, 10 Months, 12 Days	1294	Failaman Lory	Nonesco	Active	Megaworld Belmont-Iloilo	M	Safety Officer	Barangay Oñate De Leon St.Mandurriao Iloilo City	Barangay Oñate De Leon St.Mandurriao Iloilo City	9073379570	2/21/1982	44 Years, 2 Months, 22 Days							Belmont	Belmont		01-025411784-1		336-199-825		Graduate of BS Major Elecronics			
32	7/8/2025	0 Years, 10 Months, 5 Days	268	Jayrone Vista	Alipato	Active	Megaworld Belmont-Iloilo	M	Welding	Prk. Mabinuligon Brgy. Handumanan Bacolod CIty	Prk. Mabinuligon Brgy. Handumanan Bacolod CIty	9612361986	02/21/1995	31 Years, 2 Months, 22 Days					Eleonr Vista	Sovany Vista	Belmont	Belmont	35-1994125-9	11-025828175-6	9221-3055-7086			Elementary Graduate			
33	7/9/2025	0 Years, 10 Months, 4 Days	1264	Efsiaca Irish Sandra	Bonifacio	Active	Ridge 5-Tagaytay	M	Document Controller 1	Buenivista Subd. Barangay Guinhalaran Silay City	Buenivista Subd. Barangay Guinhalaran Silay City			126 Years, 4 Months, 13 Days							Rideg 5	Rideg 5						Graduate of BS Industrial Engineering			
34	6/17/2025	0 Years, 10 Months, 26 Days	1281	Amarila Felo Andrey	Blacquio	Active	Megaworld Belmont-Iloilo	M	Autocad Operator	Pontevedra Negros Occidental	Pontevedra Negros Occidental	9932996385	12/17/2002	23 Years, 4 Months, 26 Days	amarilafeloandreyb@gmail.com				Armelita Amarila	Reyn Amarila	Belmont	Belmont	07-4452134-0	11-025949230-0		678-664-843					
35	6/27/2025	0 Years, 10 Months, 16 Days	1272	Salamat Lhenie Jane	Landicho	Resigned	Ridge 5-Tagaytay	M	Safety Officer/Auotcad Operator	188 Virata Street Pajo Alfonso Cavite	188 Virata Street Pajo Alfonso Cavite	09064330103/046-4194120	10/17/2001	24 Years, 6 Months, 26 Days	salamat.lheniejane@gmail.com				Juliana Salamat	Isaac Salamat	Ridge 5	Ridge 5						Graduate of BS.Civil Engineer			
36	6/4/2025	0 Years, 11 Months, 9 Days	1093	Monton Elijah	Jagolina	AWOL	Megaworld Upper East-Bacolod	M	Mechanical Engineer	Barangay Cabug	Barangay Cabug	9914463446	5/3/2000	26 Years, 0 Months, 10 Days	elijahmaymonton@gmail.com				Elgan Monton	Gemma Monton	Upper East	Bacolod	07-4477869-8	11-0258172736	1213-5605-2386			Graduate of Bachelor of Civil Engineer			
37	7/23/2025	0 Years, 9 Months, 20 Days	1284	Geli Richard	Cabal	AWOL	Ridge 5-Tagaytay	M	Project In Charge	Bonifacio Surigao City	Bonifacio Surigao City	09300240782/0991944933	12/7/2000	25 Years, 5 Months, 6 Days	richardgeli28@gmail.com						Ridge 5	Ridge 5						Graduate of BS.Major In Electrical Engineer			
38	7/28/2025	0 Years, 9 Months, 15 Days	1279	Caban Rayselle	Pechera	AWOL	Sol Y Viento-Pansol	M	Project In Charge	Block 17 Lot 7 Buenavista Sub.Phase 2 Barangay Guinhalaran Silay City	Block 17 Lot 7 Buenavista Sub.Phase 2 Barangay Guinhalaran Silay City	09218716357/09153207653	1/16/2002	24 Years, 3 Months, 27 Days	Raysellecaban01@gmail.com					Julie Caban	Ridge 5	Ridge 5	35-0446075-8	11-251608755-5	1212-9940-5156	609-139-547		Graduate of BS.Major In Mechanical Engineer			
39	9/12/2025	0 Years, 8 Months, 1 Days	1321	Alinsonorin, Nelson Jr	Tabano	Active	Megaworld Belmont-Iloilo	M	QAQC	Bangga Totong, Brgy. Felisa, Bacolod City, Negros Occidental	Bangga Totong, Brgy. Felisa, Bacolod City, Negros Occidental	9614681719	5/26/2000	25 Years, 11 Months, 17 Days	nelsonalinsonorin83@gmail.com	Maria Tabano Naranja	9077045295	Bangga Totong, Brgy. Felisa, Bacolod City, Negros Occidental	Ester Alinsonorin	Deceased 	Bacolod	Belmont	34-8602660-5	11-254059806-4	1212-5462-3173	474-342-702-000		Graduate of Bachelor of Science in cilvil Engineering			
40	10/8/2025	0 Years, 7 Months, 5 Days	1309	James Bryan Placido	Villarin	Active	UPHD-Las Piñas	M	Electrician			9850780827	7/9/2002	23 Years, 10 Months, 4 Days	jamesbryanplacido399@gmail.com				Vevian Placido	Jay Placido	Bacolod	UPHD	07-4630553-5	11-025959616-5	1213-7172-2275			Graduate of BS Industrial Technology Electrical Technology		Electrician	
41	10/20/2025	0 Years, 6 Months, 23 Days	1365	Legados Isaachar John	Lawan	Active	Crimson Hotel-Cebu	M	Safety Officer	New Sangi Road Barangay Pajo Lapu Lapu City	New Sangi Road Barangay Pajo Lapu Lapu City	9705868315	3/1/2002	24 Years, 2 Months, 12 Days	ijohnlegados@gmail.com				Glesy Legados	Rolando Laegados	Crimson	Crimson						Graduate of Bachelor of Science Electrical Engineer			
42	1/6/2026	0 Years, 4 Months, 7 Days	1370	Samaniego Marlou	Vargas	Active	Megaworld Upper East-Bacolod	M	Auto Cad Operator	Pescadores st,Pobalicion Bago City Negros Occindental	Pescadores st,Pobalicion Bago City Negros Occindental	9056659546	9/24/1990	35 Years, 7 Months, 19 Days	louram101@gmail.com	Reinalen Samaniego		Pescadores st,Pobalicion Bago City Negros Occindental	Dinah Samaniego	Ignacio Samaniego	Bacolod	Upper East	07-2739570-0	11-251751261-6	1211-9171-8282			Graduate of BS.Major In Architechtural Drafting			
43	1/9/2026	0 Years, 4 Months, 4 Days	1379	Jandi, John Dave	Bustamante	Active	Crimson Hotel-Cebu	M	Electrician 1	Prk. Malipayon Ilawod, Brgy. Napoles Bago City	Prk. Malipayon Ilawod, Brgy. Napoles Bago City	9553203604	8/30/2003	22 Years, 8 Months, 13 Days	johndavejandi@gmail.com				Jonalie Bustamante 	Juvani Jandi	Bacolod	Crimson, Cebu				692-389-941-00000		Santa Rosa Manpower Training Center			
44	1/9/2026	0 Years, 4 Months, 4 Days	1380	Hans Philip Higtenta	Florendo	Active	Phil Arforce-Cebu	M	Electrician 1	Purok Highschool Barangay Taloc Bago City 	Purok Highschool Barangay Taloc Bago City 	9668911334	3/4/2001	25 Years, 2 Months, 9 Days	hanshigtenta2001@gmail.com				Lynne Higtenta	Ruban Higtenta	Bacolod	Bacolod	07-4622773-6	11-251703477-3	1213-7361-3225			Bachelor of Science - Major in Industrial Technology			
45	1/19/2026	0 Years, 3 Months, 24 Days	1384	Go, Steven	Santillan	Active	Megaworld Upper East-Bacolod	M	Project  In Charge	Lumina Vista Alegre, BAcolod City, Negros Occidental	Lumina Vista Alegre, BAcolod City, Negros Occidental	9942852095	11/24/2000	25 Years, 5 Months, 19 Days	firstgosteven@gmail.com 	Fernamel V. Go		Lumina Vista Alegre, BAcolod City, Negros Occidental	Lanie S. Go	Fernamel V. Go	Bacolod	Upper East	07-4316373-8	11-254819249-0	1213-3895-5318	636-613-982-00000		Graduate of BS in Mechanical Engineer	BOSH, COSH		
46	1/24/2026	0 Years, 3 Months, 19 Days	1386	Esteban, Jonathan	Mabayan	Active	UPHD-Las Piñas	M	CAD Operator	Brgy. Canroma, Pontevedra, Negros Occidental	Brgy. Canroma, Pontevedra, Negros Occidental	9938864717	06/01/2002	23 Years, 11 Months, 12 Days	estebanjonathanmabayan@gmail.com	Emiliano Esteban Sr.		Brgy. Canroma, Pontevedra, Negros Occidental	Bethel M. Esteban	Emiliano Esteban Sr.	Bacolod	UPHD	07-4045202-8	11-025967646-0	1213-7765-5190	692-840-111-00000		Graduate of BS in Mechanical Engineer			
47	1/24/2026	0 Years, 3 Months, 19 Days	1404	Singabol, Mike 	Mirano	Active	Megaworld Belmont-Iloilo	M	Warehouseman	Bulobito-on, Brgy. Miranda, Hinigaran Negros Occidental	Bulobito-on, Brgy. Miranda, Hinigaran Negros Occidental	9952348535	9/10/1986	39 Years, 8 Months, 3 Days	mrmsingabol@gmail.com	Gerlyn Luis	9212985358	Bulobito-on, Brgy. Miranda, Hinigaran Negros Occidental	Deceased 	Deceased 	Bacolod	Belmont	3427701329	03-050921375-1	1212-0393-3349			Gradute of Bachelor of Science in Marine Transportation			
48	1/26/2026	0 Years, 3 Months, 17 Days	1409	Sumagaysay, Bryan	Rivera	Active	Crimson Hotel-Cebu	M	Electrical Engineer	Purok 4, Brgy. Orong, Kabankalan City, Negros Occidental	Purok 4, Brgy. Orong, Kabankalan City, Negros Occidental	9461531663	4/18/2002	24 Years, 0 Months, 25 Days	bryansumagaysay0418@gmail.com	Aldin Sumagaysay	9756692535	Purok 4, Brgy. Orong, Kabankalan City, Negros Occidental	Adela R. Sumagaysay	Alex Sumagaysay	Bacolod	Crimsom, Cebu	07-4302530-0	11-025878725-0	1213-7760-0936			Graduate of BS.Major In Electrical Engineer	NCII Electrical Installation		
49	1/16/2026	0 Years, 3 Months, 27 Days	1387	Canque, Joanna Grace	Linghon	Active	Megaworld Belmont-Iloilo	F	Document Controller	Brgy. Pagdugue, Dumangas, Iloilo City	Brgy. Pagdugue, Dumangas, Iloilo City	9667115966	06/24/2001	24 Years, 10 Months, 19 Days	joannacanque24@gmail.com						Iloilo	Belmont						Graduate of BS in Mechanical Engineering	Registered Mechanical Engineer		
50	2/13/2026	0 Years, 3 Months, 0 Days	1444	Corteza, Godfrey Jr	Perez	Active	Maskara Colesium-Bacolod	M	Helper	Brgy. Hawaiian, Silay City, Negros Occidental	Brgy. Hawaiian, Silay City, Negros Occidental	9936225241	09/20/1992	33 Years, 7 Months, 23 Days		Nemia Corteza	9122595069	Brgy. Hawaiian, Silay City, Negros Occidental	Nemia Corteza	Godfrey Corteza Sr.	Bacolod	BMC		11-025423492-3	1211-1000-1584			Vocational TESDA graduate -	SMAW NCI		
51	2/18/2026	0 Years, 2 Months, 25 Days	1446	Mabanes, Marlon	Yvañez	Active	Maskara Colesium-Bacolod	M	Helper	Teacher's Ville Lot 7 Block 15, Silay City, Negros Occidental	Teacher's Ville Lot 7 Block 15, Silay City, Negros Occidental	9519654885	02/28/1984	42 Years, 2 Months, 15 Days	mharlmabanes28@gmail.com	Liza Mabanes	9508734798	Teacher's Ville Lot 7 Block 15, Silay City, Negros Occidental	Rosita MAbanes	Henry Mabanes	Bacolod	BMC	34-0690919-7	11-050592215-8	1210-8167-1279	461-178-356		High School Graduate			
52	2/21/2026	0 Years, 2 Months, 22 Days	1447	Vicente, Ronimae Kate	Hisona	Active	Maskara Colesium-Bacolod	F	Document Controller	Brgy. Pilar, Coraville Subdivision, Hinigaran, Negros Occidental	Brgy. Pilar, Coraville Subdivision, Hinigaran, Negros Occidental	9556914800	01/05/2001	25 Years, 4 Months, 8 Days	katevicente4@gmail.com	Ronilo Vicente 	9095935253	Brgy. Pilar, Coraville Subdivision, Hinigaran, Negros Occidental	Mary Ann Vicente 	Ronilo Vicente	Bacolod	BMC	07-4393347-6	01-251054220-5	1.21357E+11	663845241		BS in Civil Engineering	COSH		
53	2/24/2026	0 Years, 2 Months, 19 Days	1449	Atin, Isaiah	Kiamco	Active	Phil Army -Bukidnon	M	Civil Engineer	Prk-5 South Poblacion, Maramag, Bukidnon	Prk-5 South Poblacion, Maramag, Bukidnon	0956702801/09309389139	07/18/1985	40 Years, 9 Months, 25 Days	0718isaiah@gmail.com						Bukidnon	Bukidnon	08-1403342-0	15-050291858-5		494-418-297-000		BS in Civil Engineering			
54	2/24/2026	0 Years, 2 Months, 19 Days	1448	Casumpong, Darwen	Morera	Active	Phil Army -Bukidnon	M	Safety Officer 1	Prk. 6 Brgy. Banag Banag Montivesta Davao De Oro 	Prk. 6 Brgy. Banag Banag Montivesta Davao De Oro 	9277017039	10/21/1989	36 Years, 6 Months, 22 Days	dawecasumpang@gmail.com				Dolores Morera	Wilfredo Casumpang	Bukidnon	Bukidnon	34-34937403	01-05190311-1	1211-1207-6365	453-899-446-009		Bachelor of Science in Marine Engineering			
55	3/18/2026	0 Years, 1 Months, 25 Days	1454	Montemayor, Luigie 	Barsanas	Active	Megaworld Upper East-Bacolod	M	Helper	Central manapla, Brgy. Purisima Zone 3, Negros Occidental	Central manapla, Brgy. Purisima Zone 3, Negros Occidental	9941848039	07/16/2005	20 Years, 9 Months, 27 Days	montemayorluigie4@gmail.com				Melana Montemayor	Rogie Montemayor	Bacolod	Bacolod									
56	3/18/2026	0 Years, 1 Months, 25 Days	1455	Topiz, Ariel	Ortega	Active	Megaworld Upper East-Bacolod	M	Helper	Purok Pinetree, Barangay Bata Bacolod CIty, Negros Occidental	Purok Pinetree, Barangay Bata Bacolod CIty, Negros Occidental	9704948535	04/19/2004	22 Years, 0 Months, 24 Days	topizariel@gmail.com	Aimee Topiz	9704948535		Angelita Topiz	Alfonso Topiz	Bacolod	Bacolod		11-251643793-9	1213-7266-4147			SMAW NCII	NCI		
57	03/19/26	0 Years, 1 Months, 24 Days	1461	Chavez, France 	Mahinay	Active	Maskara Colesium-Bacolod	M	Welder	SItio Banica 1, Brgy. Tiglawigan Cadiz City	SItio Banica 1, Brgy. Tiglawigan Cadiz City	98532311921	04/06/2005	21 Years, 1 Months, 7 Days	francechavez92@gmail.com	Benjie Chavez	9098808559	SItio Banica 1, Brgy. Tiglawigan Cadiz City	Rowena Mahinay	Benjie B. Chavez	Bacolod	Bacolod	07-4211351-4	11-251809087-1				SMAW NCII			
58	03/19/2026	0 Years, 1 Months, 24 Days	1462	Varona, Rey John	Venus	Active	Maskara Colesium-Bacolod	M	Welder	Purok Cinco Onse, Ubay Pulupandan	Purok Cinco Onse, Ubay Pulupandan	9605114586	01/03/2004	22 Years, 4 Months, 10 Days	reyjohnvarona368@gmail.com	Regine Varona	9487826435	Purok Cinco Onse, Ubay Pulupandan	Regina Varona	Jomar Varona	Bacolod	Bacolod						Senior High Graduate			
59	03/26/2026	0 Years, 1 Months, 17 Days	1458	Agbay, Ann Jelo Josh	Gargaritano	Active	Maskara Colesium-Bacolod	M	Mechanical Engineer	Luzville Phase 1, Brgy. Talubangi, Kabankalan City	Luzville Phase 1, Brgy. Talubangi, Kabankalan City	9624846360	08/27/2002	23 Years, 8 Months, 16 Days	agbayannjelo@gmail.com	Joella Agbay	9404873327	Luzville Phase 1, Brgy. Talubangi, Kabankalan City	Joella Agbay	Aloysius Joseph D. Agbay	Bacolod	Bacolod	07-4048117-0	11-252142209-5		699-380-451		Graduate of BS in Mechanical Engineering			
60	December 02, 2024	1 Years, 5 Months, 11 Days		Macarse, Ricky		Active	Megaworld Belmont-Iloilo	M	Team leader/foreman																						
61	January 30, 2025	1 Years, 3 Months, 13 Days		Gandecila, Winnie L.		Resigned	Megaworld Belmont-Iloilo	M	WELDER FABRICATOR	Linaon, Cauayan	Linaon, Cauayan																				
62	February 05, 2025	1 Years, 3 Months, 8 Days		Macarayan, Jhunrey R.		Resigned	Megaworld Belmont-Iloilo	M	WELDER FABRICATOR	La paz	La paz																				
63	February 05, 2025	1 Years, 3 Months, 8 Days		Oñate, Ryan Chris H.		Resigned	Megaworld Belmont-Iloilo	M	WELDER PIPE FITTER	Cabatuan, Iloilo	Cabatuan, Iloilo																				
64	February 05, 2025	1 Years, 3 Months, 8 Days		Celindro, Jhoven A.		Resigned	Megaworld Belmont-Iloilo	M	WELDER PIPE FITTER	Brgy. Ayaman, Cabanatuan, Iloilo	Brgy. Ayaman, Cabanatuan, Iloilo																				
65	February 06, 2025	1 Years, 3 Months, 7 Days		Sapad, Victor C.		Resigned	Megaworld Belmont-Iloilo	M	FABRICATOR	Brgy. Central Ajuy, Iloilo	Brgy. Central Ajuy, Iloilo																				
66	March 24, 2025	1 Years, 1 Months, 19 Days		Borcilo, Elimer M.		Resigned	Megaworld Belmont-Iloilo	M	PIPE FITTER	Gov. Gabriel Hernandez Ave., Brgy. V. Roxas City, Capiz	Gov. Gabriel Hernandez Ave., Brgy. V. Roxas City, Capiz																				
67	March 24, 2025	1 Years, 1 Months, 19 Days		Selguera, James		Resigned	Megaworld Belmont-Iloilo	M	PIPE FITTER	Row 41 House 6 habitat village, San Isidro, Jaro, Iloilo City	Row 41 House 6 habitat village, San Isidro, Jaro, Iloilo City																				
68	March 27, 2025	1 Years, 1 Months, 16 Days		Sebastian, Daniel G.		Resigned	Megaworld Belmont-Iloilo	M	WELDER	Nahapay Guimbal, Iloilo	Nahapay Guimbal, Iloilo																				
69	March 31, 2025	1 Years, 1 Months, 12 Days		Abarquez, Jerald		Resigned	Megaworld Belmont-Iloilo	M	WELDER	Lapus, Iloilo City	Lapus, Iloilo City																				
70	April 10, 2025	1 Years, 1 Months, 3 Days		Trellanes, Cris		Resigned	Megaworld Belmont-Iloilo	M	WELDER	Talongonan Passi City, Iloilo	Talongonan Passi City, Iloilo																				
71	April 11, 2025	1 Years, 1 Months, 2 Days		Leonor, Cesar C.		Resigned	Megaworld Belmont-Iloilo	M	HELPER	Veterans Village Zone 8, Iloilo City	Veterans Village Zone 8, Iloilo City																				
72	April 12, 2025	1 Years, 1 Months, 1 Days		Baqueri, Rojie Clark D.		Resigned	Megaworld Belmont-Iloilo	M	WELDER	Culas Ajuy, Iloilo City	Culas Ajuy, Iloilo City																				
73	April 14, 2025	1 Years, 0 Months, 29 Days		David, Aaron Paul L.			Megaworld Belmont-Iloilo	M	WELDER	22 Zone 1, Brgy Don Esteban Lapuz Iloilo City	23 Zone 1, Brgy Don Esteban Lapuz Iloilo City																				
74	April 21, 2025	1 Years, 0 Months, 22 Days		Magalso, Melo June V.			Megaworld Belmont-Iloilo	M	WELDER	Bolilao Mandurriao Iloilo City	Bolilao Mandurriao Iloilo City																				
75	April 26, 2025	1 Years, 0 Months, 17 Days		Castor, Erenio Jr.			Megaworld Belmont-Iloilo	M	FOREMAN/DUCTMAN																						
76	April 28, 2025	1 Years, 0 Months, 15 Days		Bariñan Marlon B.			Megaworld Belmont-Iloilo	M	DUCTMAN	Mandurriao, Iloilo City	Mandurriao, Iloilo City																				
77	April 28, 2025	1 Years, 0 Months, 15 Days		Jalipa, Renberg S.			Megaworld Belmont-Iloilo	M	WELDER	Dumangas Iloilo City	Dumangas Iloilo City																				
78	April 28, 2025	1 Years, 0 Months, 15 Days		Facto, Larz B.			Megaworld Belmont-Iloilo	M	WELDER	Airport Tabucan Zone 1, R. mapa St. Iloilo City	Airport Tabucan Zone 1, R. mapa St. Iloilo City																				
79	April 28, 2025	1 Years, 0 Months, 15 Days		Sazon, Ivony, L.			Megaworld Belmont-Iloilo	M	WELDER	Calao, Dumangas Iloilo	Calao, Dumangas Iloilo																				
80	April 28, 2025	1 Years, 0 Months, 15 Days		Estacio, Jerryme Jr			Megaworld Belmont-Iloilo	M	HELPER	Calao, Dumangas Iloilo	Calao, Dumangas Iloilo																				
81	April 28, 2025	1 Years, 0 Months, 15 Days		Capagan, Mark Anthony			Megaworld Belmont-Iloilo	M	WELDER	Barangay Odong-odong Leon, iloilo	Barangay Odong-odong Leon, iloilo																				
82	April 28, 2025	1 Years, 0 Months, 15 Days		Jomantoc, Ernie Jr.			Megaworld Belmont-Iloilo	M	HELPER	Barotac, Nuevo, Iloilo	Barotac, Nuevo, Iloilo																				
83	April 28, 2025	1 Years, 0 Months, 15 Days		Barte, Daren			Megaworld Belmont-Iloilo	M	HELPER	Tagbaya, Ibajay, Aklan	Tagbaya, Ibajay, Aklan																				
84	April 29, 2025	1 Years, 0 Months, 14 Days		Royo, Jack			Megaworld Belmont-Iloilo	M	WELDER	Brgy. Doldol, San Joaquin, Iloilo	Brgy. Doldol, San Joaquin, Iloilo																				
85	April 29, 2025	1 Years, 0 Months, 14 Days		Gabawa, Larry			Megaworld Belmont-Iloilo	M	HELPER																						
86	April 30, 2025	1 Years, 0 Months, 13 Days		Garbosa, George			Megaworld Belmont-Iloilo	M	HELPER	Brgy. Taboc Suba Ilaya, Iloilo City	Brgy. Taboc Suba Ilaya, Iloilo City																				
87	April 30, 2025	1 Years, 0 Months, 13 Days		Doromal, Lope			Megaworld Belmont-Iloilo	M	DUCTMAN	Brgy. Sta. Filomena Arevalo, Iloilo City	Brgy. Sta. Filomena Arevalo, Iloilo City																				
88	May 02, 2025	1 Years, 0 Months, 11 Days		Lozada, Mike			Megaworld Belmont-Iloilo	M	HELPER																						
89	May 02, 2025	1 Years, 0 Months, 11 Days		Deasin, Jurham			Megaworld Belmont-Iloilo	M	DUCTMAN	Brgy. Layogbato, Lemery, Iloilo City	Brgy. Layogbato, Lemery, Iloilo City																				
90	May 05, 2025	1 Years, 0 Months, 8 Days		Nono, Alexander			Megaworld Belmont-Iloilo	M	HELPER																						
91	May 05, 2025	1 Years, 0 Months, 8 Days		Cagayan, Jevan			Megaworld Belmont-Iloilo	M	HELPER																						
92	May 05, 2025	1 Years, 0 Months, 8 Days		Cagayan, Aron James			Megaworld Belmont-Iloilo	M	DUCTMAN																						
93	May 05, 2025	1 Years, 0 Months, 8 Days		Genteroles, Gerald			Megaworld Belmont-Iloilo	M	WELDER																						
94	May 05, 2025	1 Years, 0 Months, 8 Days		Gape, Marvin			Megaworld Belmont-Iloilo	M	HELPER	Molo Pavia, Iloilo City	Molo Pavia, Iloilo City																				
95	May 05, 2025	1 Years, 0 Months, 8 Days		Bayona, Frenz Dee			Megaworld Belmont-Iloilo	M	WELDER																						
96	May 06, 2025	1 Years, 0 Months, 7 Days		Sarabia, Goselle I.			Megaworld Belmont-Iloilo	M	WELDER	Brgy. Luan-Luan Alimodian, Iloilo City	Brgy. Luan-Luan Alimodian, Iloilo City																				
97	May 06, 2025	1 Years, 0 Months, 7 Days		Jayson, Romlec			Megaworld Belmont-Iloilo	M	HELPER	Tupan St. Brgy. 3, Tigbauan, Iloilo City	Tupan St. Brgy. 3, Tigbauan, Iloilo City																				
98	May 08, 2025	1 Years, 0 Months, 5 Days		Arsenio, Kim Stewart			Megaworld Belmont-Iloilo	M	PIPE FITTER	Blk 15 Lot 19 Project 3, So-oc Arevalo, Iloilo City	Blk 15 Lot 19 Project 3, So-oc Arevalo, Iloilo City																				
99	May 09, 2025	1 Years, 0 Months, 4 Days		Andes, Gilbert			Megaworld Belmont-Iloilo	M	PIPE FITTER	570 Nino Sur Zone 3 Villa Arevalo, Iloilo City	571 Nino Sur Zone 3 Villa Arevalo, Iloilo City																				
100	May 10, 2025	1 Years, 0 Months, 3 Days		Aguro, Fray			Megaworld Belmont-Iloilo	M	HELPER	Holy Family, Loboc Lapuz, Iloilo City	Holy Family, Loboc Lapuz, Iloilo City																				
101	May 13, 2025	1 Years, 0 Months, 0 Days		Miag-ao, Joshua			Megaworld Belmont-Iloilo	M	HELPER																						
102	May 15, 2025	0 Years, 11 Months, 28 Days		Castor, Rowel			Megaworld Belmont-Iloilo	M	WELDER																						
103	May 15, 2025	0 Years, 11 Months, 28 Days		Villegas, Wilmar			Megaworld Belmont-Iloilo	M	HELPER																						
104	May 15, 2025	0 Years, 11 Months, 28 Days		Almanoche, Helbert			Megaworld Belmont-Iloilo	M	WELDER																						
105	May 15, 2025	0 Years, 11 Months, 28 Days		Mozunes, Rolden			Megaworld Belmont-Iloilo	M	HELPER	Brgy. Bita Sur, Oton, Iloilo City	Brgy. Bita Sur, Oton, Iloilo City																				
106	May 15, 2025	0 Years, 11 Months, 28 Days		Magallanes, Rasheed			Megaworld Belmont-Iloilo	M	HELPER	Brgy. Block 17, Mandurriao, Iloilo City	Brgy. Block 17, Mandurriao, Iloilo City																				
107	May 15, 2025	0 Years, 11 Months, 28 Days		Agtas, Jephtah			Megaworld Belmont-Iloilo	M	WELDER	Iloilo City	Iloilo City																				
108	May 15, 2025	0 Years, 11 Months, 28 Days		Tajanlangit, Jophet			Megaworld Belmont-Iloilo	M	DUCTMAN	Tampael, Tigbawan, Iloilo City	Tampael, Tigbawan, Iloilo City																				
109	May 15, 2025	0 Years, 11 Months, 28 Days		Getonzo, Efren Jr.			Megaworld Belmont-Iloilo	M	DUCTMAN																						
110	May 15, 2025	0 Years, 11 Months, 28 Days		Igmedio, Arlan			Megaworld Belmont-Iloilo	M																							
111	May 17, 2025	0 Years, 11 Months, 26 Days		Belonio, Ruben			Megaworld Belmont-Iloilo	M	WELDER																						
112	May 17, 2025	0 Years, 11 Months, 26 Days		Benguan, Julius			Megaworld Belmont-Iloilo	M	HELPER	Brgy. Tumcom Ilaud Pototan, Iloilo City	Brgy. Tumcom Ilaud Pototan, Iloilo City																				
113	May 20, 2025	0 Years, 11 Months, 23 Days		Castor, Arnel			Megaworld Belmont-Iloilo	M	HELPER																						
114	May 20, 2025	0 Years, 11 Months, 23 Days		Saputalo, Steven			Megaworld Belmont-Iloilo	M	WELDER																						
115	May 20, 2025	0 Years, 11 Months, 23 Days		Guanzon, Dexter Jr			Megaworld Belmont-Iloilo	M	WELDER																						
116	May 20, 2025	0 Years, 11 Months, 23 Days		Pableo, Jonald			Megaworld Belmont-Iloilo	M	WELDER																						
117	May 21, 2025	0 Years, 11 Months, 22 Days		Guanzon, Dexter Jr			Megaworld Belmont-Iloilo	M	HELPER																						
118	May 26, 2025	0 Years, 11 Months, 17 Days		Rizada, Christian Jay			Megaworld Belmont-Iloilo	M	HELPER																						
119	May 26, 2025	0 Years, 11 Months, 17 Days		Telesforo Mina Jr.			Megaworld Belmont-Iloilo	M	WELDER																						
120	May 28, 2025	0 Years, 11 Months, 15 Days		Tabugoc, Bon Peter			Megaworld Belmont-Iloilo	M	WELDER																						
121	May 28, 2025	0 Years, 11 Months, 15 Days		Remon Danid			Megaworld Belmont-Iloilo	M	HELPER																						
122	May 28, 2025	0 Years, 11 Months, 15 Days		Ciriaco, Chrisopher			Megaworld Belmont-Iloilo	M	WELDER																						
123	June 02, 2025	0 Years, 11 Months, 11 Days		John Aaron Andrada			Megaworld Belmont-Iloilo	M	WELDER																						
124	June 02, 2025	0 Years, 11 Months, 11 Days		Venancio, Sandy			Megaworld Belmont-Iloilo	M	WELDER																						
125	June 02, 2025	0 Years, 11 Months, 11 Days		Lasafin, Fernand			Megaworld Belmont-Iloilo	M	HELPER																						
126	May 29, 2025	0 Years, 11 Months, 14 Days		Dato-on, Dan			Megaworld Belmont-Iloilo	M	HELPER																						
127	May 29, 2025	0 Years, 11 Months, 14 Days		Castillano, Jackie Rey			Megaworld Belmont-Iloilo	M	PIPE FITTER																						
128	June 04, 2025	0 Years, 11 Months, 9 Days		Ibanez, Jay Ar			Megaworld Belmont-Iloilo	M	Ductman																						
129	June 09, 2025	0 Years, 11 Months, 4 Days		Lozada, Jason			Megaworld Belmont-Iloilo	M	Ductman																						
130	June 09, 2025	0 Years, 11 Months, 4 Days		Villafa, Jay R			Megaworld Belmont-Iloilo	M	Welder																						
131	June 10, 2025	0 Years, 11 Months, 3 Days		Fortunado, Aljohn			Megaworld Belmont-Iloilo	M	Welder																						
132	June 17, 2025	0 Years, 10 Months, 26 Days		Renante Barongo			Megaworld Belmont-Iloilo	M	Helper																						
133	June 17, 2025	0 Years, 10 Months, 26 Days		Benjamin Uy			Megaworld Belmont-Iloilo	M	Helper																						
134	June 17, 2025	0 Years, 10 Months, 26 Days		Joseph Christian Penaflor			Megaworld Belmont-Iloilo	M	Helper																						
135	June 17, 2025	0 Years, 10 Months, 26 Days		Carl Jason Fenola			Megaworld Belmont-Iloilo	M	Welder																						
136	June 23, 2025	0 Years, 10 Months, 20 Days		Jose Jerry S. Cajilig Jr			Megaworld Belmont-Iloilo	M	Heper	Villa Arevalo, Iloilo City	Villa Arevalo, Iloilo City																				
137	June 24, 2025	0 Years, 10 Months, 19 Days		Delos Santos, Gedionde			Megaworld Belmont-Iloilo	M	Welder																						
138	June 27, 2025	0 Years, 10 Months, 16 Days		Villanueva, Carl Angelo			Megaworld Belmont-Iloilo	M	Welder																						
139	June 16, 2025	0 Years, 10 Months, 27 Days		Garcia, Jernell			Megaworld Belmont-Iloilo	M	Welder																						
140	June 16, 2025	0 Years, 10 Months, 27 Days		Gayas, Kevin			Megaworld Belmont-Iloilo	M	Welder																						
141	June 16, 2025	0 Years, 10 Months, 27 Days		Abellado, Jes Martin			Megaworld Belmont-Iloilo	M	Welder																						
142	June 18, 2025	0 Years, 10 Months, 25 Days		Villanueva, Jaime Martin			Megaworld Belmont-Iloilo	M	Welder																						
143	June 18, 2025	0 Years, 10 Months, 25 Days		Concengco, Mark			Megaworld Belmont-Iloilo	M	Welder																						
144	June 28, 2025	0 Years, 10 Months, 15 Days		Rendon, Leo P. Jr			Megaworld Belmont-Iloilo	M	Helper	Zone 3, Brgy. Tacas, jaro, Iloilo City	Zone 3, Brgy. Tacas, jaro, Iloilo City																				
145	June 30, 2025	0 Years, 10 Months, 13 Days		VAlenciano, Jayboy V.			Megaworld Belmont-Iloilo	M	Welder	Brgy. San Matias, Dingle, Iloilo City	Brgy. San Matias, Dingle, Iloilo City																				
146	June 30, 2025	0 Years, 10 Months, 13 Days		Narido, Dave M			Megaworld Belmont-Iloilo	M	Helper	Brgy. Cagbang, Miagao, Iloilo City	Brgy. Cagbang, Miagao, Iloilo City																				
147	June 28, 2025	0 Years, 10 Months, 15 Days		Cruz, Janford D.			Megaworld Belmont-Iloilo	M	Ductman	Brgy. Gua-an, Leganes, Iloilo City	Brgy. Gua-an, Leganes, Iloilo City																				
148	July 07, 2025	0 Years, 10 Months, 6 Days		Mellizo, Reynaldo Jr			Megaworld Belmont-Iloilo	M	Ductman																						
149	July 07, 2025	0 Years, 10 Months, 6 Days		Hilapon, Reynan			Megaworld Belmont-Iloilo	M	Helper																						
150	July 10, 2025	0 Years, 10 Months, 3 Days		Gonzales, Ivon Jr.			Megaworld Belmont-Iloilo	M	Welder																						
151	July 10, 2025	0 Years, 10 Months, 3 Days		Gascon, Khimpee			Megaworld Belmont-Iloilo	M	Welder																						
152	July 15, 2025	0 Years, 9 Months, 28 Days		Jhon Ruzzel V. Laxinto			Megaworld Belmont-Iloilo	M	Welder																						
153	July 15, 2025	0 Years, 9 Months, 28 Days		Jhon Peter M. Laxinto			Megaworld Belmont-Iloilo	M	Welder																						
154	July 15, 2025	0 Years, 9 Months, 28 Days		Even Dale Egualan			Megaworld Belmont-Iloilo	M	Ductman (subcon)																						
155	July 17, 2025	0 Years, 9 Months, 26 Days		Ron Allen L. Fuentes			Megaworld Belmont-Iloilo	M	Welder (Subcon)																						
156	July 18, 2025	0 Years, 9 Months, 25 Days		Almer Castro			Megaworld Belmont-Iloilo	M	Welder (Subcon)																						
157	July 18, 2025	0 Years, 9 Months, 25 Days		John Mark Medez			Megaworld Belmont-Iloilo	M	Helper																						
158	July 16, 2025	0 Years, 9 Months, 27 Days		Carlo Ibjong			Megaworld Belmont-Iloilo	M	Helper																						
159	August 16, 2025	0 Years, 8 Months, 27 Days		Delos Reyes, Ivander G.			Megaworld Belmont-Iloilo	M	Welder	Brgy. Doldol Valladolid Negros Occidental	Brgy. Doldol Valladolid Negros Occidental																				
160	August 16, 2025	0 Years, 8 Months, 27 Days		Gabayeron, Archie N.			Megaworld Belmont-Iloilo	M	Helper	Park Durar-og, Mailum, Bago City	Park Durar-og, Mailum, Bago City																				
161	August 16, 2025	0 Years, 8 Months, 27 Days		Lorania, jhon Emanuel			Megaworld Belmont-Iloilo	M	Welder	Brgy. Doldol Valladolid Negros Occidental	Brgy. Doldol Valladolid Negros Occidental																				
162	August 16, 2025	0 Years, 8 Months, 27 Days		Juanica, Gerick			Megaworld Belmont-Iloilo	M	Welder	Purok 6 Brgy.Ayungon Valladolid, Negros Occidental	Purok 6 Brgy.Ayungon Valladolid, Negros Occidental																				
163	August 16, 2025	0 Years, 8 Months, 27 Days		Gordon, Jose Jr			Megaworld Belmont-Iloilo	M	Ductman	Brgy. Alijis, Valladolid Negros Occidental	Brgy. Alijis, Valladolid Negros Occidental																				
164	August 18, 2025	0 Years, 8 Months, 25 Days		Dequita, Cris Rin			Megaworld Belmont-Iloilo	M	Helper	Villa Sagrado, Brgy. Burgos, Cadiz City	Villa Sagrado, Brgy. Burgos, Cadiz City																				
165	August 19, 2025	0 Years, 8 Months, 24 Days		Pacardo, Jonathan P.			Megaworld Belmont-Iloilo	M	Welder	Molo Boulevard, Iloilo City	Molo Boulevard, Iloilo City																				
166	August 20, 2025	0 Years, 8 Months, 23 Days		Pama, Leonardo			Megaworld Belmont-Iloilo	M	PIPE FITTER																						
167	August 27, 2025	0 Years, 8 Months, 16 Days		Mapilisan, Marc Armand S.			Megaworld Belmont-Iloilo	M	Helper	Blk 8 Lot 30 Phase 4 Lumina Homes Oton, Iloilo City	Blk 8 Lot 30 Phase 4 Lumina Homes Oton, Iloilo City																				
168	August 29, 2025	0 Years, 8 Months, 14 Days		Esico, Joren M.			Megaworld Belmont-Iloilo	M	Welder	Brgy. Amerang Maasin Iloilo	Brgy. Amerang Maasin Iloilo																				
169	September 02, 2025	0 Years, 8 Months, 11 Days		Gamuza, Stephen S			Megaworld Belmont-Iloilo	M	Welder	La Paz Nueva Valencia Guimaras	La Paz Nueva Valencia Guimaras																				
170	September 02, 2025	0 Years, 8 Months, 11 Days		Sabobo, Norbert G			Megaworld Belmont-Iloilo	M	Welder	Guiwanon,  Nueva Valencia, Guimaras	Guiwanon,  Nueva Valencia, Guimaras																				
171	September 01, 2025	0 Years, 8 Months, 12 Days		Terco, Rumar			Megaworld Belmont-Iloilo	M	Helper	Calao, Dumangas, Iloilo	Calao, Dumangas, Iloilo																				
172	September 03, 2025	0 Years, 8 Months, 10 Days		Lorania, Jester John S.			Megaworld Belmont-Iloilo	M	Helper	Purok Rose, Brgy. Doldol Valladolid, Negros Occidental	Purok Rose, Brgy. Doldol Valladolid, Negros Occidental																				
173	September 03, 2025	0 Years, 8 Months, 10 Days		Villarin, Johann B.			Megaworld Belmont-Iloilo	M	Helper	Ayungon, Valladolid Purok 6, Negros Occidental	Ayungon, Valladolid Purok 6, Negros Occidental																				
174	September 03, 2025	0 Years, 8 Months, 10 Days		Palis, Bryan M.			Megaworld Belmont-Iloilo	M	Helper	Purok Adalla Brgy. Guintorilan Valladolid, Negros Occidental	Purok Adalla Brgy. Guintorilan Valladolid, Negros Occidental																				
175	September 03, 2025	0 Years, 8 Months, 10 Days		Young, Warley I.			Megaworld Belmont-Iloilo	M	Helper	Sagua Banwa,Valladolid, Negros Occidental	Sagua Banwa,Valladolid, Negros Occidental																				
176	September 23, 2025	0 Years, 7 Months, 20 Days		Laudato, Argie			Megaworld Belmont-Iloilo	M	Helper																						
177	September 23, 2025	0 Years, 7 Months, 20 Days		Lander John Salido			Megaworld Belmont-Iloilo	M	Ductman																						
178	September 25, 2025	0 Years, 7 Months, 18 Days		Canencia, Denmark			Megaworld Belmont-Iloilo	M	Mason	Blk 19, Lot 7, Villa Luisa, City of Cadiz, Negros Occidental	Blk 19, Lot 7, Villa Luisa, City of Cadiz, Negros Occidental																				
179	October 02, 2025	0 Years, 7 Months, 11 Days		Balijado, Epipanio			Megaworld Belmont-Iloilo	M	Helper																						
180	October 02, 2025	0 Years, 7 Months, 11 Days		Basas, Jeffrey			Megaworld Belmont-Iloilo	M	Helper																						
181	October 07, 2025	0 Years, 7 Months, 6 Days		Mondido,John Fyles			Megaworld Belmont-Iloilo	M	Helper	San Jose, San Miguel Iloilo City	San Jose, San Miguel Iloilo City																				
182	October 10, 2025	0 Years, 7 Months, 3 Days		Dangcalan, Eugene			Megaworld Belmont-Iloilo	M	Welder																						
183	October 13, 2025	0 Years, 7 Months, 0 Days		Zapsa, Edward			Megaworld Belmont-Iloilo	M	Helper																						
184	October 14, 2025	0 Years, 6 Months, 29 Days		Del Pilar, Jan Michael			Megaworld Belmont-Iloilo	M	Helper																						
185	October 14, 2025	0 Years, 6 Months, 29 Days		Agregado, Michael			Megaworld Belmont-Iloilo	M	Helper																						
186	October 24, 2025	0 Years, 6 Months, 19 Days		Armand S. Tabianan			Megaworld Belmont-Iloilo	M	Helper																						
187	October 24, 2025	0 Years, 6 Months, 19 Days		Randel John Selorico			Megaworld Belmont-Iloilo	M	Helper																						
188	October 24, 2025	0 Years, 6 Months, 19 Days		Tristan Constantino			Megaworld Belmont-Iloilo	M	Warehouseman																						
189	October 24, 2025	0 Years, 6 Months, 19 Days		RJ Mark Lastimozo			Megaworld Belmont-Iloilo	M	Helper																						
190	October 24, 2025	0 Years, 6 Months, 19 Days		Unong, Norman Rey			Megaworld Belmont-Iloilo	M	Welder																						
191	November 17, 2025	0 Years, 5 Months, 26 Days		Palma, Pabby Pab			Megaworld Belmont-Iloilo	M	Helper	Poblacion Sur, Balasan, Iloilo	Poblacion Sur, Balasan, Iloilo																				
192	November 18, 2025	0 Years, 5 Months, 25 Days		Roman Magsusi			Megaworld Belmont-Iloilo	M	Helper																						
193	November 19, 2025	0 Years, 5 Months, 24 Days		Benjie Antom			Megaworld Belmont-Iloilo	M	Helper																						
194	November 21, 2025	0 Years, 5 Months, 22 Days		Norly Baladia Jr.			Megaworld Belmont-Iloilo	M	Helper																						
195	November 21, 2025	0 Years, 5 Months, 22 Days		Ramie Panes			Megaworld Belmont-Iloilo	M	Welder																						
196	December 02, 2025	0 Years, 5 Months, 11 Days		Galan, Paul			Megaworld Belmont-Iloilo	M	Welder																						
197	December 02, 2025	0 Years, 5 Months, 11 Days		Edemburgo, Mario			Megaworld Belmont-Iloilo	M	Welder																						
198	December 02, 2025	0 Years, 5 Months, 11 Days		Lozada, Jimmar			Megaworld Belmont-Iloilo	M	Helper																						
199	December 04, 2025	0 Years, 5 Months, 9 Days		Lopez, Jesmark			Megaworld Belmont-Iloilo	M	Welder																						
200	December 04, 2025	0 Years, 5 Months, 9 Days		Lopez, Bobby			Megaworld Belmont-Iloilo	M	Welder																						
201	December 04, 2025	0 Years, 5 Months, 9 Days		Arroyo, Janine			Megaworld Belmont-Iloilo	M	Pipe Fitter																						
202		Brgy. Patun-an, Calatrava, Negros Occ	Brgy. Patun-an, Calatrava, Negros Occ																							
203		Brgy. Suba, Calatrava, Negros Occ	Brgy. Suba, Calatrava, Negros Occ																							
204		Labadan, Donavil			Megaworld Belmont-Iloilo	M																							
205		Purisima, Regienald			Megaworld Belmont-Iloilo	M																							
206		Emeterio, Sam			Megaworld Belmont-Iloilo	M																							
207		Salem, Jade			Megaworld Belmont-Iloilo	M																							
208		Duco, Jay			Megaworld Belmont-Iloilo	M																							
209		Sazon, Joe Pit			Megaworld Belmont-Iloilo	M																							
210		Duazon, Gerald			Megaworld Belmont-Iloilo	M	Welder/Skilled 2																						
211		Magnabijon, Ariel		Active	Megaworld Upper East-Bacolod	M	Welder/Skilled 2																						
212		Pelarion, Jade		Active	Megaworld Upper East-Bacolod	M	Welder/Skilled 2																						
213		Gonzales, Jomel 		Active	Megaworld Upper East-Bacolod	M	Helper																						
214		Montalbo, Edmark		Active	Megaworld Upper East-Bacolod	M	Helper																						
215		Villabeto, Jobel		Active	Megaworld Upper East-Bacolod	M	Welder/Skilled 1																						
216		Lumampao,Andrew		Active	Megaworld Upper East-Bacolod	M	Plumber 																						
217		Gomez, Mark Angelo		Active	Megaworld Upper East-Bacolod	M	Helper																						
218		Basas, Joeffrey		Active	Megaworld Upper East-Bacolod	M	Mason/Skilled 1																						
219		Cabuncag, Rey Nino		Active	Megaworld Upper East-Bacolod	M	Helper																						
220		Navarro, Reyan		Active	Megaworld Upper East-Bacolod	M	Leadman Mason																						
221		Aribato, Ralph Jester		Active	Megaworld Upper East-Bacolod	M	Helper																						
222		Hermosoro, Rogelio		Active	Megaworld Upper East-Bacolod	M	Welder/Skilled 1																						
223		Armada, Rodel		Active	Megaworld Upper East-Bacolod	M	Welder/Skilled 1																						
224		Haboc,Joel		Active	Megaworld Upper East-Bacolod	M	Helper																						
225		Pendon, Joey		Active	Megaworld Upper East-Bacolod	M	Welder																						
226		Garcia, Dennis		Active	Megaworld Upper East-Bacolod	M	Welder																						
227		Cabungcag, Ronie		Active	Megaworld Upper East-Bacolod	M	Helper																						
228	5/6/2019	6 Years, 3 Months, 1 Days		Matta, Michael Andrew	Adrao	Active	Phil Arforce-Cebu	M	Electrician	Brgy. Tiza Roxas City Capiz	Brgy. Tiza Roxas City Capiz	0946-629-5455	2/23/1996	29 Years, 5 Months, 15 Days					Maria Matta	Ireneo Matta	Bacolod	Vma		11-025617573-8	1212-6087-0833						
229	1/21/2023	3 Years, 2 Months, 11 Days		Villamia Juluwie	Gumagda	Active	Ridge 5-Tagaytay	M	Plumber/Electrician	Prk Mahidaiton Brgy 39 Bacolod City Negros Occ	Prk Mahidaiton Brgy 39 Bacolod City Negros Occ	9271443102	5/17/1984	41 Years, 10 Months, 15 Days					Lydia Gumagda	Winy Villarmia	Pasudeco	Ridge 5	07-3636422-7	11-025490790-1	1212-9207-2218			High School Graduate	NC II Plumbing	Welding,Wiring	
230	8/13/2022	3 Years, 7 Months, 19 Days		Balolot Marlo	Arevalo	Active	Megaworld Belmont-Iloilo	M	Welder	Villa Barbas II Cadiz City Negros Occ	Villa Barbas II Cadiz City Negros Occ	9071209928	8/13/1981	44 Years, 7 Months, 19 Days					Elma Balolot	Marcelino Balolot	Bacolod	Cccpm	07-3460885-9	11-025507565-9	1211-5488-0091	475-385-777		Undergraduate of BS Criminology	NC I	Welding	
231	8/12/2022	3 Years, 7 Months, 20 Days		Gonzaga Diosdado		Active	VYKIM-Bacolod	M	Welder	Prk Totong 1 Brgy Felisa Bacolod City	Prk Totong 1 Brgy Felisa Bacolod City	9383767130	8/19/1998	27 Years, 7 Months, 13 Days	19jeegonzaga@gmail.com				Nosomie Gonzaga	Diosdado Gonzaga	Bacolod	Bacolod	07-4085368-5	1.12515E+11	1213-0872-4292			Senior High School Graduate	NC II	Welding	
232	10/21/2022	3 Years, 5 Months, 11 Days		Victorio Henden	Diaz	Active	Megaworld Upper East-Bacolod	M	Warehouseman	Brgy Efigenio Lizares Talisay City Neg Occ	Brgy Efigenio Lizares Talisay City Neg Occ	9637593630	4/6/1985	40 Years, 11 Months, 26 Days		Aiza Esquillo	0963 759 3630		Sonia Victorio	Holden Victorio	Bacolod	Upper East	33-8369030-9	01-050472330-0	1210-2900-1213			High School Graduate			
233	11/17/2022	3 Years, 4 Months, 15 Days		Amante Joseph	Gomez	Active	VYKIM-Bacolod	M	Fabricator/Welder	Totong 1 Brgy Felisa Bcolod City	Totong 1 Brgy Felisa Bcolod City	9701992839	9/16/1981	44 Years, 6 Months, 16 Days					Teresita Gomez	Roberto Amante	Bacolod	Bacolod	07-1799414-0	11-050263746-0	1590-0066-1703			High School Graduate			
234	1/26/2023	3 Years, 2 Months, 6 Days		Gruzo Jome	Salomon	Active	VYKIM-Bacolod	M	Fabricator/Welder	Prk Malapitan Brgy Alijis Bacolod City	Prk Malapitan Brgy Alijis Bacolod City	9121404263	7/22/1996	29 Years, 8 Months, 10 Days	jhayem31@yahoo.com				Marivic P. Salomon	Ireneo V. Gruzo	Bacolod	Smile Res	34-8992870-0	1102-5751079-4	1212-7283-3641			Tesda Graduate of Shielded Metal Arch Welding	NCII	Welding	
235	2/1/2023	3 Years, 2 Months, 0 Days		Nono Alixander	Naria	Active	Megaworld Belmont-Iloilo	M	Fabricator/Welder	Brgy Bolho Miagao Iloilo City	Brgy Bolho Miagao Iloilo City	9636639990	11/7/1991	34 Years, 4 Months, 25 Days		Anie Nono	963639990		Azucena Nono	Wilfredo Nono	Bacolod	Wilcon	07-4165382-6	11-202217750-0	1213-1924717-5			High School Graduate			
236	6/21/2023	2 Years, 9 Months, 11 Days		Jay Louie Lasola		Active	Megaworld Upper East-Bacolod	M	Safety Officer	Golf Area Vmc Victorias City Negros Occidental	Golf Area Vmc Victorias City Negros Occidental	09687713005/092840444651	5/23/1985	40 Years, 10 Months, 9 Days	Jaylouielasola@gmail.com						Bacolod	Upper East	07-3144408-7	11-050287480-2	1210-7121-2635			Colllege Undergrduate	BOSH,FIRST AID TRAINING,LCMC.		
237	6/26/2023	2 Years, 9 Months, 6 Days		Paul John Denaga	Idiosolo	Terminated	Fabrication-Bacolod	M	Fabricator/Welder	Prk Kasilingan, Tangub Bacolod City	Prk Kasilingan, Tangub Bacolod City	9060579512	7/13/1996	29 Years, 8 Months, 19 Days	pjdenaga28@gmail.com	John Denaga	9260458441		Mary Grace Denaga	John Denaga	Bacolod	Bacolod	07-3466951-3	11-253381644-7	1211-6207-7457			Graduate of Vocational School	NC II	Welding	
238	1/20/2023	3 Years, 2 Months, 12 Days		Francis Aranguin	Aranguin	Active	UPHD-Las Piñas	M	Electrician	Prk. Masinadyahon Brgy. Atipuluan Bago City	Prk. Masinadyahon Brgy. Atipuluan Bago City	0950-293-3456	3/17/1997	29 Years, 0 Months, 15 Days							Bacolod	Wilcon	07-3516810-7	11-050740237-2	1211-6705-5499			Tesda Graduate(Electrcical)	NC II		
239	11/6/2023	2 Years, 4 Months, 26 Days		Hermosura Erica Marie	Jimenez	Active	Megaworld Belmont-Iloilo	F	Document Controller	#11 Bonifacio Street Prk Mainuswagon,Brgy II Silay City Negros Occidental	#11 Bonifacio Street Prk Mainuswagon,Brgy II Silay City Negros Occidental	9630077184	4/6/2000	25 Years, 11 Months, 26 Days	ericamarieheremusora@gmail.com				Ma.Lourdes Hermosura	Edgardo Hermosura	Bacolod	Wilcon	07-4048000-3	01-251652848-4	1213-3274-9210			Graduate of BS. In Electro-Mechanical			
240	11/6/2023	2 Years, 4 Months, 26 Days		Sentoy Melody	Arsaga	Active	Wilcon-Pila Laguna	F	Document Controller	kenon Street,Brgy 9,Isabela Negros Occidental	kenon Street,Brgy 9,Isabela Negros Occidental	9949886058	5/6/2000	25 Years, 10 Months, 26 Days	Sentoy.melody.a@gmail.com				Mary Rose Sentoy	Lemuel Sentoy	Bacolod	Wilcon Villasis	07-4002198-3	11-025837610-2	1213-3276-0569			Graduate of BS. In Electronics			
241	3/20/2024	2 Years, 0 Months, 12 Days		Pahilanga Rommel John	Diabor	Active	VYKIM-Bacolod	M	Auto Cad Operator	Sitio Pulo Barangay.Bubog Talisay City Negros Occidental	Sitio Pulo Barangay.Bubog Talisay City Negros Occidental	9296693233	11/9/1990	35 Years, 4 Months, 23 Days	Gersan31elad@gmail.com				Ermilo Pahilanga	Leilang Diabor	Bacolod	Smile Res	07-2983639-3	11-050665191-3	9141-7902-5485			Graduate of BS.Major In Architechtural Drafting			
242	4/1/2024	2 Years, 0 Months, 0 Days		Gersan Dale Memis	Memis	Resigned	Phil Arforce-Cebu	M	Civil Engineer	Jesusa 1,Alegria,Murcia,Negros Occidental	Jesusa 1,Alegria,Murcia,Negros Occidental	9959367223	4/20/1999	26 Years, 11 Months, 12 Days					Susana Solis	Rogelio Memis	Bacolod	Vma	07-4081465-3	11-025808746-1	1213-0771-9962			Graduate of Bachelor of Civil Engineer	Civil Engineer Passer 2023		
243	1/9/2025	1 Years, 2 Months, 23 Days		Maga Wyne	Cabarles	Active	VYKIM-Bacolod	M	Safety Officer	Zone 11 Talisay Negros Occidental	Zone 11 Talisay Negros Occidental	9955724331	4/1/2000	26 Years, 0 Months, 0 Days	magawyne@gmail.com				Maria Cecil Maga	Willy Maga	Bacolod	Smile Res	07-427523-9	11-25899721-2	1213-3410-9270	665-722-134		Graduate of Bachelor of Science Major in Mechanical Engineer			
244	5/30/2025	0 Years, 10 Months, 2 Days		Gonzaga Marvin	Sagrado	Active	Megaworld Belmont-Iloilo	M	Project In Charge	Barangay Bata,Bacolod City	Barangay Bata,Bacolod City	9185520573	11/15/2000	25 Years, 4 Months, 17 Days	gonzaga.marvinsagrado38215@gmail.com				Arcili Sagrado	Gargar Gonzaga	Bacolod	Belmont	35-0029128-0	11-025837051-1				Graduate of BS.Major In Mechanical Engineer	PRC License	DOST-SEI Scholar  Graduate,Problem Solving,Basic Autocad,Sketch up	
245	6/27/2025	0 Years, 9 Months, 5 Days		Vidal Geraldine		Active	VYKIM-Bacolod	F	Document Controller	Purok Binuldusan Brgy Isidro	Purok Binuldusan Brgy Isidro	9617146374	7/2/2002	23 Years, 8 Months, 30 Days	vidalgeraldine15@gmail.com					Ray Ocampo Escanan	Bacolod	Belmont	07-4308430-9	11-203880375-4	1213-5523-7099			Graduate of BS.Major In Manufacturing Engineering			
246	6/16/2025	0 Years, 9 Months, 16 Days		Genaca Victor Nino	Peneiro	Active	Megaworld Belmont-Iloilo	F	Junior Mechanical Engineer 1	Providence Balabag Pavia Iloilo Philippines	Providence Balabag Pavia Iloilo Philippines	9774948976	10/19/2001	24 Years, 5 Months, 13 Days	vicninogenaca@gmail.com				Rosalie Peniero	Victor Genaca	Bacolod	Belmont				677-885-865		Graduate of BS.Major In Mechanical Engineer	PRC License		
247	6/24/2025	0 Years, 9 Months, 8 Days		Deocampo Beatrice	Arroyo	Resigned	Megaworld Belmont-Iloilo	F	Document Controller 1	Blk 13 Lot 19,Daffodil St. Easthome 3 Barangay Estefania	Blk 13 Lot 19,Daffodil St. Easthome 3 Barangay Estefania	9085482593	8/24/2000	25 Years, 7 Months, 8 Days	Beadeocampo2400@gmail.com				Cherryl Arroyo	Jeffery Deocampo	Bacolod	Belmont	07-4569636-8	11-025820293-7	1213-6709-6168	678-632-898		Graduate of BS.Civil Engineer	Basic Autocad	Authocad,Construction Management,Civil Engeering Estimates	
248	6/26/2025	0 Years, 9 Months, 6 Days		Arroz Alister	Moralidad	Resigned	Crimson Hotel-Cebu	M	Project In Charge	Tubod Street Inayuan Cauyan	Tubod Street Inayuan Cauyan	9859750163	9/24/2000	25 Years, 6 Months, 8 Days	arrozalister@gmail.com				Thelma Arroz	Abner Arroz	Bacolod	Paf Cebu	07-4408413-3	11-254061-5104	1213-6707-5742			Graduate of BS.Major In Electrical Engineer	PRC License		
249	7/1/2025	0 Years, 9 Months, 0 Days		Failaman Lory	Nonesco	Active	Megaworld Belmont-Iloilo	M	Safety Officer	Barangay Oñate De Leon St.Mandurriao Iloilo City	Barangay Oñate De Leon St.Mandurriao Iloilo City	9073379570	2/21/1982	44 Years, 1 Months, 11 Days							Belmont	Belmont		01-025411784-1		336-199-825		Graduate of BS Major Elecronics			
250	7/9/2025	0 Years, 8 Months, 23 Days		Efsiaca Irish Sandra	Bonifacio	Active	Ridge 5-Tagaytay	F	Document Controller 1	Buenivista Subd. Barangay Guinhalaran Silay City	Buenivista Subd. Barangay Guinhalaran Silay City			126 Years, 3 Months, 1 Days							Rideg 5	Rideg 5						Graduate of BS Industrial Engineering			
251	6/17/2025	0 Years, 9 Months, 15 Days		Amarila Felo Andrey	Blacquio	Active	Megaworld Belmont-Iloilo	M	Autocad Operator	Pontevedra Negros Occidental	Pontevedra Negros Occidental	9932996385	12/17/2002	23 Years, 3 Months, 15 Days	amarilafeloandreyb@gmail.com				Armelita Amarila	Reyn Amarila	Belmont	Belmont	07-4452134-0	11-025949230-0		678-664-843					
252	6/27/2025	0 Years, 9 Months, 5 Days		Salamat Lhenie Jane	Landicho	Resigned	Ridge 5-Tagaytay	F	Safety Officer/Auotcad Operator	188 Virata Street Pajo Alfonso Cavite	188 Virata Street Pajo Alfonso Cavite	09064330103/046-4194120	10/17/2001	24 Years, 5 Months, 15 Days	salamat.lheniejane@gmail.com				Juliana Salamat	Isaac Salamat	Rideg 5	Rideg 5						Graduate of BS.Civil Engineer			
253	6/4/2025	0 Years, 9 Months, 28 Days		Monton Elijah	Jagolina	AWOL	Megaworld Upper East-Bacolod	F	Mechanical Engineer	Barangay Cabug	Barangay Cabug	9914463446	5/3/2000	25 Years, 10 Months, 29 Days	elijahmaymonton@gmail.com				Elgan Monton	Gemma Monton	Upper East	Bacolod	07-4477869-8	11-0258172736	1213-5605-2386			Graduate of Bachelor of Civil Engineer			
254	7/23/2025	0 Years, 8 Months, 9 Days		Geli Richard	Cabal	AWOL	Ridge 5-Tagaytay	M	Project In Charge	Bonifacio Surigao City	Bonifacio Surigao City	09300240782/0991944933	12/7/2000	25 Years, 3 Months, 25 Days	richardgeli28@gmail.com						Rideg 5	Rideg 5						Graduate of BS.Major In Electrical Engineer			
255	7/28/2025	0 Years, 8 Months, 4 Days		Caban Rayselle	Pechera	AWOL	Sol Y Viento-Pansol	M	Project In Charge	Block 17 Lot 7 Buenavista Sub.Phase 2 Barangay Guinhalaran Silay City	Block 17 Lot 7 Buenavista Sub.Phase 2 Barangay Guinhalaran Silay City	09218716357/09153207653	1/16/2002	24 Years, 2 Months, 16 Days	Raysellecaban01@gmail.com					Julie Caban	Rideg 5	Rideg 5	35-0446075-8	11-251608755-5	1212-9940-5156	609-139-547		Graduate of BS.Major In Mechanical Engineer			
RAW;

        $lines = explode("\n", $rawData);
        if (count($lines) < 2) {
            return [];
        }

        $header = str_getcsv(array_shift($lines), "\t");
        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        $employees = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            $row = str_getcsv($line, "\t");
            if (count($row) < count($header)) continue;

            $data = array_combine($header, $row);

            $name = trim($data['name'] ?? '');
            if (empty($name)) continue;

            $email = !empty($data['email address']) ? trim($data['email address']) : null;
            if (!$email) {
                $emailName = strtolower(str_replace([' ', ','], ['.', ''], $name));
                $email = $emailName . '@example.com';
            }

            // Handle missing emp_code
            $empCode = !empty($data['id number']) ? (int) $data['id number'] : null;
            if (!$empCode) {
                // Generate a unique negative emp_code to avoid conflicts
                static $missingCounter = 0;
                $missingCounter--;
                $empCode = $missingCounter;
            }

            $employees[] = [
                'name'                  => $name,
                'email'                 => $email,
                'emp_code'              => $empCode,
                'employee_status'       => strtolower($data['status'] ?? 'active'),
                'gender'                => $data['sex'] ?? null,
                'position_name'         => $data['position'] ?? null,
                'permanent_address'     => $data['permanent address'] ?? null,
                'present_address'       => $data['present address'] ?? null,
                'contact_number'        => $data['number'] ?? null,
                'dob'                   => !empty($data['date of birth']) ? Carbon::parse($data['date of birth'])->format('Y-m-d') : null,
'age'      => $this->extractYearsFromString($data['age'] ?? null),
'duration' => $this->extractYearsFromString($data['duration'] ?? null),
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
        // Static admin / HR users (unchanged)
        $staticUsers = [
            ['name' => 'Warlito', 'email' => 'warlito@gmail.com', 'emp_id' => 1000, 'role' => 'admin'],
            ['name' => 'Elena', 'email' => 'elena@gmail.com', 'emp_id' => 1001, 'role' => 'admin'],
            ['name' => 'Jona', 'email' => 'jona@gmail.com', 'emp_id' => 2222, 'role' => 'hr_head'],
            ['name' => 'Rica', 'email' => 'rica@gmail.com', 'emp_id' => 3333, 'role' => 'hr_head'],
        ];

        $spreadsheetEmployees = $this->parseEmployeeData();

        $dynamicUsers = [];
        foreach ($spreadsheetEmployees as $emp) {
            $dynamicUsers[] = [
                'name'     => $emp['name'],
                'email'    => $emp['email'],
                'emp_id'   => $emp['emp_code'],
                'role'     => 'employee',
                'employee_attrs' => $emp,
            ];
        }

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
     * Map M/F to Male/Female for gender column
     */
    private function mapGender(?string $gender): ?string
    {
        if (empty($gender)) return null;
        $g = strtoupper(trim($gender));
        if ($g === 'M') return 'male';
        if ($g === 'F') return 'female';
        return strtolower($gender);
    }

    /**
     * Create employee record (exclude admin users)
     */
    private function createEmployeeRecord(Position $position, User $user, Branch $branch, int $empId, ?Site $site = null, array $extraAttrs = []): void
    {
        if ($user->hasRole('admin')) {
            $this->command->info("Skipping employee record for admin: {$user->name} ({$user->email})");
            return;
        }

        // Ensure emp_code is unique (if duplicate, generate a new one)
        $empCode = $empId;
        if (Employee::where('emp_code', $empCode)->exists()) {
            $this->command->warn("Duplicate emp_code {$empCode} for {$user->name}, generating a new unique code.");
            do {
                $empCode = rand(1000000, 9999999);
            } while (Employee::where('emp_code', $empCode)->exists());
        }

        $employeeData = [
            'position_id'               => $position->id,
            'branch_id'                 => $branch->id,
            'user_id'                   => $user->id,
            'site_id'                   => $site?->id,
            'slug_emp'                  => Str::slug($user->name . '-' . $empCode),
            'emp_code'                  => $empCode,
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
            'skills'                    => !empty($extraAttrs['skills']) ? explode(',', $extraAttrs['skills']) : null,
            'age'                       => $extraAttrs['age'] ?? null,
            'gender'                    => $this->mapGender($extraAttrs['gender'] ?? null) ?? 'male',
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
 * Extract only the number of years from a string like "41 Years, 11 Months, 26 Days"
 */
private function extractYearsFromString(?string $str): ?int
{
    if (empty($str)) {
        return null;
    }
    // Match the first number before "Year" or "Years"
    if (preg_match('/(\d+)\s*Year/', $str, $matches)) {
        return (int) $matches[1];
    }
    // Fallback: try to get any leading number
    if (preg_match('/^\d+/', $str, $matches)) {
        return (int) $matches[0];
    }
    return null;
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
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            $extraAttrs = $data['employee_attrs'] ?? [];
            $user = $this->getOrCreateUser($data, $roleMap);

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

        $this->command->info('Creating employee records (excluding admin users)...');
        $nonAdminUsers = array_filter($processedUsers, fn($item) => $item['role'] !== 'admin');
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
        $this->displayStatistics();
    }
}