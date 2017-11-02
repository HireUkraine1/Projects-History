<?php

namespace App\Http\Requests;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RedirectRequest extends FormRequest
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
        // `newurl` related to custom validation message in validation.php

        return [
            'oldurl'       => $this->getOldUrlRules(),
            'newurl'       => $this->getNewUrlRules(),
            'coderedirect' => 'required|numeric',
        ];
    }

    private function getOldUrlRules()
    {
        // Add Unique Rule validation
        $unique = Rule::unique('redirects', 'oldurl');

        // Ignore current id to be validated on unique
        if ($id = request()->route('redirect')) {
            $unique = $unique->ignore($id, 'id');
        }

        return ['required', $unique, 'check_uri'];
    }

    private function getNewUrlRules()
    {
        $rules = ['required', 'unique:redirects,oldurl'];

        // Check if newurl is url
        $validator = Validator::make($this->request->all(), ['newurl' => 'required|url']);

        // If it is not url it is uri
        if ($validator->fails()) {
            array_push($rules, 'check_uri');
        }

        return $rules;
    }
}
