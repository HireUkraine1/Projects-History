<?php

namespace App\Http\Requests;

class EditWorker extends Request
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
            'fname' => 'required|min:2|max:120|regex:/^[а-яА-ЯёЁa-zA-Z]/',
            'pcity' => 'required|numeric',
            'oinfo' => 'required',
            'prof' => 'max:5',
            'email' => 'email',
            'avatar' => 'image|mimes:jpeg,jpg|max:5000',
        ];
    }
}
