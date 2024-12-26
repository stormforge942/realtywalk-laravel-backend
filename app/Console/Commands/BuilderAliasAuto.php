<?php

namespace App\Console\Commands;

use App\Models\Builder;
use Illuminate\Console\Command;

class BuilderAliasAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'builder:alias-auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto combines similar builders as aliases';

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
        $recentlyModifiedBuilders = Builder::selectRaw('*, SOUNDEX(name) as name_sound')
            ->get();

        $soundsChecked = [];

        foreach ($recentlyModifiedBuilders as $builder) {
            if (in_array($builder->name_sound, $soundsChecked)) {
                continue;
            }
            $soundsChecked[] = $builder->name_sound;

            $similars = Builder::whereRaw('SOUNDEX(name) like ?', [$builder->name_sound])->get();
            if ($similars->count() > 1) {
                $first = null;
                foreach ($similars as $similarBuilder) {
                    if (!$first) {
                        $first = $similarBuilder;
                        continue;
                    }
                    $similarBuilder->update(['alias_of_builder_id' => $first->id]);
                }
            }
        }

        return 0;
    }
}
