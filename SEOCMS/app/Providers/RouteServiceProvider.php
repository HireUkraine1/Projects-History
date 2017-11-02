<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->getFilesRoutes();
        $this->getAuthRoutes();
        $this->getAdminpanelRoutes();
        $this->getPostRoutes();
        $this->getErrorsPages();
        $this->getPageRoutes();
    }

    /**
     * Define the dynamic files routes
     *
     * @return void
     */
    protected function getFilesRoutes()
    {
        Route::namespace('App\Http\Controllers')
            ->group(function () {

                Route::get('{sitemap}', 'SitemapController@index')
                    ->where('sitemap', '(?i:sitemap)')
                    ->name('sitemap');

                Route::get('{robots}', 'RobotController@index')
                    ->where('robots', '(?i:robots\.txt)')
                    ->name('robots');
            });
    }


    /**
     *
     * Define the auth routes.
     *
     * @return void
     *
     */
    protected function getAuthRoutes()
    {
        Route::middleware(['web'])
            ->namespace('App\Http\Controllers\Auth')
            ->group(function () {
                // Authentication Routes...
                $this->get('loginadmin', 'LoginController@showLoginForm')
                    ->name('login');

                $this->post('loginadmin', 'LoginController@login');

                $this->post('logoutadmin', 'LoginController@logout')
                    ->name('logout');

                // Password Reset Routes...
                $this->get('password/reset', 'ForgotPasswordController@showLinkRequestForm')
                    ->name('password.request');

                $this->post('password/email', 'ForgotPasswordController@sendResetLinkEmail')
                    ->name('password.email');

                $this->get('password/reset/{token}', 'ResetPasswordController@showResetForm')
                    ->name('password.reset');

                $this->post('password/reset', 'ResetPasswordController@reset');
            });

    }

    /**
     * Define the admin panel routes
     *
     * @return void
     */
    protected function getAdminpanelRoutes()
    {
        Route::prefix('adminpanel')
            ->middleware(['web', 'auth'])
            ->namespace('App\Http\Controllers\Adminpanel')
            ->group(base_path('routes/admin.php'));

    }

    /**
     * Define the post method.
     *
     * @return void
     */
    protected function getPostRoutes()
    {
        Route::namespace('App\Http\Controllers\FrontendControllers')
            ->group(function () {

                Route::middleware(['json'])
                    ->post('{sendorder}', 'SendOrderController@index')
                    ->where('sendorder', '(?i:sendorder)')
                    ->name('sendorder');

                Route::middleware(['json'])
                    ->post('{getblock}', 'GetBlockController@index')
                    ->where('getblock', '(?i:getblock)')
                    ->name('getblock');
            });

    }

    /**
     * Define the error pages routes
     */
    protected function getErrorsPages()
    {
        Route::middleware(['web'])
            ->namespace('App\Http\Controllers')
            ->group(function () {
                Route::get('404', 'ErrorsControllers@error_404')
                    ->name('error.404');
            });
    }


    /**
     * Define the "web" dynamic routes.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function getPageRoutes()
    {
        Route::middleware(['web'])
            ->middleware(['redirect'])
            ->namespace('App\Http\Controllers\FrontendControllers')
            ->group(function () {
                Route::get('{slug}', 'PageController@index')
                    ->where('slug', '([0-9a-zA-Z_-]+\/?)*');
            });
    }


}
