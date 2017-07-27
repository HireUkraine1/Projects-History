<?php

namespace App\Http\Controllers\School;

// use App\Http\Requests\CourseRequest as CreateRequest;
// use App\Http\Requests\CourseRequest as UpdateRequest;
use App\Http\Traits\TraitCrudController;
use CrudController;
use Illuminate\Http\Request as StoreRequest;

class SchoolDocumentCrudController extends CrudController
{

    use TraitCrudController;

    public $school;
    private $access = ['list', 'update', 'create', 'delete'];
    private $school_id;
    private $document_id;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->school = \Auth::guard('school')->user();
            if ($this->school->status_id === 1) {
                return redirect('school/account');
            }
            return $next($request);
        });
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\SchoolDocument");
        $this->crud->setRoute('/school/document');
        $this->crud->setEntityNameStrings('school document', 'school documents');
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
            'school_id' => 'auth',
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
            'school_id' => 'auth',
        ]);
        $this->crud->addField([   // Upload
            'name' => 'path',
            'label' => 'Document',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'school_document'
        ]);
    }

    public function index()
    {
        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        // get all entries if AJAX is not enabled
        if (!$this->data['crud']->ajaxTable()) {
            $this->data['entries'] = $this->crud->model->where('school_id', $this->school->id)->get();
        }
        return view($this->crud->getListView(), $this->data);
    }

    public function store(StoreRequest $request)
    {
        $request->merge(array('school_id' => $this->school->id));
        return parent::storeCrud();
    }

    public function edit($id)
    {
        $document = $this->crud->model->where('school_id', $this->school->id)->where('id', '=', $id)->first();
        if (!$document) {
            abort("404");
        }
        return parent::edit($id);
    }
}
