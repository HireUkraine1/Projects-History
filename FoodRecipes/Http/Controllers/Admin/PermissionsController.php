<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionRequest;
use App\Permission;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.permissions_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.permissions_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $permission = new Permission;
        $permission->name = $request['name'];
        $permission->save();

        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/permissions');

        return redirect()->intended($url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $permissions = Permission::where('id', $id)->first();
        $role = Permission::with('roles')->find($id);
        return view('admin.pages.permissions_edit', [
            'permissions' => $permissions
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $id = \Input::get('permissionsId');
        $name = \Input::get('name');
        $permission = Permission::where('id', $id)->first();
        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/permissions');
        if ($name == $permission->name) {
            return redirect()->intended($url);
        } else {
            $namePermission = Permission::where('name', $name)->first();
            if ($namePermission instanceof Permission) {
                return redirect()->back()->withErrors(['The Permission name has already been taken.']);
            } else {
                $permission->name = $name;
                $permission->save();
                return redirect()->intended($url);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            $permission->delete();
        }
        $url = \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/permissions');
        return redirect()->intended($url);
    }

    public function anyData()
    {
        $permissions = Permission::select(['id', 'name']);
        return Datatables::of($permissions)->addColumn('action', function ($permissions) {
            return '<a href="' . \LaravelLocalization::getLocalizedURL(null, ADMINPANEL . '/permissions/' . $permissions->id . '/edit/') . '" class="btn btn-xs btn-primary">
                        <i class="glyphicon glyphicon-edit"></i> ' . trans('admin.edit') . '
                    </a> &nbsp;
                    <a href="#" data-id="' . $permissions->id . '" class="btn btn-xs btn-danger delete-permission">
                        <i class="glyphicon glyphicon-remove"></i> ' . trans('admin.delete') . '
                    </a>
                    ';
        })->editColumn('id', 'ID: {{$id}}')->make(true);
    }
}
