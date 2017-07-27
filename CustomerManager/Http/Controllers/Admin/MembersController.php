<?php

namespace App\Http\Controllers\Admin;

use App\Model;
use Illuminate\Http\Request;

class MembersController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * return \Illuminate\Http\Response
     */
    public function index()
    {
        $season = Model\CurentSeason::select('seasone_id')->first();
        $usersAccount = Model\User::
        with(array('members' => function ($query) use ($season) {
            $query->where('season_id', '=', $season->seasone_id)
                ->where('order_status_id', '=', 2)
                ->orWhere('order_status_id', '=', 3)
                ->orderBy('id', 'ASC')
                ->with('order', 'relation', 'options', 'options.type', 'pools', 'volunteers', 'swims', 'sailings', 'dues',
                    'order.user', 'order.status', 'order.waiting_list', 'order.docks',
                    'order.pram_storages', 'order.sailboat_storages', 'order.winter_storages'
                )
                ->get();
        }))->get();

        //part for price

        $services = $this->services();

        $members = [];

        foreach ($usersAccount as $account) {
            $order = null;

            if ($account->members->count()) {
                $invoice = \MembershipStep::step_6($account, $season);
                $order = $account->members[0]->order;
                $i = 1;
                foreach ($account->members as $member) {
                    $members[$account->id]['member'][] = $member;
                    $members[$account->id]['status'] = $member->order->status->name;
                    if ($i === 1) {
                        $members[$account->id]['additions'] = $member->order;
                        $members[$account->id]['invoice'] = $invoice;
                    }
                    $i++;
                }
            }
        }
        return view('admin.members.index')->with('membersList', $members)->with('services', $services);
    }

    /**
     * Members Services
     *
     * @return array
     */
    public function services()
    {
        //pool
        Model\Pool::all()->each(function ($item) use (&$additional_pool_member) {
            if ($item->id == 2) {
                $additional_pool_member = $item->price;
            }
        });
        //programs
        Model\SwimProgram::all()->each(function ($item) use (&$swim_lesson, &$swim_team) {
            if ($item->id == 1) {
                $swim_lesson = $item->price;
            }
            if ($item->id == 2) {
                $swim_team = $item->price;
            }
        });
        Model\SailingProgram::all()->each(function ($item) use (&$beginning_sailing_club_boat, &$beginning_ailing_own_boat, & $intermediate_racing, &$lbiyra) {
            if ($item->id == 1) {
                $beginning_sailing_club_boat = $item->price;
            }
            if ($item->id == 2) {
                $beginning_ailing_own_boat = $item->price;
            }
            if ($item->id == 3) {
                $intermediate_racing = $item->price;
            }
            if ($item->id == 4) {
                $lbiyra = $item->price;
            }
        });
        //storage
        Model\PramStorage::all()->each(function ($item) use (&$sunfish_dolly_storage, &$kayak_storage, &$locker_storage) {
            if ($item->id == 1) {
                $sunfish_dolly_storage = $item->price;
            }
            if ($item->id == 2) {
                $kayak_storage = $item->price;
            }
            if ($item->id == 3) {
                $locker_storage = $item->price;
            }
        });
        Model\SailboatStorage::all()->each(function ($item) use (&$opti_racks, &$sunfish_racks, &$laser_racks, &$racks_420) {
            if ($item->id == 1) {
                $opti_racks = $item->price;
            }
            if ($item->id == 2) {
                $sunfish_racks = $item->price;
            }
            if ($item->id == 3) {
                $laser_racks = $item->price;
            }
            if ($item->id == 14) {
                $racks_420 = $item->price;
            }
        });

        Model\WinterStorage::all()->each(function ($item) use (&$winter_power_boat_storage, &$winter_sailboat, &$winter_kayak_boat_storage, &$winter_locker_storage) {
            if ($item->id == 1) {
                $winter_power_boat_storage = $item->price;
            }
            if ($item->id == 2) {
                $winter_sailboat = $item->price;
            }
            if ($item->id == 3) {
                $winter_kayak_boat_storage = $item->price;
            }
            if ($item->id == 4) {
                $winter_locker_storage = $item->price;
            }
        });

        Model\Dock::all()->each(function ($item) use (&$dock_1_13, &$dock_14_44) {
            if ($item->id == 1) {
                $dock_1_13 = $item->price;
            }
            if ($item->id == 2) {
                $dock_14_44 = $item->price;
            }
        });

        $services = [
            'additional_pool_member' => $additional_pool_member,
            'swim_lesson' => $swim_lesson,
            'swim_team' => $swim_team,
            'beginning_sailing_club_boat' => $beginning_sailing_club_boat,
            'beginning_ailing_own_boat' => $beginning_ailing_own_boat,
            'intermediate_racing' => $intermediate_racing,
            'lbiyra' => $lbiyra,
            'dock_1_13' => $dock_1_13,
            'dock_14_44' => $dock_14_44,
            'opti_racks' => $opti_racks,
            'sunfish_racks' => $sunfish_racks,
            'laser_racks' => $laser_racks,
            'racks_420' => $racks_420,
            'sunfish_dolly_storage' => $sunfish_dolly_storage,
            'kayak_storage' => $kayak_storage,
            'locker_storage' => $locker_storage,
            'winter_power_boat_storage' => $winter_power_boat_storage,
            'winter_sailboat' => $winter_sailboat,
            'winter_kayak_boat_storage' => $winter_kayak_boat_storage,
            'winter_locker_storage' => $winter_locker_storage,
        ];

        return $services;
    }

    /**
     * Excel maker
     *
     */
    public function excel()
    {
        $members = [];
        $season = Model\CurentSeason::select('seasone_id')->first();

        $usersAccount = Model\User::
        with(array('members' => function ($query) use ($season) {
            $query->where('season_id', '=', $season->seasone_id)
                ->where('order_status_id', '=', 2)
                ->orWhere('order_status_id', '=', 3)
                ->orderBy('id', 'ASC')
                ->with('order', 'relation', 'options', 'options.type', 'pools', 'volunteers', 'swims', 'sailings', 'dues',
                    'order.user', 'order.status', 'order.waiting_list', 'order.docks',
                    'order.pram_storages', 'order.sailboat_storages', 'order.winter_storages'
                )
                ->get();
        }))->get();

        foreach ($usersAccount as $account) {
            $order = null;

            if ($account->members->count()) {
                $invoice = \MembershipStep::step_6($account, $season);
                $order = $account->members[0]->order;
                $i = 1;
                foreach ($account->members as $member) {
                    $membersList[$account->id]['member'][] = $member;
                    $membersList[$account->id]['status'] = $member->order->status->name;
                    if ($i === 1) {
                        $membersList[$account->id]['additions'] = $member->order;
                        $membersList[$account->id]['invoice'] = $invoice;
                    }
                    $i++;
                }
            }
        }
        $services = $this->services();
        $exels = [];
        foreach ($membersList as $user_id => $members) {
            $i = 1;
            foreach ($members['member'] as $member) {
                if ($member->CM_dir == 1) {
                    $CM_dir = 'Unpublish';
                } else {
                    $CM_dir = 'Publish';
                }
                $vol = '';
                foreach ($member->volunteers as $volunteer) {
                    $vol .= $volunteer->name . " \n";
                }
                $t_short = '';
                foreach ($member->options as $option) {
                    if ($option->type->id == 1) {
                        $t_short = $option->value;
                        break;
                    }
                }

                $boat = '';
                foreach ($member->options as $option) {
                    if ($option->type->id == 2) {
                        $boat = $option->value;
                        break;
                    }
                }
                $level = '';
                foreach ($member->options as $option) {
                    if ($option->type->id == 3) {
                        $level = $option->value;
                        break;
                    }
                }

                if (isset($members['additions']) && $i === 1) {
                    if ($members['additions']->waiting_list->dock_waiting === 1) {
                        $dock_waiting = 'Yes';
                    } else {
                        $dock_waiting = 'No';
                    }
                } else {
                    $dock_waiting = 'No';
                }

                $size_type_boat = '';
                if (isset($members['additions']) && $i === 1) {
                    foreach ($members['additions']->docks as $dock) {
                        if (trim($dock->pivot->size_type_boat)) {
                            $size_type_boat = $dock->pivot->size_type_boat;
                            break;
                        }
                    }
                }

                $opti_racks = '';
                $sunfish_racks = '';
                $laser_racks = '';
                $racks_420 = '';
                if (isset($members['additions']) && $i === 1) {
                    foreach ($members['additions']->sailboat_storages as $sailboat_storages) {
                        if ($sailboat_storages->id == 1 && $sailboat_storages->pivot->count) {
                            $opti_racks = $sailboat_storages->pivot->count;
                        }
                        if ($sailboat_storages->id == 2 && $sailboat_storages->pivot->count) {
                            $sunfish_racks = $sailboat_storages->pivot->count;
                        }
                        if ($sailboat_storages->id == 3 && $sailboat_storages->pivot->count) {
                            $laser_racks = $sailboat_storages->pivot->count;
                        }
                    }
                }

                $sunfish_dolly_storage = '';
                $kayak_storage = '';
                $locker_storage = '';

                $waiting_list_sunfish_dolly_storage = 'No';
                $waiting_list_kayak_storage = 'No';
                $waiting_list_locker_storage = 'No';

                $winter_power_boat_storage = '';
                $winter_sailboat = '';
                $winter_kayak_boat_storage = '';
                $winter_locker_storage = '';

                if (isset($members['additions']) && $i === 1) {
                    foreach ($members['additions']->pram_storages as $pram_storages) {
                        if ($pram_storages->id == 1 && $pram_storages->pivot->count) {
                            $sunfish_dolly_storage = $pram_storages->pivot->count;
                        }
                        if ($pram_storages->id == 2 && $pram_storages->pivot->count) {
                            $kayak_storage = $pram_storages->pivot->count;
                        }
                        if ($pram_storages->id == 3 && $pram_storages->pivot->count) {
                            $locker_storage = $pram_storages->pivot->count;
                        }
                    }
                    if ($members['additions']->waiting_list->sunfish_dolly === 1) {
                        $waiting_list_sunfish_dolly_storage = 'Yes';
                    }
                    if ($members['additions']->waiting_list->kayak_storage === 1) {
                        $waiting_list_kayak_storage = 'Yes';
                    }
                    if ($members['additions']->waiting_list->locker_renewal === 1) {
                        $waiting_list_locker_storage = 'Yes';
                    }
                    foreach ($members['additions']->winter_storages as $winter_storages) {
                        if ($winter_storages->id == 1 && $winter_storages->pivot->count) {
                            $winter_power_boat_storage = $winter_storages->pivot->count;
                        }
                        if ($winter_storages->id == 2 && $winter_storages->pivot->count) {
                            $winter_sailboat = $winter_storages->pivot->count;
                        }
                        if ($winter_storages->id == 3 && $winter_storages->pivot->count) {
                            $winter_kayak_boat_storage = $winter_storages->pivot->count;
                        }
                        if ($winter_storages->id == 4 && $winter_storages->pivot->count) {
                            $winter_locker_storage = $winter_storages->pivot->count;
                        }
                    }
                }


                $item = [
                    'Account Id' => $user_id,
                    'Last name' => $member->last_name,
                    'First name' => $member->first_name,
                    'Membership Due Type' => $members['member'][0]->dues[0]->name,
                    'Relationship to Member' => $member->relation->name,
                    'Birthdate' => date('m/d/Y', strtotime($member->birthdate)),
                    'Cell Phone' => $member->cell_phone,
                    'Primary Email' => $member->primary_email,
                    'Secondary Email' => $member->secondary_email,
                    'Winter Street Address' => $member->winter_address,
                    'Winter City' => $member->winter_city,
                    'Winter State' => $member->winter_state,
                    'Winter ZIP Code' => $member->winter_zip_code,
                    'Winter Phone' => $member->winter_phone,
                    'Summer Address' => $member->summer_address,
                    'Summer City' => $member->summer_town,
                    'Summer Zip Code' => $member->summer_zip_code,
                    'Summer Phone' => $member->summer_phone,
                    'Summer State' => $member->summer_state,
                    'CM Directory' => $CM_dir,
                    'Main Pool Member ' => (isset($member->pools[0]) && $member->pools[0]->id == 1) ? 'Yes' : 'No',
                    'Additional Pool Member, $' . $services['additional_pool_member'] => (isset($member->pools[0]) && $member->pools[0]->id == 2) ? 'Yes' : 'No',
                    'Youth Service' => ($member->service_group === 1) ? 'Yes' : 'No',
                    'Volunteer Choice' => $vol,
                    'Swim Lesson, $' . $services['swim_lesson'] => ((isset($member->swims[0]) && $member->swims[0]->id == 1) || (isset($member->swims[1]) && $member->swims[1]->id == 1)) ? 'Yes' : 'No',
                    'Swim T-Shirt' => $t_short,
                    'Swim Team, $' . $services['swim_team'] => ((isset($member->swims[0]) && $member->swims[0]->id == 2) || (isset($member->swims[1]) && $member->swims[1]->id == 2)) ? 'Yes' : 'No',
                    'Swim Team T-shirt' => $t_short,
                    'Beginning Sailing Club Boat, $' . $services['beginning_sailing_club_boat'] => (isset($member->sailings[0]) && $member->sailings[0]->id == 1) ? 'Yes' : 'No',
                    'Beginning Sailing Own Boat, $' . $services['beginning_ailing_own_boat'] => (isset($member->sailings[0]) && $member->sailings[0]->id == 2) ? 'Yes' : 'No',
                    'Intermediate Racing, $' . $services['intermediate_racing'] => (isset($member->sailings[0]) && $member->sailings[0]->id == 3) ? 'Yes' : 'No',
                    'Intermediate Racing Type Boat' => $boat,
                    'Racing Level' => $level,
                    'Sailing T-shirt' => $t_short,
                    'LBIYRA Only, $' . $services['lbiyra'] => (isset($member->sailings[0]) && $member->sailings[0]->id == 4) ? 'Yes' : 'No',
                    'Dock 1-13, $' . $services['dock_1_13'] => ((isset($members['additions']) && $i === 1) && ((isset($members['additions']->docks[0]) && $members['additions']->docks[0]->id == 1) || (isset($members['additions']->docks[1]) && $members['additions']->docks[1]->id == 1))) ? 'Yes' : 'No',
                    'Dock 14-44, $' . $services['dock_14_44'] => ((isset($members['additions']) && $i === 1) && ((isset($members['additions']->docks[0]) && $members['additions']->docks[0]->id == 2) || (isset($members['additions']->docks[1]) && $members['additions']->docks[1]->id == 2))) ? 'Yes' : 'No',
                    'Dock Waitlist' => $dock_waiting,
                    'Boat Type/Length' => $size_type_boat,
                    'Opti Racks, $' . $services['opti_racks'] => $opti_racks,
                    'Sunfish Racks, $' . $services['sunfish_racks'] => $sunfish_racks,
                    'Laser Racks, $' . $services['laser_racks'] => $laser_racks,
                    '420 Racks, $' . $services['racks_420'] => $racks_420,
                    'Sunfish Dolly Storage, $' . $services['sunfish_dolly_storage'] => $sunfish_dolly_storage,
                    'Sunfish Dolly Waitlist' => $waiting_list_sunfish_dolly_storage,
                    'Kayak Storage, $' . $services['kayak_storage'] => $kayak_storage,
                    'Kayak Waitlist' => $waiting_list_kayak_storage,
                    'Locker Storage, $' . $services['locker_storage'] => $locker_storage,
                    'Locker Waitlist' => $locker_storage,
                    'Winter Power Boat Storage, $' . $services['winter_power_boat_storage'] => $winter_power_boat_storage,
                    'Winter Sailboat, $' . $services['winter_sailboat'] => $winter_sailboat,
                    'Kayak Boat Storage, $' . $services['winter_kayak_boat_storage'] => $winter_kayak_boat_storage,
                    'Winter Locker Storage, $' . $services['winter_locker_storage'] => $winter_locker_storage,
                    'Invoice Total' => (isset($members['additions']) && isset($members['invoice']) && $i === 1) ? $members['invoice']['total'] : '',
                    'Balance' => (isset($members['additions']) && isset($members['invoice']) && $i === 1) ? $members['invoice']['balance'] : ''

                ];
                $i++;
                $exels[] = $item;
            }
        }

        \Excel::create('members', function ($excel) use ($exels) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('members');
            $excel->setCreator('BC')->setCompany('BC');
            $excel->setDescription('members list');

            // // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exels) {
                $sheet->fromArray($exels, null, 'A1')->setAutoSize(true);
            });
        })->download('xlsx');
    }
}
