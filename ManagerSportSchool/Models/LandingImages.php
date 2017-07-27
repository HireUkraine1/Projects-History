<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LandingImages extends Authenticatable
{

    use Notifiable;
    use CrudTrait;

    protected $table = 'landing_images';
    protected $primaryKey = 'id';
    protected $fillable = ['landing_id', 'image', 'created_at', 'updated_at'];

}
