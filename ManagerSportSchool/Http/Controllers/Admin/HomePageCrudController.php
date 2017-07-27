<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PageRequest as UpdateRequest;
use CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation

class HomePageCrudController extends CrudController
{
    private $access = ['update'];

    public function __construct()
    {
        parent::__construct();
        /*
        |--------------------------------------------------------------------------
        | BsportC CRUD INFORMATION
        |--------------------------------------------------------------------------
         */
        $this->crud->setModel("\App\Models\HomePage");
        $this->crud->setRoute(config('base.route_prefix', 'admin') . '/home-page');
        $this->crud->setEntityNameStrings('home page', 'home page');
        $this->crud->access = $this->access;
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
            'type' => 'hidden',
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
    }


}
