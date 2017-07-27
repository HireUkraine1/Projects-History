<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class PoolController extends CommonController
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
        $allPool = Model\Pool::all();
        return view('admin.services.pool.index')->with('pools', $allPool);
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
        $pool = Model\Pool::where('id', '=', $id)->first();
        if (!$pool instanceof Model\Pool) {
            abort(404);
        }
        return view('admin.services.pool.edit')->with('pool', $pool);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\PoolRequest $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $pool = Model\Pool::where('id', '=', $id)->first();
        if (!$pool instanceof Model\Pool) {
            abort(404);
        }
        $pool->type = $request->type;
        $pool->price = $request->price ? $request->price : 0.00;
        $pool->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }

}
