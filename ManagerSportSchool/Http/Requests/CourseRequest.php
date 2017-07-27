<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends CrudRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "date" => "required|date",
            "landing_id" => "required|numeric",
            "activity_id" => "required|numeric",
            "landing_locations_id" => "numeric",
            "landing_locations_id" => "required|numeric",
            "quantity_lessons" => "required|numeric",
            "quantity_places" => "required|numeric",
            "busy_places" => "required|numeric|max:$this->quantity_places",
            "price" => "required|numeric|max:100000000",
        ];
    }
}
