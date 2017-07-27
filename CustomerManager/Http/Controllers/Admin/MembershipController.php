<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Model;
use Illuminate\Http\Request;

class MembershipController extends CommonController
{

    private $season;

    /**
     * MembershipController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->season = Model\CurentSeason::select('seasone_id')->first();
    }

    /**
     * End season handler
     *
     * @param $id
     * @return mixed
     */
    public function end_season($id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }
        $invoice = \MembershipStep::end_season($user, $this->season);
        return view('admin.membership.end_season')->with('invoice', $invoice);
    }

    /**
     * First step of questionnaire
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function step_1(Request $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }
        $data = \MembershipStep::step_1($request, $user, $this->season);


        return view('admin.membership.step_1')->with('user', $user)->with('data', $data['data'])->with('edit', $data['edit']);
    }

    /**
     * handler of first step of questionnaire
     * @param $id
     * @param Requests\EditStep1 $request
     * @return mixed
     */
    public function updateStep_1($id, Requests\EditStep1 $request)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        try {
            $user = Model\User::find($id);
            if (!$user instanceof Model\User) {
                abort(404);
            }
            \MembershipStep::updateStep_1($request, $user, $this->season);
            return redirect('/admin/membership_step_2/' . $id);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Second step of questionnaire
     *
     * @return \Illuminate\Http\Response
     */
    public function step_2(Request $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }
        $data = \MembershipStep::step_2($request, $user, $this->season);
        return view('admin.membership.step_2')->with('states', $data['states'])->with('addPoolPrice', $data['addPoolPrice'])->with('user', $user)->with('members', $data['members'])->with('due_id', $data['due_id'])->with('relations', $data['relations'])->with('address', $data['address'])->with('edit', $data['edit']);
    }

    /**
     * Handler of second step of questionnaire
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function updateStep_2(Request $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        try {
            $user = Model\User::find($id);
            if (!$user instanceof Model\User) {
                abort(404);
            }
            \MembershipStep::updateStep_2($request, $user, $this->season);
            return redirect('/admin/membership_step_3/' . $id);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * 3th step of questionnaire
     *
     * @return \Illuminate\Http\Response
     */
    public function step_3(Request $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }
        $data = \MembershipStep::step_3($request, $user, $this->season);

        return view('admin.membership.step_3')->with('due', $data['due'])->with('membersVol', $data['membersVol'])->with('user', $user)->with('volunterPrograms', $data['volunterPrograms'])->with('members', $data['members'])->with('edit', $data['edit']);
    }


    public function updateStep_3(Request $request, $id)
    {
        try {
            if ($this->admin->role_id == 3) {
                abort(403);
            }
            $user = Model\User::find($id);
            if (!$user instanceof Model\User) {
                abort(404);
            }
            \MembershipStep::updateStep_3($request, $user, $this->season);
            return redirect('/admin/membership_step_4/' . $id);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function step_4(Request $request, $id)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $user = Model\User::find($id);
        if (!$user instanceof Model\User) {
            abort(404);
        }
        $data = \MembershipStep::step_4($request, $user, $this->season);
        return view('admin.membership.step_4')->with('user', $user)->with('edit', $data['edit'])->with('members', $data['members'])->with('tShirts', $data['tShirts'])->with('boats', $data['boats'])->with('levels', $data['levels'])->with('programs', $data['programs']);
    }


}
