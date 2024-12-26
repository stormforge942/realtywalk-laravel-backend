<?php

namespace App\Mail;

use App\Exports\BuildersExport;
use App\Exports\PropertiesExport;
use App\Exports\PropertyFavoritesExport;
use App\Exports\UsersExport;
use App\Exports\ViewingSchedulesExport;
use App\Models\Builder;
use App\Models\Polygon;
use App\Models\Property;
use App\Models\PropertyFavorite;
use App\Models\Setting;
use App\Models\ViewingSchedule;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as BaseExcel;

class DailyUpdateReport extends Mailable
{
    use Queueable, SerializesModels;

    public $start_date;
    public $end_date;
    public $logo;
    public $greeting;
    public $introLines;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date   = $end_date;
        $this->logo       = url('images/logo-rw-horizontal.png', [], true);
        $this->greeting   = __('mail.daily_report_update.greeting');
        $this->subject    = __('mail.daily_report_update.subject');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        ini_set('memory_limit', '-1');
        $dates = [$this->start_date, $this->end_date];
        $dates_utc = [$dates[0]->tz('UTC'), $dates[1]->tz('UTC')];
        $bsettings = Setting::getBy('builder_settings');
        $blacklist = explode("\r\n", $bsettings['blacklist_names']);

        $startTime = microtime(true);
        $totalNewBuilders = Builder::query()
            ->whereBetween('created_at', $dates_utc)
            ->whereRaw('created_at = updated_at')
            // ->oldest('created_at')
            ->count();
        // $newBuilderExcel = $this->getBuildersExcel($newBuilders);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update new builders query finished in $time s");

        $startTime = microtime(true);
        $totalBuilders = Builder::query()
            ->whereBetween('updated_at', $dates_utc)
            ->whereRaw('created_at != updated_at')
            // ->oldest('updated_at')
            ->count();
        // $builderExcel = $this->getBuildersExcel($builders);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update updated builders query finished in $time s");

        $startTime = microtime(true);
        $totalUnmatchedBuilders = DB::table('builders_unmatched')
            ->whereNotIn('name', $blacklist)
            ->whereBetween('last_seen', $dates_utc)
            ->count();
        // $unmatchedBuildersExcel = $this->getUBuildersExcel($unmatchedBuilders);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update unmatched builders query finished in $time s");

        $startTime = microtime(true);
        $totalBlacklistedBuilders = DB::table('builders_unmatched')
            ->whereIn('name', $blacklist)
            ->whereBetween('last_seen', $dates_utc)
            ->count();
        // $blacklistedBuildersExcel = $this->getUBuildersExcel($blacklistedBuilders);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update blacklisted builders query finished in $time s");

        $startTime = microtime(true);
        $subQueryNewProperties = DB::query()
            ->select('id')
            ->from(DB::raw('`properties` USE INDEX(`big_dates_index`)'))
            ->whereRaw('DATE(CONCAT(created_year, "-", created_month, "-", created_day)) BETWEEN "' . $dates[0]->format('Y-m-d') . '" AND "' . $dates[0]->format('Y-m-d') . '"')
            ->whereRaw('DATE(CONCAT(created_year, "-", created_month, "-", created_day)) = DATE(CONCAT(updated_year, "-", updated_month, "-", updated_day))');

        $totalNewProperties = DB::query()
            // ->select([
            //     'p.id', 'p.title', 'p.type', 'p.status', 'p.mls_number', 'p.path_url',
            //     'p.price_from', 'p.price_to', 'p.lat', 'p.lng', 'p.acres', 'p.agent',
            //     'p.agent_id', 'p.agent_website', 'p.bathrooms_full', 'p.bathrooms_half',
            //     'p.bedrooms', 'p.finance_type', 'p.garage_capacity', 'p.hoa_annual_fee',
            //     'p.levels', 'p.lot_size', 'p.sqft', 'p.year_built', 'p.office_id',
            //     'p.office_name', 'p.office_website', 'p.video_embed', 'p.descr',
            //     'p.request_status', 'p.created_at', 'p.updated_at',
            //     'p.builder_id', 'p.polygon_id', 'p.category_id',
            //     DB::raw('IFNULL(builders.name, p.builder_name) as the_builder_name'),
            //     'categories.name as category_name',
            //     'price_formats.name as price_format_name'
            // ])
            ->from($subQueryNewProperties, 'sub_p')
            ->join('properties as p', 'sub_p.id', '=', 'p.id')
            // ->leftJoin('builders', 'p.builder_id', '=', 'builders.id')
            // ->leftJoin('categories', 'p.category_id', '=', 'categories.id')
            // ->leftJoin('price_formats', 'p.price_format_id', '=', 'price_formats.id')
            // ->oldest('p.created_at')
            ->count();

