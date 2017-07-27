<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest
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
            "first_name" => "required|min:2|max:45",
            "last_name" => "required|min:2|max:45",
            "email" => "required|email",
            "balance" => "required|numeric",
            "phone" => "required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/",
            "status" => "digits_between:0,1",
            "entrance_fee" => "digits_between:0,1",
            "note" => "max:255",
            "password" => "min:6|confirmed"
        ];
    }

}
