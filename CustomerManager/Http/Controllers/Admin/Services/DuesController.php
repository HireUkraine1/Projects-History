<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class DuesController extends CommonController
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
        $allDue = Model\Due::all();
        return view('admin.services.dues.index')->with('dues', $allDue);
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
        $due = Model\Due::where('id', '=', $id)->first();
        if (!$due instanceof Model\Due) {
            abort(404);
        }
        return view('admin.services.dues.edit')->with('due', $due);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\DueRequest $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $due = Model\Due::where('id', '=', $id)->first();
        if (!$due instanceof Model\Due) {
            abort(404);
        }
        $due->fill($request->only(['name', 'description', 'price']));
        $due->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }

}
