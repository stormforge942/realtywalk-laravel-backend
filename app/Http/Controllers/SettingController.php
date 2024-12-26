<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmailSettingsRequest;
use App\Http\Requests\UpdateGeneralSettingsRequest;
use App\Http\Requests\UpdateScriptsSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class SettingController extends Controller
{
    /**
     * Show general settings page
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings = new Setting;

        return view('settings.general', compact('settings'));
    }

    /**
     * Show email settings page
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function indexEmail(Request $request)
    {
        $settings = new Setting;
// dd($settings);
        return view('settings.email', compact('settings'));
    }

    /**
     * Get terms of service setting
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getByName(Request $request)
    {
        $key = $request->query('key', null);

        if (!$key && in_array($key, ['terms_of_service', 'privacy_policy'])) {
            abort(400, 'Key parameter is required and must be an allowed key.');
        }

        $setting = Setting::whereName($key)->firstOrFail();

        return $setting->value;
    }

    /**
    * Show settings scripts for admin page
    */
    public function indexScripts()
    {
        $header_scripts = removeTrailingQuotes(Setting::getBy('header_scripts'));
        $footer_scripts = removeTrailingQuotes(Setting::getBy('footer_scripts'));
        return view('settings.scripts', compact('header_scripts', 'footer_scripts'));
    }

    /**
     * Shows builder settings for admin page
     */
    public function indexBuilder()
    {
        $builder_settings = Setting::getBy('builder_settings');

        return view('settings.builder', ['settings' => $builder_settings]);
    }

    /**
     * Update email setting
     *
     * @param  \App\Http\Requests\UpdateScriptsSettingsRequest $request
     */
    public function updateScripts(UpdateScriptsSettingsRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            Setting::updateOrCreate(['name' => 'header_scripts'], [
                'value' => '"'.$validated['header_scripts'].'"'
            ]);
            Setting::updateOrCreate(['name' => 'footer_scripts'], [
                'value' => '"'.$validated['footer_scripts'].'"'
            ]);
        });

        Flash::success('Scripts have been updated succesfully.')->important();

        return redirect(route('settings.scripts'));
    }

    /**
     * Update general setting
     *
     * @param  \App\Http\Requests\UpdateGeneralSettingsRequest $request
     */
    public function updateGeneralSettings(UpdateGeneralSettingsRequest $request)
    {
        DB::transaction(function () use ($request) {
            // site title
            $site_title = $request->input('site_title');
            Setting::whereName('site_title')->update([
                'value' => '"'.$site_title.'"'
            ]);

            // site expanded sidebar logo
            if ($request->hasFile('site_logo_expanded')) {
                $file = $request->file('site_logo_expanded');

                $this->saveLogo($file, 'site_logo_expanded');
            }

            // site collapsed sidebar logo
            if ($request->hasFile('site_logo_collapsed')) {
                $file = $request->file('site_logo_collapsed');

                $this->saveLogo($file, 'site_logo_collapsed');
            }

            $tos = addslashes($request->input('terms_of_service'));
            $tos = str_replace("\r\n", "", $tos);
            Setting::whereName('terms_of_service')->update([
                'value' => '"'.$tos.'"'
            ]);

            $pp = addslashes($request->input('privacy_policy'));
            $pp = str_replace("\r\n", "", $pp);
            Setting::whereName('privacy_policy')->update([
                'value' => '"'.$pp.'"'
            ]);
        });

        Flash::success('General settings has been updated succesfully.')->important();

        return redirect(route('settings.index'));
    }

    /**
     * Update builder settings
     */
    public function updateBuilderSettings(Request $request)
    {
        DB::transaction(function () use ($request) {
            $blacklist_names = $request->input('blacklist_names');
            $generic_names = $request->input('generic_names');
            $builder_link_enabled = filter_var($request->input('builder_link_enabled'), FILTER_VALIDATE_BOOL);

            $data = compact('blacklist_names', 'generic_names', 'builder_link_enabled');

            Setting::updateOrCreate([
                'name' => 'builder_settings'
            ], ['value' => $data]);
        });

        Flash::success('Builder settings has been updated succesfully.')->important();

        return redirect(route('settings.builder'));
    }

    protected function saveLogo($file, $name)
    {
        $old_logo    = Setting::getBy($name);
        $extension   = $file->getClientOriginalExtension();
        $isCollapsed = $name == 'site_logo_collapsed';
        $filename    = 'logo'.($isCollapsed ? '-small' : '').'.' . $extension;

        if ($old_logo) {
            Storage::disk('Wasabi')->delete('site' . $old_logo);
        }

        Storage::disk('Wasabi')->putFileAs('site', $file, $filename);

        Setting::whereName($name)->update([
            'value' => '"' . $filename . '"'
        ]);
    }

    /**
     * Update email setting
     *
     * @param  \App\Http\Requests\UpdateEmailSettingsRequest $request
     */
    public function updateEmailSettings(UpdateEmailSettingsRequest $request)
    {

        $input = $request->all();
        $name = Setting::NOTIFICATION_EMAIL;
        if ($request->type === 'secretary') {
            $name = Setting::SECRETARY_NOTIFICATION_EMAIL;
        }

        DB::transaction(function () use ($input, $name) {

            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $input[$name]]
            );
        });

        Flash::success('Email settings has been updated successfully.')->important();

        return redirect(route('settings.email'));

    }
}
