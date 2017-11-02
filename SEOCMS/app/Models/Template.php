<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "templates";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'virtualroot', 'body'
    ];

    public function scopeNameFilter($query, $name)
    {
        return $query->where('name', $name);
    }

    public function scopeVirtualrootFilter($query, $virtualroot)
    {
        return $query->where('virtualroot', $virtualroot);
    }

}
