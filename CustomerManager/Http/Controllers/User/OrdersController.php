<?php

namespace App\Http\Controllers\User;

use App\Http\Helper;
use App\Model;
use Illuminate\Http\Request;

class OrdersController extends CommonController
{

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDb = Model\Order::where('id', $id)->where('users_id', $this->user->id)
            ->with('services', 'status', 'seasons')
            ->first()
            ->toArray();

        $orderInfo = Helper\Order::orderInfo($orderDb);
        return view('users.order.show')->with('orderInfo', $orderInfo);
    }


}
