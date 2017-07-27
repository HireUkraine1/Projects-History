<?php

namespace App\Shortcodes;

use App\Models;

class ClubShortcode
{
    /**
     * @param $shortcode
     * @param $content
     * @param $compiler
     * @param $name
     * @return string
     */
    public function register($shortcode, $content, $compiler, $name)
    {
        $slug = $shortcode->sport;
        $clubs = Models\Club::whereHas('category', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->get();
        ob_start();
        echo '<ul>';
        foreach ($clubs as $club) {
            echo '<li><a href="">' . $club->name . '</a></li>';
        }
        echo '</ul>';
        return ob_get_clean();
    }
}
