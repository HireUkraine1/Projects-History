<?php

namespace App\Controllers;

class Controller
{
    protected $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }
}
