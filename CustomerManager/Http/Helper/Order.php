<?php

namespace App\Http\Helper;

use App\Model;

class Order
{
    /**
     * Orders list
     *
     * @return array
     */
    public function allOrders()
    {
        $season = Model\CurentSeason::select('seasone_id')->first();
        $usersAccount = Model\User::
        with(array('members' => function ($query) use ($season) {
            $query->where('season_id', '=', $season->seasone_id)
                ->where('order_status_id', '<>', 4)
                ->orderBy('id', 'DESC')
                ->with('order', 'relation', 'options', 'options.type', 'pools', 'volunteers', 'swims', 'sailings', 'dues', 'order.user', 'order.status', 'order.waiting_list', 'order.docks', 'order.pram_storages', 'order.sailboat_storages', 'order.winter_storages')
                ->get();
        }))->get();
        $usersAccountOrders = [];
        foreach ($usersAccount as $account) {
            $order = null;
            if ($account->members->count()) {
                $order = $account->members[0]->order;
            }
            if (!$order instanceof Model\Order) {
                $usersAccountOrders[$account->id]['status'] = null;
                $usersAccountOrders[$account->id]['account_name'] = $account->first_name . ' ' . $account->last_name;;
                $usersAccountOrders[$account->id]['season_id'] = $season->seasone_id;
                $usersAccountOrders[$account->id]['total'] = 0;
                $usersAccountOrders[$account->id]['subtotal'] = 0;
                $usersAccountOrders[$account->id]['order_id'] = null;
                $usersAccountOrders[$account->id]['order_notes'] = null;
                $usersAccountOrders[$account->id]['updated_at'] = null;
            } else {
                $orderInfo = $this->orderInfo($order, $account);
                $usersAccountOrders[$account->id]['status'] = $orderInfo['order_status'];
                $usersAccountOrders[$account->id]['account_name'] = $account->first_name . ' ' . $account->last_name;
                $usersAccountOrders[$account->id]['season_id'] = $season->seasone_id;
                $usersAccountOrders[$account->id]['total'] = $orderInfo['total'];
                $usersAccountOrders[$account->id]['subtotal'] = $orderInfo['subtotal'];
                $usersAccountOrders[$account->id]['order_id'] = $orderInfo['order_id'];
                $usersAccountOrders[$account->id]['order_notes'] = $orderInfo['note_order'];;
                $usersAccountOrders[$account->id]['updated_at'] = date('m/d/Y H:i:s', strtotime((string)$orderInfo['updated_at']));
            }
        }
        return $usersAccountOrders;
    }

