<?php

namespace App\Models;

use CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    use CrudTrait;

    public $timestamps = false;
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id', 'name', 'sport_section_id', 'path'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function section()
    {
        return $this->belongsTo('App\Models\sportSection', 'sport_section_id');
    }

    public function setPathAttribute($value)
    {
        $attribute_name = "path";
        $disk = "resourses";
        $destination_path = "/";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
