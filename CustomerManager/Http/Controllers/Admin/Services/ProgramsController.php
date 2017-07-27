<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Admin\CommonController;
use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class ProgramsController extends CommonController
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
        $swims = Model\SwimProgram::all();
        $salings = Model\SailingProgram::all();
        $volunteers = Model\VolunteerProgram::all();
        $programs = [
            'swims' => $swims,
            'salings' => $salings,
            'volunteers' => $volunteers
        ];
        return view('admin.services.programs.index')->with('programs', $programs);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function editSwims($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $program = Model\SwimProgram::where('id', '=', $id)->first();
        if (!$program instanceof Model\SwimProgram) {
            abort(404);
        }
        return view('admin.services.programs.swim')->with('program', $program);
    }

    public function editSalings($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $program = Model\SailingProgram::where('id', '=', $id)->first();
        if (!$program instanceof Model\SailingProgram) {
            abort(404);
        }
        return view('admin.services.programs.saling')->with('program', $program);
    }

    public function editVolunteers($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $program = Model\VolunteerProgram::where('id', '=', $id)->first();
        if (!$program instanceof Model\VolunteerProgram) {
            abort(404);
        }
        return view('admin.services.programs.volunteer')->with('program', $program);
    }


    public function updateSwims(Requests\EditProgram $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $swim = Model\SwimProgram::where('id', '=', $id)->first();
        if (!$swim instanceof Model\SwimProgram) {
            abort(404);
        }
        $swim->fill($request->only(['name', 'price']));
        $swim->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }

    public function updateSalings(Requests\EditProgram $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $saling = Model\SailingProgram::where('id', '=', $id)->first();
        if (!$saling instanceof Model\SailingProgram) {
            abort(404);
        }
        $saling->fill($request->only(['name', 'price']));
        $saling->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }

    public function updateVolunteers(Requests\EditVolunteer $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $saling = Model\VolunteerProgram::where('id', '=', $id)->first();
        if (!$saling instanceof Model\VolunteerProgram) {
            abort(404);
        }
        $saling->fill($request->only(['name', 'status']));
        $saling->save();
        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }


    public function createVolunteers()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        return view('admin.services.programs.create_volunteer');
    }


    public function storeVolunteers(Requests\StoreVolunteer $request)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        Model\VolunteerProgram::create($request->only("name", "status"));
        return redirect('/admin/programs');
    }

    public function destroyVolunteers($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $program = Model\VolunteerProgram::where('id', '=', $id)->first();
        if (!$program instanceof Model\VolunteerProgram) {
            abort(404);
        }
        $program->delete();
        return redirect('/admin/programs');
    }

}
