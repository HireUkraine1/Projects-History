<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessStructure extends Model
{

    use CrudTrait;

    public $timestamps = false;
    protected $table = 'business_structures';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'ASC');
        });
    }
}
