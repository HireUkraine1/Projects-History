<?php

namespace App\Providers;

use Illuminate\sportport\ServiceProvider;

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \Shortcode::register('gallery', \App\Shortcodes\GalleryShortcode::class);
        \Shortcode::register('slider', \App\Shortcodes\SliderShortcode::class);
        \Shortcode::register('banner', \App\Shortcodes\BannerShortcode::class);
        \Shortcode::register('category', \App\Shortcodes\CategoryShortcode::class);
        \Shortcode::register('club', \App\Shortcodes\ClubShortcode::class);
        \Shortcode::register('contact-form', \App\Shortcodes\ContactFormShortcode::class);
    }
}
