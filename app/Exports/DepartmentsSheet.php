<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DepartmentsSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $departments) {}

    public function title(): string
    {
        return 'Departments';
    }

    public function headings(): array
    {
        return [
            'department_id',
            'name',
            'code',
            'description',
            'manager_user_id',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }

    public function collection(): Collection
    {
        return $this->departments->map(fn ($d) => [
            $d->department_id,
            $d->name,
            $d->code,
            $d->description,
            $d->manager_user_id,
            $d->is_active ? 1 : 0,
            $d->created_at,
            $d->updated_at,
        ]);
    }
}
