<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DomainAliasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $master = ($this->get('master') == 'on');
        $this->merge(['master' => $master]);
        return [
            'domain_url' => [
                'required',
                'url',
                'regex:#^http(s)?://[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}((:[0-9]{1,5})?\.*)?$#',
                Rule::unique('domains_alias')->ignore($this->id)->where('domain_url', $this->domain_url)
            ],
            "robotstxt" => 'max:10000'
        ];


    }
}
