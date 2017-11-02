@extends('layouts.panel')

@section('title')
    {{ __('adminpanel/adminpanel.robots.name') }}
@endsection

@section('content')
    <div id="admin-template-page">
        <div class="page-title">
            <div class="title_left">
                <h3>{{ __('adminpanel/adminpanel.template.list_title') }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <ul class="nav navbar-left panel_toolbox">
                            <li>
                                <button id="create_template"
                                        class="btn btn-success">{{ __('adminpanel/adminpanel.common.btn.create') }}</button>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="template-datatable" class="table table-striped table-bordered dt-responsive nowrap"
                               cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>{{ __('adminpanel/adminpanel.common.fields.id') }}</th>
                                <th>{{ __('adminpanel/adminpanel.template.fields.name') }}</th>
                                <th>{{ __('adminpanel/adminpanel.template.fields.virtualroot') }}</th>
                                <th>{{ __('adminpanel/adminpanel.common.fields.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-window" tabindex="-1" role="dialog" aria-hidden="true">
            @if(isset($edit))
                {!! $edit !!}
            @endif
        </div>
    </div>
@endsection

@push('before-scripts')
    <script type="text/javascript" src="/vendors/edit_area/edit_area_full.js"></script>
@endpush

@push('scripts')
    <!-- Pretty Alert -->
    <script src="/vendors/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Datatables -->
    <script src="/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script>
        @if(isset($edit))
            $(document).ready(function () {
                $('#modal-window').modal('toggle');
            });
        @endif
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" type="text/css" href="/vendors/sweetalert/dist/sweetalert.css">
@endpush

@push('styles')
    <link rel="stylesheet" type="text/css" href="/vendors/sweetalert/dist/sweetalert.css">
@endpush