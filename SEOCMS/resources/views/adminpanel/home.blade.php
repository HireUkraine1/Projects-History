@extends('layouts.panel')

@section('title')
    {{ __('adminpanel/adminpanel.home.name') }}
@endsection

@section('content')
    <div id="home-page">
        <div class="page-title">
            <div class="title_left">
                <h2>{{ __('adminpanel/adminpanel.home.name') }}</h2>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    <div class="x_title">
                        <ul class="nav navbar-left panel_toolbox">

                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-window" tabindex="-1" role="dialog" aria-hidden="true"></div>
    </div>
@endsection



