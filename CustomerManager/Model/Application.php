<?php

namespace App\Model;

class Application extends AppModel
{
    protected $table = 'applications';

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'message', 'status'];
}
       