<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainAlias extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "domains_alias";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_url', 'robotstxt', 'master'
    ];
}
