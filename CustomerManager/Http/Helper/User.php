<?php

namespace App\Http\Helper;

use App\Http\Requests;
use App\Model;

class User
{

    /**
     * User list
     *
     * @return array
     */
    public function userList()
    {
        $list = Model\User::getAll()->toArray();
        $userList = [];
        if (count($list[0])):
            foreach ($list as $key => $user) {
                $userList[$key]['id'] = (isset($user['id'])) ? $user['id'] : '';
                $userList[$key]['first_name'] = (isset($user['first_name'])) ? $user['first_name'] : '';
                $userList[$key]['last_name'] = (isset($user['last_name'])) ? $user['last_name'] : '';
                $userList[$key]['email'] = (isset($user['email'])) ? $user['email'] : '';
                $userList[$key]['phone'] = (isset($user['phone'])) ? $user['phone'] : '';
                $userList[$key]['status'] = (isset($user['status'])) ? $user['status'] : '';
                $userList[$key]['note'] = (isset($user['note'])) ? $user['note'] : '';
                $userList[$key]['start_date'] = (isset($user['created_at'])) ? date('d/m/Y', strtotime($user['created_at'])) : '';
                // entrance_fee 0 - false, 1- half, 2 - true;
                if (isset($user['entrance_fee'])) {
                    switch ($user['entrance_fee']):
                        case 0:
                            $userList[$key]['entrance_fee'] = 'Unpaid';
                            break;
                        case 1:
                            $userList[$key]['entrance_fee'] = 'Paid';
                            break;
                    endswitch;
                }
            }
        endif;
        return $userList;
    }

    /**
     * User's info
     *
     * @param $id
     * @return mixed
     */
    public function userInfo($id)
    {
        $userDbInfo = Model\User::where('id', $id)->getAll()->first()->toArray();
        $userInfo["id"] = (isset($userDbInfo["id"])) ? $userDbInfo["id"] : '';
        $userInfo["first_name"] = (isset($userDbInfo["first_name"])) ? $userDbInfo["first_name"] : '';
        $userInfo["last_name"] = (isset($userDbInfo["last_name"])) ? $userDbInfo["last_name"] : '';
        $userInfo["email"] = (isset($userDbInfo["email"])) ? $userDbInfo["email"] : '';
        $userInfo["phone"] = (isset($userDbInfo["phone"])) ? $userDbInfo["phone"] : '';
        $userInfo["balance"] = $userDbInfo["balance"];
        $userInfo["note"] = $userDbInfo["note"];
        $userInfo["status"] = $userDbInfo["status"] ? 'Active' : 'Disable';
        $userInfo["entrance_fee"] = $userDbInfo["entrance_fee"] ? 'Paid' : 'Unpaid';

        return $userInfo;
    }

    /**
     * Admin updating user
     *
     * @param Requests\UpdateUser $request
     * @param Model\User $usersUpdate
     */
    public function updateUserByAdmin(Requests\UpdateUser $request, Model\User $usersUpdate)
    {
        $this->updateUser($request, $usersUpdate);
        $usersUpdate->status = $request->status;
        $usersUpdate->entrance_fee = $request->entrance_fee;
        $usersUpdate->note = $request->note;
        $usersUpdate->save();
    }

    /**
     * Update user
     *
     * @param Requests\UpdateUser $request
     * @param Model\User $usersUpdate
     */
    public function updateUser(Requests\UpdateUser $request, Model\User $usersUpdate)
    {
        $usersUpdate->first_name = $request->first_name;
        $usersUpdate->last_name = $request->last_name;
        $usersUpdate->email = $request->email;
        $usersUpdate->phone = $request->phone;
        $usersUpdate->balance = $request->balance;
        if ($request->password) {
            $usersUpdate->password = $request->password;
        }
        $usersUpdate->save();
    }

}
