<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTablesSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(ZonesTableSeeder::class);
        $this->call(PolygonsTableSeeder::class);
        $this->call(PolygonPointsTableSeeder::class);
        $this->call(BuildersTableSeeder::class);
        $this->call(AmenitiesTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(StylesTableSeeder::class);
        $this->call(BuildersPolygonsTableSeeder::class);
        $this->call(PropertiesTableSeeder::class);
        $this->call(PropertyFeatureTableSeeder::class);
        $this->call(PropertyStyleTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(StatisticsTablesSeeder::class);
    }
}
