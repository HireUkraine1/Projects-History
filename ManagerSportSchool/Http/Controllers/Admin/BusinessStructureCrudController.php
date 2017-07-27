<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BusinessStructureRequest as StoreRequest;
use App\Http\Requests\BusinessStructureRequest as UpdateRequest;
use CrudController;

class BusinessStructureCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->crud->setModel("\App\Models\BusinessStructure");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/business_structure');
        $this->crud->setEntityNameStrings('business structure', 'business structures');
        // Columns
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name',
        ]);
        // Fields
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Name',
        ]);
    }

}
