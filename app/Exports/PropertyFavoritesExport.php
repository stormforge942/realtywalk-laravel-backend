<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyFavoritesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $favorites;

    public function __construct($favorites)
    {
        $this->favorites = $favorites;
    }

    public function collection()
    {
        return $this->favorites;
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user->name ?? '',
            $row->user->email ?? '',
            $row->property->id ?? '',
            $row->property->getFullAddress() ?? '',
            $row->property->page_url ?? '',
            $row->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email address',
            'Property ID',
            'Property address',
            'Page URL',
            'Added at',
        ];
    }
}
