<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

use Illuminate\Http\Response;

Route::group(['middleware' => ['api']], function () {

    Route::get('api/instance/{id}', [
        'uses' => 'API\ApiController@getBranch'
    ]);

    Route::any('api/git-clone', [
        'uses' => 'API\ApiController@gitLog'
    ]);

});

Route::group(['middleware' => ['web']], function () {

    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', [
            'uses' => 'Users\SessionContrloller@login'
        ]);

        Route::POST('/', [
            'uses' => 'Users\SessionContrloller@loginPost'
        ]);
    });


    Route::group(['middleware' => ['auth']], function () {

        Route::get('/dashboard', [
            'uses' => 'Protect\DashboardController@index'
        ]);

        Route::get('/deploy', [
            'uses' => 'Protect\DeployController@index'
        ]);

        Route::post('/deploy/getBranchesList', [
            'uses' => 'Protect\DeployController@getBranchesList'
        ]);

        Route::post('/deploy/getInstancesList', [
            'uses' => 'Protect\DeployController@getInstancesList'
        ]);

        Route::post('/deploy/getCommitsList', [
            'uses' => 'Protect\DeployController@getCommitsList'
        ]);

        Route::post('/deploy/createBranch', [
            'uses' => 'Protect\DeployController@createBranch'
        ]);

        Route::post('/deploy/instanceIsExists', [
            'uses' => 'Protect\DeployController@instanceIsExists'
        ]);

        Route::post('/deploy/redirectPage', [
            'uses' => 'Protect\DeployController@redirectPage'
        ]);

        Route::post('/deploy/createInstance', [
            'uses' => 'Protect\DeployController@createInstance'
        ]);

        Route::get('/instances', [
            'uses' => 'Protect\InstanceController@index'
        ]);

        Route::get('/instances/{name}', [
            'uses' => 'Protect\InstanceController@instanceName'
        ]);

        Route::post('/instances/getInstances', [
            'uses' => 'Protect\InstanceController@getInstances'
        ]);

        Route::post('/instances/getInstanceLog', [
            'uses' => 'Protect\InstanceController@getInstanceLog'
        ]);

        Route::post('/instances/instanceAction', [
            'uses' => 'Protect\InstanceController@instanceAction'
        ]);

        Route::get('/users', [
            'uses' => 'Users\UserController@user'
        ]);

        Route::get('/settings', [
            'uses' => 'Users\SettingController@settings'
        ]);

        Route::post('/settings/new', [
            'uses' => 'Protect\SettingController@newSetting'
        ]);

        Route::post('/settings/delete', [
            'uses' => 'Protect\SettingController@deleteSetting'
        ]);

        Route::post('/settings/edit', [
            'uses' => 'Protect\SettingController@editSetting'
        ]);

        Route::get('/logout', [
            'uses' => 'Users\SessionContrloller@logout'
        ]);

    });

});
