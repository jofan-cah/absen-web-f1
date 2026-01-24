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
        // 1. Create Departments based on actual company structure
        $departments = [
            ['name' => 'General Support', 'code' => 'GS'],
            ['name' => 'Commercial Product', 'code' => 'CP'],
            ['name' => 'Technical Support', 'code' => 'TS'],
            ['name' => 'Support & Administration', 'code' => 'SA'],
            ['name' => 'Technology Operations & Product', 'code' => 'TOP'],
            ['name' => 'Technical Support Aktivasi, Maintenance, Expand 1', 'code' => 'TSAME1'],
            ['name' => 'Technical Support Aktivasi, Maintenance, Expand 2', 'code' => 'TSAME2'],
            ['name' => 'Technical Support Aktivasi, Maintenance, Expand 3', 'code' => 'TSAME3'],
            ['name' => 'Technical Support Aktivasi, Maintenance, Expand 4', 'code' => 'TSAME4'],
            ['name' => 'Operasional Technology & Inovation', 'code' => 'OTI'],
            ['name' => 'Network Operation & Maintenance', 'code' => 'NOM'],
            ['name' => 'Billing & Collection', 'code' => 'BC'],
            ['name' => 'Support & Public Relation', 'code' => 'SPR'],
            ['name' => 'Team 1 Network Operation Center', 'code' => 'NOC1'],
            ['name' => 'Team 2 Network Operation Center', 'code' => 'NOC2'],
            ['name' => 'Team 3 Network Operation Center', 'code' => 'NOC3'],
            ['name' => 'Infrastructure & Asset', 'code' => 'IA'],
            ['name' => 'Digital Creative', 'code' => 'DC'],
            ['name' => 'Product Development', 'code' => 'PD'],
            ['name' => 'Warehouse & Logistic', 'code' => 'WL'],
            ['name' => 'Administration', 'code' => 'ADM'],
            ['name' => 'Compliance & Purchasing', 'code' => 'COMP'],
            ['name' => 'Customer Support', 'code' => 'CS'],
            ['name' => 'Customer Relation Officer', 'code' => 'CRO'],
            ['name' => 'Purchasing, Tax, & Asset', 'code' => 'PTA'],
            ['name' => 'Software Development', 'code' => 'SD'],
            ['name' => 'Project Fiber Optic Management', 'code' => 'PFOM'],
            ['name' => 'Legal Advice', 'code' => 'LA'],
            ['name' => 'Procurement Logistic', 'code' => 'PL'],
            ['name' => 'Technical Support Aktivasi Fiber Optic 1', 'code' => 'TSAFO1'],
            ['name' => 'Technical Support Gangguan Fiber Optic 1', 'code' => 'TSGFO1'],
            ['name' => 'Infrastructure, Technical Support, Permit', 'code' => 'ITSP'],
            ['name' => 'Management', 'code' => 'MGT'],
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
            $departmentIds[$dept['name']] = $department->department_id;
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
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'break_start' => '18:00:00',
                'break_end' => '19:00:00',
                'break_duration' => 60,
            ],
            [
                'name' => 'Shift Malam',
                'code' => 'MALAM',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'break_duration' => 60,
                'is_overnight' => true,
            ],
            [
                'name' => 'OnCall',
                'code' => 'ONCALL',
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'break_start' => null,
                'break_end' => null,
                'break_duration' => 0,
            ],
        ];

        foreach ($shifts as $index => $shift) {
            // OnCall pakai ID khusus 'SHIFT-ONCALL' sesuai controller
            $shiftId = $shift['code'] === 'ONCALL'
                ? 'SHIFT-ONCALL'
                : 'SHF' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Shift::create([
                'shift_id' => $shiftId,
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

        // 3. Karyawan Data - 61 employees
        $karyawanData = [
            // NO 1-15
            ['no' => 1, 'nama' => 'Kuncoro Fendi Nugroho', 'department' => 'General Support', 'status' => 'Staff'],
            ['no' => 2, 'nama' => 'Wisnu Prasetyo', 'department' => 'Commercial Product', 'status' => 'Kepala Bidang'],
            ['no' => 3, 'nama' => 'Mahfud Saifudzin', 'department' => 'Technical Support', 'status' => 'Wakil Kepala Bidang'],
            ['no' => 4, 'nama' => 'Agha Denisvar', 'department' => 'Support & Administration', 'status' => 'Kepala Bidang'],
            ['no' => 5, 'nama' => 'Rohmadi', 'department' => 'General Support', 'status' => 'Staff'],
            ['no' => 6, 'nama' => 'Sonya Mahardika Andriano Saputra', 'department' => 'Management', 'status' => 'Vice General Manager'],
            ['no' => 7, 'nama' => 'Arief Nur Huda', 'department' => 'Technology Operations & Product', 'status' => 'Kepala Bidang'],
            ['no' => 8, 'nama' => 'Supriyanto', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 3', 'status' => 'Supervisor'],
            ['no' => 9, 'nama' => 'Azis Syahrul D', 'department' => 'Technical Support', 'status' => 'Kepala Bidang'],
            ['no' => 10, 'nama' => 'Muhammad Fauzan Fahmi', 'department' => 'Operasional Technology & Inovation', 'status' => 'Manager'],
            ['no' => 11, 'nama' => 'Ikhsan Rizki Pambudi', 'department' => 'Network Operation & Maintenance', 'status' => 'Kepala Bidang'],
            ['no' => 12, 'nama' => 'Adhin Nila Krisna', 'department' => 'Billing & Collection', 'status' => 'Supervisor'],
            ['no' => 13, 'nama' => 'Widiastuti Ayuningrum', 'department' => 'Support & Public Relation', 'status' => 'Kepala Bidang'],
            ['no' => 14, 'nama' => 'Arif Wijayanto', 'department' => 'Team 1 Network Operation Center', 'status' => 'Leader'],
            ['no' => 15, 'nama' => 'Joko Prastiyo', 'department' => 'Infrastructure & Asset', 'status' => 'Staff'],

            // NO 16-29
            ['no' => 16, 'nama' => 'Bambang Parikesiet', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 1', 'status' => 'Supervisor'],
            ['no' => 17, 'nama' => 'Alfan Cahyo Nugroho', 'department' => 'Team 3 Network Operation Center', 'status' => 'Leader'],
            ['no' => 18, 'nama' => 'Hafid Kurniawan', 'department' => 'Team 2 Network Operation Center', 'status' => 'Leader'],
            ['no' => 19, 'nama' => 'Rois Hudaf Kurniawan', 'department' => 'Digital Creative', 'status' => 'Supervisor'],
            ['no' => 20, 'nama' => 'Catur Apriyanto Saputro', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 2', 'status' => 'Supervisor'],
            ['no' => 21, 'nama' => 'Hasbibi Fahmi Jami', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 1', 'status' => 'Staff'],
            ['no' => 22, 'nama' => 'Ardi Risdiyanto', 'department' => 'Technical Support Aktivasi Fiber Optic 1', 'status' => 'Staff'],
            ['no' => 23, 'nama' => 'Prasetyo Bayu Aji', 'department' => 'Product Development', 'status' => 'Staff'],
            ['no' => 24, 'nama' => 'Syahri S', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 2', 'status' => 'Staff'],
            ['no' => 25, 'nama' => 'Satrio Damar Alam Pambudi', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 4', 'status' => 'Staff'],
            ['no' => 26, 'nama' => 'Irfan Afrizal', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 3', 'status' => 'Staff'],
            ['no' => 27, 'nama' => 'Nisa Triutami', 'department' => 'Warehouse & Logistic', 'status' => 'Supervisor'],
            ['no' => 28, 'nama' => 'Rivan Meilano Chandra Bintang S', 'department' => 'Digital Creative', 'status' => 'Staff'],
            ['no' => 29, 'nama' => 'Mashitoh Diva Az Zahra', 'department' => 'Administration', 'status' => 'Supervisor'],

            // NO 30-43
            ['no' => 30, 'nama' => 'Muhammad Firman Hidayattuloh', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 4', 'status' => 'Supervisor'],
            ['no' => 31, 'nama' => 'Friska Ema Aiyana', 'department' => 'Compliance & Purchasing', 'status' => 'Kepala Bidang'],
            ['no' => 32, 'nama' => 'Reyhan Yafi Setiaji', 'department' => 'Warehouse & Logistic', 'status' => 'Staff'],
            ['no' => 33, 'nama' => 'Nessa Hadiyani', 'department' => 'Billing & Collection', 'status' => 'Staff'],
            ['no' => 34, 'nama' => 'Umi Khasum Ambarwati', 'department' => 'Commercial Product', 'status' => 'Supervisor'],
            ['no' => 35, 'nama' => 'Tia Kristanti', 'department' => 'Customer Support', 'status' => 'Staff'],
            ['no' => 36, 'nama' => 'Rizka Aprilianingrum', 'department' => 'Customer Relation Officer', 'status' => 'Staff'],
            ['no' => 37, 'nama' => 'Bondan Kinanti Wahyu Hapsari', 'department' => 'Purchasing, Tax, & Asset', 'status' => 'Supervisor'],
            ['no' => 38, 'nama' => 'Jofan Fathurahman', 'department' => 'Software Development', 'status' => 'Kepala Bidang'],
            ['no' => 39, 'nama' => 'Eka Ali Fauzi', 'department' => 'Infrastructure & Asset', 'status' => 'Supervisor'],
            ['no' => 40, 'nama' => 'Rahmad Solikhin', 'department' => 'Project Fiber Optic Management', 'status' => 'Staff'],
            ['no' => 41, 'nama' => 'Agung Wibowo', 'department' => 'Technical Support Aktivasi Fiber Optic 1', 'status' => 'Staff'],
            ['no' => 42, 'nama' => 'Bayu Aji Saputra', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 1', 'status' => 'Staff'],
            ['no' => 43, 'nama' => 'Mischa Ahmad Syarifudin', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 2', 'status' => 'Staff'],

            // NO 44-57
            ['no' => 44, 'nama' => 'Dhito Kyan Saputro', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 4', 'status' => 'Staff'],
            ['no' => 45, 'nama' => 'Fahmi Lumidha Awaliyah', 'department' => 'Warehouse & Logistic', 'status' => 'Staff'],
            ['no' => 46, 'nama' => 'Dina Lutfiatinisa', 'department' => 'Administration', 'status' => 'Staff'],
            ['no' => 47, 'nama' => 'Mareta Uzi Hanifah', 'department' => 'Procurement Logistic', 'status' => 'Staff'],
            ['no' => 48, 'nama' => 'Vita Rahmawati', 'department' => 'Legal Advice', 'status' => 'Staff'],
            ['no' => 49, 'nama' => 'Berliyana Juliyana Wati', 'department' => 'Administration', 'status' => 'Staff'],
            ['no' => 50, 'nama' => 'Muhamad Ihsan Qairi', 'department' => 'Technical Support Aktivasi, Maintenance, Expand 3', 'status' => 'Staff'],
            ['no' => 51, 'nama' => 'Wahyu Nugraha', 'department' => 'Commercial Product', 'status' => 'Staff'],
            ['no' => 52, 'nama' => 'Andri Tri Wibowo', 'department' => 'Technical Support Aktivasi Fiber Optic 1', 'status' => 'Supervisor'],
            ['no' => 53, 'nama' => 'Feri Adi Prabowo', 'department' => 'Technical Support Aktivasi Fiber Optic 1', 'status' => 'Staff'],
            ['no' => 54, 'nama' => 'Egix Aditya', 'department' => 'Technical Support Gangguan Fiber Optic 1', 'status' => 'Supervisor'],
            ['no' => 55, 'nama' => 'Toni Setiyawan', 'department' => 'Technical Support Gangguan Fiber Optic 1', 'status' => 'Staff'],
            ['no' => 56, 'nama' => 'Raditya Henry Prayoga', 'department' => 'Technical Support Gangguan Fiber Optic 1', 'status' => 'Staff'],
            ['no' => 57, 'nama' => 'Aleysia Alimka Julia Indahsari', 'department' => 'Procurement Logistic', 'status' => 'Supervisor'],

            // NO 58-61
            ['no' => 58, 'nama' => 'Tiara Fitria Wulandari', 'department' => 'Customer Relation Officer', 'status' => 'Staff'],
            ['no' => 59, 'nama' => 'Fajri Aprian', 'department' => 'Digital Creative', 'status' => 'Staff'],
            ['no' => 60, 'nama' => 'Rizky Aji Pamungkas', 'department' => 'Infrastructure, Technical Support, Permit', 'status' => 'Manager Operasional'],
            ['no' => 61, 'nama' => 'Arfian Deva Pratama', 'department' => 'Project Fiber Optic Management', 'status' => 'Kepala Bidang'],
        ];

        // Map status to staff_status (ENUM: staff, koordinator, pkwtt)
        $staffStatusMapping = [
            'Staff' => 'staff',
            'Supervisor' => 'koordinator',
            'Leader' => 'koordinator',
            'Kepala Bidang' => 'pkwtt',
            'Wakil Kepala Bidang' => 'pkwtt',
            'Manager' => 'koordinator',
            'Manager Operasional' => 'koordinator',
            'Vice General Manager' => 'koordinator',
        ];

        // Female names for gender detection
        $femaleNames = [
            'sonya', 'widiastuti', 'nisa', 'mashitoh', 'friska', 'nessa', 'umi', 'tia', 'rizka',
            'bondan', 'fahmi', 'dina', 'mareta', 'vita', 'berliyana', 'aleysia', 'tiara'
        ];

        // Phone prefixes
        $phoneNumbers = ['0812', '0813', '0821', '0822', '0852', '0853', '0856', '0857', '0858'];
        $birthYears = range(1985, 2000);

        foreach ($karyawanData as $data) {
            $no = $data['no'];
            $paddedNo = str_pad($no, 3, '0', STR_PAD_LEFT);

            // Generate NIP: 01 + 25 + 01 + no (contoh: 0125010001)
            $nip = '012501' . $paddedNo;

            // Generate random data
            $phonePrefix = $phoneNumbers[array_rand($phoneNumbers)];
            $phoneNumber = $phonePrefix . rand(10000000, 99999999);

            $birthYear = $birthYears[array_rand($birthYears)];
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);
            $birthDate = Carbon::createFromDate($birthYear, $birthMonth, $birthDay);

            // Generate email
            $emailName = strtolower(str_replace([' ', "'"], ['.', ''], $data['nama']));
            $emailName = preg_replace('/[^a-z0-9.]/', '', $emailName);
            $email = $emailName . '@company.com';

            // Determine role - Vice General Manager is admin
            $role = $data['status'] === 'Vice General Manager' ? 'admin' : 'karyawan';

            // Create User
            $user = User::create([
                'user_id' => 'USR' . $paddedNo,
                'nip' => $nip,
                'name' => $data['nama'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => $role,
                'is_active' => true,
            ]);

            // Determine gender
            $firstName = strtolower(explode(' ', $data['nama'])[0]);
            $gender = in_array($firstName, $femaleNames) ? 'P' : 'L';

            // Get department ID
            $departmentId = $departmentIds[$data['department']] ?? $departmentIds['General Support'];

            // Get staff status
            $staffStatus = $staffStatusMapping[$data['status']] ?? 'karyawan';

            // Create Karyawan
            Karyawan::create([
                'karyawan_id' => 'KAR' . $paddedNo,
                'user_id' => $user->user_id,
                'department_id' => $departmentId,
                'nip' => $nip,
                'full_name' => $data['nama'],
                'position' => $data['status'],
                'phone' => $phoneNumber,
                'address' => 'Alamat ' . $data['nama'],
                'hire_date' => Carbon::parse('2025-01-01'),
                'birth_date' => $birthDate,
                'gender' => $gender,
                'employment_status' => 'active',
                'staff_status' => $staffStatus,
            ]);
        }

        // Update department managers (Kepala Bidang as manager)
        $this->updateDepartmentManagers($departmentIds);

        $this->command->info('');
        $this->command->info('=============================================');
        $this->command->info('      DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('=============================================');
        $this->command->info('');
        $this->command->info('Created:');
        $this->command->info('  - ' . count($departments) . ' departments');
        $this->command->info('  - ' . count($shifts) . ' shifts');
        $this->command->info('  - ' . count($karyawanData) . ' users & karyawans');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  Admin (Vice GM): 0125010006 / password123');
        $this->command->info('  Karyawan: 0125010001 / password123');
        $this->command->info('');
        $this->command->info('NIP Format: 01 + 25 + 01 + XXX');
        $this->command->info('  (Company Code + Year + Month + Employee No)');
        $this->command->info('=============================================');
    }

    private function updateDepartmentManagers($departmentIds)
    {
        // Set Kepala Bidang as department managers
        $kepalaBidang = Karyawan::where('position', 'Kepala Bidang')
            ->orWhere('position', 'Wakil Kepala Bidang')
            ->with('user')
            ->get();

        foreach ($kepalaBidang as $kepala) {
            Department::where('department_id', $kepala->department_id)
                ->whereNull('manager_user_id')
                ->update(['manager_user_id' => $kepala->user_id]);
        }
    }
}
