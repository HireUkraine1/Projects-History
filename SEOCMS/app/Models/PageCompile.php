<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageCompile extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "page_compile";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id',
        'status',
        'error'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function page()
    {
        return $this->belongsTo(Pagesheet::class, 'page_id', 'id');
    }
}
