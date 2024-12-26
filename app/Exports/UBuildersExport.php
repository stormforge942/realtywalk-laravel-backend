<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UBuildersExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $builders;

    public function __construct($builders)
    {
        $this->builders = $builders;
    }

    public function collection()
    {
        return $this->builders;
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->last_seen,
            $row->created_at,
            $row->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Last Seen',
            'Created at',
            'Updated at',
        ];
    }
}
