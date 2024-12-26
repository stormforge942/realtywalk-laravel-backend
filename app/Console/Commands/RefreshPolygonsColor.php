<?php

namespace App\Console\Commands;

use App\Models\Polygon;
use Illuminate\Console\Command;

class RefreshPolygonsColor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygons:refresh-color';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh polygons color';

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
        $polygons = Polygon::get();
        $bar = $this->output->createProgressBar($polygons->count());
        $bar->start();

        foreach ($polygons as $polygon) {
            if ($polygon->zoom == 2 && $polygon->parent) {
                $polygon->update(['color' => $polygon->parent->color]);
            }
            $bar->advance();
        }

        $bar->finish();
    }
}
