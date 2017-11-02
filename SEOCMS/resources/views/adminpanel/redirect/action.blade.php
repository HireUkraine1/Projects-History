<button class="btn btn-warning edit" data-redirect-id="{{ $redirect->id }}">
    {{ __('adminpanel/adminpanel.common.btn.edit') }}
</button>
<button class="btn btn-danger delete"
        data-redirect-id="{{ $redirect->id }}"
        data-trans-question="{{ __('adminpanel/adminpanel.common.action.delete.question') }}"
        data-trans-success="{{ __('adminpanel/adminpanel.common.action.delete.success') }}">
    {{ __('adminpanel/adminpanel.common.btn.delete') }}
</button>