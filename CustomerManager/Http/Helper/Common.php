<?php

namespace App\Http\Helper;

use App\Model;

class Common
{

    /**
     * Check contact
     *
     * @param null $oldContact
     * @param null $newContact
     * @param $type
     * @return bool
     */
    public function checkContact($oldContact = null, $newContact = null, $type)
    {
        if ($oldContact != $newContact) {
            $findUser = Model\User::where($type, $newContact)->first();
            if ($findUser instanceof Model\User) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check admin contact
     *
     * @param null $oldContact
     * @param null $newContact
     * @param $type
     * @return bool
     */
    public function checkContactAdmin($oldContact = null, $newContact = null, $type)
    {

        if ($oldContact != $newContact) {
            $findUser = Model\Admin::where($type, $newContact)->first();
            if ($findUser instanceof Model\User) {
                return false;
            }
        }
        return true;
    }

}
