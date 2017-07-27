<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Requests\CourseRequest as CreateRequest;
// use App\Http\Requests\CourseRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;
use Illuminate\Http\Request as CreateRequest;
use Illuminate\Http\Request as UpdateRequest;

class SchoolDocumentCrudController extends CrudController
{

    use TraitCrudController;

    private $access = ['list', 'update', 'create', 'delete'];
    private $school_id;
    private $document_id;

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        // $this->middleware(function ($request, $next) {
        $this->school_id = \Route::getCurrentRoute()->hasParameter('school_id') ? \Route::getCurrentRoute()->parameter('school_id') : 0;
        $this->document_id = \Route::getCurrentRoute()->hasParameter('document_id') ? \Route::getCurrentRoute()->parameter('document_id') : 0;
        //     return $next($request);
        // });
        $this->crud->setModel("\App\Models\School");
        $name = 'school document';
        $list = 'school documents';
        if ($this->school_id) {
            $name = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'document';
            $list = 'school "' . $this->crud->getEntry($this->school_id)->name . '" ' . 'documents';
        }
        $this->crud->setModel("\App\Models\SchoolDocument");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/school/document');
        $this->crud->setEntityNameStrings($name, $list);
        $this->crud->access = $this->access;
        $landings = $this->schoolType();
        /*
        |----------------
        | CRUD COLUMNS
        |----------------
        */
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Document Name',
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
            'label' => 'Document Name',
        ]);
        $this->crud->addField([
            'label' => "Sport",
            'type' => 'select_section',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'school_id' => $this->school_id,
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
            'school_id' => $this->school_id,
        ]);
        $this->crud->addField([   // Upload
            'name' => 'path',
            'label' => 'Document',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'school_document'
        ]);
    }

}
