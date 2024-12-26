<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = array(
            [
                'name'  => 'site_title',
                'value' => 'Realty WALK CMS',
            ], [
                'name'  => 'site_logo_expanded',
                'value' => 'logo.png',
            ], [
                'name'  => 'site_logo_collapsed',
                'value' => 'logo-small.png'
            ], [
                'name'  => 'property_status',
                'value' => [
                    'Active',
                    'Option Pending',
                    'Pending',
                    'Pending Continue to Show',
                    'Off Market'
                ]
            ], [
                'name'  => 'notification_email',
                'value' => 'hello@yefta.me'
            ], [
                'name'  => 'smtp',
                'value' => [
                    'host'       => 'smtp.mailtrap.io',
                    'port'       => 2525,
                    'username'   => null,
                    'password'   => null,
                    'encryption' => null
                ]
            ], [
                'name' => 'terms_of_service',
                'value' => 'Terms of Service'
            ], [
                'name' => 'privacy_policy',
                'value' => 'Privacy Policy'
            ]
        );

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['name' => $setting['name']],
                ['value' => $setting['value']],
            );
        }
    }
}
