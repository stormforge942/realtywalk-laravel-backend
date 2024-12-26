<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ViewingSchedulesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $schedules;

    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    public function collection()
    {
        return $this->schedules;
    }

    public function map($row): array
    {
        $user = $row->user ? "{$row->name} (ID: {$row->user->id})" : $row->name;

        return [
            $row->id,
            $row->datetime->format('Y-m-d H:i:s'),
            $user,
            $row->email,
            $row->phone_number,
            $row->property->id ?? '',
            $row->property->getFullAddress() ?? '',
            $row->property->page_url ?? '',
            $row->message,
            $row->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Schedule date & time',
            'Name',
            'Email address',
            'Phone number',
            'Property ID',
            'Property address',
            'Property page URL',
            'Message',
            'Submitted at',
        ];
    }
}
