<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ __('adminpanel/adminpanel.page.create') }}</h4>
        </div>
        <div class="modal-body">
            <div id="error" class="alert alert-danger hide">
            </div>
            <form id="form-create-page" class="form-horizontal form-label-left">
                <input name="_token" value="{{ csrf_token() }}" type="hidden"/>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="url">
                        {{ __('adminpanel/adminpanel.page.fields.url') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="url"
                               class="form-control col-md-7 col-xs-12"
                               name="url"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="h1">
                        {{ __('adminpanel/adminpanel.page.fields.h1') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="h1"
                               class="form-control col-md-7 col-xs-12"
                               name="h1"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="title">
                        {{ __('adminpanel/adminpanel.page.fields.title') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="title"
                               class="form-control col-md-7 col-xs-12"
                               name="title"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="description">
                        {{ __('adminpanel/adminpanel.page.fields.description') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea id="description" required="required" class="form-control textarea" name="description"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="keywords">
                        {{ __('adminpanel/adminpanel.page.fields.keywords') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea id="keywords" required="required" class="form-control textarea" name="keywords"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div id="template-chose">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-xs-12" for="template_id">
                            {{ __('adminpanel/adminpanel.page.fields.template') }}
                            <span class="required">*</span>
                        </label>
                        <div class="col-sm-9 col-xs-12">
                            <select id="template_id"
                                    class="select2_single form-control"
                                    name="template_id"
                                    required>
                            </select>
                            <a id="template-create" href="#">
                                {{ __('adminpanel/adminpanel.page.btn.create_template') }}
                            </a>
                            <a id="template-edit" href="" hidden>
                                {{ __('adminpanel/adminpanel.page.btn.edit_template') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div id="template-create-block" style="display: none">
                    <hr>
                    @include('adminpanel.template.fields')
                    <div class="form-group">
                        <div class="col-sm-9 col-xs-12 col-sm-offset-3 col-xs-offset-12">
                            <a id="template-cancel" href="#">
                                {{ __('adminpanel/adminpanel.common.btn.cancel') }}
                            </a>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="sitemappriority">
                        {{ __('adminpanel/adminpanel.page.fields.sitemappriority') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <select id="sitemappriority"
                                class="form-control col-md-7 col-xs-12"
                                name="sitemappriority"
                                required>
                            @for($i = 0.1; $i <= 1; $i = $i + 0.1)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                           for="criticalcss">{{ __('adminpanel/adminpanel.page.fields.criticalcss') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea id="criticalcss" required="required" class="form-control textarea" name="criticalcss"
                                  rows="20"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="switch_editor">
                        {{ __('adminpanel/adminpanel.common.btn.switch_editor') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="checkbox" onclick="eAL.toggle('criticalcss');" accesskey="e" checked
                               id="switch_editor">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-xs-12" for="active">
                        {{ __('adminpanel/adminpanel.page.fields.active') }}
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <input id="active" class="form-check-input" name="active" value="0" type="hidden">
                        <input id="active" class="form-check-input" name="active" value="1" type="checkbox" checked>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default"
               data-dismiss="modal">{{ __('adminpanel/adminpanel.common.btn.close') }}</a>
            <a type="button" class="btn btn-primary" id="save-page"
               data-trans-success="{{ __('adminpanel/adminpanel.common.action.create.success') }}"
            >{{ __('adminpanel/adminpanel.common.btn.save') }}</a>
        </div>
    </div>
</div>
