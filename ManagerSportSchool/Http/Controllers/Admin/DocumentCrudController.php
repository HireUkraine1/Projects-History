<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActivityRequest as StoreRequest;
use App\Http\Requests\ActivityRequest as UpdateRequest;
use CrudController;

class DocumentCrudController extends CrudController
{
    private $access = ['list', 'create', 'update', 'delete'];

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Document");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/document');
        $this->crud->setEntityNameStrings('document', 'documents');
        $this->crud->access = $this->access;
        /*
        |----------------
        | CRUD COLUMNS
        |----------------
        */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Document Type',
        ]);
        $this->crud->addColumn([
            'label' => "Sport",
            'type' => 'select',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "App\Models\Category",
        ]);
        $this->crud->addColumn([
            'label' => "Section",
            'type' => 'select',
            'name' => 'sport_section_id',
            'entity' => 'section',
            'attribute' => 'name',
            'model' => "App\Models\sportSection",
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
        */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Document Type',
        ]);
        $this->crud->addField([
            'label' => "Sport",
            'type' => 'select_section',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "App\Models\Category",
        ]);
        $this->crud->addField([
            'label' => "Section",
            'type' => 'parent_sport',
            'name' => 'sport_section_id',
            'entity' => 'section',
            'attribute' => 'name',
            'category_name' => 'category_id',
            'model' => "App\Models\sportSection",
        ]);
        $this->crud->addField([
            'name' => 'path',
            'label' => 'Document',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'uploads'
        ]);
    }
}
