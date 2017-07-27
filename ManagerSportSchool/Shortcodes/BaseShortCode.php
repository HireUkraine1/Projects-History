<?php

namespace App\Shortcodes;

class BaseShortCode
{
    /**
     * @param $shortcode
     * @param $name
     * @return null
     */
    protected function getModelorNull($shortcode, $name)
    {
        $shortcodeId = $shortcode->id;
        $name = ucfirst(strtolower($name));
        $className = "\\App\\Models\\$name";
        if (!class_exists($className)) {
            return null;
        }
        return $className::find($shortcodeId);

    }
}
