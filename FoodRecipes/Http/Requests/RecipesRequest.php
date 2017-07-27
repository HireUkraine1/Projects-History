<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RecipesRequest extends Request
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
            'name' => 'required|max:255',
            'name_seo' => 'required',
            'vimeo_url' => 'required|url',
            'description' => 'required',
            'locale' => 'required',
        ];
    }

    /**
     * Altering data request data
     * @return array
     */
    public function all()
    {
        $data = parent::all();
        $data['name_seo'] = str_slug($data['name_seo']);
        return $data;
    }
}