        // $polygon_ids = $newProperties->pluck('polygon_id')->unique()->values()->all();
        // $polygons = Polygon::query()->without('seourl')
        //     ->select(['id', 'title', 'parent_id', '_lft', '_rgt', 'zone_id'])
        //     ->with(['zone' => function ($q) {
        //         $q->select('id', 'name', 'code', '_lft', '_rgt', 'parent_id');
        //         $q->with('ancestors:id,name,code,_lft,_rgt,parent_id');
        //     }])
        //     ->whereIn('id', $polygon_ids)
        //     ->get();
        // $newProperties = $newProperties->map(function ($property) use ($polygons) {
        //     $property->polygon = $property->polygon_id ? $polygons->firstWhere('id', $property->polygon_id) : null;
        //     return $property;
        // });

        // $newPropertiesExcel = $this->getPropertiesExcel($newProperties);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update new properties query finished in $time s");

        $startTime = microtime(true);
        $subQueryUpdatedProperties = DB::query()
            ->select('id')
            ->from(DB::raw('`properties` USE INDEX(`big_dates_index`)'))
            ->whereRaw('DATE(CONCAT(updated_year, "-", updated_month, "-", updated_day)) BETWEEN "' . $dates[0]->format('Y-m-d') . '" AND "' . $dates[0]->format('Y-m-d') . '"')
            ->whereRaw('DATE(CONCAT(created_year, "-", created_month, "-", created_day)) != DATE(CONCAT(updated_year, "-", updated_month, "-", updated_day))');

        $totalUpdatedProperties = DB::query()
            // ->select([
            //     'p.id', 'p.title', 'p.type', 'p.status', 'p.mls_number', 'p.path_url',
            //     'p.price_from', 'p.price_to', 'p.lat', 'p.lng', 'p.acres', 'p.agent',
            //     'p.agent_id', 'p.agent_website', 'p.bathrooms_full', 'p.bathrooms_half',
            //     'p.bedrooms', 'p.finance_type', 'p.garage_capacity', 'p.hoa_annual_fee',
            //     'p.levels', 'p.lot_size', 'p.sqft', 'p.year_built', 'p.office_id',
            //     'p.office_name', 'p.office_website', 'p.video_embed', 'p.descr',
            //     'p.request_status', 'p.created_at', 'p.updated_at',
            //     'p.builder_id', 'p.polygon_id', 'p.category_id',
            //     DB::raw('IFNULL(builders.name, p.builder_name) as the_builder_name'),
            //     'categories.name as category_name',
            //     'price_formats.name as price_format_name'
            // ])
            ->from($subQueryUpdatedProperties, 'sub_p')
            ->join('properties as p', 'sub_p.id', '=', 'p.id')
            // ->leftJoin('builders', 'p.builder_id', '=', 'builders.id')
            // ->leftJoin('categories', 'p.category_id', '=', 'categories.id')
            // ->leftJoin('price_formats', 'p.price_format_id', '=', 'price_formats.id')
            ->oldest('p.updated_at')
            ->count();

        // $polygon_ids = $updatedProperties->pluck('polygon_id')->unique()->values()->all();
        // $polygons = Polygon::query()->without('seourl')
        //     ->select(['id', 'title', 'parent_id', '_lft', '_rgt', 'zone_id'])
        //     ->with(['zone' => function ($q) {
        //         $q->select('id', 'name', 'code', '_lft', '_rgt', 'parent_id');
        //         $q->with('ancestors:id,name,code,_lft,_rgt,parent_id');
        //     }])
        //     ->whereIn('id', $polygon_ids)
        //     ->get();
        // $updatedProperties = $updatedProperties->map(function ($property) use ($polygons) {
        //     $property->polygon = $property->polygon_id ? $polygons->firstWhere('id', $property->polygon_id) : null;
        //     return $property;
        // });

