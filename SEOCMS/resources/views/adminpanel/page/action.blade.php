<button class="btn btn-warning edit" data-page-id="{{ $page->id }}">
    {{ __('adminpanel/adminpanel.common.btn.edit') }}
</button>
<button class="btn btn-danger delete"
        data-page-id="{{ $page->id }}"
        data-trans-question="{{ __('adminpanel/adminpanel.common.action.delete.question') }}"
        data-trans-success="{{ __('adminpanel/adminpanel.common.action.delete.success') }}">
    {{ __('adminpanel/adminpanel.common.btn.delete') }}
</button>