<?php

namespace App\Console\Commands;

use Database\Seeders\MenusTableSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SystemMenuReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:menu-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the menu to fix links broken by APP_URL changes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('menu_role')->delete();
        DB::table('menu_list')->delete();
        DB::table('menus')->delete();

        (new MenusTableSeeder())->run();
    }
}
