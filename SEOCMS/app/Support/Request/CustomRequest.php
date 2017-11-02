<?php

namespace App\Support\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class CustomRequest extends FormRequest
{
    /**
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors)
    {
        return new JsonResponse([
            'error' => $errors
        ], 422);
    }
}
