<?php

namespace App\Model;

class SummerEmail extends AppModel
{
    protected $table = 'send_summer_mails';

    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany('App\Model\Orders', 'send_summer_mail_id');
    }
}
