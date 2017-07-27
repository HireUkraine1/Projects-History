<?php
// Frontend
Route::group([
    // https://github.com/mcamara/laravel-localization
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect']
], function () {
    Route::get('/', 'HomeController@view');

    try {
        Route::get(LaravelLocalization::transRoute('frontend.recipes_route') . '/{slug}', [
            'uses' => 'RecipesController@view',
            'as' => 'recipes'
        ]);
    } catch (Exception $exc) {
        // when lang table not installed
    }
});

// Admin group
Route::group([
    // https://github.com/mcamara/laravel-localization
    'prefix' => LaravelLocalization::setLocale() . '/ldesign-admin',
    'middleware' => ['localeSessionRedirect', 'localizationRedirect']
], function () {

    // LOGIN / Register
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::post('login', 'Auth\AuthController@postLogin');
    Route::get('register', 'Auth\AuthController@getRegister');
    Route::post('register', 'Auth\AuthController@postRegister');

    // Admin routers restricted // https://github.com/Zizaco/entrust
    Route::group(['middleware' => 'role:admin|superadmin'], function () {

        // enable debugging
        \Debugbar::disable();

        Route::get('/', 'Admin\HomeController@index');
        Route::get('logout', 'Auth\AuthController@getLogout');

        // Resource

        //Settings

        Route::resource('settings', 'Admin\SettingsController');

        Route::get('settings_datatables', [
            'uses' => 'Admin\SettingsController@anyData',
            'as' => 'datatables.settings'
        ]);

        Route::post('/settings/update/', 'Admin\SettingsController@update');

        Route::get('settings/destroy/{id}', [
            'uses' => 'Admin\SettingsController@destroy',
            'as' => 'settings.destroy',
        ]);

        //Permissions
        Route::resource('permissions', 'Admin\PermissionsController');

        Route::get('permissions_datatables', [
            'uses' => 'Admin\PermissionsController@anyData',
            'as' => 'datatables.permissions'
        ]);

        Route::post('/permissions/update/', 'Admin\PermissionsController@update');

        Route::get('permissions/destroy/{id}', [
            'uses' => 'Admin\PermissionsController@destroy',
            'as' => 'permissions.destroy',
        ]);


        //Roles
        Route::resource('roles', 'Admin\RolesController');

        Route::post('/roles/update/', 'Admin\RolesController@update');

        Route::get('roles/destroy/{id}', [
            'uses' => 'Admin\RolesController@destroy',
            'as' => 'roles.destroy',
        ]);

        Route::get('roles_datatables', [
            'uses' => 'Admin\RolesController@anyData',
            'as' => 'datatables.roles'
        ]);


        // Recipes
        Route::get('recipes_datatables', [
            'uses' => 'Admin\RecipesController@anyData',
            'as' => 'datatables.recipes'
        ]);
        Route::resource('recipes', 'Admin\RecipesController');

        // Users
        Route::get('users_datatables', [
            'uses' => 'Admin\UsersController@anyData',
            'as' => 'datatables.users'
        ]);


        //Users
        Route::resource('users', 'Admin\UsersController');
        Route::post('/users/update/', 'Admin\UsersController@updateUser');


        Route::get('users/destroy/{id}', [
            'uses' => 'Admin\UsersController@destroy',
            'as' => 'users.destroy',
        ]);

        Route::get('clearcache', function () {
            event(new App\Events\TranslationUpdated());

            return redirect(LaravelLocalization::getLocalizedURL(null, ADMINPANEL));
        });

        // Inline edit frontpage
        Route::get('edit/frontpage', 'Admin\FrontpageEditController@index');

        // Translation panel
        Route::group([
            'namespace' => '\Admin',
            'prefix' => '/translate',
        ], function () {

            Route::get('/', [
                'uses' => 'TranslationsController@getIndex',
                'as' => 'translations.index',
            ]);
            Route::get('/groups', [
                'uses' => 'TranslationsController@getGroups',
                'as' => 'translations.groups',
            ]);
            Route::get('/locales', [
                'uses' => 'TranslationsController@getLocales',
                'as' => 'translations.locales',
            ]);
            Route::post('/items', [
                'uses' => 'TranslationsController@postItems',
                'as' => 'translations.items',
            ]);
            Route::post('/store', [
                'uses' => 'TranslationsController@postStore',
                'as' => 'translations.store',
            ]);
            Route::post('/translate', [
                'uses' => 'TranslationsController@postTranslate',
                'as' => 'translations.translate',
            ]);
            Route::post('/delete', [
                'uses' => 'TranslationsController@postDelete',
                'as' => 'translations.delete',
            ]);
        });
    });
});
