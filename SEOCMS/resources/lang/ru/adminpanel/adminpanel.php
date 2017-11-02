<?php
return [
    'common'   => [
        'role'    => 'Админ',
        'logout'  => 'Выход',
        'welcome' => 'Добро пожаловать',
        'fields'  => [
            'id'     => '№',
            'action' => 'Дейстиве',
        ],
        'btn'     => [
            'create'        => 'Создать',
            'save'          => 'Сохранить',
            'edit'          => 'Редактировать',
            'delete'        => 'Удалить',
            'close'         => 'Закрыть',
            'switch_editor' => 'Переключить редактор',
            'cancel'        => 'Отмена',
        ],
        'action'  => [
            'delete' => [
                'question' => 'Вы действительно хотите удалить эту запись?',
                'success'  => 'Запись удалена!',
            ],
            'update' => [
                'question' => 'Вы действительно хотите обновить эту запись?',
                'success'  => 'Обновление прошло успешно!',
            ],
            'create' => [
                'success' => 'Создание прошло успешно!',
            ]
        ],
    ],
    'home'     => [
        'name' => 'Админ Панель'
    ],
    'domain'   => [
        'name'       => 'Домены',
        'list_title' => 'Список доменов',
        'create'     => 'Форма создания домена',
        'fields'     => [
            'domain_url'    => 'URL',
            'robots_domain' => 'robots.txt',
            'main_domain'   => 'Главный домен',
        ]
    ],
    'redirect' => [
        'name'       => 'Редиректы',
        'list_title' => 'Список редиректов',
        'create'     => 'Форма создания редиректа',
        'edit'       => 'Форма редактирования редиректа',
        'fields'     => [
            'id'           => '№',
            'oldurl'       => 'Старый Url',
            'newurl'       => 'Новый Url',
            'coderedirect' => 'HTTP код редиректа',
        ],
    ],
    'template' => [
        'name'       => 'Шаблоны',
        'list_title' => 'Список шаблонов',
        'create'     => 'Форма создания шаблона',
        'edit'       => 'Форма редактирования шаблона',
        'fields'     => [
            'id'          => '№',
            'virtualroot' => 'Виртуальный путь',
            'name'        => 'Имя',
            'body'        => 'Blade Шаблон',
        ]
    ],
    'page'     => [
        'name'       => 'Страницы',
        'list_title' => 'Список страниц',
        'create'     => 'Форма создания страницы',
        'edit'       => 'Форма редактирования страницы',
        'btn'        => [
            'edit_template'   => 'Редактировать шаблон',
            'create_template' => 'Создать шаблон'
        ],
        'fields'     => [
            'id'              => '#',
            'url'             => 'Url',
            'h1'              => 'h1',
            'title'           => 'Title',
            'description'     => 'Description',
            'keywords'        => 'Keywords',
            'active'          => 'Опубликовано',
            'template'        => 'Шаблон',
            'sitemappriority' => 'Приоритет в Sitemap',
            'criticalcss'     => 'Critical CSS',
        ],
    ],
    'critical' => [
        'name'             => 'Critical CSS',
        'list_title'       => 'Critical CSS',
        'generate_message' => 'Добавлено в обработку',
        'fields'           => [
            'process_all' => 'Обработать все',
            'date'        => 'Дата',
            'url'         => 'Url',
            'status'      => 'Статус',
            'resolutions' => 'Разрешение',
            'routes'      => 'Роуты',
        ],
        'btn'              => ['generate' => 'Сгенерировать Critical CSS'],
        'statuses'         => [
            '0' => 'Ожидание',
            '1' => 'Успешно',
            '2' => 'Ошибка',
        ]
    ],
];