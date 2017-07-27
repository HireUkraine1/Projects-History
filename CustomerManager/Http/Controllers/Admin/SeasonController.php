<?php

namespace App\Http\Controllers\Admin;

use App\Mail;
use App\Model;
use Illuminate\Http\Request;

class SeasonController extends CommonController
{
    /**
     * Season setting
     *
     * @return mixed
     */
    public function index()
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        $curentSeason = Model\CurentSeason::first();
        return view('admin.settings.season')->with('curentSeason', $curentSeason);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateSeason(Request $request)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        try {
            $curentSeason = Model\CurentSeason::first();
            if ($request->account_member_can_edit == 1 || $request->account_member_can_edit == 0) {

                $season = Model\Season::where('id', '=', $curentSeason->seasone_id)->first();

                if ($season instanceof Model\Season) {
                    $season->account_member_can_edit = $request->account_member_can_edit;
                    $season->save();
                    \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
                    return redirect()->back();
                } else {
                    throw new \Exception('Season not found');
                }

            }
            abort(403);
        } catch (\Exception $e) {
            \Session::flash('message', ['alert-danger' => $e->getMessage()]);
            return redirect()->back();
        }

    }

    /**
     * Create season
     *
     * @param Request $request
     * @return mixed
     */
    public function newSeason(Request $request)
    {
        if ($this->admin->role_id == 3) {
            abort(403);
        }
        //Model\Season::create();

        $users = Model\User::all();
        \DB::table('orders')->update(['order_status_id' => 1, 'step' => 1]);
        foreach ($users as $user) {
            $user->note_order = '';
            $user->save();
            \Mail::to($user->email)->send(new Mail\StartSeason());
        }

        $docks = Model\Dock::all();
        foreach ($docks as $dock) {
            \DB::table('orders_docks')->where('dock_id', '=', $dock->id)->update(['price' => $dock->price]);
        }

        $orders_pram_storage = Model\PramStorage::all();
        foreach ($orders_pram_storage as $pram_storage) {
            \DB::table('orders_pram_storage')->where('pram_storage_id', '=', $pram_storage->id)->update(['price' => $pram_storage->price]);
        }

        //SailboatStorage
        //orders_sailboat_storage
        $orders_sailboat_storage = Model\SailboatStorage::all();
        foreach ($orders_pram_storage as $sailboat_storage) {
            \DB::table('orders_sailboat_storage')->where('sailboat_storage_id', '=', $sailboat_storage->id)->update(['price' => $sailboat_storage->price]);
        }

        //WinterStorage
        //orders_winter_storage
        $orders_winter_storage = Model\WinterStorage::all();
        foreach ($orders_winter_storage as $winter_storage) {
            \DB::table('orders_winter_storage')->where('winter_storage_id', '=', $winter_storage->id)->update(['price' => $winter_storage->price]);
        }

        //Pool Due SailingProgram SwimProgram
        //services
        $services = \DB::table('services')->get();
        foreach ($services as $service) {
            $service_type = $service->service_type::where('id', '=', $service->service_id)->first();
            \DB::table('services')
                ->where('member_id', '=', $service->member_id)
                ->where('service_id', '=', $service->service_id)
                ->where('service_type', '=', $service->service_type)
                ->update(['price' => $service_type->price]);
        }

        \Session::flash('message', ['alert-success' => \Lang::get('messages.success')]);
        return redirect()->back();
    }
}
