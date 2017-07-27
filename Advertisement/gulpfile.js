var elixir = require('laravel-elixir');

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

//sign-in superadmin
elixir(function(mix) {
     //commone css
    mix.styles([
        'css/bootstrap.min.css',
        'css/font-awesome.min.css',
        'css/smartadmin-production-plugins.min.css',
        'css/smartadmin-production.min.css',
        'css/smartadmin-skins.min.css',
        'css/smartadmin-rtl.min.css'
        
    ], 'public/protected/super-admin/css/style.css', 'resources/assets/protected/super-admin');
    //commone css 
    
    //template super-admin 
    mix.scripts([
        'js/app.config.js',
        'js/plugin/jquery-touch/jquery.ui.touch-punch.min.js',
        'js/bootstrap/bootstrap.min.js',
        'js/notification/SmartNotification.min.js',
        'js/smartwidgets/jarvis.widget.min.js',
        'js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js'
        
    ], 'public/protected/super-admin/js/common-first.min.js', 'resources/assets/protected/super-admin');
    
    mix.scripts([ 
        'js/plugin/sparkline/jquery.sparkline.min.js',
        'js/plugin/jquery-validate/jquery.validate.min.js',
        'js/plugin/masked-input/jquery.maskedinput.min.js',
        'js/plugin/select2/select2.min.js',
        'js/plugin/bootstrap-slider/bootstrap-slider.min.js',
        'js/plugin/msie-fix/jquery.mb.browser.min.js'
        
    ], 'public/protected/super-admin/js/common-second.min.js', 'resources/assets/protected/super-admin');
    
    mix.scripts([
        'js/plugin/fastclick/fastclick.min.js',
        'js/app.min.js',
        'js/speech/voicecommand.min.js',
        'js/smart-chat-ui/smart.chat.ui.min.js',
        'js/smart-chat-ui/smart.chat.manager.min.js'
        
    ], 'public/protected/super-admin/js/common-third.min.js', 'resources/assets/protected/super-admin');
    //end template super-admin  

    //user page
    mix.scripts([
        'js/plugin/datatables/jquery.dataTables.min.js',
        'js/plugin/datatables/dataTables.colVis.min.js',
        'js/plugin/datatables/dataTables.tableTools.min.js',
        'js/plugin/datatables/dataTables.bootstrap.min.js',
        'js/plugin/datatable-responsive/datatables.responsive.min.js',
        'custom/js/user.js'
        
    ], 'public/protected/super-admin/js/user.js', 'resources/assets/protected/super-admin');
    //user page

    //category page
    mix.scripts([
   
        'custom/js/category.js'
        
    ], 'public/protected/super-admin/js/category.js', 'resources/assets/protected/super-admin');
    //category page
    
    
    mix.scripts([
        'js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js',
        'js/plugin/bootstrapvalidator/bootstrapValidator.min.js',
        'custom/js/register-validate.js'
        
    ], 'public/protected/super-admin/js/register-validate.js', 'resources/assets/protected/super-admin');
    
  
});