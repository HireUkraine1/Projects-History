<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class AdminController extends CommonController
{
    /**
     * Admins list
     * @return mixed
     */
    public function index()
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        $adminList = \Admin::adminList();
        return view('admin.admins.index')->with('adminList', $adminList);
    }

    /**
     * Create Admin view
     *
     * @return mixed
     */
    public function create()
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        return view('admin.admins.create');
    }

    /**
     * Create admin
     *
     * @param Requests\CreateAdmin $request
     * @return mixed
     */
    public function store(Requests\CreateAdmin $request)
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        try {
            Model\Admin::create($request->only("name", "email", "role_id", "password"));
            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => \Lang::get('messages.error')]);
        } finally {
            return redirect('/admin/users');
        }
    }


    public function edit($id)
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        $adminInfo = Model\Admin::where('id', '=', $id)->first();
        if ($adminInfo instanceof Model\Admin) {
            return view('admin.admins.edit')->with('adminInfo', $adminInfo->toArray());
        }

        abort(404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateAdmin $request, $id)
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }

        $adminInfo = Model\Admin::where('id', '=', $id)->first();
        if ($adminInfo instanceof Model\Admin) {

            if (!\Common::checkContactAdmin($adminInfo->email, $request->email, 'email')) {
                return redirect()->back()->withErrors(['email' => 'The email is busy.']);
            }

            if (!\Common::checkContactAdmin($adminInfo->name, $request->name, 'name')) {
                return redirect()->back()->withErrors(['name' => 'The name is busy.']);
            }

            try {
                \Admin::adminUpdate($adminInfo, $request);
                \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
            } catch (\Exception $e) {
                \Session::flash('message', ['alert-danger' => \Lang::get('messages.error')]);
            } finally {
                return redirect('/admin/users/' . $id . '/edit');
            }
        }
        abort(404);
    }

    /**
     * Delete admin
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        if ($this->admin->role_id !== 1) {
            abort(403);
        }
        try {
            Model\Admin::destroy($id);
            \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => \Lang::get('messages.error')]);
        } finally {
            return redirect('/admin/users');
        }
    }
}
