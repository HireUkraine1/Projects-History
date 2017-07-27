<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditMember extends FormRequest
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
            "phone" => "required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/",
            "relationship" => "required",
            "birthdate" => "required|regex:/^[0-9]{1,2}\/[0-9]{2}\/[1-9]{4}$/",
            "winter_state" => "required",
            "winter_address" => "required",
            "winter_city" => "required",
            "winter_zip_code" => "required|regex:/^[0-9]{5}$/"
        ];
    }
}
