<?php

namespace App\Providers;

use App\Http\Helper;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Member', function () {
            return new Helper\Member();
        });
        $this->app->bind('MembershipStep', function () {
            return new Helper\MembershipStep();
        });
        $this->app->bind('User', function () {
            return new Helper\User();
        });
        $this->app->bind('Admin', function () {
            return new Helper\Admin();
        });
        $this->app->bind('Order', function () {
            return new Helper\Order();
        });

        $this->app->bind('Common', function () {
            return new Helper\Common();
        });

        $this->app->bind('Service', function () {
            return new Helper\Service();
        });
    }
}
