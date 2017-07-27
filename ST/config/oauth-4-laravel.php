<?php
return array(

    /*
    |--------------------------------------------------------------------------
    | oAuth Config https://github.com/artdarek/oauth-4-laravel
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => 'Session',

    /**
     * Consumers
     */
    'consumers' => array(

        'Facebook' => array(
            'client_id' => 'xxxxxxxxxxxxx',
            'client_secret' => 'xxxxxxxxxxxxxxx',
            'scope' => array('email', 'create_event'),
        ),
        'Twitter' => array(
            'client_id' => 'xxxxxxxxxxxxxxxxxxx',
            'client_secret' => 'xxxxxxxxxxxxxxxxxxxx',
            'acccess_token' => 'xxxxxxxxxxxxxxxx',
            'access_token_secret' => 'xxxxxxxxxxxxxxxxxxxxxx',
        ),
        'Google' => array(
            'client_id' => 'xxxxxxxxxx-x.apps.googleusercontent.com',
            'client_secret' => 'xxxxxxxxxxxxxx-',
            'scope' => array('userinfo_email', 'userinfo_profile'),
        ),

    )

);