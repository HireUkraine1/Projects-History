<?php

namespace App\Providers;

use App\Models\Redirect;
use App\Observers\RedirectObserver;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class RedirectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Redirect::observe(RedirectObserver::class);

        Validator::extend('check_uri', function ($attribute, $value, $parameters, $validator) {
            $request = \Request::create($value);
            $routes  = new RouteCollection();

            collect(\Route::getRoutes()->getRoutes())
                ->each(function ($item) use (&$routes) {
                    if (in_array('redirect', $item->middleware())) {
                        $routes->add($item);
                    }
                });

            try {
                $routes->match($request);
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        });

        Validator::extend('protected_uri', function ($attribute, $value, $parameters, $validator) {
            $protectedRoutes = config('protected-routes')['list'];

            array_walk($protectedRoutes, function ($item) {
                return trim($item, '{}');
            });

            return !in_array($value, $protectedRoutes);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
