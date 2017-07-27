<?php

namespace App\Helpers;

class Settinggroups
{

    /**
     * @var array
     */
    static $groups = ['system', 'admin', 'frontend'];


    /**
     * Get all groups
     * @return array
     */
    public static function all()
    {
        return static::$groups;
    }
}
