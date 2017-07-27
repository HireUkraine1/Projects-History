<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolFrontRequest extends FormRequest
{
    protected $redirectRoute = 'membership';

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
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            'trading_name' => 'min:2|max:255',
            'business_number' => 'required',
            'country' => 'required',
            'street' => 'required|min:2|max:255',
            'address_line' => 'max:255',
            'city' => 'required|min:2|max:255',
            'state' => 'required|max:100',
            'postal' => 'required|regex:/[a-zA-Z0-9]/',
            'phone' => 'required|regex:/\(?([0-9]{1,4})\)?([ .-]?)([0-9]{3})\2([0-9]{4})$/',
            'mobile' => 'required|regex:/\(?([0-9]{1,4})\)?([ .-]?)([0-9]{3})\2([0-9]{4})$/',
            'email' => 'required|email|unique:schools,email',
            'website' => 'regex:/^(https?\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$/',
            'street_mailing' => 'max:255',
            'postal_mailing' => 'regex:/[a-zA-Z0-9]/',
            'insurance' => 'required_if:country,Australia',
            'insurance_annual_revenue' => 'required_if:country,Australia',
            'insurance_incidents' => 'required_if:country,Australia',
            'insurance_start_date' => 'date_format:"d/m/Y"',
            'categories' => 'required',
            'businesses' => 'required',
            'sportsections' => 'required_if:categories.0,2|required_if:categories.1,2|required_if:categories.2,2',
            'terms' => 'required',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The Business Name field is required.',
            'name.min' => 'The Business Name  must be at least :min.',
            'name.max' => 'The Business Name  may not be greater :max',
            'trading_name.min' => 'The Trading Name  must be at least :min.',
            'trading_name.max' => 'The Trading Name  may not be greater :max',
            'sportsections.required_if' => "The sport Section is required if selected sport"
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            session()->forget('membership');
        });
    }
}
