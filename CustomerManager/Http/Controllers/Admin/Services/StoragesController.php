<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class StoragesController extends CommonController
{

    public function index()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $prams = Model\PramStorage::all();
        $sailboats = Model\SailboatStorage::all();
        $winters = Model\WinterStorage::all();
        $docks = Model\Dock::all();

        $storages = [
            'prams' => $prams,
            'sailboats' => $sailboats,
            'winters' => $winters,
            'docks' => $docks
        ];
        return view('admin.services.storages.index')->with('storages', $storages);
    }

    public function editPram($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $storage = Model\PramStorage::where('id', '=', $id)->first();
        if (!$storage instanceof Model\PramStorage) {
            abort(404);
        }
        return view('admin.services.storages.pram')->with('storage', $storage);
    }

    public function editSailboat($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $storage = Model\SailboatStorage::where('id', '=', $id)->first();
        if (!$storage instanceof Model\SailboatStorage) {
            abort(404);
        }
        return view('admin.services.storages.sailboat')->with('storage', $storage);
    }

    public function editWinter($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $storage = Model\WinterStorage::where('id', '=', $id)->first();
        if (!$storage instanceof Model\WinterStorage) {
            abort(404);
        }
        return view('admin.services.storages.winter')->with('storage', $storage);
    }

    public function editDock($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $storage = Model\Dock::where('id', '=', $id)->first();
        if (!$storage instanceof Model\Dock) {
            abort(404);
        }
        return view('admin.services.storages.dock')->with('storage', $storage);
    }

    public function updatePram(Requests\EditStorage $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $pram = Model\PramStorage::where('id', '=', $id)->first();
        if (!$pram instanceof Model\PramStorage) {
            abort(404);
        }
        $pram->fill($request->only(['name', 'price']));
        $pram->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }


    public function updateSailboat(Requests\EditStorage $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $sailboat = Model\SailboatStorage::where('id', '=', $id)->first();
        if (!$sailboat instanceof Model\SailboatStorage) {
            abort(404);
        }
        $sailboat->fill($request->only(['name', 'price']));
        $sailboat->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }


    public function updateWinter(Requests\EditStorage $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $winter = Model\WinterStorage::where('id', '=', $id)->first();
        if (!$winter instanceof Model\WinterStorage) {
            abort(404);
        }
        $winter->fill($request->only(['name', 'price', 'description']));
        $winter->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }


    public function updateDock(Requests\EditStorage $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $dock = Model\Dock::where('id', '=', $id)->first();
        if (!$dock instanceof Model\Dock) {
            abort(404);
        }
        $dock->fill($request->only(['name', 'price']));
        $dock->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }

}
