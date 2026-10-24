<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KaryawansSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $karyawans) {}

    public function title(): string
    {
        return 'Karyawans';
    }

    public function headings(): array
    {
        return [
            'karyawan_id',
            'user_id',
            'department_id',
            'nip',
            'full_name',
            'position',
            'phone',
            'address',
            'hire_date',
            'birth_date',
            'gender',
            'employment_status',
            'staff_status',
            'uang_kuota',
            'is_shift_normal',
            'created_at',
            'updated_at',
        ];
    }

    public function collection(): Collection
    {
        return $this->karyawans->map(fn ($k) => [
            $k->karyawan_id,
            $k->user_id,
            $k->department_id,
            $k->nip,
            $k->full_name,
            $k->position,
            $k->phone,
            $k->address,
            $k->hire_date?->format('Y-m-d'),
            $k->birth_date?->format('Y-m-d'),
            $k->gender,
            $k->employment_status,
            $k->staff_status,
            $k->uang_kuota ? 1 : 0,
            $k->is_shift_normal ? 1 : 0,
            $k->created_at,
            $k->updated_at,
        ]);
    }
}
