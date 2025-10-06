<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ijin;
use App\Models\IjinType;
use App\Models\Karyawan;
use App\Models\User;
use Carbon\Carbon;

class IjinTestSeeder extends Seeder
{
    public function run()
    {
        $karyawan = Karyawan::where('karyawan_id', 'KAR021')->first();

        if (!$karyawan) {
            $this->command->error('Karyawan KAR021 tidak ditemukan!');
            return;
        }

        $coordinatorKaryawan = Karyawan::where('karyawan_id', 'KAR002')->first();

        if (!$coordinatorKaryawan) {
            $this->command->error('Karyawan KAR002 (Coordinator) tidak ditemukan!');
            return;
        }

        $coordinator = User::where('user_id', $coordinatorKaryawan->user_id)->first();

        if (!$coordinator) {
            $this->command->error('User untuk coordinator KAR002 tidak ditemukan!');
            return;
        }

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->error('Admin tidak ditemukan!');
            return;
        }

        $ijinTypes = IjinType::where('is_active', true)->get();

        if ($ijinTypes->isEmpty()) {
            $this->command->error('Tidak ada ijin type yang aktif!');
            return;
        }

        $this->command->info('Membuat sample ijin untuk KAR021...');
        $this->command->info('Coordinator: KAR002 (' . $coordinator->name . ')');
        $this->command->info('');

