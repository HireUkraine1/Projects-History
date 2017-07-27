<?php

namespace App\Http\Requests;

class ClubRequest extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2|max:255',
            'established' => 'required|min:2|max:4',
            'meeting_details' => 'required|min:2|max:255',
            'meeting_location' => 'required|min:2|max:255',
            'divisions' => 'max:65000',
            'special_events' => 'max:65000',
            'contact_details' => 'max:255',
        ];
    }

}
