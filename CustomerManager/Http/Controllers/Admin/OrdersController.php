<?php

namespace App\Http\Controllers\Admin;

use App\Model;
use Illuminate\Http\Request;

class OrdersController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $orders = \Order::allOrders();
        return view('admin.orders.index')->with('orders', $orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $accountUser = Model\User::find($id);
        if (!$accountUser instanceof Model\User) {
            abort(404);
        }
        $season = Model\CurentSeason::select('seasone_id')->first();
        $invoice = \MembershipStep::end_season($accountUser, $season);
        return view('admin.orders.show')->with('invoice', $invoice);
    }

}
