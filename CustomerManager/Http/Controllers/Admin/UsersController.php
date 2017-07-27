<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Mail;
use App\Model;
use Illuminate\Http\Request;

class UsersController extends CommonController
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
        $userList = \User::userList();
        return view('admin.users.index')->with('userList', $userList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateUser $request)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        try {
            Model\User::create($request->only("first_name", "last_name", "email", "phone", "entrance_fee", "status", "note", "password", 'CM_directory'));

            \Mail::to($request->email)->send(new Mail\AcceptMember([
                'email' => $request->email,
                'password' => $request->password,
                'name' => $request->first_name
            ]));

            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
        } finally {
            return redirect('/admin/accounts');
        }
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
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }

        $season = Model\CurentSeason::select('seasone_id')->first();
        $userInfo = \User::userInfo($id);
        $data = [
            'userInfo' => $userInfo,
        ];
        //order not paid
        $order = Model\Order::where('user_id', '=', $id)
            ->where('season_id', '=', $season->seasone_id)
            ->where(function ($query) {
                $query->where('order_status_id', '=', 1)->orWhere('order_status_id', '=', 2);
            })
            ->with('user', 'status', 'waiting_list', 'docks', 'pram_storages', 'sailboat_storages', 'winter_storages', 'members', 'members.relation', 'members.options', 'members.options.type', 'members.pools', 'members.volunteers', 'members.swims', 'members.sailings', 'members.dues'
            )->first();
        if (!$order instanceof Model\Order) {
            $order = Model\Order::where('user_id', '=', $id)
                ->where('season_id', '=', $season->seasone_id)
                ->where('order_status_id', '=', 3)
                ->with('user', 'status', 'waiting_list', 'docks', 'pram_storages', 'sailboat_storages', 'winter_storages', 'members', 'members.relation', 'members.options', 'members.options.type', 'members.pools', 'members.volunteers', 'members.swims', 'members.sailings', 'members.dues'
                )->first();

        }

        $invoice = \Order::orderInfo($order, $user);

        return view('admin.users.show')->with('data', $data)->with('invoice', $invoice);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $userInfo = \User::userInfo($id);
        return view('admin.users.edit')->with('userInfo', $userInfo);
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
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::where('id', $id)->first();
        if ($user instanceof Model\User) {
            if (!\Common::checkContact($user->email, $request->email, 'email')) {
                return redirect()->back()->withErrors(['email' => 'The email is busy.']);
            }
            if (!\Common::checkContact($user->phone, $request->phone, 'phone')) {
                return redirect()->back()->withErrors(['phone' => 'The phone is busy.']);
            }
            try {
                \DB::transaction(function () use ($request, $user) {
                    \User::updateUserByAdmin($request, $user);
                });
                \DB::commit();
                \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
            } catch (\Exception $e) {
                \DB::rollback();
                \Session::flash('message', ['alert-danger' => $e->getMessage() . ' ' . $e->getLine()]); // \Lang::get('messages.error')
            } finally {
                return redirect('/admin/accounts/' . $id . '/edit');
            }
        } else {
            abort(404);
        }
    }

}
