<?php
Route::resource('/domain', 'DomainAliasController', ['except' => ['show', 'edit']]);
Route::resource('/redirect', 'RedirectController', ['except' => ['show']]);
Route::resource('/template', 'TemplateController');
Route::resource('/page', 'PageController', ['except' => ['show']]);
Route::get('/critical-css', 'CriticalCssController@index')->name('critical-css.index');
Route::get('/critical-css/generate', 'CriticalCssController@generate')->name('critical-css.generate');
