<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdditionMember extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:45',
            'last_name' => 'required|min:2|max:45',
            'birthdate' => 'required|date_format:"m/d/Y"|before:today|after:01/01/1900|regex:/[0-9]{1,2}\/[0-9]{2}\/[0-9]{4}/',
            'relation_id' => 'required|regex:/^[1-9]{1}$/',
            'cell_phone' => 'regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/',
            'winter_state' => 'required|min:2|max:45',
            'winter_city' => 'required|min:2|max:45',
            'winter_address' => 'required|min:2|max:45',
            'winter_zip_code' => 'required|regex:/^[0-9]{5}$/',
            'primary_email' => 'email',
        ];
    }

    public function messages()
    {
        return [
            'birthdate.date_format' => 'Please put in format MM/DD/YYYY',
            'birthdate.required' => 'Birthdate is required',
            'birthdate.regex' => 'Please put in format MM/DD/YYYY',
            'birthdate.before' => 'Invalid input data',
            'birthdate.after' => 'Invalid input data',
        ];
    }
}
