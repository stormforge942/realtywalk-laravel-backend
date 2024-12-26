<?php

namespace App\Console\Commands;

use App\Mail\OldPostingsNotification;
use App\Mail\SimilarBuildersNotification;
use App\Models\Property;
use App\Models\Builder;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Mail;

class NotifySecretaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretary:notify {--historic=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily command to notify secretaries';

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
        $secretaries = User::whereHas('roles', function ($query) {
            $query->where('name', 'secretary');
        })->select('email')->get()->pluck('email');

        $notificationEmails = Setting::getBy(Setting::SECRETARY_NOTIFICATION_EMAIL);
        $recipients = $secretaries->merge(collect($notificationEmails))->unique();

        $historicSymbol = $this->option('historic') == '1' ? '<' : '=';

        $oldPostings = Property::where('type', Property::TYPE_POSTING)
            ->whereDate('updated_at', $historicSymbol, today()->subDays(30))
            ->get();

        if ($oldPostings->count() > 0) {
            $this->table($oldPostings->keys(), $oldPostings);

            Mail::to($recipients)->send(new OldPostingsNotification($oldPostings));
        }

        $todayAliases = Builder::with('aliasOf:id,name')->whereNotNull('alias_of_builder_id')->whereDate('updated_at', '=', today())->get();

        $recentlyModifiedBuilders = Builder::selectRaw('*, SOUNDEX(name) as name_sound')->whereDate('updated_at', $historicSymbol, today())
            ->get();

        $similarBuilders = collect();
        $soundsChecked = [];

        foreach ($recentlyModifiedBuilders as $builder) {
            if (in_array($builder->name_sound, $soundsChecked)) {
                continue;
            }
            $soundsChecked[] = $builder->name_sound;

            $similars = Builder::whereRaw('SOUNDEX(name) like ?', [$builder->name_sound])->get();
            if ($similars->count() > 1) {
                $similarBuilders->push($similars);
            }
        }
        if ($similarBuilders->count() > 0) {
            Mail::to($recipients)->send(new SimilarBuildersNotification($similarBuilders, $todayAliases));
        }

        return 0;
    }
}
