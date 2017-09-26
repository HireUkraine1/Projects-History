<?php

namespace App\Forward\Gateway\Exceptions;

use App\Exception;

class ExceptionHandler extends Exception\ExceptionHandler
{
    protected $handle = [
        OutOfGateways::class => self::class . '@handleOutOfGateways',
    ];

    public function handleOutOfGateways(OutOfGateways $exception)
    {
        $msg = trans('gateway.exception.out_of_gateways', ['group' => $exception->group->name]);

        return response()->warning($msg, null, 409);
    }
}
