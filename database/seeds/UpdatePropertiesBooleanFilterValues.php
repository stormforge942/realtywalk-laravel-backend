<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePropertiesBooleanFilterValues extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Property::withoutEvents(function () {
            Property::query()->update([
                'has_pool' => DB::raw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM property_amenity pa
                            JOIN amenities a ON a.id = pa.amenity_id
                            WHERE pa.property_id = properties.id
                            AND a.name IN ('Private Pool', 'Area Pool')
                        ) THEN true
                        ELSE false
                    END
                "),
                'elevator_type' => DB::raw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM property_amenity pa
                            JOIN amenities a ON a.id = pa.amenity_id
                            WHERE pa.property_id = properties.id
                            AND a.name = 'Elevator'
                        ) THEN '1'
                        WHEN EXISTS (
                            SELECT 1
                            FROM property_amenity pa
                            JOIN amenities a ON a.id = pa.amenity_id
                            WHERE pa.property_id = properties.id
                            AND a.name = 'Elevator Shaft'
                        ) THEN '2'
                        ELSE false
                    END
                "),
            ]);
        });
    }
}
