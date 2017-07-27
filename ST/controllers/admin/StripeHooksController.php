<?php

class StripeHookController extends BaseController
{
    public function getHook()
    {
        $myfile = fopen("/var/www/site/app/storage/logs/stripe.log", "w");
        $input = @file_get_contents("php://input");
        fwrite($myfile, $input);
        http_response_code(200); // PHP 5.4 or greater
    }
}

