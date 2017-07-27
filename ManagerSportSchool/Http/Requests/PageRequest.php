<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PageRequest extends CrudRequest
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

        if ($this->is_homepage == 'True') {
            $this->merge(array('category_id' => null));
        }

        $this->merge(array('slug' => $this->slugify($this->slug)));
        return [
            'slug' => Rule::unique('pages')->ignore($this->id)->where('slug', $this->slug) . '|required|unique:categories,slug|max:190',
            'name' => 'required|max:190',
            'meta_title' => 'max:190',
            'meta_description' => 'max:190',
            'meta_keywords' => 'max:190',
            'content' => 'max:65000',
            'thumbnail' => 'max:190',
            'baner_image' => 'max:190',
            'baner_text' => 'max:190'
        ];
    }

}
