<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function map($row): array
    {
        $builder = $row->builder ? $row->builder->name . '(ID: ' . $row->builder->id . ')' : '';

        return [
            $row->id,
            $row->name,
            $row->email,
            $row->roles?->pluck('name')?->join(', '),
            $builder,
            $row->last_login_at?->format('Y-m-d H:i:s'),
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email address',
            'Role',
            'Builder',
            'Last login time',
            'Created at',
            'Updated at',
        ];
    }
}
