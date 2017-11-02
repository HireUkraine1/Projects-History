<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}" />

        <title> {{ config('app.name') }} | @yield('title', 'Adminpanel')</title>

        <link href="{{ asset('/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/custom.min.css') }}" rel="stylesheet">
        @stack('styles')
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                    <div class="left_col scroll-view">
                        <div class="navbar nav_title" style="border: 0;">
                            <a href="/" class="site_title"><span>{{ config('app.name') }}</span></a>
                        </div>
                        <div class="clearfix"></div>
                        <!-- menu profile quick info -->
                        @include('layouts.parts.menu_profile_quick_info')
                        <!-- /menu profile quick info -->
                        <br/>
                        <!-- sidebar menu -->
                        @include('layouts.parts.sidebar_menu')
                        <!-- /sidebar menu -->
                    </div>
                </div>
                <!-- top navigation -->
                @include('layouts.parts.user_navbar')
                <!-- /top navigation -->
                <!-- page content -->
                <div class="right_col" role="main">
                    @yield('content')    
                </div>
                <!-- /page content -->
                <!-- footer content -->
                <footer>
                    <div class="pull-right">
                      {{ config('app.name') }}
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
            </div>
        </div>
        <!-- jQuery -->
        <script src="{{ asset('/vendors/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        @stack('before-scripts')
        <!-- Custom Theme Scripts -->
        <script src="{{ asset('/js/custom.min.js') }}"></script>
        @stack('scripts')
    </body>
</html>
