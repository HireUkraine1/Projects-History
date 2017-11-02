<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\GetBlockRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class GetBlockController extends BaseController
{
    /**
     * @param GetBlockRequest $request
     * @return JsonResponse
     */
    public function index(GetBlockRequest $request)
    {
        try {
            $view = dbview($request->virtualroot, $request->parameters)->render();
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json(['result' => $view], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
