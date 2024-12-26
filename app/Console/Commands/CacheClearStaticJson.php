<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CacheClearStaticJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-static-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears static .cache.json files from the `local` disk.';

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
        $files = ['neighborhood-tree.cache.json', 'polygons-level1.cache.json'];
        foreach ($files as $file) {
            if (Storage::disk('local')->exists($file))
                Storage::disk('local')->delete($file);
        }
        return 0;
    }
}
