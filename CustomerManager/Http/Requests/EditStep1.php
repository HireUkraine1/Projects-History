<?php

namespace App\Http\Requests;

use App\Model;
use Illuminate\Foundation\Http\FormRequest;


class EditStep1 extends FormRequest
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
        $fee = Model\Setting::where('slug', '=', 'fee')->select('value')->first();
        return [
            'first_name.0' => 'required|min:2|max:45',
            'last_name.0' => 'required|min:2|max:45',
            'birthdate.0' => 'date_format:"m/d/Y"|required|before:18 years ago +1 day|after:01/01/1900|regex:/[0-9]{1,2}\/[0-9]{2}\/[0-9]{4}/',
            'first_name.1' => 'required_with:last_name.1|min:2|max:45',
            'last_name.1' => 'required_with:first_name.1|max:45',
            'birthdate.1' => 'required_with:first_name.1,last_name.1|date_format:"m/d/Y"|before:today|after:01/01/1900|regex:/[0-9]{1,2}\/[0-9]{2}\/[0-9]{4}/',//regex:/^[0-9]{2}\/[0-9]{2}\/[1-9]{4}$/
            'primary_email' => 'required|email',
            'secondary_email' => 'email',
            'winter_phone' => 'required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/',
            'summer_phone' => 'required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/',
            'cell_phone' => 'required|regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/',
            'winter_state' => 'required|min:2|max:45',
            'winter_city' => 'required|min:2|max:45',
            'winter_address' => 'required|min:2|max:45',
            'winter_zip_code' => 'required|regex:/^[0-9]{5}$/',
            'summer_town' => 'required|min:2|max:45',
            'summer_address' => 'required|min:2|max:45',
            'summer_zip_code' => 'required|regex:/^[0-9]{5}$/',
            'summer_state' => 'required|min:2|max:45',
            'fee' => 'regex:/^' . $fee->value . '$/',
            'due' => 'required|digits_between:1,3',
        ];
    }

    public function messages()
    {
        return [
            'birthdate.0.date_format' => 'Please put in format MM/DD/YYYY',
            'birthdate.1.date_format' => 'Please put in format MM/DD/YYYY',
            'birthdate.0.required' => 'Birthdate is required',
            'birthdate.1.required' => 'Birthdate is required',
            'birthdate.0.regex' => 'Please put in format MM/DD/YYYY',
            'birthdate.1.regex' => 'Please put in format MM/DD/YYYY',
            'birthdate.0.before' => 'Invalid input data',
            'birthdate.1.before' => 'Invalid input data',
            'birthdate.0.after' => 'Invalid input data',
            'birthdate.1.after' => 'Invalid input data',
        ];
    }


}
