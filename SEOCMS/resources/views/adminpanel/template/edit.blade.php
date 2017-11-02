<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ __('adminpanel/adminpanel.template.edit') }}</h4>
        </div>
        <div class="modal-body">
            <div id="error" class="alert alert-danger hide"></div>
            <form id="form-edit-template" data-template-id="{{ $template->id }}" class="form-horizontal form-label-left">
                <input name="_token" value="{{ csrf_token() }}" type="hidden"/>
                <input name="_method" value="PUT" type="hidden"/>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="oldurl">
                        {{ __('adminpanel/adminpanel.template.fields.name') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="name"
                               class="form-control col-md-7 col-xs-12"
                               name="name"
                               value="{{ $template->name }}"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="virtualroot">
                        {{ __('adminpanel/adminpanel.template.fields.virtualroot') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="virtualroot"
                               class="form-control col-md-7 col-xs-12"
                               name="virtualroot"
                               value="{{ $template->virtualroot }}"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="t-body">{{ __('adminpanel/adminpanel.template.fields.body') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea id="t-body" required="required"  class=" form-control textarea" name="body" rows="40">{{ $template->body }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="switch_editor">
                        {{ __('adminpanel/adminpanel.common.btn.switch_editor') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="checkbox" onclick="eAL.toggle('t-body');" accesskey="e" checked id="switch_editor">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default"
               data-dismiss="modal">{{ __('adminpanel/adminpanel.common.btn.close') }}</a>
            <a type="button" class="btn btn-primary"
               data-trans-success="{{ __('adminpanel/adminpanel.common.action.update.success') }}"
               id="update-template">{{ __('adminpanel/adminpanel.common.btn.save') }}</a>
        </div>
    </div>
</div>
