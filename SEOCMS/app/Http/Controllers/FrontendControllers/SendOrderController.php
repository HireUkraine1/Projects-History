<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Support\RabbitMQ\Rabbit;

class SendOrderController extends BaseController
{
    /**
     * @var Rabbit
     */
    private $rabbit;

    /**
     * SendOrderController constructor.
     *
     * @param Rabbit $rabbit
     */
    public function __construct(Rabbit $rabbit)
    {
        $this->rabbit = $rabbit;
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(OrderRequest $request)
    {
        $order = $this->createOrder($request);

        //not sure but maybe should move queue to event
        $message = $request->all() + [
                'site' => env('SITE'),
                'time' => $order->created_at->getTimestamp(),
                'kodorder' => $order->getKey(),
            ];

        $this->putToQueue($message);

        return response()->json([
            'result' => [
                'success' => [
                    'orderkod' => $order->getKey(),
                    'site' => env('SITE'),
                ]
            ]
        ]);
    }

    /**
     * @param $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    private function createOrder($request)
    {
        return Order::create([
            'data' => $request->getContent(),
        ]);
    }

    /**
     * @param $message
     */
    private function putToQueue($message = [])
    {
        $this->rabbit
            ->connect()
            ->put($message)
            ->close();
    }
}
