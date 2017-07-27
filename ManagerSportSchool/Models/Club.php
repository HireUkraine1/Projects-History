<?php

namespace App\Models;

use App\Events\ClubCreatedEvent;
use CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Club extends Authenticatable
{

    use Notifiable;
    use CrudTrait;

    protected $table = 'clubs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sport_id',
        'name',
        'established',
        'meeting_details',
        'meeting_location',
        'divisions',
        'special_events',
        'contact_details',
    ];

    protected $events = [
        'created' => ClubCreatedEvent::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'sport_id');
    }

}
