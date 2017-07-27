<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActivityRequest as StoreRequest;
use App\Http\Requests\ActivityRequest as UpdateRequest;
use CrudController;

class ActivityCrudController extends CrudController
{
    private $access = ['list', 'create', 'update', 'delete', 'activity-courses-list'];

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Activity");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/activity');
        $this->crud->setEntityNameStrings('activity', 'activities');
        $this->crud->addButton('line', 'Templates list', 'getOpenButton', 'crud.buttons.activity.courses_list_show');
        $this->crud->access = $this->access;
        /*
        |----------------
        | CRUD COLUMNS
        |----------------
        */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);
        $this->crud->addColumn([
            'label' => "Category",
            'type' => 'select',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "App\Models\Category",
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
        */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Activity Name',
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
            'label' => "Image",
            'name' => "image",
            'type' => 'image',
            'upload' => true,
        ]);

    }


}
