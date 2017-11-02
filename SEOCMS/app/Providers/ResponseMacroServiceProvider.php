<?php
namespace App\Providers;

use App\Support\Robots\TxtResponse;
use App\Support\Sitemap\XmlResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('sitemap', function ($value) {
            $domain = config('settings.current_domain') . '/';
            return (new XmlResponse($domain))->sitemap($value);
        });

        Response::macro('robots', function ($value) {
            $domain = config('settings.current_domain') . '/';
            return (new TxtResponse($domain))->rules($value);
        });
    }
}