<?php

$loader = new Twig_Loader_Filesystem('../app/Views');

$twig = new Twig_Environment($loader, array(
    'cache'       => '../bootstrap/cache',
    'auto_reload' => true
));
