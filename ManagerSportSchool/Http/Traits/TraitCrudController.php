<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait TraitCrudController
{
    private function fieldArrayToJson(Request $request, $fieldName)
    {
        $input[$fieldName] = [];
        foreach ($request->$fieldName ?? [] as $key => $member) {
            if (!empty($member["src"]) || !empty($member["name"]) || !empty($member["description"])) {
                $input[$fieldName][] = $request->$fieldName[$key];
            }
        }
        if (count($input[$fieldName])) {
            $input[$fieldName] = json_encode($input[$fieldName]);
        } else {
            $input[$fieldName] = '{}';
        }
        $request->merge($input);
        return $request;
    }
}
