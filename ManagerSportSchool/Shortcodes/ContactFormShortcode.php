<?php

namespace App\Shortcodes;

class ContactFormShortcode extends BaseShortCode
{
    /**
     * @param $shortcode
     * @param $content
     * @param $compiler
     * @param $name
     * @return mixed
     */
    public function register($shortcode, $content, $compiler, $name)
    {
        return view('templates.elements.forms.contact_form');
    }
}
