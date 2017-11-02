@extends('layouts.panel')

@section('title')
    {{ __('adminpanel/adminpanel.robots.name') }}
@endsection

@section('content')
    <div class="domain-page">
        <div class="page-title">
            <div class="title_left">
                <h3>{{ __('adminpanel/adminpanel.domain.list_title') }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    <div class="x_title">
                        <ul class="nav navbar-left panel_toolbox">
                            <li>
                                <button type="button" class="btn btn-minw btn-success" id="create_domain">
                                    {{ __('adminpanel/adminpanel.common.btn.create') }}
                                </button>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div id="domains">
                            @foreach($domainCollection as $domain)
                                @include('adminpanel.domain.domain', ['domain' => $domain])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-window" tabindex="-1" role="dialog" aria-hidden="true"></div>
    </div>
@endsection

@push('scripts')
    <!-- Pretty Alert -->
    <script src="/vendors/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Pretty checkbox -->
    <script src="/vendors/switchery/dist/switchery.min.js"></script>
@endpush

@push('styles')
    <!-- Pretty Alert -->
    <link rel="stylesheet" type="text/css" href="/vendors/sweetalert/dist/sweetalert.css">
    <!-- Pretty checkbox -->
    <link href="/vendors/switchery/dist/switchery.min.css" rel="stylesheet">
@endpush