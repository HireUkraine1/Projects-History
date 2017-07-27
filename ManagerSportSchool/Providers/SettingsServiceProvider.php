<?php

namespace App\Providers;

use App\Models\Setting;
use Config;
use Illuminate\sportport\Facades\Schema;
use Illuminate\sportport\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // only use the Settings package if the Settings table is present in the database
        if (!\App::runningInConsole() && count(Schema::getColumnListing('settings'))) {
            // get all settings from the database
            $settings = Setting::where('active', '=', 'Yes')->get();
            // bind all settings to the Laravel config, so you can call them like
            // Config::get('settings.contact_email')
            foreach ($settings as $key => $setting) {
                Config::set('settings.' . $setting->key, $setting->value);
            }
        }
    }

}
