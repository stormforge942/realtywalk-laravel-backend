<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PolygonService;

class RemovePolygons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygons:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove polygons that have the same names as what is scraped from apartments.com for all zip codes in Houston, TX.';

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
        (new PolygonService())->removePolygons();
    }
}
