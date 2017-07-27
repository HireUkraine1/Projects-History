<?php

namespace App\Http\Controllers\User;

use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class UsersController extends CommonController
{

    private $season;

    public function __construct()
    {
        parent::__construct();
        $this->season = Model\CurentSeason::select('seasone_id')->first();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = $this->user->id;

        $userInfo = \User::userInfo($id);
        $data = [
            'userInfo' => $userInfo,
        ];
        //order not paid
        $order = Model\Order::where('user_id', '=', $this->user->id)
            ->where('season_id', '=', $this->season->seasone_id)
            ->where(function ($query) {
                $query->where('order_status_id', '=', 1)->orWhere('order_status_id', '=', 2);
            })
            ->with('user', 'status', 'waiting_list', 'docks', 'pram_storages', 'sailboat_storages', 'winter_storages', 'members', 'members.relation', 'members.options', 'members.options.type', 'members.pools', 'members.volunteers', 'members.swims', 'members.sailings', 'members.dues'
            )->first();
        if (!$order instanceof Model\Order) {
            $order = Model\Order::where('user_id', '=', $this->user->id)
                ->where('season_id', '=', $this->season->seasone_id)
                ->where('order_status_id', '=', 3)
                ->with('user', 'status', 'waiting_list', 'docks', 'pram_storages', 'sailboat_storages', 'winter_storages', 'members', 'members.relation', 'members.options', 'members.options.type', 'members.pools', 'members.volunteers', 'members.swims', 'members.sailings', 'members.dues'
                )->first();

        }

        $invoice = \Order::orderInfo($order, $this->user);
        return view('users.user.show')->with('data', $data)->with('invoice', $invoice);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userInfo = \User::userInfo($id);

        return view('users.user.edit')->with('userInfo', $userInfo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateUser $request, $id)
    {

        if ($this->user->id != $id) {
            abort(404);
        }
        if (!\Common::checkContact($this->user->email, $request->email, 'email')) {
            return redirect()->back()->withErrors(['email' => 'The email is busy.']);
        }
        if (!\Common::checkContact($this->user->phone, $request->phone, 'phone')) {
            return redirect()->back()->withErrors(['phone' => 'The phone is busy.']);
        }


        try {
            if ($this->user->balance != $request->balance) {
                throw new \Exception('Good try');
            }
            \DB::transaction(function () use ($request) {
                \User::updateUser($request, $this->user);
            });
            \DB::commit();
            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Session::flash('message', ['alert-danger' => $e->getMessage() . ' ' . $e->getLine()]); // \Lang::get('messages.error')
        } finally {
            return redirect('/account/' . $id . '/edit');
        }
    }
}
