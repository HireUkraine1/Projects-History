<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\CommonController;

class SettingController extends CommonController
{
    public function settings()
    {
        return view('pages.design-settings');
    }


}
