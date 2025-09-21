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
        // 1. Create Departments
        $departments = [
            ['name' => 'IT Department', 'code' => 'IT'],
            ['name' => 'Technician', 'code' => 'TECH'],
            ['name' => 'Network Operations Center', 'code' => 'NOC'],
            ['name' => 'Administration', 'code' => 'ADM'],
            ['name' => 'Pre Sales', 'code' => 'PRESALES'],
            ['name' => 'Customer Service', 'code' => 'CS'],
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

        // 3. Create Users & Karyawans from Excel data
        $karyawanData = [
            ['nama' => 'JOFAN FATHURAHMAN', 'posisi' => 'ADMIN', 'departements' => 'IT', 'alamat' => 'Jl. Admin No. 1, Klaten', 'tanggal_masuk' => '2024-12-12'],
            ['nama' => 'Agus Prabowo', 'posisi' => 'COORDINATOR', 'departements' => 'TECHNICIAN', 'alamat' => 'Gantiwarno, Klaten'],
            ['nama' => 'Irfan Ardiansyah', 'posisi' => 'COORDINATOR WAKIL', 'departements' => 'TECHNICIAN', 'alamat' => 'Watu Kelir, Klaten'],
            ['nama' => 'Agus Darmawan', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Watu Kelir, Klaten'],
            ['nama' => 'Ahmad Fauzi', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Watu Kelir, Klaten'],
            ['nama' => 'Rangga Widodo', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Jl. Widodo No. 15, Klaten', 'tanggal_masuk' => '2025-01-17'],
            ['nama' => 'Rayhan Nursahin', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Jogonalan, Klaten', 'tanggal_masuk' => '2025-01-17'],
            ['nama' => 'Said Alidrus', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Jogonalan, Klaten'],
            ['nama' => 'Basuki Danar Tomo', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Watu Kelir, Klaten'],
            ['nama' => 'Ridho Kurniawan', 'posisi' => 'STAFF', 'departements' => 'TECHNICIAN', 'alamat' => 'Jl. Kurniawan No. 8, Klaten'],
            ['nama' => 'Kayis Fadillah', 'posisi' => 'STAFF', 'departements' => 'NOC', 'alamat' => 'Prambanan, Klaten', 'tanggal_masuk' => '2025-02-20'],
            ['nama' => 'Rizky Agung Saputra', 'posisi' => 'STAFF', 'departements' => 'NOC', 'alamat' => 'Watu Kelir, Klaten'],
            ['nama' => 'Regal Fairuz Albar', 'posisi' => 'COORDINATOR', 'departements' => 'NOC', 'alamat' => 'Prambanan, Klaten', 'tanggal_masuk' => '2025-01-17'],
            ['nama' => 'Ayu Mutiara', 'posisi' => 'STAFF', 'departements' => 'ADM', 'alamat' => 'Jl. Mutiara No. 12, Klaten'],
            ['nama' => 'Vinanda Salma', 'posisi' => 'STAFF', 'departements' => 'ADM', 'alamat' => 'Jl. Salma No. 5, Klaten'],
            ['nama' => 'Anisa Novita Salma', 'posisi' => 'COORDINATOR', 'departements' => 'PRESALES', 'alamat' => 'Jimbung, Klaten'],
            ['nama' => 'Sri Niyati', 'posisi' => 'STAFF', 'departements' => 'PRESALES', 'alamat' => 'Deles, Klaten'],
            ['nama' => 'Ernida Kumala', 'posisi' => 'STAFF', 'departements' => 'PRESALES', 'alamat' => 'Jogonalan, Klaten'],
            ['nama' => 'Kharisma Yogi', 'posisi' => 'STAFF', 'departements' => 'PRESALES', 'alamat' => 'Ceper, Klaten'],
            ['nama' => 'Novi Astuti', 'posisi' => 'STAFF', 'departements' => 'CS', 'alamat' => 'Manisrenggo, Klaten'],
            ['nama' => 'Dwita', 'posisi' => 'COORDINATOR', 'departements' => 'CS', 'alamat' => 'Trucuk, Klaten'],
        ];

        // Phone numbers for random generation
        $phoneNumbers = ['0812', '0813', '0821', '0822', '0852', '0853', '0856', '0857', '0858'];

        // Birth years for random generation
        $birthYears = range(1985, 2000);

        $userCounter = 1;
        $karyawanCounter = 1;

        foreach ($karyawanData as $data) {
            // Map department
            $deptMapping = [
                'IT' => 'IT',
                'TECHNICIAN' => 'TECH',
                'NOC' => 'NOC',
                'ADM' => 'ADM',
                'PRESALES' => 'PRESALES',
                'CS' => 'CS',
            ];

            // Map position to staff_status
            $staffStatusMapping = [
                'ADMIN' => 'koordinator',
                'COORDINATOR' => 'koordinator',
                'COORDINATOR WAKIL' => 'wakil_koordinator',
                'STAFF' => 'staff',
            ];

            // Generate random data
            $phonePrefix = $phoneNumbers[array_rand($phoneNumbers)];
            $phoneNumber = $phonePrefix . rand(10000000, 99999999);

            $birthYear = $birthYears[array_rand($birthYears)];
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);
            $birthDate = Carbon::createFromDate($birthYear, $birthMonth, $birthDay);

            // Generate hire date
            $hireDate = isset($data['tanggal_masuk'])
                ? Carbon::parse($data['tanggal_masuk'])
                : Carbon::createFromDate(rand(2020, 2024), rand(1, 12), rand(1, 28));

            // Generate email
            $emailName = strtolower(str_replace(' ', '.', $data['nama']));
            $email = $emailName . '@company.com';

            // Generate NIP
            $nipNumber = str_pad($userCounter, 3, '0', STR_PAD_LEFT);
            $nip = 'NIP' . $nipNumber;

            // Determine role (first person is admin, rest are karyawan)
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

            // Create Karyawan
            Karyawan::create([
                'karyawan_id' => 'KAR' . str_pad($karyawanCounter, 3, '0', STR_PAD_LEFT),
                'user_id' => $user->user_id,
                'department_id' => $departmentIds[$deptMapping[$data['departements']]],
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
        $this->command->info('🔐 Login credentials:');
        $this->command->info('   Admin: NIP001 / password123');
        $this->command->info('   Karyawan: NIP002, NIP003, ... / password123');
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
