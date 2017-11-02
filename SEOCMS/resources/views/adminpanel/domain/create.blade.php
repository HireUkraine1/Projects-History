<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title" >{{ __('adminpanel/adminpanel.domain.create') }}</h4>
        </div>
        <div class="modal-body">
            <div id="error" class="alert alert-danger hide">
            </div>
            <form id="form-create-domain" class="form-horizontal form-label-left">
                <input name="_token" value="{{ csrf_token() }}" type="hidden"/>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="domain_url">{{ __('adminpanel/adminpanel.domain.fields.domain_url') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-6 col-xs-12">
                        <input type="text" id="domain_url" name="domain_url" required="required" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="robotstxt">{{ __('adminpanel/adminpanel.domain.fields.robots_domain') }}
                    </label>
                    <div class="col-sm-6 col-xs-12">
                        <textarea id="robotstxt" required="required" class="form-control textarea" name="robotstxt" rows="10"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-sm-6 col-sm-offset-3 col-xs-12">
                        <input type="checkbox" name="master" value="on">{{ __('adminpanel/adminpanel.domain.fields.main_domain') }}
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default" data-dismiss="modal">{{ __('adminpanel/adminpanel.common.btn.close') }}</a>
            <a type="button" class="btn btn-primary" id="safe-domain"
               data-trans-success="{{ __('adminpanel/adminpanel.common.action.create.success') }}"
            >{{ __('adminpanel/adminpanel.common.btn.save') }}</a>
        </div>
    </div>
</div>
