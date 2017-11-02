<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">{{ __('adminpanel/adminpanel.template.create') }}</h4>
        </div>
        <div class="modal-body">
            <div id="error" class="alert alert-danger hide">
            </div>
            <form id="form-create-template" class="form-horizontal form-label-left">
                @include('adminpanel.template.fields')
            </form>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default"

               data-dismiss="modal">{{ __('adminpanel/adminpanel.common.btn.close') }}</a>
            <a type="button" class="btn btn-primary" id="save-template"
               data-trans-success="{{ __('adminpanel/adminpanel.common.action.create.success') }}"
            >{{ __('adminpanel/adminpanel.common.btn.save') }}</a>

        </div>
    </div>
</div>


