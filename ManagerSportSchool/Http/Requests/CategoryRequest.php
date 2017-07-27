<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CategoryRequest extends CrudRequest
{
    use \App\Http\Traits\Slug;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->merge(array('slug' => $this->slugify($this->slug)));

        return [
            'slug' => Rule::unique('categories')->ignore($this->id)->where('slug', $this->slug) . '|required|unique:pages,slug|max:190',
            'name' => 'required|max:190',
            'alias' => 'required|max:190',
            'meta_title' => 'max:190',
            'meta_description' => 'max:190',
            'meta_keywords' => 'max:190',
            'short_description' => 'max:190',
            'content' => 'max:65000',
            'thumbnail' => 'max:190',
            'baner_image' => 'max:190'
        ];
    }

}
