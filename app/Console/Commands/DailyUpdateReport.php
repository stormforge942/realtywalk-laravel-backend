<?php

namespace App\Console\Commands;

use App\Exports\BuildersExport;
use App\Exports\PropertiesExport;
use App\Exports\PropertyFavoritesExport;
use App\Exports\UsersExport;
use App\Exports\ViewingSchedulesExport;
use App\Mail\DailyUpdateReport as MailDailyUpdateReport;
use App\Models\Builder;
use App\Models\Property;
use App\Models\PropertyFavorite;
use App\Models\Setting;
use App\Models\ViewingSchedule;
use App\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class DailyUpdateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-update {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email command about daily update. Default date is today.';

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
        Log::debug('report:daily-update is running...');
        $date = $this->argument('date');
        $date = $date ? Carbon::parse($date.' America/Chicago') : now(new DateTimeZone('America/Chicago'));
        $emails = $this->getEmails();
        $start_date = $date->clone()->startOfDay()->tz('UTC');
        $end_date = $date->clone()->endOfDay()->tz('UTC');

        if ($date->isFuture()) {
            $this->error('You can\'t send report with a future date.');
            return 0;
        }

        Mail::to($emails)->send(new MailDailyUpdateReport($start_date, $end_date));
        $this->info('Report has successfully sent to:');
        $this->line($this->getEmailsInString());
        Log::debug('report:daily-update is finished...');
    }

    private function getEmails()
    {
        $emails = collect(Setting::getBy('notification_email'));

        $secretaries = User::whereHas('roles', function ($query) {
            $query->where('name', 'secretary');
        })->get();

        return $emails->merge($secretaries);
    }

    public function getEmailsInString()
    {
        $emails = collect(Setting::getBy('notification_email'));

        $secretaries = User::whereHas('roles', function ($query) {
            $query->where('name', 'secretary');
        })->get()->pluck('email');

        return $emails->merge($secretaries)->join("\n");
    }
}
