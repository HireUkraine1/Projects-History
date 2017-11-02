<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ __('adminpanel/adminpanel.redirect.create') }}</h4>
        </div>
        <div class="modal-body">
            <div id="error" class="alert alert-danger hide">
            </div>
            <form id="form-create-redirect" class="form-horizontal form-label-left">
                <input name="_token" value="{{ csrf_token() }}" type="hidden"/>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="oldurl">
                        {{ __('adminpanel/adminpanel.redirect.fields.oldurl') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-6 col-xs-12">
                        <input id="oldurl"
                               class="form-control col-md-7 col-xs-12"
                               name="oldurl"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="newurl">
                        {{ __('adminpanel/adminpanel.redirect.fields.newurl') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-6 col-xs-12">
                        <input id="newurl"
                               class="form-control col-md-7 col-xs-12"
                               name="newurl"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="coderedirect">
                        {{ __('adminpanel/adminpanel.redirect.fields.coderedirect') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-6 col-xs-12">
                        <select id="coderedirect" class="form-control col-md-7 col-xs-12" name="coderedirect" required>
                            <option value="301">301</option>
                            <option value="302">302</option>
                            <option value="303">303</option>
                            <option value="307">307</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default"
               data-dismiss="modal">{{ __('adminpanel/adminpanel.common.btn.close') }}</a>
            <a type="button" class="btn btn-primary" id="save-redirect"
               data-trans-success="{{ __('adminpanel/adminpanel.common.action.create.success') }}"
            >{{ __('adminpanel/adminpanel.common.btn.save') }}</a>
        </div>
    </div>
</div>
