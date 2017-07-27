<?php

namespace App\Http\Controllers\User;

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
     * Page of end season
     * @return mixed
     */
    public function end_season()
    {
        $invoice = \MembershipStep::end_season($this->user, $this->season);
        return view('users.membership.end_season')->with('invoice', $invoice);
    }

    public function step_1(Request $request)
    {
        $data = \MembershipStep::step_1($request, $this->user, $this->season);
        return view('users.membership.step_1')->with('data', $data['data'])->with('edit', $data['edit']);
    }

    public function updateStep_1(Requests\EditStep1 $request)
    {
        try {
            \MembershipStep::updateStep_1($request, $this->user, $this->season);
            return redirect('/membership_step_2');
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function step_2(Request $request)
    {
        $data = \MembershipStep::step_2($request, $this->user, $this->season);
        return view('users.membership.step_2')->with('states', $data['states'])->with('addPoolPrice', $data['addPoolPrice'])->with('members', $data['members'])->with('due_id', $data['due_id'])->with('relations', $data['relations'])->with('address', $data['address'])->with('edit', $data['edit']);
    }

    public function updateStep_2(Request $request)
    {
        try {
            \MembershipStep::updateStep_2($request, $this->user, $this->season);
            return redirect('/membership_step_3');
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function step_3(Request $request)
    {
        $data = \MembershipStep::step_3($request, $this->user, $this->season);
        return view('users.membership.step_3')->with('due', $data['due'])->with('membersVol', $data['membersVol'])->with('volunterPrograms', $data['volunterPrograms'])->with('members', $data['members'])->with('edit', $data['edit']);
    }

    public function updateStep_3(Request $request)
    {
        try {
            \MembershipStep::updateStep_3($request, $this->user, $this->season);
            return redirect('/membership_step_4');
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
    public function step_4(Request $request)
    {
        $data = \MembershipStep::step_4($request, $this->user, $this->season);

        return view('users.membership.step_4')->with('edit', $data['edit'])->with('members', $data['members'])->with('tShirts', $data['tShirts'])->with('boats', $data['boats'])->with('levels', $data['levels'])->with('programs', $data['programs']);
    }

    public function updateStep_4(Request $request)
    {
        try {
            \MembershipStep::updateStep_4($request, $this->user, $this->season);
            return redirect('/membership_step_5');
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function step_5(Request $request)
    {
        $data = \MembershipStep::step_5($request, $this->user, $this->season);
        return view('users.membership.step_5')->with('docks', $data['docks'])
            ->with('sailboatStorages', $data['sailboatStorages'])
            ->with('pramStorages', $data['pramStorages'])
            ->with('winterStorages', $data['winterStorages'])
            ->with('edit', $data['edit'])
            ->with('issetfamaly', $data['issetfamaly']);
    }

    public function updateStep_5(Requests\EditStep5 $request)
    {
        //current order
        try {
            \MembershipStep::updateStep_5($request, $this->user, $this->season);
            return redirect('/membership_step_6');
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back()->withInput();
        }
    }

    public function step_6()
    {
        //order not paid
        $_StripePublic = \Config::get('services.stripe.key');
        $paypalClientId = \Config::get('paypal.ClientId');
        $invoice = \MembershipStep::step_6($this->user, $this->season);
        return view('users.membership.step_6')->with('invoice', $invoice)->with('paypalClientId', $paypalClientId)->with('stripePublic', $_StripePublic);
    }


    public function paidOrder(Request $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                \MembershipStep::paidOrder($this->user, $this->season, $request->total);
            });
            \DB::commit();
            return redirect('/membership_step_1/');
        } catch (\Exception $e) {
            \DB::rollback();
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function editPaidOrder()
    {
        try {
            \MembershipStep::editPaidOrder($this->user, $this->season);
            return redirect('/membership_step_1');
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back();
        }
    }


    public function createNewMember(Requests\CreateAdditionMember $request)
    {
        return json_encode(\MembershipStep::createNewMember($request, $this->user, $this->season));
    }

    public function getMemberInfo($id)
    {
        return Model\Member::where('id', '=', $id)->whereHas('relation', function ($query) {
            $query->where('id', '<>', 1);
        })->first();
    }

    public function updateMemberInfo(Requests\CreateAdditionMember $request)
    {
        return json_encode(\MembershipStep::updateMemberInfo($request, $this->user, $this->season));
    }
}
