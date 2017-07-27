<?php

Class NewsController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */
    protected $layout = 'frontend.layouts.site';



    public function index($catslug = false)
    {
        try {
            $conf = App::make('conf');

            $now = date('Y-m-d H:i:s');
            $categories = Category::all();

            if ($catslug):
                $category = Category::where('slug', $catslug)->first();

                if ($category):
                    $announcements = $category
                        ->announcements()
                        ->with('categories')
                        ->where('status', 1)
                        ->where('publish_date', '<=', $now)
                        ->orderBy('publish_date', 'DESC')
                        ->paginate($conf['annuncements_per_page']);

                    $breadcrumbs = [
                        ['link' => $conf['annuncements_index_url'], 'title' => 'Tour News and Concert Announcements'],
                        ['link' => '#', 'title' => $category->category],
                    ];
                    $title = "{$category->category} Tour News & Concert Announcements";
                    $description = "The latest {$category->category} tour news for tour announcements, tour guests, concert schedules, music festivals and more ";
                    $keywords = "{$category->category}, site, tour news, tour announcements, concert information";
                    View::share('breadcrumbs', $breadcrumbs);
                    $metadata = [
                        'meta' => [
                            'description' => $description,
                            'keywords' => $keywords,
                            'robots' => "index, follow",
                            'title' => $title,
                        ],
                        'og' => [
                            'description' => $description,
                            'keywords' => $keywords,
                            'robots' => "index, follow",
                            'title' => $title
                        ]
                    ];
                    View::share('metadata', $metadata);

                    $this->layout->customjs = View::make('frontend.news.customjs');
                    $this->layout->tagline = View::make('frontend.news.announcements-tagline', ['categories' => $categories, 'category' => $category]);
                    $this->layout->content = View::make('frontend.news.announcements-content', ['announcements' => $announcements, 'category' => $category]);
                else:
                    $now = date('Y-m-d H:i:s');

                    $catslug = strtolower($catslug);
                    $announcement = Announcement::where('status', 1)
                        ->where('slug', $catslug)
                        ->where('publish_date', '<', $now)
                        ->with('categories')->first();
                    $announcements = Announcement::where('status', 1)
                        ->has('categories', '<>', 0)
                        ->where('publish_date', '<', $now)
                        ->with('categories')
                        ->orderBy('publish_date', 'DESC')
                        ->take(5)->get();
                    if (!$announcement) App::abort(404);
                    $breadcrumbs = [
                        ['link' => $conf['annuncements_index_url'], 'title' => 'Tour News and Concert Announements'],
                        ['link' => '#', 'title' => $announcement->title],
                    ];
                    View::share('breadcrumbs', $breadcrumbs);

                    View::share('announcement', $announcement);
                    View::share('announcements', $announcements);
                    View::share('categories', $categories);
                    $metadata = [
                        'meta' => [
                            'description' => $announcement->excerpt,
                            'keywords' => "site, tour news, tour announcements, concert information",
                            'robots' => "index, follow",
                            'title' => $announcement->title,
                        ],
                        'og' => [
                            'description' => $announcement->excerpt,
                            'keywords' => "site, tour news, tour announcements, concert information",
                            'robots' => "index, follow",
                            'title' => $announcement->title,
                        ]
                    ];
                    View::share('metadata', $metadata);
                    $this->layout->tagline = View::make('frontend.news.announcement-tagline', ['category' => false]);
                    $this->layout->content = View::make('frontend.news.announcement-content');
                endif;
            else:
                $title = "Tour News & Concert Announcements";
                $description = "The latest tour news for tour announcements, tour guests, concert schedules, music festivals and more ";
                $keywords = "site, tour news, tour announcements, concert information";
                $category = false;
                $announcements = Announcement::where('status', 1)
                    ->has('categories', '<>', 0)
                    ->where('publish_date', '<=', $now)
                    ->with('categories')
                    ->orderBy('publish_date', 'DESC')
                    ->paginate($conf['annuncements_per_page']);
                // dd($announcements->toArray());
                $breadcrumbs = [
                    ['link' => $conf['annuncements_index_url'], 'title' => 'Tour News and Concert Announements'],
                ];
                $title = "Tour News & Concert Announcements";
                $description = "The latest tour news for tour announcements, tour guests, concert schedules, music festivals and more ";
                $keywords = "site, tour news, tour announcements, concert information";
                View::share('breadcrumbs', $breadcrumbs);
                $metadata = [
                    'meta' => [
                        'description' => $description,
                        'keywords' => $keywords,
                        'robots' => "index, follow",
                        'title' => $title,
                    ],
                    'og' => [
                        'description' => $description,
                        'keywords' => $keywords,
                        'robots' => "index, follow",
                        'title' => $title
                    ]
                ];
                View::share('metadata', $metadata);

                $this->layout->customjs = View::make('frontend.news.customjs');
                $this->layout->tagline = View::make('frontend.news.announcements-tagline', ['categories' => $categories]);
                $this->layout->content = View::make('frontend.news.announcements-content', ['announcements' => $announcements, 'category' => $category]);
            endif;

        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }


}

?>