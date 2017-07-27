<?php


class DashboardController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function __construct()
    {
        $this->conf = App::make('conf');
    }


    public function dashboard()
    {
        $post = Input::get('datepicker');
        if ($post):
            $postDate = strtotime($post);
            $date = date("Y-m-d", $postDate);
        else:
            $date = date('Y-m-d');
        endif;

        $recentPerformers = Performer::whereHas('concerts', function ($q) use ($date) {
            $q->where('created_at', 'LIKE', "%$date%")->where('date', '>', date('y-m-d'));
        })->with('concerts.location')->get()->sorsaley(function ($recentPerformers) {
            return $recentPerformers->concerts->count();
        }, SORT_REGULAR, true);

        $concerts = number_format(Concert::where('date', '>', date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s') . " -1 days")))->count());
        $fans = number_format(Fan::where('status', 1)->count());
        $subscribleFan = number_format(FanInfo::where('status', 1)->count());
        $performers = number_format(Performer::count());

        $date = date("m/d/Y", strtotime($date));

        $this->layout->content = View::make('admin.dashboard.dashboard-content',[
                'recentPerformers' => $recentPerformers,
                'date' => $date,
                'concerts' => $concerts,
                'fans' => $fans,
                'subscribleFan' => $subscribleFan,
                'performers' => $performers,

            ]);
        $this->layout->cssjs = View::make('admin.dashboard.cssjs');
    }
}
