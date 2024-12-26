<?php

use App\Models\Statistic;
use App\Models\StatisticType;
use Illuminate\Database\Seeder;

class StatisticsTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatisticType::create([
            'name'   => 'Race Makeup',
            'format' => '%'
        ])->statistics()->createMany([
            ['name' => 'Hispanic'],
            ['name' => 'African'],
            ['name' => 'Native American'],
            ['name' => 'Caucasian'],
            ['name' => 'Inuit'],
        ]);

        StatisticType::create([
            'name'   => 'Marital Status',
            'format' => '%'
        ])->statistics()->createMany([
            ['name' => 'Maried'],
            ['name' => 'Single'],
        ]);

        StatisticType::create([
            'name'   => 'Home Ownership',
            'format' => '%'
        ])->statistics()->createMany([
            ['name' => 'Homeowners'],
            ['name' => 'Non-Homeowners'],
        ]);

        StatisticType::create([
            'name'   => 'Age Breakdown',
            'format' => '%'
        ])->statistics()->createMany([
            ['name' => '80+'],
            ['name' => '60-79'],
            ['name' => '40-59'],
            ['name' => '20-39'],
            ['name' => '<20'],
        ]);

        StatisticType::create([
            'name'   => 'Veteran Status',
            'format' => '%'
        ])->statistics()->createMany([
            ['name' => 'Veteran'],
            ['name' => 'Non-Veteran'],
        ]);
    }
}
