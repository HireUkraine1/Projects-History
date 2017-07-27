<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdmin extends FormRequest
{
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
            "name" => "required|min:2|max:45|unique:admins,name",
            "email" => "required|email|unique:admins,email",
            "role_id" => "required|digits_between:1,3",
            "password" => "required|min:6|confirmed"
        ];
    }
}
