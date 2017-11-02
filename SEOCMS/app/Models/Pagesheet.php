<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagesheet extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "pagesheets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'h1',
        'title',
        'description',
        'keywords',
        'active',
        'template_id',
        'sitemappriority',
        'criticalcss',
    ];

    public function template()
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }
}
