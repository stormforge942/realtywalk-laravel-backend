<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertiesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $properties;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function collection()
    {
        return $this->properties;
    }

    public function map($row): array
    {
        $polygon = $row->polygon ? $row->polygon->title . '(ID: ' . $row->polygon->id . ')' : '';

        return [
            $row->id,
            $row->title,
            $row->type == 0 ? 'Listing' : 'Posting',
            $row->status,
            $row->mls_number,
            $row->page_url,
            $row->builder?->name,
            $row->category?->name,
            $row->priceformat?->name,
            $row->price_from,
            $row->price_to,
            $row->getFullAddress(),
            $row->lat,
            $row->lng,
            $row->acres,
            $row->agent,
            $row->agent_id,
            $row->agent_website,
            $row->bathrooms_full,
            $row->bathrooms_half,
            $row->bedrooms,
            $row->finance_type,
            $row->garage_capacity,
            $row->hoa_annual_fee,
            $row->levels,
            $row->lot_size,
            $row->sqft,
            $row->year_built,
            $row->office_id,
            $row->office_name,
            $row->office_website,
            $polygon,
            $row->video_embed,
            $row->descr,
            $row->created_at?->format('Y-m-d H:i:s'),
            $row->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Property type',
            'Status',
            'MLS number',
            'URL',
            'Builder',
            'Category',
            'Price format',
            'Price from',
            'Price to',
            'Address',
            'Latitude',
            'Longitude',
            'Acres',
            'Agent',
            'Agent ID',
            'Agent website',
            'Bathrooms full',
            'Bathrooms half',
            'Bedrooms',
            'Finance type',
            'Garage capacity',
            'HOA annual fee',
            'Levels',
            'Lot size',
            'Sqft',
            'Year built',
            'Office ID',
            'Office name',
            'Office website',
            'Polygon',
            'Video embed',
            'Description',
            'Created at',
            'Updated at',
        ];
    }
}
