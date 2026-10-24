<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $users) {}

    public function title(): string
    {
        return 'Users';
    }

    public function headings(): array
    {
        return [
            'user_id',
            'nip',
            'name',
            'email',
            'password',
            'role',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }

    public function collection(): Collection
    {
        return $this->users->map(fn ($u) => [
            $u->user_id,
            $u->nip,
            $u->name,
            $u->email,
            $u->password,
            $u->role,
            $u->is_active ? 1 : 0,
            $u->created_at,
            $u->updated_at,
        ]);
    }
}
