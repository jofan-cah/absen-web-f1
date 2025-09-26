<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TunjanganType;
use App\Models\TunjanganDetail;
use App\Models\TunjanganKaryawan;
use App\Models\Penalti;
use Carbon\Carbon;

class TunjanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Tunjangan Types
        $tunjanganTypes = [
            [
                'tunjangan_type_id' => 'TJT001',
                'name' => 'Uang Makan',
                'code' => 'UANG_MAKAN',
                'category' => 'mingguan',
                'base_amount' => 17500,
                'description' => 'Tunjangan makan mingguan untuk karyawan',
                'is_active' => true,
            ],
            [
                'tunjangan_type_id' => 'TJT002',
                'name' => 'Uang Kuota Internet',
                'code' => 'UANG_KUOTA',
                'category' => 'bulanan',
                'base_amount' => 50000,
                'description' => 'Tunjangan kuota internet bulanan',
                'is_active' => true,
            ],
            [
                'tunjangan_type_id' => 'TJT003',
                'name' => 'Uang Lembur',
                'code' => 'UANG_LEMBUR',
                'category' => 'harian',
                'base_amount' => 25000,
                'description' => 'Tunjangan lembur per jam',
                'is_active' => true,
            ],
        ];

        foreach ($tunjanganTypes as $type) {
            TunjanganType::create($type);
        }

        // 2. Seed Tunjangan Details (Nominal per staff status)
        $tunjanganDetails = [
            // Uang Makan
            ['tunjangan_type_id' => 'TJT001', 'staff_status' => 'training', 'amount' => 15000],
            ['tunjangan_type_id' => 'TJT001', 'staff_status' => 'karyawan', 'amount' => 20000],
            ['tunjangan_type_id' => 'TJT001', 'staff_status' => 'koordinator', 'amount' => 25000],
            ['tunjangan_type_id' => 'TJT001', 'staff_status' => 'wakil_koordinator', 'amount' => 22000],

            // Uang Kuota
            ['tunjangan_type_id' => 'TJT002', 'staff_status' => 'training', 'amount' => 30000],
            ['tunjangan_type_id' => 'TJT002', 'staff_status' => 'karyawan', 'amount' => 50000],
            ['tunjangan_type_id' => 'TJT002', 'staff_status' => 'koordinator', 'amount' => 75000],
            ['tunjangan_type_id' => 'TJT002', 'staff_status' => 'wakil_koordinator', 'amount' => 60000],

            // Uang Lembur
            ['tunjangan_type_id' => 'TJT003', 'staff_status' => 'training', 'amount' => 15000],
            ['tunjangan_type_id' => 'TJT003', 'staff_status' => 'karyawan', 'amount' => 25000],
            ['tunjangan_type_id' => 'TJT003', 'staff_status' => 'koordinator', 'amount' => 35000],
            ['tunjangan_type_id' => 'TJT003', 'staff_status' => 'wakil_koordinator', 'amount' => 30000],
        ];

        $detailId = 1;
        foreach ($tunjanganDetails as $detail) {
            TunjanganDetail::create([
                'tunjangan_detail_id' => 'TJD' . str_pad($detailId, 3, '0', STR_PAD_LEFT),
                'tunjangan_type_id' => $detail['tunjangan_type_id'],
                'staff_status' => $detail['staff_status'],
                'amount' => $detail['amount'],
                'effective_date' => Carbon::now()->startOfMonth(),
                'end_date' => null,
                'is_active' => true,
            ]);
            $detailId++;
        }

        // 3. Seed Penalti untuk KAR002
        $penaltis = [
            [
                'penalti_id' => 'PNL001',
                'karyawan_id' => 'KAR002',
                'absen_id' => null,
                'jenis_penalti' => 'telat',
                'deskripsi' => 'Terlambat 3 kali dalam seminggu',
                'hari_potong_uang_makan' => 2,
                'tanggal_penalti' => Carbon::now()->subDays(5),
                'periode_berlaku_mulai' => Carbon::now()->startOfWeek(),
                'periode_berlaku_akhir' => Carbon::now()->endOfWeek(),
                'status' => 'active',
                'created_by_user_id' => 'USR001',
                'notes' => 'Penalti karena sering terlambat',
            ],
            [
                'penalti_id' => 'PNL002',
                'karyawan_id' => 'KAR002',
                'absen_id' => null,
                'jenis_penalti' => 'pelanggaran',
                'deskripsi' => 'Tidak menggunakan seragam lengkap',
                'hari_potong_uang_makan' => 1,
                'tanggal_penalti' => Carbon::now()->subDays(10),
                'periode_berlaku_mulai' => Carbon::now()->subWeek()->startOfWeek(),
                'periode_berlaku_akhir' => Carbon::now()->subWeek()->endOfWeek(),
                'status' => 'completed',
                'created_by_user_id' => 'USR001',
                'approved_by_user_id' => 'USR001',
                'approved_at' => Carbon::now()->subDays(9),
                'notes' => 'Penalti sudah dilaksanakan',
            ],
        ];

        foreach ($penaltis as $penalti) {
            Penalti::create($penalti);
        }

        // 4. Seed Tunjangan Karyawan untuk KAR002
        $tunjanganKaryawan = [
            // Uang Makan Minggu Lalu (sudah diterima)
            [
                'tunjangan_karyawan_id' => 'TJK001',
                'karyawan_id' => 'KAR002',
                'tunjangan_type_id' => 'TJT001',
                'absen_id' => null,
                'period_start' => Carbon::now()->subWeek()->startOfWeek(),
                'period_end' => Carbon::now()->subWeek()->endOfWeek(),
                'amount' => 20000, // Asumsi KAR002 staff_status = karyawan
                'quantity' => 5,
                'hari_kerja_asli' => 5,
                'hari_potong_penalti' => 1,
                'hari_kerja_final' => 4,
                'total_amount' => 80000, // 20000 * 4
                'status' => 'received',
                'notes' => 'Uang makan minggu lalu',
                'requested_at' => Carbon::now()->subWeek()->addDay(),
                'requested_via' => 'mobile',
                'approved_by_user_id' => 'USR001',
                'approved_at' => Carbon::now()->subWeek()->addDays(2),
                'received_at' => Carbon::now()->subWeek()->addDays(3),
                'penalti_id' => 'PNL002',
                'history' => [
                    [
                        'status' => 'pending',
                        'notes' => 'Tunjangan dibuat otomatis oleh sistem',
                        'user_id' => null,
                        'timestamp' => Carbon::now()->subWeek()->toISOString(),
                        'created_at' => Carbon::now()->subWeek()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'requested',
                        'notes' => 'Request tunjangan via mobile',
                        'user_id' => 'USR002',
                        'timestamp' => Carbon::now()->subWeek()->addDay()->toISOString(),
                        'created_at' => Carbon::now()->subWeek()->addDay()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'approved',
                        'notes' => 'Tunjangan disetujui',
                        'user_id' => 'USR001',
                        'timestamp' => Carbon::now()->subWeek()->addDays(2)->toISOString(),
                        'created_at' => Carbon::now()->subWeek()->addDays(2)->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'received',
                        'notes' => 'Tunjangan telah diterima karyawan',
                        'user_id' => 'USR002',
                        'timestamp' => Carbon::now()->subWeek()->addDays(3)->toISOString(),
                        'created_at' => Carbon::now()->subWeek()->addDays(3)->format('Y-m-d H:i:s'),
                    ],
                ],
            ],

            // Uang Makan Minggu Ini (sedang requested)
            [
                'tunjangan_karyawan_id' => 'TJK002',
                'karyawan_id' => 'KAR002',
                'tunjangan_type_id' => 'TJT001',
                'absen_id' => null,
                'period_start' => Carbon::now()->startOfWeek(),
                'period_end' => Carbon::now()->endOfWeek(),
                'amount' => 20000,
                'quantity' => 5,
                'hari_kerja_asli' => 5,
                'hari_potong_penalti' => 2,
                'hari_kerja_final' => 3,
                'total_amount' => 60000, // 20000 * 3
                'status' => 'requested',
                'notes' => 'Uang makan minggu ini',
                'requested_at' => Carbon::now()->subDay(),
                'requested_via' => 'mobile',
                'penalti_id' => 'PNL001',
                'history' => [
                    [
                        'status' => 'pending',
                        'notes' => 'Tunjangan dibuat otomatis oleh sistem',
                        'user_id' => null,
                        'timestamp' => Carbon::now()->startOfWeek()->toISOString(),
                        'created_at' => Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'requested',
                        'notes' => 'Request tunjangan via mobile',
                        'user_id' => 'USR002',
                        'timestamp' => Carbon::now()->subDay()->toISOString(),
                        'created_at' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
                    ],
                ],
            ],

            // Uang Kuota Bulan Ini (pending)
            [
                'tunjangan_karyawan_id' => 'TJK003',
                'karyawan_id' => 'KAR002',
                'tunjangan_type_id' => 'TJT002',
                'absen_id' => null,
                'period_start' => Carbon::now()->startOfMonth(),
                'period_end' => Carbon::now()->endOfMonth(),
                'amount' => 50000,
                'quantity' => 1,
                'hari_kerja_asli' => 1,
                'hari_potong_penalti' => 0,
                'hari_kerja_final' => 1,
                'total_amount' => 50000,
                'status' => 'pending',
                'notes' => 'Uang kuota bulan ' . Carbon::now()->format('F Y'),
                'history' => [
                    [
                        'status' => 'pending',
                        'notes' => 'Tunjangan dibuat otomatis oleh sistem',
                        'user_id' => null,
                        'timestamp' => Carbon::now()->startOfMonth()->toISOString(),
                        'created_at' => Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'),
                    ],
                ],
            ],

            // Uang Lembur (approved)
            [
                'tunjangan_karyawan_id' => 'TJK004',
                'karyawan_id' => 'KAR002',
                'tunjangan_type_id' => 'TJT003',
                'absen_id' => 'ABS001', // Asumsi ada absen dengan ID ini
                'period_start' => Carbon::now()->subDays(3),
                'period_end' => Carbon::now()->subDays(3),
                'amount' => 25000,
                'quantity' => 3,
                'hari_kerja_asli' => 3,
                'hari_potong_penalti' => 0,
                'hari_kerja_final' => 3,
                'total_amount' => 75000, // 25000 * 3
                'status' => 'approved',
                'notes' => 'Lembur 3 jam pada ' . Carbon::now()->subDays(3)->format('d-m-Y'),
                'requested_at' => Carbon::now()->subDays(2),
                'requested_via' => 'web',
                'approved_by_user_id' => 'USR001',
                'approved_at' => Carbon::now()->subDay(),
                'history' => [
                    [
                        'status' => 'pending',
                        'notes' => 'Tunjangan dibuat otomatis oleh sistem',
                        'user_id' => null,
                        'timestamp' => Carbon::now()->subDays(3)->toISOString(),
                        'created_at' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'requested',
                        'notes' => 'Request tunjangan via web',
                        'user_id' => 'USR002',
                        'timestamp' => Carbon::now()->subDays(2)->toISOString(),
                        'created_at' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status' => 'approved',
                        'notes' => 'Tunjangan disetujui',
                        'user_id' => 'USR001',
                        'timestamp' => Carbon::now()->subDay()->toISOString(),
                        'created_at' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
                    ],
                ],
            ],
        ];

        foreach ($tunjanganKaryawan as $tunjangan) {
            TunjanganKaryawan::create($tunjangan);
        }

        $this->command->info('✅ Tunjangan seeder completed successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 3 Tunjangan Types');
        $this->command->info('   - 12 Tunjangan Details');
        $this->command->info('   - 2 Penaltis for KAR002');
        $this->command->info('   - 4 Tunjangan Karyawan for KAR002');
    }
}