        // $updatedPropertiesExcel = $this->getPropertiesExcel($updatedProperties);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update updated properties query finished in $time s");

        $startTime = microtime(true);
        $totalUsers = User::query()
            // ->with('roles', 'builder')
            ->whereBetween('created_at', $dates)
            // ->oldest()
            ->count();
        // $userExcel = $this->getUsersExcel($users);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update new users query finished in $time s");

        $startTime = microtime(true);
        $totalFavorites = PropertyFavorite::query()
            // ->with('user', 'property.polygon.zone.ancestors')
            ->whereBetween('created_at', $dates)
            ->whereNotNull('user_id')
            ->whereNotNull('property_id')
            // ->orderBy('user_id')
            // ->oldest()
            ->count();
        // $favoriteExcel = $this->getFavoritesExcel($favorites);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update new favorites query finished in $time s");

        $startTime = microtime(true);
        $totalSchedules = ViewingSchedule::query()
            // ->with('user', 'property.polygon.zone.ancestors')
            ->whereBetween('created_at', $dates)
            ->whereNotNull('property_id')
            // ->latest('datetime')
            ->count();
        // $scheduleExcel = $this->getViewingsScheduled($schedules);
        $time = number_format(microtime(true) - $startTime, 4);
        Log::debug("report:daily-update new viewing schedules query finished in $time s");

        $this->introLines = __('mail.daily_report_update.lines', [
            'date'                      => $this->start_date->format('j F Y'),
            'total_added_builders'      => $totalNewBuilders,
            'total_updated_builders'    => $totalBuilders,
            'total_unmatched_builders'  => $totalUnmatchedBuilders,
            'total_blacklisted_builders' => $totalBlacklistedBuilders,
            'total_added_properties'    => $totalNewProperties,
            'total_updated_properties'  => $totalUpdatedProperties,
            'total_users'               => $totalUsers,
            'total_favorites'           => $totalFavorites,
            'total_schedules'           => $totalSchedules,
        ]);

        $email = $this->view('emails.daily_updates_report');

        // if ($newBuilders->count()) {
        //     $email->attachData($newBuilderExcel, 'builders-new-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($builders->count()) {
        //     $email->attachData($builderExcel, 'builders-update-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($unmatchedBuilders->count()) {
        //     $email->attachData($unmatchedBuildersExcel, 'builders-unmatched-from-import-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($blacklistedBuilders->count()) {
        //     $email->attachData($blacklistedBuildersExcel, 'builders-blacklisted-from-import-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($newProperties->count()) {
        //     $email->attachData($newPropertiesExcel, 'properties-new-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($updatedProperties->count()) {
        //     $email->attachData($updatedPropertiesExcel, 'properties-update-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($users->count()) {
        //     $email->attachData($userExcel, 'users-update-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($favorites->count()) {
        //     $email->attachData($favoriteExcel, 'favorites-update-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        // if ($schedules->count()) {
        //     $email->attachData($scheduleExcel, 'schedules-update-' . $this->start_date->format('Y-m-d') . '.xlsx');
        // }

        return $email;
    }

    private function getBuildersExcel($builders)
    {
        return Excel::raw(new BuildersExport($builders), BaseExcel::XLSX);
    }

    private function getUBuildersExcel($builders)
    {
        // return Excel::raw(new UBuildersExport($builders), BaseExcel::XLSX);
    }

    private function getPropertiesExcel($properties)
    {
        return Excel::raw(new PropertiesExport($properties), BaseExcel::XLSX);
    }

    private function getUsersExcel($users)
    {
        return Excel::raw(new UsersExport($users), BaseExcel::XLSX);
    }

    private function getFavoritesExcel($favorites)
    {
        return Excel::raw(new PropertyFavoritesExport($favorites), BaseExcel::XLSX);
    }

    private function getViewingsScheduled($schedules)
    {
        return Excel::raw(new ViewingSchedulesExport($schedules), BaseExcel::XLSX);
    }
}
