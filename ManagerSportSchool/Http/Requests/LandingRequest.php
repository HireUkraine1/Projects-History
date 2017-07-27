<?php

namespace App\Http\Requests;

class LandingRequest extends CrudRequest
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
            'address' => 'required',
            'address.*.*.alias' => 'required',
            'about_us' => 'max:2000',
            'service_overview' => 'max:2000',
            'location_features' => 'max:2000',
            'tourist_attributes' => 'max:2000',
            'accomodations' => 'max:2000',
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
            'address.*.*.alias.required' => 'The alias for location is required'
        ];
    }
}
