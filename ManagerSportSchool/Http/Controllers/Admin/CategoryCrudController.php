<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryRequest as UpdateRequest;
use App\Models;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class CategoryCrudController extends CrudController
{
    private $access = ['list', 'update', 'create', 'delete'];
    private $id;

    public function __construct()
    {
        $this->id = \Route::getCurrentRoute()->hasParameter('category') ? \Route::getCurrentRoute()->parameter('category') : 0;
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Category");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/category');
        $this->crud->setEntityNameStrings('category', 'categories');
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
            'name' => 'slug',
            'label' => 'Slug',
        ]);
        $this->crud->addColumn([
            'label' => 'Parent',
            'type' => 'select',
            'name' => 'parent_id',
            'entity' => 'parent',
            'attribute' => 'name',
            'model' => "\App\Models\Category",
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([
            'name' => 'name',
            'label' => 'Name*',
        ]);
        $this->crud->addField([
            'name' => 'alias',
            'label' => 'Alias*',
        ]);
        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug (URL)*',
            'type' => 'text',
        ]);
        $this->crud->addField([
            'label' => 'Parent',
            'type' => 'select_category',
            'name' => 'parent_id',
            'entity' => 'parent',
            'attribute' => 'name',
            'id' => $this->id,
            'model' => "\App\Models\Category",
        ]);
        $this->crud->addField([
            'name' => 'metas_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Metas</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'metas_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Metas</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'meta_title',
            'label' => 'Meta Title',
        ]);
        $this->crud->addField([
            'name' => 'meta_description',
            'label' => 'Meta Description',
        ]);
        $this->crud->addField([
            'name' => 'meta_keywords',
            'type' => 'textarea',
            'label' => 'Meta Keywords',
        ]);
        $this->crud->addField([
            'name' => 'baner_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Top Banner</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'baner_image',
            'label' => 'Banner Image',
            'type' => 'browse_image',
        ]);
        $this->crud->addField([
            'name' => 'baner_text',
            'label' => 'Banner Text',
            'type' => 'textarea',
        ]);
        $this->crud->addField([
            'name' => 'slogan',
            'label' => 'Slogan',
            'type' => 'enum',
        ]);
        $this->crud->addField([ // ENUM
            'name' => 'search_form',
            'label' => 'Search Club',
            'type' => 'enum',
        ]);
        $this->crud->addField([
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Content</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);
        $this->crud->addField([
            'name' => 'thumbnail',
            'label' => 'Category Thumbnail',
            'type' => 'browse_image',
        ]);
        $this->crud->addField([
            'name' => 'short_description',
            'type' => 'textarea',
            'label' => 'Short Description',
        ]);
        $this->crud->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'ckeditor',
            'placeholder' => 'Your textarea text here',
        ]);
    }

}
