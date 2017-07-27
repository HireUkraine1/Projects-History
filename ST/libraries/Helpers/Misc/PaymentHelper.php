<?php

use Stripe;
use Stripe\Customer;
use Stripe\Plan;

class PaymentHelper
{
    public static function getCustomer($id = null)
    {
        if ($id):
            $sCustomer = Customer::retrieve($id);
            return $sCustomer;
        endif;
        return false;
    }

    public static function createCustomer($data = array())
    {
        $newCust = Customer::create(array(
            "description" => $data['full_name'],
            "email" => $data['email'],
            "metadata" => ['fan_id' => $data['id']],
        ));
        return $newCust;
    }

    public static function subscribe($fanId, $token, $coupon_code)
    {

        try {
            // I Swear to god-bejesus-christ, this is the worst way of replicating Stripe data  to local machine, but it wil do for now, i will fix this someday..... someday
            $coupon = Coupon::where('code', $coupon_code)->first();
            $plan = FanHelper::getActivePlan();
            $fan = Fan::where('id', $fanId)->with('info')->first();
            $stripeId = (isset($fan->info->stripe_id)) ? $fan->info->stripe_id : false;
            if (!$stripeId):
                return ['error' => 1, 'message' => "Register first!"];
            endif;
            $sCustomer = \Stripe\Customer::retrieve($stripeId);
            $sCustomer->card = $token;
            $sCustomer->plan = $plan->stripe_id;
            if ($coupon):
                $sCustomer->coupon = $coupon->stripe_id;
            endif;
            $sCustomer->save();
        } catch (Exception $e) {
            mail("saleadmin@site.com,email@site.com", "STRIPE-Error charging member: ",
                "{$e->getMessage()}\n\r Fan ID: {$fan->id} Stripe ID: {$stripeId} \n\rERROR TRACE: \n\r" . $e->getTraceAsString());
            return ['error' => 1, 'message' => "Payment gateway refused your payment. Contact us at <a href='mailto:payments@site.com'>payments@site.com</a>"];
        }

        try {
            $subs = $sCustomer->subscriptions;
            $activeSub = $subs->data[0];
            //subscribe
            $localSub = FanHelper::signupMember($fanId, $plan, $activeSub);
            //create invoice
            $invoice = FanHelper::generateInvoice($localSub, $coupon, false);
            //use coupon
            FanHelper::applyCoupon($coupon, $invoice);
            //pay invoice
            FanHelper::paymentOnInvoice($invoice);
            $fan->info->status = 1;
            $fan->info->save();
            return ['error' => 0, 'message' => "OK!"];
            //set customer data
        } catch (Exception $e) {
            $fan->info->status = 3;
            $fan->info->save();
            //NOTIFY ADMIN BY EMAIL
            mail("saleadmin@site.com,email@site.com", "Error subscribing member: ",
                "{$e->getMessage()}\n\r Fan ID: {$fan->id} - Stripe ID: {$stripeId}\n\rERROR TRACE: \n\r" . $e->getTraceAsString());
            return ['error' => 1, 'message' => "Something went wrong. Administrator was notified and we will fix the issue as soon as possible. If you have any questions, please contact us at <a href='mailto:payments@site.com'>payments@site.com</a> "];
        }


    }

    public static function updateCustomer($data, $id)
    {

    }

    public static function createPlan($data)
    {
        try {
            $newPlan = \Stripe\Plan::create($data);

        } catch (Exception $e) {
            $success = ['success' => false, 'message' => $e->getMessage(), 'data' => false];
            return $success;

        }
        $success = ['success' => true, 'message' => '', 'data' => $newPlan];
        return $success;

    }

    public static function createCoupon($data)
    {
        try {
            $newPlan = \Stripe\Coupon::create($data);

        } catch (Exception $e) {
            $success = ['success' => false, 'message' => $e->getMessage(), 'data' => false];
            return $success;

        }
        $success = ['success' => true, 'message' => '', 'data' => $newPlan];
        return $success;

    }

    public static function updatePlan($data)
    {
        try {
            $p = \Stripe\Plan::retrieve($data['stripe_id']);
            $p->name = ($data['name']) ? $data['name'] : "Default Plan";
            $p->save();
        } catch (Exception $e) {
            $success = ['success' => false, 'message' => $e->getMessage(), 'data' => false];
            return $success;

        }
        $success = ['success' => true, 'message' => '', 'data' => $p];
        return $success;

    }


    public static function deletePlan($id)
    {
        try {
            $plan = \Stripe\Plan::retrieve($id);
            $ret = $plan->delete();
        } catch (Exception $e) {
            $success = ['success' => false, 'message' => $e->getMessage(), 'data' => false];
            return $success;
        }
        $success = ['success' => true, 'message' => $ret, 'data' => $plan];
        return $success;
    }

    public static function deleteCoupon($id)
    {
        try {
            $c = \Stripe\Coupon::retrieve($id);
            $ret = $c->delete();
        } catch (Exception $e) {
            $success = ['success' => false, 'message' => $e->getMessage(), 'data' => false];
            return $success;
        }
        $success = ['success' => true, 'message' => $ret, 'data' => $c];
        return $success;
    }

    public static function getCC($id)
    {
        $sCust = Customer::retrieve($id);
        $card = (isset($sCust->sources->data[0])) ? $sCust->sources->data[0] : false;
        return $card;

    }
}


?>