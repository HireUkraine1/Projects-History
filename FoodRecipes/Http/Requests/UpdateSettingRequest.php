<?php

namespace App\Http\Requests;

class UpdateSettingRequest extends Request
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
        return [
            'setting_key' => 'required|max:100',
            'setting_value' => 'required',
            'group' => 'required',
            'setting_key_old' => 'required'
        ];
    }
}
