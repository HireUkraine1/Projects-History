<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdvertisementRequest as StoreRequest;
use App\Http\Requests\AdvertisementRequest as UpdateRequest;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class AdvertisementCrudController extends CrudController
{

    public function __construct()
    {

        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Advertisement");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/advertisement');
        $this->crud->setEntityNameStrings('advertisement', 'advertisements');
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
            'label' => 'Name',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Name'
        ]);
        $this->crud->addField([ // Image
            'name' => 'image',
            'label' => 'Image',
            'type' => 'browse_image',
        ]);
        $this->crud->addField([
            'name' => 'link',
            'label' => 'Link',
        ]);
        $this->crud->addField([
            'label' => 'Pages',
            'type' => 'checklist',
            'name' => 'pages',
            'entity' => 'pages',
            'attribute' => 'name',
            'model' => "\App\Models\Page",
            'pivot' => true,
        ]);
        $this->crud->addField([
            'label' => 'Categories',
            'type' => 'checklist',
            'name' => 'categories',
            'entity' => 'categories',
            'attribute' => 'name',
            'model' => "\App\Models\Category",
            'pivot' => true,
        ]);
    }

}
