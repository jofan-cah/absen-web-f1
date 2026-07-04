<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HrdUserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun HRD — hanya User, TIDAK ada record Karyawan
        // Sehingga 100% aman dari generate tunjangan, PDF, absen, lembur
        User::updateOrCreate(
            ['email' => 'hrd@fiberone.id'],
            [
                'user_id'  => 'USR-HRD-001',
                'nip'      => 'HRD001',
                'name'     => 'HRD FiberOne',
                'email'    => 'hrd@fiberone.id',
                'password' => Hash::make('hrd@F1ber2026'),
                'role'     => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info('HRD user created: hrd@fiberone.id / hrd@F1ber2026');
    }
}
