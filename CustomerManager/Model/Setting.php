<?php

namespace App\Model;

class Setting extends AppModel
{
    protected $table = 'settings';

    protected $primaryKey = 'slug';

    protected $fillable = ['slug', 'value'];
}
