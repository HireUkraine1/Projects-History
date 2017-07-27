<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminSetting extends Controller
{

    /**
     * Settings page
     * @return mixed
     */
    public function settings()
    {
        $networks = \App\SocialNetworkSetting::all()->toArray();
        $networksLinks = [];
        foreach ($networks as $network):
            $networksLinks[$network['type']] = $network['link'];
        endforeach;

        $admin = \Sentinel::check()->toArray();
        $data = [
            'networks' => $networksLinks,
            'adminEmail' => $admin['email'],
            'adminTel' => $admin['tel']
        ];

        if (!$admin) {
            abort(404);
        }

        return view('super-admin.setting')->with('data', $data);

    }

    /**
     * Setting of soc. networks
     *
     * @param Request $request
     * @return mixed
     */
    public function settingsCommon(Request $request)
    {
        $this->saveLink('facebook', trim($request->facebook));
        $this->saveLink('twitter', trim($request->twitter));
        $this->saveLink('linkedIn', trim($request->linkedIn));
        $this->saveLink('google', trim($request->google));
        $this->saveLink('instagram', trim($request->instagram));
        $this->saveLink('vk', trim($request->vk));
        \Session::flash('message', [['true' => 'Настройки обновлены!']]);

        return back();
    }

    /**
     * Save link
     *
     * @param $type
     * @param $Requestlink
     */
    private function saveLink($type, $Requestlink)
    {
        $network = \App\SocialNetworkSetting::where('type', $type)->first();

        if ($network && empty($Requestlink)) {
            $network->link = '#';
            $network->save();
        } elseif (!$network && empty($Requestlink)) {
            $network = new \App\SocialNetworkSetting;
            $network->type = $type;
            $network->link = '#';
            $network->save();
        } elseif (!$network && !empty($Requestlink)) {
            $network = new \App\SocialNetworkSetting;
            $network->type = $type;
            $network->link = $this->isLink($Requestlink);
            $network->save();
        } else {
            $network->link = $this->isLink($Requestlink);
            $network->save();
        };

    }

    /**
     * Check is link or not
     *
     * @param $Requestlink
     * @return mixed|string
     */
    private function isLink($Requestlink)
    {
        $network = filter_var(trim($Requestlink), FILTER_VALIDATE_URL);

        if ($network) {
            $link = $network;
        } else {
            $link = '#';
        }
        return $link;
    }


}
