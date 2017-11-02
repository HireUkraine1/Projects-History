@extends('layouts.panel')

@section('title')
    {{ __('adminpanel/adminpanel.critical.name') }}
@endsection

@section('content')
    <div id="admin-critical-page">
        <div class="page-title">
            <div class="title_left">
                <h3>{{ __('adminpanel/adminpanel.critical.list_title') }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    <div class="x_content">

                        <div class="col-xs-12">
                            <div class="col-xs-6 form-inline">
                                <div class="form-group form-check">
                                    <div class="form-group form-check">
                                        <label class="form-check-label" for="active">
                                            <input class="form-check-input" id="process_all" type="checkbox">
                                            {{ __('adminpanel/adminpanel.critical.fields.process_all') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <a data-success="{{ trans('adminpanel/adminpanel.critical.generate_message') }}" id="critical-generate" type="button" class="btn btn-danger">
                                        {{ __('adminpanel/adminpanel.critical.btn.generate') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-xs-6 pull-right">
                                <div class="form-group">
                                    <a id="critical-compile-update" type="button" class="btn btn-default">
                                        <span class="fa fa-refresh"></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <label class="control-label col-sm-12" for="resolutions">
                                    {{ __('adminpanel/adminpanel.critical.fields.resolutions') }}
                                    <textarea id="resolutions"
                                              name="resolutions"
                                              rows="6"
                                              class="form-control">1280*1024&#13;&#10;640*380&#13;&#10;320*640</textarea>
                                </label>
                            </div>
                            <div class="col-sm-3 col-xs-12">
                                <label class="control-label col-sm-12" for="routes">
                                    {{ __('adminpanel/adminpanel.critical.fields.routes') }}
                                    <textarea id="routes"
                                              name="routes"
                                              rows="6"
                                              class="form-control"></textarea>
                                </label>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <table id="critical-datatable"
                                       class="table table-striped table-bordered dt-responsive nowrap"
                                       cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>{{ __('adminpanel/adminpanel.common.fields.id') }}</th>
                                        <th>{{ __('adminpanel/adminpanel.critical.fields.date') }}</th>
                                        <th>{{ __('adminpanel/adminpanel.critical.fields.url') }}</th>
                                        <th>{{ __('adminpanel/adminpanel.critical.fields.status') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-window" tabindex="-1" role="dialog" aria-hidden="true"></div>
        </div>
    </div>
    @endsection

    @push('scripts')
        <!-- Pretty Alert -->
        <script src="/vendors/sweetalert/dist/sweetalert.min.js"></script>
        <!-- Datatables -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js"></script>
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
    @endpush

    @push('styles')
        <link rel="stylesheet" type="text/css" href="/vendors/sweetalert/dist/sweetalert.css">
    @endpush