<?php

namespace App\Http\Requests;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
        $id = request()->route('page');

        return [
            'url' => [
                'required',
                Rule::unique('pagesheets')->ignore($id)->where('url', $this->url),
                'check_uri',
                'protected_uri',
                /* validation.custom.url.regex message */
                'regex:/\//'
            ],
            'h1' => [
                'required',
                'max:255',
                Rule::unique('pagesheets')->ignore($id)->where('h1', $this->h1),

            ],
            'title' => [
                'required',
                'max:255',
                Rule::unique('pagesheets')->ignore($id)->where('title', $this->title),

            ],
            'description' => 'required|max:255',
            'keywords' => 'required|max:255',
            'template_id' => 'required|exists:templates,id',
            'sitemappriority' => 'required|numeric|between:0.0,1.0',
            'criticalcss' => 'max:65000',
        ];
    }

}