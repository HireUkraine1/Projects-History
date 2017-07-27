<?php

namespace App\Http\Controllers\School;

use CrudController;

class DocumentCrudController extends CrudController
{
    private $access = ['list', 'show-document'];

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
        $this->crud->setModel("\App\Models\Document");
        $this->crud->setRoute('school/resourse');
        $this->crud->setEntityNameStrings('document', 'documents');
        $this->crud->access = $this->access;
        $this->crud->addButton('line', 'Document', 'getOpenButton', 'crud.buttons.school.document_show');
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
        $this->crud->addField([ // Upload
            'name' => 'path',
            'label' => 'Document',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'uploads',
        ]);
    }

    public function index()
    {
        $categories = $last_names = array_column($this->school->categories->toArray(), 'id');
        $sportsections = array_column($this->school->sportsections->toArray(), 'id');
        $sportsections[] = 0;
        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = ucfirst($this->crud->entity_name_plural);
        if (!$this->data['crud']->ajaxTable()) {
            $this->data['entries'] = $this->crud->model->whereIn('category_id', $categories)->WhereIn('sport_section_id', $sportsections)->get();
        }
        return view($this->crud->getListView(), $this->data);
    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show-document');
        $categories = $last_names = array_column($this->school->categories->toArray(), 'id');
        $sportsections = array_column($this->school->sportsections->toArray(), 'id');
        $sportsections[] = 0;
        $document = $this->crud->model->whereIn('category_id', $categories)->WhereIn('sport_section_id', $sportsections)->where('id', '=', $id)->first();
        if (!$document) {
            abort("404");
        }
        $document = $this->crud->getEntry($id);
        $file = public_path() . "/storage/" . $document->path;
        return \Response::download($file, $document->path);
    }
}
