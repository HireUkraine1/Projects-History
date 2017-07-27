<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GalleryRequest as StoreRequest;
use App\Http\Requests\GalleryRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class GalleryCrudController extends CrudController
{
    use TraitCrudController;

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Gallery");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/gallery');
        $this->crud->setEntityNameStrings('gallery', 'galleries');

        /*
        |----------------
        | CRUD COLUMNS
        |----------------
         */
        $this->crud->addColumn([
            'name' => 'id',
            'label' => '#',
        ]);
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Gallery Name',
        ]);

        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Display description',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Gallery Name',
            'hint' => 'Only for admin.',
        ]);
        $this->crud->addField([
            'name' => 'description',
            'label' => 'Display description',
            'type' => 'enum',
        ]);
        $this->crud->addField([
            'name' => 'blocks',
            'label' => 'Gallery',
            'type' => 'image_multitle',
        ]);
    }

}
