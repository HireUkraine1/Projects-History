//noinspection JSUnresolvedFunction
var elixir = require('laravel-elixir');
// elixir.config.sourcemaps = false; // this handy for debugging reference for debugging
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
// edit Gulp set default arguments --production
elixir(function (mix) {

    // compression is done with gulp --production

    // Container
    var admincss = ['admin/smartadmin-production-plugins.scss',
        'admin/smartadmin-production.scss',
        'admin/smartadmin-skins.scss'
    ];

    // Twitter font
    mix.sass(['admin/fontawesome/font-awesome.scss'], 'public/css/lib/font-awesome.min.css')

        // Frontend
        .sass('frontend/all.scss', 'public/css/frontend/all.min.css', {outputStyle: 'compressed'})

        // ADMIN compressed
        .sass(admincss, 'public/css/admin/style.min.css', {outputStyle: 'compressed'})

        // Default dependenties for the admin
        .scripts([
                "lib/log.js",
            "lib/app.config.js",
            "lib/plugin/jquery-touch/jquery.ui.touch-punch.js",
            "../../../vendor/twbs/bootstrap/dist/js/bootstrap.js",
            "lib/notification/SmartNotification.js",
            "lib/plugin/jquery-validate/jquery.validate.js",
            "lib/plugin/masked-input/jquery.maskedinput.js",
            "lib/plugin/select2/select2.js",
            "lib/plugin/bootstrap-slider/bootstrap-slider.js",
            "lib/plugin/msie-fix/jquery.mb.browser.js",
            "lib/plugin/fastclick/fastclick.js",
            /* CUSTOM PART */
            "lib/app.js",
            "admin/custom.js",
        ], 'public/js/admin/all.js')

        // Frontend
        .scripts([
            "lib/log.js",
            "lib/ie10-viewport-bug-workaround.js",
            "../../../vendor/twbs/bootstrap/dist/js/bootstrap.js",
            /**"lib/smooth-scroll.js",**/
            "lib/parallax.js",
            "lib/smooth-scroll.js",
            "lib/waypoints.js",
            /*CUSTOM PARTS*/
            "frontend/main.js",
        ], 'public/js/frontend/all.js')
        .scripts([
            "frontend/edit.js",
        ], 'public/js/frontend/edit.js')

        // Sync with browser sync
        .browserSync({proxy: 'mtcv3.dev'});

});
