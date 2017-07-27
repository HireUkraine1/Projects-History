<?php

namespace App\Http\Requests;

class RegisterUser extends Request
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
            'password' => 'required|confirmed|min:6',
            'email' => 'email|max:30|unique:users,email',
            'codGet' => 'required'
        ];
    }
}
