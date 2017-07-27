<?php

namespace App\Helper;

class CreateSlug
{

    static function ruSlug($url)
    {
        $slug = mb_strtolower(trim($url));
        $slug = preg_replace("/[^a-zа-я0-9\s]/ui", "-", $slug);
        $slug = preg_replace("/[\' '\?]{1,}/", "-", $slug);
        $slug = preg_replace("/[\-\?]{2,}/", "-", $slug);
        $slug = preg_replace("/^-/", "", $slug);
        return $slug;
    }

}