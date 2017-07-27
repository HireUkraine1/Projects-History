<?php

class FanHelper
{

    public static function getActivePlan()
    {
        $plan = Plan::where('status', 1)->first();
        return $plan;
    }

    public static function sendWelcome($fan = null, $profile = null)
    {
        $name = $fan->name;
        Mail::send('emails.frontend.fans.welcome', array('fan' => $fan, 'profile' => $profile), function ($message) use ($email, $name) {
            $message->to($email, $name)->subject('Welcome To site!');
        });
    }

    public static function fetchFansLocations($fanId = null, $print = false, $grabGeo = false)
    {
        if ($grabGeo):
            $thisFanWithLocations = Fan::where('id', $fanId)->with('locations')->with('locations.geo_location')->first();
        else:
            $thisFanWithLocations = Fan::where('id', $fanId)->with('locations')->first();
        endif;

        if ($curLocs = $thisFanWithLocations->locations):
            if ($print):
                echo "D";
            else:
                return $curLocs;
            endif;
        endif;
    }

    public static function fetchUpcomingConcerts($fanId)
    {
        $fanWithLocations = Fan::where('id', $fanId)->with('locations')->first();
        $notifiations = NotificationSettings::where('fan_id', $fanId)->get();
        $locations = ['0'];
        $performers = ['0'];
        foreach ($fanWithLocations->locations as $loc):
            $locations[] = $loc->id;
        endforeach;
        foreach ($notifiations as $p):
            $performers[] = $p->performer_id;
        endforeach;

        $performers = array_unique($performers);
        $performersWithConcerts = Performer::whereIn('id', $performers)
            ->with(['upcoming_concerts' => function ($q) use ($locations) {
                    $q->whereIn('location_id', $locations)
                        ->orderBy('date', 'desc');
                }]
            )
            ->with('images')
            ->get();
        return $performersWithConcerts;

    }

    public static function fetchSimilarConcerts($fanId)
    {

        $fanWithLocations = Fan::where('id', $fanId)->with('locations')->first();
        $notifiations = NotificationSettings::where('fan_id', $fanId)->get();
        $locations = ['0'];
        $performers = ['0'];
        foreach ($fanWithLocations->locations as $loc):
            $locations[] = $loc->id;
        endforeach;
        foreach ($notifiations as $p):
            $performers[] = $p->performer_id;
        endforeach;

        $performers = array_unique($performers);
        $similarList = [];
        foreach ($performers as $performer):
            // echo $performer;
            $performerDetails = PerformerDetails::where("performer_id", $performer)->get()->toArray();//first(['performer_id'=> $performer->id]);
            $similarPerformers = (isset($performerDetails[0]['similar'])) ? $performerDetails[0]['similar'] : false;
            if ($similarPerformers):
                $similarPerformers = VarsHelper::get_similar_performers($similarPerformers);
                foreach ($similarPerformers as $sp) :
                    // DebugHelper::pdd($sp->name);
                    $similarList[] = $sp->id;
                endforeach;
            endif;
        endforeach;

        $similarList = array_unique($similarList);

        if (count($similarList) && count($locations)):
            $similarWithConcerts = Performer::whereIn('id', $similarList)
                ->with(['upcoming_concerts' => function ($q) use ($locations) {
                        $q->whereIn('location_id', $locations)
                            ->orderBy('date', 'desc');
                    }]
                )
                ->with('images')
                ->get();
            return $similarWithConcerts;
        endif;
        return false;
    }

    public static function fetchSimilarArtists($fanId)
    {
        $fanWithLocations = Fan::where('id', $fanId)->with('locations')->first();
        $notifiations = NotificationSettings::where('fan_id', $fanId)->get();
        $locations = [];
        $performers = [];
        foreach ($fanWithLocations->locations as $loc):
            $locations[] = $loc->id;
        endforeach;
        foreach ($notifiations as $p):
            $performers[] = $p->performer_id;
        endforeach;

        $performers = array_unique($performers);
        $similarList = [];
        foreach ($performers as $performer):
            // echo $performer;
            $performerDetails = PerformerDetails::where("performer_id", $performer)->get()->toArray();//first(['performer_id'=> $performer->id]);
            $similarPerformers = (isset($performerDetails[0]['similar'])) ? $performerDetails[0]['similar'] : false;
            if ($similarPerformers):
                $similarPerformers = VarsHelper::get_similar_performers($similarPerformers);
                foreach ($similarPerformers as $sp) :
                    // DebugHelper::pdd($sp->name);
                    if (!in_array($sp->id, $performers)):
                        $similarList[$sp->id] = $sp;
                    endif;
                endforeach;

                // DebugHelper::pdd($similarPerformers);
            endif;
        endforeach;
        shuffle($similarList);
        $similarList = array_slice($similarList, 0, 10);

        return $similarList;
    }

