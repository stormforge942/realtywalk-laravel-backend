<?php

use App\Models\Style;
use Illuminate\Database\Seeder;

class StylesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Style::create(['name' => 'Traditional']);
        Style::create(['name' => 'Mediterranean']);
        Style::create(['name' => 'Ranch']);
        Style::create(['name' => 'Contemporary/Modern']);
        Style::create(['name' => 'Spanish']);
        Style::create(['name' => 'English']);
        Style::create(['name' => 'Colonial']);
        Style::create(['name' => 'French']);
        Style::create(['name' => 'Split Level']);
        Style::create(['name' => 'Victorian']);
        Style::create(['name' => 'Georgian']);
        Style::create(['name' => 'Other Style']);
    }
}
