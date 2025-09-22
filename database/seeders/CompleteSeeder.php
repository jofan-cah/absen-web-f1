<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Departments based on actual data from Excel
        $departments = [
            ['name' => 'Programmer', 'code' => 'PROG'],
            ['name' => 'Technical Support', 'code' => 'TECH'],
            ['name' => 'Project Management Fiber Optic', 'code' => 'PMO'],
            ['name' => 'Network Operation Center', 'code' => 'NOC'],
            ['name' => 'Logistic Warehouse', 'code' => 'LOG'],
            ['name' => 'Administrasi', 'code' => 'ADM'],
            ['name' => 'Presales Officer', 'code' => 'PRESALES'],
            ['name' => 'Customer Support', 'code' => 'CS'],
        ];

        $departmentIds = [];
        foreach ($departments as $index => $dept) {
            $department = Department::create([
                'department_id' => 'DEPT' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => $dept['name'],
                'code' => $dept['code'],
                'description' => $dept['name'] . ' Department',
                'is_active' => true,
            ]);
            $departmentIds[$dept['code']] = $department->department_id;
        }

        // 2. Create Shifts
        $shifts = [
            [
                'name' => 'Shift Pagi',
                'code' => 'PAGI',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'break_duration' => 60,
            ],
            [
                'name' => 'Shift Siang',
                'code' => 'SIANG',
                'start_time' => '13:00:00',
                'end_time' => '22:00:00',
                'break_start' => '18:00:00',
                'break_end' => '19:00:00',
                'break_duration' => 60,
            ],
            [
                'name' => 'Shift Malam',
                'code' => 'MALAM',
                'start_time' => '22:00:00',
                'end_time' => '07:00:00',
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'break_duration' => 60,
                'is_overnight' => true,
            ],
        ];

        foreach ($shifts as $index => $shift) {
            Shift::create([
                'shift_id' => 'SHF' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => $shift['name'],
                'code' => $shift['code'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'break_start' => $shift['break_start'],
                'break_end' => $shift['break_end'],
                'break_duration' => $shift['break_duration'],
                'late_tolerance' => 15,
                'early_checkout_tolerance' => 15,
                'is_overnight' => $shift['is_overnight'] ?? false,
                'is_active' => true,
            ]);
        }

        // 3. Create Users & Karyawans from actual Excel data (sorted by hire date from oldest to newest)
        $karyawanData = [
            ['nama' => 'Sonya', 'posisi' => 'COORDINATOR', 'departements' => 'Technical Support', 'alamat' => 'Klaten Utara', 'tanggal_masuk' => '2020-01-12'],
            ['nama' => 'Agus Prabowo', 'posisi' => 'COORDINATOR', 'departements' => 'Technical Support', 'alamat' => 'Gantiwarno', 'tanggal_masuk' => '2021-09-04'],
            ['nama' => 'Kharisma Yogi A', 'posisi' => 'STAFF', 'departements' => 'Presales Officer', 'alamat' => 'Ceper, Klaten', 'tanggal_masuk' => '2022-07-16'],
            ['nama' => 'Anisa Novita Salma', 'posisi' => 'COORDINATOR', 'departements' => 'Presales Officer', 'alamat' => 'Jimbung, Klaten', 'tanggal_masuk' => '2023-06-04'],
            ['nama' => 'Sri Niyati', 'posisi' => 'STAFF', 'departements' => 'Presales Officer', 'alamat' => 'Deles, Klaten', 'tanggal_masuk' => '2023-10-29'],
            ['nama' => 'Dwita Putri R', 'posisi' => 'COORDINATOR', 'departements' => 'Customer Support', 'alamat' => 'Trucuk, Klaten', 'tanggal_masuk' => '2023-10-29'],
            ['nama' => 'Irfan Ardiansyah', 'posisi' => 'COORDINATOR', 'departements' => 'Project Management Fiber Optic', 'alamat' => 'Watu Kelir', 'tanggal_masuk' => '2024-05-16'],
            ['nama' => 'Agus Darmawan', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Watu Kelir', 'tanggal_masuk' => '2024-05-16'],
            ['nama' => 'Basuki Danar Tomo', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Watu Kelir', 'tanggal_masuk' => '2024-05-16'],
            ['nama' => 'JOFAN FATHURAHMAN', 'posisi' => 'COORDINATOR', 'departements' => 'PROGRAMMER', 'alamat' => 'isi sesuai ktp', 'tanggal_masuk' => '2024-12-12'],
            ['nama' => 'Ernida Kumalasari', 'posisi' => 'STAFF', 'departements' => 'Presales Officer', 'alamat' => 'Jogonalan, Klaten', 'tanggal_masuk' => '2025-04-16'],
            ['nama' => 'Novi Astuti', 'posisi' => 'STAFF', 'departements' => 'Customer Support', 'alamat' => 'Manisrenggo, Klaten', 'tanggal_masuk' => '2025-04-16'],
            ['nama' => 'Rangga Widodo S', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Jogonalan', 'tanggal_masuk' => '2025-04-27'],
            ['nama' => 'Rayhan Nursahin', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Jogonalan', 'tanggal_masuk' => '2025-04-27'],
            ['nama' => 'Regal Fairuz Albar', 'posisi' => 'COORDINATOR', 'departements' => 'Network Operation Center', 'alamat' => 'Prambanan, Klaten', 'tanggal_masuk' => '2025-04-27'],
            ['nama' => 'Ahmad Fauzi', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Watu Kelir', 'tanggal_masuk' => '2025-05-12'],
            ['nama' => 'Said Aldi Al Idrus', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Jogonalan', 'tanggal_masuk' => '2025-05-12'],
            ['nama' => 'Rizky Agung Saputra', 'posisi' => 'COORDINATOR', 'departements' => 'Logistic Warehouse', 'alamat' => 'Watu Kelir', 'tanggal_masuk' => '2025-05-12'],
            ['nama' => 'Vinanda Salma A', 'posisi' => 'STAFF', 'departements' => 'Administrasi', 'alamat' => 'Jl. Salma No. 5, Klaten', 'tanggal_masuk' => '2025-06-30'],
            ['nama' => 'Ayu Mutiara A', 'posisi' => 'COORDINATOR', 'departements' => 'Administrasi', 'alamat' => 'Jl. Mutiara No. 12, Klaten', 'tanggal_masuk' => '2025-08-03'],
            ['nama' => 'Ridho Kurniawan', 'posisi' => 'STAFF', 'departements' => 'Technical Support', 'alamat' => 'Jl. Kurniawan No. 8, Klaten', 'tanggal_masuk' => '2025-08-04'],
            ['nama' => 'Kayis Fadillah', 'posisi' => 'STAFF', 'departements' => 'Network Operation Center', 'alamat' => 'Prambanan, Klaten', 'tanggal_masuk' => '2025-09-08'],
        ];

        // Phone numbers for random generation
        $phoneNumbers = ['0812', '0813', '0821', '0822', '0852', '0853', '0856', '0857', '0858'];

        // Birth years for random generation
        $birthYears = range(1985, 2000);

        // Map department names to codes
        $deptMapping = [
            'PROGRAMMER' => 'PROG',
            'Programmer' => 'PROG',
            'Technical Support' => 'TECH',
            'Project Management Fiber Optic' => 'PMO',
            'Network Operation Center' => 'NOC',
            'Logistic Warehouse' => 'LOG',
            'Administrasi' => 'ADM',
            'Presales Officer' => 'PRESALES',
            'Customer Support' => 'CS',
        ];

        // Map position to staff_status
        $staffStatusMapping = [
            'COORDINATOR' => 'koordinator',
            'STAFF' => 'staff',
        ];

        $userCounter = 1;
        $karyawanCounter = 1;

        foreach ($karyawanData as $data) {
            // Parse hire date
            $hireDate = Carbon::parse($data['tanggal_masuk']);

            // Generate NIP with format: 01 (company code) + YYMM + urut 3 digit
            $companyCode = '01';
            $year = $hireDate->format('y');  // 2 digit year (20, 21, etc)
            $month = $hireDate->format('m'); // 2 digit month (01, 02, etc)
            $urut = str_pad($userCounter, 3, '0', STR_PAD_LEFT); // 3 digit counter
            $nip = $companyCode . $year . $month . $urut;

            // Generate random data
            $phonePrefix = $phoneNumbers[array_rand($phoneNumbers)];
            $phoneNumber = $phonePrefix . rand(10000000, 99999999);

            $birthYear = $birthYears[array_rand($birthYears)];
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);
            $birthDate = Carbon::createFromDate($birthYear, $birthMonth, $birthDay);

            // Generate email from name
            $emailName = strtolower(str_replace(' ', '.', $data['nama']));
            $emailName = preg_replace('/[^a-z0-9.]/', '', $emailName); // Remove special characters
            $email = $emailName . '@company.com';

            // Determine role (first person in sorted list is admin - Sonya as the oldest/most senior employee)
            $role = $userCounter === 1 ? 'admin' : 'karyawan';

            // Create User
            $user = User::create([
                'user_id' => 'USR' . str_pad($userCounter, 3, '0', STR_PAD_LEFT),
                'nip' => $nip,
                'name' => $data['nama'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => $role,
                'is_active' => true,
            ]);

            // Determine gender from name (simple logic)
            $femaleNames = ['ayu', 'vinanda', 'anisa', 'sri', 'ernida', 'novi', 'dwita'];
            $firstName = strtolower(explode(' ', $data['nama'])[0]);
            $gender = in_array($firstName, $femaleNames) ? 'P' : 'L';

            // Get department code
            $deptCode = $deptMapping[$data['departements']] ?? 'TECH';
            $departmentId = $departmentIds[$deptCode];

            // Create Karyawan
            Karyawan::create([
                'karyawan_id' => 'KAR' . str_pad($karyawanCounter, 3, '0', STR_PAD_LEFT),
                'user_id' => $user->user_id,
                'department_id' => $departmentId,
                'nip' => $nip, // Same NIP as user
                'full_name' => $data['nama'],
                'position' => $data['posisi'],
                'phone' => $phoneNumber,
                'address' => $data['alamat'],
                'hire_date' => $hireDate,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'employment_status' => 'active',
                'staff_status' => $staffStatusMapping[$data['posisi']],
            ]);

            $userCounter++;
            $karyawanCounter++;
        }

        // Update department managers
        $this->updateDepartmentManagers($departmentIds);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   • ' . count($departments) . ' departments');
        $this->command->info('   • ' . count($shifts) . ' shifts');
        $this->command->info('   • ' . count($karyawanData) . ' users & karyawans');
        $this->command->info('');
        $this->command->info('🔐 Login credentials (using NIP):');
        $this->command->info('   Admin: 012001001 / password123 (Sonya - Most Senior Employee)');
        $this->command->info('   Karyawan: 012109002 / password123 (Agus Prabowo)');
        $this->command->info('   Karyawan: 012207003 / password123 (Kharisma Yogi A)');
        $this->command->info('   ... dan seterusnya');
        $this->command->info('');
        $this->command->info('📝 NIP Format Examples (01 = Company Code):');
        $this->command->info('   • 012001001 = 01(company) + 20(year) + 01(month) + 001(sequence)');
        $this->command->info('   • 012109002 = 01(company) + 21(year) + 09(month) + 002(sequence)');
        $this->command->info('   • 012207003 = 01(company) + 22(year) + 07(month) + 003(sequence)');
        $this->command->info('   Format: 01 + YY + MM + 001-999 (based on hire date from oldest)');
    }

    private function updateDepartmentManagers($departmentIds)
    {
        // Set coordinators as department managers
        $coordinators = Karyawan::where('staff_status', 'koordinator')
            ->with('user')
            ->get();

        foreach ($coordinators as $coordinator) {
            Department::where('department_id', $coordinator->department_id)
                ->update(['manager_user_id' => $coordinator->user_id]);
        }
    }
}