    public static function fetchTrackedEvents($fanId, $locations = null, $days = null)
    {
        return true;
    }

    public static function signupMember($fanId, $plan, $stripeSub)
    {
        $now = date('Y-m-d H:i:s');
        $subs = new Subscription;
        $subs->description = $plan->description;
        $subs->summary = $plan->summary;
        if ($plan->type == 'annual'):
            $subs->end_date = date('Y-m-d', $stripeSub->current_period_end);
        endif;
        $subs->fan_id = $fanId;
        $subs->next_billing_date = date('Y-m-d', $stripeSub->current_period_end);
        $subs->plan_id = $plan->id;
        $subs->start_date = $now;
        $subs->end_date = date('Y-m-d', $stripeSub->current_period_end);
        $subs->stripe_id = $stripeSub->id;
        $subs->save();
        return $subs;
    }

    public static function generateInvoice($sub)
    {
        $invoice = new Invoice;
        $plan = $sub->plan()->first();

        $invoice->ballance = $plan->cost;
        $invoice->sub_total = $plan->cost;
        $invoice->total_due = $plan->cost;
        $invoice->currency = 'USD';
        $invoice->due_date = date('Y-m-d H:i:s');
        $invoice->subscription_id = $sub->id;
        $invoice->save();
        return $invoice;
    }


    public static function getSubWithInvoice($fanId = null, $invoiceId = null, $any = true)
    {
        $now = date('Y-m-d');


        if ($invoiceId): //get by invoice id
            $currentSub = Subscription::where('fan_id', $fanId)->with(['invoice' => function ($query) use ($invoiceId, $any) {
                $query->where('id', $invoiceId);
                if (!$any):
                    $query->where('paid_date', '0000-00-00 00:00:00');
                endif;
            }])->with('plan')->first();
        else:
            $currentSub = Subscription::where('start_date', '<', $now)
                ->where('end_date', '>', $now)
                ->where('fan_id', $fanId)
                ->with(['invoice' => function ($query) {
                    $query->where('paid_date', '0000-00-00 00:00:00')->orderBy('due_date', 'asc');
                }])
                ->with('plan')->first();
        endif;
        return $currentSub;


        return false;

    }

    public static function paymentOnInvoice($invoice, $amnt = false)
    {
        $invoice = Invoice::where('id', $invoice->id)->first();
        $invoice->paid_amount = ($amnt) ? $amnt : $invoice->total_due;
        $invoice->paid_date = date('Y-m-d H:i:s');
        $invoice->save();

        //SEND EMAIL NOTIFICATION THAT IT"S PAID!!!

        return true;

    }

    public static function activateMember($id)
    {
        // $fan = Fan::where('id',$id)->with('info')->with('subscriptions', function($query)
        // 											{
        // 												$now = date('Y-m-d H:i:s');
        // 												$query->where('start_date', '<', $now)->where('end_date', '>', $now);
        // 											})->with('subscriptions.plan')->first();

        // dd($fan);
    }

    public static function applyCoupon($coupon, $invoice)
    {
        if (!$coupon) return false;
        if ($invoice->coupon_id != null): //apply different coupon
            $toPay = $invoice->ballance;
        else:
            $toPay = $invoice->total_due;
        endif;
        if ($offPercent = $coupon->percent_off): // it is percentage deal
            if ($offPercent >= 100):
                $discount = $toPay;
                $toPay = 0;
            elseif ($offPercent > 0 && $offPercent < 100):
                $percent = $coupon->percent_off / 100;
                $discount = ($invoice->sub_total * $percent);
                $toPay = $toPay - $discount;
            else:
                $discount = 0;
            endif;

        elseif ($discount = $coupon->dollars_off): // nope it's dollars off
            if ($discount >= $toPay):
                $discount = $toPay;
                $toPay = 0;
            else:
                $toPay = $toPay - $discount;
            endif;
        endif;
        // if($coupon->use_limit != 0):
        $coupon->use_count = $coupon->use_count + 1;
        $coupon->save();
        // endif;
        $invoice->coupon_id = $coupon->id;
        $invoice->discount = $discount;
        $invoice->total_due = $toPay;
        $invoice->save();
        return true;
    }


}

?>