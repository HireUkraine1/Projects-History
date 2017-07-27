<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PageRequest as StoreRequest;
use App\Http\Requests\PageRequest as UpdateRequest;
use App\Models;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class PageCrudController extends CrudController
{
    use \App\Http\Traits\Slug;

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\Page");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/page');
        $this->crud->setEntityNameStrings('page', 'pages');
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
            'name' => 'status',
            'label' => 'Status',
        ]);
        $this->crud->addColumn([
            'label' => 'Category',
            'type' => 'select',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "\App\Models\Category",
        ]);
        /*
        |----------------
        | CRUD FIELDS
        |----------------
         */
        $this->crud->addField([ // TEXT
            'name' => 'name',
            'label' => 'Name*',
            'type' => 'text',
            'placeholder' => 'Your title here',
        ]);
        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug (URL)*',
            'type' => 'text',
        ]);
        $this->crud->addField([ // CustomHTML
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
        $this->crud->addField([ // CustomHTML
            'name' => 'baner_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Top Banner</h2><hr>',
        ]);
        $this->crud->addField([ // Image
            'name' => 'baner_image',
            'label' => 'Banner Image',
            'type' => 'browse_image',
        ]);
        $this->crud->addField([ // ENUM
            'name' => 'baner_text',
            'label' => 'Banner Text',
            'type' => 'textarea',
        ]);
        $this->crud->addField([ // ENUM
            'name' => 'search_form',
            'label' => 'Search Form',
            'type' => 'enum',
        ]);
        $this->crud->addField([ // ENUM
            'name' => 'slogan',
            'label' => 'Slogan',
            'type' => 'enum',
        ]);
        $this->crud->addField([ // CustomHTML
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Content</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);
        $this->crud->addField([ // Image
            'name' => 'thumbnail',
            'label' => 'Thumbnail',
            'type' => 'browse_image',
        ]);
        $this->crud->addField([ // WYSIWYG
            'name' => 'content',
            'label' => 'Content',
            'type' => 'ckeditor',
            'placeholder' => 'Your textarea text here',
        ]);
        $this->crud->addField([ // SELECT
            'label' => 'Category',
            'type' => 'select',
            'name' => 'category_id',
            'entity' => 'category',
            'attribute' => 'name',
            'model' => "\App\Models\Category",
        ]);
        $this->crud->addField([ // ENUM
            'name' => 'status',
            'label' => 'Status',
            'type' => 'enum',
        ]);

    }

}
