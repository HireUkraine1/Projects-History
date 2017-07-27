<?php

namespace App\Http\Helper;

use App\Http\Requests;
use App\Model;

class Admin
{
    /**
     * List of admins
     * @return mixed
     */
    public function adminList()
    {
        $admins = Model\Admin::getAll();
        return $admins;
    }

    /**
     * Update admin
     *
     * @param Model\Admin $adminInfo
     * @param Requests\UpdateAdmin $request
     */
    public function adminUpdate(Model\Admin $adminInfo, Requests\UpdateAdmin $request)
    {
        $adminInfo->name = $request->name;
        $adminInfo->email = $request->email;
        $adminInfo->role_id = $request->role_id;
        if ($request->password) {
            $adminInfo->password = $request->password;
        }
        $adminInfo->save();
    }

}
