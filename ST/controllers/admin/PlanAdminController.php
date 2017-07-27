<?php

class PlanAdminController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Concert Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'ConcertController@showWelcome');
    |
    */
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function __construct()
    {
        $this->conf = App::make('conf');
    }

    public function plans()
    {
        if (Input::has('op')):
            $op = Input::get('op');
            if ($op == 'delete' && $id = Input::get('id')): //delete plan
                $plan = Plan::with('subscriptions')->first();
                if ($plan->subscriptions->count() > 0):
                    $message = ['error' => "Cannot Delete Plan With Current or Past Subscribers!"];
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
                else:
                    $stripe_id = $plan->stripe_id;
                    $plan->delete();
                    $result = PaymentHelper::deletePlan($stripe_id);
                    if ($result['success']):
                        $message = ['success' => "You have deleted local and stripe plan!"];
                    else:
                        $message = ['warning' => "You have deleted local plan! However, Stripe returned: " . $result['message']];
                    endif;
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
                endif;
            elseif ($op == 'add'):
                $data = Input::all();
                $promotional = (isset($data['promotional'])) ? 1 : 0;
                $plan = new Plan;
                $plan->cost = $data['cost'];
                $plan->description = $data['description'];
                $plan->name = $data['name'];
                $plan->promotional = $promotional;
                $plan->type = $data['type'];
                $plan->summary = $data['summary'];
                $plan->status = 0;
                $plan->save();

                $data = array(
                    "amount" => (int)(100 * $plan->cost),
                    "interval" => $plan->type,
                    "name" => $plan->name,
                    "currency" => "USD",
                    "id" => $plan->id,
                    "statement_descriptor" => "site.COM MEMBERSHP",
                );
                if ($promotional):
                    $data['trial_period_days'] = 365;
                endif;

                $return = PaymentHelper::createPlan($data);

                if ($return['success']):
                    $plan->stripe_id = $return['data']->id;
                    $plan->save();
                    $message = ['success' => "Plan succesfully added! Status is set to INACTIVE!"];
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
                else:
                    $plan->delete();
                    $message = ['danger' => "Payment Gateway (stripe) refused plan. Nothing was created! Try again!"];
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
                endif;


            elseif ($op == 'activate'):
                $data = Input::all();
                $toActivate = Plan::where('id', $data['id'])->first();
                if (isset($data['confirmed'])):
                    DB::table('plans')->update(['status' => 0]);
                    DB::table('plans')->where('id', $data['id'])->update(['status' => 1]);
                    $message = ['success' => "Plan succesfully activated!"];
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);

                endif;
                $this->layout->content = View::make('admin.plans.activate-plan-content', ['data' => $data, 'toActivate' => $toActivate]);
            elseif ($op == 'deactivateAll'):
                DB::table('plans')->update(['status' => 0]);
                $message = ['success' => "<h4><strong>What have you done!!!?</strong></h4>Members cannot register anymore, ALL PLANS WERE DEACTIVATED!!!"];
                return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
            else:

            endif;
        else:
            $plans = Plan::with('subscriptions')->orderBy('status', 'desc')->get();
            $this->layout->content = View::make('admin.plans.plans-content', ['plans' => $plans]);

        endif;
    }

    public function plan($id = null)
    {
        if (Input::has('op')):
            if ($id == Input::get('id')):
                $plan = Plan::where('id', $id)->first();
                $plan->name = Input::get('name');
                $plan->description = Input::get('description');
                $plan->summary = Input::get('summary');
                $plan->save();

                $data = ['name' => Input::get('name'), 'stripe_id' => $plan->stripe_id];
                $return = PaymentHelper::updatePlan($data);
                if ($return['success']):
                    $message = ['success' => "Plan updated!"];
                    return Redirect::to('/saleadminpanel/plans')->with('notifications', $message);
                else:
                    $message = ['danger' => "Local Updated, BUT, Payment Gateway (stripe) refused plan update with: " . $return['message']];
                    return Redirect::to('/saleadminpanel/plans/' . $plan->id)->with('notifications', $message);
                endif;
            else:
                $message = ['error' => "Something went wrong!"];
                return Redirect::to('/saleadminpanel/plans/' . $id)->with('notifications', $message);
            endif;

        endif;
        $plan = Plan::where('id', $id)->first();
        $this->layout->content = View::make('admin.plans.plan-content', ['plan' => $plan]);

    }

    public function coupons()
    {
        if (Input::has('op')):
            $op = Input::get('op');
            if ($op == 'delete' && $id = Input::get('id')): //delete coupon
                $coupon = Coupon::where('id', $id)->first();
                $stripe_id = $coupon->stripe_id;
                $coupon->delete();
                $result = PaymentHelper::deleteCoupon($stripe_id);
                if ($result['success']):
                    $message = ['success' => "You have deleted local and stripe coupon!"];
                else:
                    $message = ['warning' => "You have deleted local coupon! However, Stripe returned: " . $result['message']];
                endif;
                return Redirect::to('/saleadminpanel/coupons')->with('notifications', $message);

            elseif ($op == 'add'):
                $data = Input::all();
                $coupon = new Coupon;
                $code = strtoupper(trim($data['code']));
                $type = $data['type'];
                $off = $data['off'];
                $limit = $data['limit'];
                $expire = strtotime($data['expire']);
                $coupon->code = $code;
                $coupon->title = $data['title'];
                $coupon->summary = $data['summary'];
                $coupon->dollars_off = ($type == 'ammount') ? $off : 0;
                $coupon->percent_off = ($type == 'ammount') ? 0 : $off;
                $coupon->status = 1;
                $coupon->start_date = date('Y-m-d H:i:s');
                $coupon->end_date = ($expire) ? date('Y-m-d H:i:s', $expire) : '0000-00-00 00:00:00';
                $coupon->use_limit = ($limit) ? $limit : 0;
                $coupon->use_count = 0;
                $coupon->save();

                $data = array(
                    "id" => $code,
                    "duration" => "once",
                    "metadata" => ['sale_id' => $coupon->id]
                );
                if ($expire):
                    $data['redeem_by'] = $expire;
                endif;
                if ($type == 'ammount'):
                    $data['amount_off'] = 100 * $off;
                    $data['currency'] = 'USD';
                elseif ($type == 'percent'):
                    $data['percent_off'] = $off;
                endif;
                if ($limit):
                    $data['max_redemptions'] = $limit;
                endif;

                $return = PaymentHelper::createCoupon($data);

                if ($return['success']):
                    $coupon->stripe_id = $return['data']->id;
                    $coupon->save();
                    $message = ['success' => "Coupon $code succesfully added! Status is set to ACTIVE!"];
                    return Redirect::to('/saleadminpanel/coupons')->with('notifications', $message);
                else:
                    $coupon->delete();
                    $message = ['danger' => "Payment Gateway (stripe) refused coupon. Nothing was created! Try again!"];
                    return Redirect::to('/saleadminpanel/coupons')->with('notifications', $message);
                endif;
            elseif ($op == 'deactivateAll'):
                DB::table('coupons')->update(['status' => 0]);
                $message = ['success' => "<h4><strong>All coupons are OFF!!</strong></h4>"];
                return Redirect::to('/saleadminpanel/coupons')->with('notifications', $message);
            endif;
        endif;
        $coupons = Coupon::get();
        $this->layout->customjs = View::make('admin.plans.cssjs');

        $this->layout->content = View::make('admin.plans.coupons-content', ['coupons' => $coupons]);

    }

    public function coupon($id = null)
    {
        $coupon = Coupon::where('id', $id)->first();
        if (Input::has('op') && Input::get('op') == 'edit'):
            $data = Input::all();
            $coupon->status = $data['status'];
            $coupon->title = $data['title'];
            $coupon->summary = $data['summary'];
            $coupon->save();
            $message = ['success' => "<h4><strong>{$coupon->code}</strong> was updated!</h4>"];
            return Redirect::to('/saleadminpanel/coupons')->with('notifications', $message);
        else:

        endif;

        $this->layout->content = View::make('admin.plans.coupon-content', ['coupon' => $coupon]);


    }

}