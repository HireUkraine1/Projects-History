<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BanerRequest as StoreRequest;
use App\Http\Requests\BanerRequest as UpdateRequest;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class BanerCrudController extends CrudController
{

    public function __construct()
    {

        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Banner");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/banner');
        $this->crud->setEntityNameStrings('banner', 'banners');
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
        $this->crud->addColumn([
            'name' => 'title',
            'label' => 'Title',
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Name',
            'hint' => 'Only for admin.',
        ]);
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);
        $this->crud->addField([
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Content</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'image',
            'label' => 'Image',
            'type' => 'browse_image',

        ]);
        $this->crud->addField([
            'name' => 'button_text',
            'label' => 'Button Text',
        ]);
        $this->crud->addField([
            'name' => 'link',
            'label' => 'Link',
        ]);

    }
}
