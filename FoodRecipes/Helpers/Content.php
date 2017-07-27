<?php

namespace App\Helpers;

class Content
{

    /**
     * Remove translation data-trans="*" tags from content
     * This param will be used for inline editing by a admin
     *
     * @param $content
     *
     * @return mixed
     */
    public static function removeTranslationTag($content)
    {

        if (preg_match_all('/ data-trans=\"+[A-Za-z0-9_+:]+\"/', $content, $matches)) {
            $content = str_replace($matches[0], '', $content);
        }

        return $content;
    }
}