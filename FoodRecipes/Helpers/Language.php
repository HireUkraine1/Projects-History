<?php

namespace App\Helpers;

use LaravelLocalization;

class Language
{

    /**
     * Get locales enabled in the config
     * can be used for a dropdown
     * @return array
     */
    public static function locales()
    {
        $array = LaravelLocalization::getSupportedLocales();
        $list = [];

        foreach ($array as $key => $values) {
            $list[$key] = $values['name'];
        }

        return $list;
    }

}
