<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
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
        $id = request()->route('template');

        return [
            'virtualroot' => [
                'required',
                'max:255',
                Rule::unique('templates')->ignore($id)
            ],
            'name'        => 'required|max:255',
            'body'        => 'max:65000'
        ];
    }
}