        // 1. PENDING - Menunggu review coordinator
        Ijin::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'ijin_type_id' => $ijinTypes->where('code', 'sick')->first()?->ijin_type_id ?? $ijinTypes->first()->ijin_type_id,
            'date_from' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'date_to' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'reason' => 'Sakit demam tinggi, perlu istirahat',
            'status' => 'pending',
            'coordinator_id' => $coordinator->user_id,
            'coordinator_status' => 'pending',
            'admin_status' => 'pending',
        ]);
        $this->command->info('✓ Created: PENDING (Coordinator belum review)');

        // 2. PENDING - Coordinator approved, menunggu admin
        Ijin::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'ijin_type_id' => $ijinTypes->where('code', 'annual')->first()?->ijin_type_id ?? $ijinTypes->first()->ijin_type_id,
            'date_from' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'date_to' => Carbon::now()->addDays(9)->format('Y-m-d'),
            'reason' => 'Cuti tahunan untuk liburan keluarga',
            'status' => 'pending',
            'coordinator_id' => $coordinator->user_id,
            'coordinator_status' => 'approved',
            'coordinator_note' => 'Approved, tidak ada bentrok jadwal',
            'coordinator_reviewed_at' => Carbon::now()->subHours(2),
            'admin_status' => 'pending',
        ]);
        $this->command->info('✓ Created: PENDING (Coordinator approved, menunggu admin)');

        // ✅ 3. APPROVED - Buat dengan status pending dulu, baru update
        $approvedIjin = Ijin::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'ijin_type_id' => $ijinTypes->where('code', 'personal')->first()?->ijin_type_id ?? $ijinTypes->first()->ijin_type_id,
            'date_from' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'date_to' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'reason' => 'Ada keperluan keluarga mendesak',
            'status' => 'pending', // ✅ Pending dulu
            'coordinator_id' => $coordinator->user_id,
            'coordinator_status' => 'pending',
            'admin_status' => 'pending',
        ]);

        // ✅ Update manual tanpa trigger event updated
        $approvedIjin->timestamps = false; // Disable timestamps
        $approvedIjin->coordinator_status = 'approved';
        $approvedIjin->coordinator_note = 'Approved';
        $approvedIjin->coordinator_reviewed_at = Carbon::now()->subDays(6);
        $approvedIjin->admin_id = $admin->user_id;
        $approvedIjin->admin_status = 'approved';
        $approvedIjin->admin_note = 'Approved by admin';
        $approvedIjin->admin_reviewed_at = Carbon::now()->subDays(5)->addHours(2);
        $approvedIjin->status = 'approved';
        $approvedIjin->saveQuietly(); // ✅ Save tanpa trigger event

        $this->command->info('✓ Created: APPROVED (Data historis)');

        // 4. REJECTED - Rejected by coordinator
        Ijin::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'ijin_type_id' => $ijinTypes->first()->ijin_type_id,
            'date_from' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'date_to' => Carbon::now()->addDays(16)->format('Y-m-d'),
            'reason' => 'Ingin liburan panjang',
            'status' => 'rejected',
            'coordinator_id' => $coordinator->user_id,
            'coordinator_status' => 'rejected',
            'coordinator_note' => 'Ditolak karena ada project penting yang harus diselesaikan minggu ini',
            'coordinator_reviewed_at' => Carbon::now()->subDays(1),
            'admin_status' => 'pending',
        ]);
        $this->command->info('✓ Created: REJECTED (Ditolak coordinator)');

        // 5. REJECTED - Coordinator approved, rejected by admin
        Ijin::create([
            'karyawan_id' => $karyawan->karyawan_id,
            'ijin_type_id' => $ijinTypes->first()->ijin_type_id,
            'date_from' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'date_to' => Carbon::now()->addDays(25)->format('Y-m-d'),
            'reason' => 'Cuti panjang untuk mudik',
            'status' => 'rejected',
            'coordinator_id' => $coordinator->user_id,
            'coordinator_status' => 'approved',
            'coordinator_note' => 'Approved dari koordinator',
            'coordinator_reviewed_at' => Carbon::now()->subDays(3),
            'admin_id' => $admin->user_id,
            'admin_status' => 'rejected',
            'admin_note' => 'Ditolak karena terlalu lama, maksimal 5 hari',
            'admin_reviewed_at' => Carbon::now()->subDays(2),
        ]);
        $this->command->info('✓ Created: REJECTED (Coordinator approved, ditolak admin)');

        // 6. SHIFT SWAP - Pending
        $shiftSwapType = $ijinTypes->where('code', 'shift_swap')->first();
        if ($shiftSwapType) {
            Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $shiftSwapType->ijin_type_id,
                'date_from' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'date_to' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'reason' => 'Tukar shift karena ada acara keluarga',
                'original_shift_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'replacement_shift_date' => Carbon::now()->addDays(17)->format('Y-m-d'),
                'status' => 'pending',
                'coordinator_id' => $coordinator->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
            ]);
            $this->command->info('✓ Created: SHIFT SWAP (Pending)');
        }

        // ✅ 7. COMPENSATION LEAVE - Approved (cara yang sama)
        $compLeaveType = $ijinTypes->where('code', 'compensation_leave')->first();
        if ($compLeaveType) {
            $compLeave = Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $compLeaveType->ijin_type_id,
                'date_from' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'date_to' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'reason' => 'Cuti pengganti karena piket hari libur',
                'original_shift_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'status' => 'pending',
                'coordinator_id' => $coordinator->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
            ]);

            $compLeave->timestamps = false;
            $compLeave->coordinator_status = 'approved';
            $compLeave->coordinator_note = 'Approved, sudah piket hari minggu';
            $compLeave->coordinator_reviewed_at = Carbon::now()->subDays(11);
            $compLeave->admin_id = $admin->user_id;
            $compLeave->admin_status = 'approved';
            $compLeave->admin_note = 'Approved';
            $compLeave->admin_reviewed_at = Carbon::now()->subDays(10)->addHours(3);
            $compLeave->status = 'approved';
            $compLeave->saveQuietly();

            $this->command->info('✓ Created: COMPENSATION LEAVE (Approved)');
        }

        // 8. SICK - Pending coordinator review
        $sickType = $ijinTypes->where('code', 'sick')->first();
        if ($sickType) {
            Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $sickType->ijin_type_id,
                'date_from' => Carbon::now()->format('Y-m-d'),
                'date_to' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'reason' => 'Sakit tipes, rawat inap',
                'status' => 'pending',
                'coordinator_id' => $coordinator->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
            ]);
            $this->command->info('✓ Created: SICK (Pending)');
        }

        // 9. ANNUAL LEAVE - Multiple days, pending
        $annualType = $ijinTypes->where('code', 'annual')->first();
        if ($annualType) {
            Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $annualType->ijin_type_id,
                'date_from' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'date_to' => Carbon::now()->addDays(34)->format('Y-m-d'),
                'reason' => 'Cuti tahunan untuk pernikahan saudara',
                'status' => 'pending',
                'coordinator_id' => $coordinator->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
            ]);
            $this->command->info('✓ Created: ANNUAL LEAVE (5 hari, pending)');
        }

        // ✅ 10. PERSONAL LEAVE - Approved kemarin
        $personalType = $ijinTypes->where('code', 'personal')->first();
        if ($personalType) {
            $personalLeave = Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $personalType->ijin_type_id,
                'date_from' => Carbon::yesterday()->format('Y-m-d'),
                'date_to' => Carbon::yesterday()->format('Y-m-d'),
                'reason' => 'Mengurus dokumen penting di kantor pemerintah',
                'status' => 'pending',
                'coordinator_id' => $coordinator->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
            ]);

            $personalLeave->timestamps = false;
            $personalLeave->coordinator_status = 'approved';
            $personalLeave->coordinator_note = 'Approved';
            $personalLeave->coordinator_reviewed_at = Carbon::yesterday()->subHours(3);
            $personalLeave->admin_id = $admin->user_id;
            $personalLeave->admin_status = 'approved';
            $personalLeave->admin_note = 'Approved by admin';
            $personalLeave->admin_reviewed_at = Carbon::yesterday()->subHours(1);
            $personalLeave->status = 'approved';
            $personalLeave->saveQuietly();

            $this->command->info('✓ Created: PERSONAL LEAVE (Approved kemarin)');
        }

        $this->command->info('');
        $this->command->info('==============================================');
        $totalIjin = Ijin::where('karyawan_id', 'KAR021')->count();
        $this->command->info("✓ Selesai! Total {$totalIjin} ijin dibuat untuk KAR021");
        $this->command->info('==============================================');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- Pending (Coordinator): ' . Ijin::where('karyawan_id', 'KAR021')->where('coordinator_status', 'pending')->where('status', 'pending')->count());
        $this->command->info('- Pending (Admin): ' . Ijin::where('karyawan_id', 'KAR021')->where('coordinator_status', 'approved')->where('admin_status', 'pending')->where('status', 'pending')->count());
        $this->command->info('- Approved: ' . Ijin::where('karyawan_id', 'KAR021')->where('status', 'approved')->count());
        $this->command->info('- Rejected: ' . Ijin::where('karyawan_id', 'KAR021')->where('status', 'rejected')->count());
        $this->command->info('');
        $this->command->info('Coordinator: ' . $coordinator->name . ' (KAR002)');
    }
}
