<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BuildersExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            $row->id,
            $row->name,
            $row->slug,
            $row->descr,
            $row->email,
            $row->address1,
            $row->address2,
            $row->address3,
            $row->city,
            $row->phone,
            $row->website,
            $row->hidden ? 'Yes' : 'No',
            $row->page_url,
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Description',
            'Email address',
            'Address 1',
            'Address 2',
            'Address 3',
            'City',
            'Phone',
            'Website',
            'Is hidden?',
            'Page URL',
            'Created at',
            'Updated at',
        ];
    }
}
