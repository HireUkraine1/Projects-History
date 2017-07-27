<?php

namespace App\Http\Helper;

use App\Http\Requests;
use App\Model;

class Member
{

    /**
     * List of members
     * @return mixed
     */
    public function allMembers()
    {
        $membersDb = Model\Member::with('relationship', 'social')
            ->getAll()
            ->toArray();
        foreach ($membersDb as $key => $member) {
            $members[$key] = $this->memberInfo($member);
        }
        return $members;
    }

    /**
     * Member info
     *
     * @param $member
     * @return mixed
     */
    private function memberInfo($member)
    {
        $memberInfo["id"] = (isset($member["id"])) ? $member["id"] : '';
        $memberInfo["CM_dir"] = (isset($member["CM_dir"])) ? $member["CM_dir"] : '';
        $memberInfo["first_name"] = (isset($member["first_name"])) ? $member["first_name"] : '';
        $memberInfo["last_name"] = (isset($member["last_name"])) ? $member["last_name"] : '';
        $memberInfo["email"] = (isset($member["email"])) ? $member["email"] : '';
        $memberInfo["phone"] = (isset($member["phone"])) ? $member["phone"] : '';
        $memberInfo["birthdate"] = (isset($member["birthdate"])) ? date('m/d/Y', strtotime($member["birthdate"])) : '';
        $memberInfo["relationship"] = (isset($member["relationship"]["relationship"])) ? $member["relationship"]["relationship"] : '';
        $memberInfo["winter_address"] = (isset($member["winter_address"])) ? $member["winter_address"] : '';
        $memberInfo["winter_city"] = (isset($member["winter_city"])) ? $member["winter_city"] : '';
        $memberInfo["winter_state"] = (isset($member["winter_state"])) ? $member["winter_state"] : '';
        $memberInfo["winter_zip_code"] = (isset($member["winter_zip_code"])) ? $member["winter_zip_code"] : '';
        $memberInfo["social"] = (isset($member["social"])) ? $member["social"] : '';
        $memberInfo["created_at"] = (isset($member["created_at"])) ? $member["created_at"] : '';
        return $memberInfo;
    }

    /**
     * User Members
     *
     * @param $id
     * @return mixed
     */
    public function userMembers($id)
    {
        $userMembersDb = Model\Member::where('user_id', $id)
            ->with('relationship', 'social')
            ->getAll()
            ->toArray();
        foreach ($userMembersDb as $key => $member) {
            $userMembers[$key] = $this->memberInfo($member);
        }
        return $userMembers;
    }

    /**
     * Member's info
     *
     * @param $id
     * @return mixed
     */
    public function member($id)
    {
        $memberDb = Model\Member::where('id', '=', $id)
            ->with('relationship', 'social')
            ->getAll()
            ->first()
            ->toArray();
        $member = $this->memberInfo($memberDb);
        return $member;
    }

    /**
     * update Members
     *
     * @param Requests\EditMember $request
     * @param Model\Member $memberUpdate
     */
    public function updateMember(Requests\EditMember $request, Model\Member $memberUpdate)
    {
        $memberUpdate->first_name = $request->first_name;
        $memberUpdate->last_name = $request->last_name;
        $memberUpdate->email = $request->email;
        $memberUpdate->phone = $request->phone;
        $memberUpdate->member_relationship_id = $request->relationship;
        $memberUpdate->birthdate = date('Y-m-d', strtotime($request->birthdate));
        $memberUpdate->winter_state = $request->winter_state;
        $memberUpdate->winter_address = $request->winter_address;
        $memberUpdate->winter_city = $request->winter_city;
        $memberUpdate->winter_zip_code = $request->winter_zip_code;
        $memberUpdate->save();
    }

}
