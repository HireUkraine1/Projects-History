<form class="form" id="domain_{{$domain->id}}" autocomplete="off">
    <input name="_token" value="{{ csrf_token() }}" type="hidden"/>
    <input name="id" value="{{$domain->id}}" type="hidden"/>
    <div class="form__left">
        <input type="url" name="domain_url" value="{{ $domain->domain_url }}">
        <div class="form-gorup domain-check">
            <label for="main-domain{{$domain->id}}">{{ __('adminpanel/adminpanel.domain.fields.main_domain') }}</label>
            <input type="checkbox"
                   id="main-domain{{$domain->id}}"
                   name="master"
                   class="js-switch switcher"
                    {{ $domain->master ? 'checked' : ''  }}
            />
        </div>
        <button type="button" class="btn btn-danger delete-action"
                data-trans-question="{{ __('adminpanel/adminpanel.common.action.delete.question') }}"
                data-trans-success="{{ __('adminpanel/adminpanel.common.action.delete.success') }}"
                data-id="{{$domain->id}}">{{ __('adminpanel/adminpanel.common.btn.delete') }}
        </button>
        <button type="button" class="btn btn-warning update-action"
                data-trans-question="{{ __('adminpanel/adminpanel.common.action.update.question') }}"
                data-trans-success="{{ __('adminpanel/adminpanel.common.action.update.success') }}"
                data-id="{{$domain->id}}">{{ __('adminpanel/adminpanel.common.btn.save') }}
        </button>
    </div>
    <div class="form__right">
        <textarea class="textarea" name="robotstxt">{{ $domain->robotstxt }}</textarea>
    </div>
</form>