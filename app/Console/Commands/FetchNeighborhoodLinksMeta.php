<?php

namespace App\Console\Commands;

use App\Models\Polygon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchNeighborhoodLinksMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neighborhood:fetch-links-meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Meta details of Neighborhood Links';

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
        $this->info("Updating links details");

        $polygons = Polygon::whereNotNull('links')->get();

        $this->withProgressBar($polygons, function ($polygon) {
            $data = [];
            $links = is_array($polygon->links) ? $polygon->links : unserialize($polygon->links);
            $links = collect($links)->reject(fn($link) => is_null($link));

            $links->each(function ($link) use (&$data, $polygon) {
                $url = isset($link['url']) ? $link['url'] : (is_string($link) ? $link : null);
                $fetched = isset($link['status']) && $link['status'] === 200;

                if ($url === 'a:1:{i:0;N;}') return;

                if ($fetched) {
                    $data[] = $link;
                    return;
                }
                $this->info("\nFetching link: {$url} for polygon #{$polygon->id}");
                $details = Polygon::fetchLink($link);

                if (isset($details['error'])) {
                    $this->error("Error fetching {$url} : {$details['error']}");
                    unset($details['error']);
                } else {
                    $this->info("Fetched details: \n - Status: {$details['status']}\n - Name: {$details['title']}");
                }

                $data[] = $details;
            });

            $polygon->updateQuietly(['links' => serialize($data)]);
        });

        return 0;
    }
}
