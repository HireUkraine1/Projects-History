<?php
return [
    'common'   => [
        'role'    => 'ADMIN',
        'logout'  => 'Logout',
        'welcome' => 'Welcome',
        'fields'  => [
            'id'     => 'ID',
            'action' => 'Action',
        ],
        'btn'     => [
            'create'        => 'Create',
            'save'          => 'Save',
            'edit'          => 'Edit',
            'delete'        => 'Delete',
            'close'         => 'Close',
            'switch_editor' => 'Switch editor',
            'cancel'        => 'Cancel',
        ],
        'action'  => [
            'delete' => [
                'question' => 'Do you really want to delete this record?',
                'success'  => 'Record deleted!',
            ],
            'update' => [
                'question' => 'Do you really want to update this record?',
                'success'  => 'Record updated!',
            ],
            'create' => [
                'success' => 'Record created!',
            ]
        ]
    ],
    'home'     => [
        'name' => 'Home'
    ],
    'domain'   => [
        'name'       => 'Domains Alias',
        'list_title' => 'List of domains',
        'create'     => 'Create new domain form',
        'fields'     => [
            'domain_url'    => 'URL',
            'robots_domain' => 'robots.txt',
            'main_domain'   => 'Master',
        ]
    ],
    'redirect' => [
        'name'       => 'Redirects',
        'list_title' => 'List of redirects',
        'create'     => 'Create new redirect form',
        'edit'       => 'Edit redirect form',
        'fields'     => [
            'id'           => 'Id',
            'oldurl'       => 'Old Url',
            'newurl'       => 'New Url',
            'coderedirect' => 'Redirect HTTP code',
        ],
    ],
    'template' => [
        'name'       => 'Templates',
        'list_title' => 'List of templates',
        'create'     => 'Create new template form',
        'edit'       => 'Edit template form',
        'fields'     => [
            'id'          => '#',
            'virtualroot' => 'Virtual root',
            'name'        => 'Name',
            'body'        => 'Blade Template',
        ],
    ],
    'page'     => [
        'name'       => 'Page Sheets',
        'list_title' => 'List of pages',
        'create'     => 'Create new page form',
        'edit'       => 'Edit page form',
        'btn'        => [
            'edit_template'   => 'Edit template',
            'create_template' => 'Create template'
        ],
        'fields'     => [
            'id'              => '#',
            'url'             => 'Url',
            'h1'              => 'h1',
            'title'           => 'Title',
            'description'     => 'Description',
            'keywords'        => 'Keywords',
            'active'          => 'Active',
            'template'        => 'Template',
            'sitemappriority' => 'Sitemap Priority',
            'criticalcss'     => 'Critical CSS',
        ],
    ],
    'critical' => [
        'name'             => 'Critical CSS',
        'list_title'       => 'Critical CSS',
        'generate_message' => 'Added to process',
        'fields'           => [
            'process_all' => 'Process all',
            'date'        => 'Date',
            'url'         => 'Url',
            'status'      => 'Status',
            'resolutions' => 'Resolutions',
            'routes'      => 'Routes',
        ],
        'btn'              => ['generate' => 'Generate Critical CSS'],
        'statuses'         => [
            '0' => 'Waiting',
            '1' => 'Success',
            '2' => 'Error',
        ]
    ],
];