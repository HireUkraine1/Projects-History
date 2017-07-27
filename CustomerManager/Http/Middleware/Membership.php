<?php

namespace App\Http\Middleware;

use App\Model;
use Closure;

class Membership
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $curent_season = Model\CurentSeason::first();
        $user = \Auth::user();

        $order = Model\Order::where('user_id', '=', $user->id)
            ->where('season_id', '=', $curent_season->seasone_id)
            ->with('members', 'status')
            ->orderBy('id', 'DESC')
            ->first();

        $curent_uri = $request->server->get("REQUEST_URI");

        if (!$curent_season && $curent_uri != '/membership_step_0') {
            return redirect('/membership_step_0');
        }

        if (!$curent_season->account_member_can_edit && $curent_uri != '/membership_step_0') {
            return redirect('/membership_step_0');
        }
        if (!$curent_season->account_member_can_edit && $curent_uri == '/membership_step_0') {
            return $next($request);
        }

        if (!$order && $curent_uri != '/membership_step_1') {
            return redirect('/membership_step_1');
        }

        if ($order && !isset($request->edit)) {
            $step = $order->step;
            if ($curent_uri != '/membership_step_' . $step) {
                return redirect('/membership_step_' . $step);
            }
        }

        if (isset($request->edit)) {
            $pool_members = Model\Member::where('order_id', '=', $order->id)->whereHas('pools', function ($query) {
                $query->where('service_type', '=', 'App\Model\Pool');
            })->with('dues')->get();

            foreach ($pool_members as $member) {

                foreach ($member->dues as $due) {
                    if ($order) {
                        if ((($due->id == 1 || $due->id == 2) && ($order->status->id == 1 || $order->status->id == 2)) && $curent_uri == '/membership_step_1?edit') {
                            return $next($request);
                        }
                        if ((($due->id == 1 || $due->id == 2) && ($order->status->id == 1 || $order->status->id == 2)) && $curent_uri == '/membership_step_3?edit') {
                            return $next($request);
                        }
                        if ((($due->id == 1 || $due->id == 2) && ($order->status->id == 1 || $order->status->id == 2)) && $curent_uri == '/membership_step_5?edit') {
                            return $next($request);
                        }
                        if ((($due->id == 1 || $due->id == 2) && ($order->status->id == 1 || $order->status->id == 2)) && ($curent_uri != '/membership_step_1?edit' || $curent_uri != '/membership_step_3?edit' || $curent_uri != '/membership_step_5?edit')) {
                            return redirect('/membership_step_1');
                        }
                    }
                    if ((($due->id == 1 || $due->id == 2) && ($order->status->id == 1)) && $curent_uri != '/membership_step_5') {
                        return redirect('/membership_step_5');
                    }
                }
            }

            if ($order) {

                if ($order->order_status_id == 3 && $curent_uri != '/membership_step_6') {
                    return redirect('/membership_step_6');
                }
                if ($order->step <= 2 && ($curent_uri == '/membership_step_2?edit' || $curent_uri == '/membership_step_3?edit' || $curent_uri == '/membership_step_4?edit' || $curent_uri == '/membership_step_5?edit' || $curent_uri == '/membership_step_6?edit')) {
                    return redirect('/membership_step_2');
                }
                if ($order->step <= 3 && ($curent_uri == '/membership_step_3?edit' || $curent_uri == '/membership_step_4?edit' || $curent_uri == '/membership_step_5?edit' || $curent_uri == '/membership_step_6?edit')) {
                    return redirect('/membership_step_3');
                }
                if ($order->step <= 4 && ($curent_uri == '/membership_step_4?edit' || $curent_uri == '/membership_step_5?edit' || $curent_uri == '/membership_step_6?edit')) {
                    return redirect('/membership_step_4');
                }
                if ($order->step <= 5 && ($curent_uri == '/membership_step_5?edit' || $curent_uri == '/membership_step_6?edit')) {
                    return redirect('/membership_step_5');
                }
            }

        }
        return $next($request);
    }

}
