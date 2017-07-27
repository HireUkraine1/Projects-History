<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Sentinel;

class UserController extends CommonController
{
    public function user()
    {
        return view('pages.design-users');
    }

    protected function userRole()
    {
        $user = Sentinel::getUser();
        $roleAdmin = Sentinel::findRoleByName('Admins');
        $roleUsers = Sentinel::findRoleByName('Users');

        if ($user->inRole($roleAdmin)) {
            $adminRole = 1;
        } elseif ($user->inRole($roleUsers)) {
            $adminRole = 0;
        } else {
            return redirect('dashboard');
        }
        return $adminRole;
    }
}
