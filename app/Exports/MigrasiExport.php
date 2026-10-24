<?php

namespace App\Exports;

use App\Models\Department;
use App\Models\Karyawan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MigrasiExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        $karyawans = Karyawan::with('user')->get();
        $userIds   = $karyawans->pluck('user_id')->filter()->unique()->values();

        return [
            'Departments' => new DepartmentsSheet(Department::all()),
            'Users'       => new UsersSheet(User::whereIn('user_id', $userIds)->get()),
            'Karyawans'   => new KaryawansSheet($karyawans),
        ];
    }
}