    /**
     * Order Info
     *
     * @param $order
     * @param $user
     * @return array
     */
    public function orderInfo($order, $user)
    {
        $invoice = [];
        $invoice['order_id'] = $order['id'];
        switch ($order['send_summer_mail']) {
            case 1:
                $invoice['send_summer_mail'] = 'Send July/August mailing to winter residence';
                break;
            case 2:
                $invoice['send_summer_mail'] = 'Send July/August mailing to summer residence';
                break;
        }
        $invoice['fee'] = '';
        $invoice['CM_dir'] = '';
        $invoice['dues'] = [];
        $invoice['order_status'] = '';
        $invoice['updated_at'] = '';
        $invoice['order_id'] = '';
        $invoice['order_status_id'] = '';
        $invoice['pools'] = [];
        $invoice['volunteers'] = [];
        $invoice['swims'] = [];
        $invoice['sailings'] = [];
        $invoice['options']['members'] = [];
        $invoice['docks'] = [];
        $invoice['pram_storages'] = [];
        $invoice['winter_storages'] = [];
        $invoice['sailboat_storages'] = [];
        $invoice['waiting_list'] = [];
        $invoice['total'] = '';
        $invoice['subtotal'] = 0;
        $invoice['service_group'] = [];
        $invoice['id_user'] = '';
        $invoice['buyer']['name'] = '';
        $invoice['buyer']['address1'] = '';
        $invoice['buyer']['address2'] = '';
        $invoice['buyer']['phone'] = '';
        $invoice['buyer']['email'] = '';
        $invoice['note_order'] = [];
        if ($order instanceof Model\Order) {
            $invoice['note_order'] = (isset($user->note_order)) ? json_decode($user->note_order) : [];
            $invoice['id_user'] = $user->id;
            $invoice['order_status'] = $order->status->name;
            $invoice['order_id'] = $order->id;
            $invoice['updated_at'] = $order->updated_at;
            if ($order->fee) {
                $invoice['fee'] = Model\Setting::where('slug', 'fee')->first()->value;
                $invoice['subtotal'] += $invoice['fee'];
            }
            $invoice['order_status_id'] = $order->status->id;
            $i = 1;
            foreach ($order['members'] as $member) {
                //due
                foreach ($member['dues'] as $due) {
                    $invoice['dues'][$due['name']]['members'][] = $member->first_name . ' ' . $member->last_name;
                    $invoice['dues'][$due['name']]['price'] = $due['pivot']['price'];
                    if ($i === 1) {
                        $invoice['CM_dir'] = $member->CM_dir ? 'Unpublish' : 'Publish';
                        $invoice['subtotal'] += $due['pivot']['price'];
                        $invoice['buyer']['name'] = $member->first_name . ' ' . $member->last_name;
                        $invoice['buyer']['address1'] = $member->winter_address;
                        $invoice['buyer']['address2'] = $member->winter_city . ', ' . $member->winter_state . ', ' . $member->winter_zip_code;
                        $invoice['buyer']['phone'] = $member->winter_phone;
                        $invoice['buyer']['email'] = $member->primary_email;
                    }
                }
                //pools   
                foreach ($member['pools'] as $pool) {
                    $invoice['pools'][$pool['type']]['members'][] = $member->first_name . ' ' . $member->last_name;
                    $invoice['pools'][$pool['type']]['price'] = $pool['pivot']['price'];
                    $invoice['subtotal'] += $pool['pivot']['price'];
                }
                //volunteers
                foreach ($member['volunteers'] as $volunteer) {
                    $invoice['volunteers'][$volunteer['name']]['members'][] = $member->first_name . ' ' . $member->last_name;
                    $invoice['volunteers'][$volunteer['name']]['price'] = $volunteer['pivot']['price'];
                    $invoice['subtotal'] += $volunteer['pivot']['price'];
                }
                //swims
                foreach ($member['swims'] as $swims) {
                    $invoice['swims'][$swims['name']]['members'][] = $member->first_name . ' ' . $member->last_name;
                    $invoice['swims'][$swims['name']]['price'] = $swims['pivot']['price'];
                    $invoice['subtotal'] += $swims['pivot']['price'];
                }
                //sailings
                foreach ($member['sailings'] as $sailings) {
                    $invoice['sailings'][$sailings['name']]['members'][] = $member->first_name . ' ' . $member->last_name;
                    $invoice['sailings'][$sailings['name']]['price'] = $sailings['pivot']['price'];
                    $invoice['subtotal'] += $sailings['pivot']['price'];
                }
                //options
                foreach ($member['options'] as $option) {
                    $invoice['options']['members'][$member->first_name . ' ' . $member->last_name][$option['type']['name']] = $option['value'];
                }
                //service group
                if ($member->service_group) {
                    $invoice['service_group'][] = $member->first_name . ' ' . $member->last_name;
                }
                $i++;
            }

            foreach ($order['docks'] as $dock) {
                $invoice['docks'][$dock->id]['name'] = $dock->name;
                $invoice['docks'][$dock->id]['price'] = $dock['pivot']['price'];
                $invoice['docks'][$dock->id]['size'] = $dock['pivot']['size_type_boat'];
                $invoice['subtotal'] += $dock['pivot']['price'];
            }

            foreach ($order['pram_storages'] as $pram_storages) {
                if ($pram_storages['pivot']['count']) {
                    $invoice['pram_storages'][$pram_storages['name']]['price'] = $pram_storages['pivot']['price'];
                    $invoice['pram_storages'][$pram_storages['name']]['count'] = $pram_storages['pivot']['count'];
                    $invoice['subtotal'] += $pram_storages['pivot']['price'] * $pram_storages['pivot']['count'];
                }
            }

            foreach ($order['winter_storages'] as $winter_storages) {
                if ($winter_storages['pivot']['count']) {
                    $invoice['winter_storages'][$winter_storages['name']]['price'] = $winter_storages['pivot']['price'];
                    $invoice['winter_storages'][$winter_storages['name']]['count'] = $winter_storages['pivot']['count'];
                    $invoice['subtotal'] += $winter_storages['pivot']['price'] * $winter_storages['pivot']['count'];
                }
            }


            foreach ($order['sailboat_storages'] as $sailboat_storages) {
                if ($sailboat_storages['pivot']['count']) {
                    $invoice['sailboat_storages'][$sailboat_storages['name']]['price'] = $sailboat_storages['pivot']['price'];
                    $invoice['sailboat_storages'][$sailboat_storages['name']]['count'] = $sailboat_storages['pivot']['count'];
                    $invoice['subtotal'] += $sailboat_storages['pivot']['price'] * $sailboat_storages['pivot']['count'];
                }
            }

            if ($order['waiting_list']['sunfish_dolly']) {
                $invoice['waiting_list']['sunfish dolly'] = 'Waiting list Of Storage Sunfish Dolly (June 15 through October 15)';
            }
            if ($order['waiting_list']['kayak_storage']) {
                $invoice['waiting_list']['kayak storage'] = 'Waiting list Of Storage Kayak (June 15 through October 15)';
            }
            if ($order['waiting_list']['locker_renewal']) {
                $invoice['waiting_list']['locker renewal'] = 'Waiting list Of Storage Locker  (June 15 through October 15)';
            }

            if ($order['waiting_list']['dock_waiting']) {

                $invoice['waiting_list']['dock waiting'] = (!empty($order['waiting_list']['size_type_boat'])) ? $order['waiting_list']['size_type_boat'] : 'True';

            }

            $invoice['total'] = sprintf('%01.2f', $invoice['subtotal']);
            $invoice['subtotal'] = sprintf('%01.2f', $invoice['subtotal']);

            if ($order->status->id != 3) {
                $invoice['balance'] = $user->balance;
                if ($invoice['balance'] >= $invoice['subtotal']) {
                    $invoice['total'] = 0.00;
                }
                if ($invoice['balance'] < $invoice['subtotal']) {
                    $invoice['total'] = sprintf('%01.2f', $invoice['subtotal'] - $invoice['balance']);
                }
            }
        }

        return $invoice;
    }

}
