<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LandingLocation extends Authenticatable
{

    use Notifiable;
    use CrudTrait;

    protected $table = 'landing_locations';
    protected $primaryKey = 'id';
    protected $fillable = ['landing_id', 'country', 'address', 'latitude', 'longitude', 'created_at', 'updated_at', 'alias'];

}
