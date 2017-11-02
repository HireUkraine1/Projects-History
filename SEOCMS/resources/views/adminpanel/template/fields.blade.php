<input name="_token" value="{{ csrf_token() }}" type="hidden"/>
<div class="form-group">
    <label class="control-label col-sm-3 col-xs-12" for="oldurl">
        {{ __('adminpanel/adminpanel.template.fields.name') }}
        <span class="required">*</span>
    </label>

    <div class="col-sm-9 col-xs-12">
        <input id="name"
               class="form-control col-md-7 col-xs-12"
               name="name"
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
               required>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"
           for="body">{{ __('adminpanel/adminpanel.template.fields.body') }}
    </label>
    <div class="col-sm-9 col-xs-12">
        <textarea id="t-body" required="required" class="form-control textarea" name="body" rows="10"></textarea>
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
