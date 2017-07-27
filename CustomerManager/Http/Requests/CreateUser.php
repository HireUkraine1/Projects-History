<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUser extends FormRequest
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
            "first_name" => "required|min:2|max:45",
            "last_name" => "required|min:2|max:45",
            "email" => "required|email|unique:users,email",
            "phone" => "required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/|unique:users,phone",
            "status" => "required|digits_between:0,1",
            "entrance_fee" => "required|digits_between:0,1",
            "note" => "max:255",
            "password" => "required|min:6|confirmed"
        ];
    }
}
