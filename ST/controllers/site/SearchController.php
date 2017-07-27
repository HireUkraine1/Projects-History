<?php

class SearchController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default City Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |
    |
    */
    protected $layout = 'frontend.layouts.site';

    public function search($terms = false)
    {
        if (!$terms):
            $terms = Input::get('terms');
        endif;
        $terms = strtolower($terms);
        $clean = str_replace('+', ' ', $terms);
        $garbage = ['at', 'in', 'playing', 'performing', 'concert', 'at', 'in the', 'in a', 'at a', 'by', 'when'];
        foreach ($garbage as $g):
            $clean = str_replace(" " . $g . " ", ' ', $clean);
        endforeach;
        $query = explode(' ', $clean);
        $result = Search::where('type', 'performer')->where('term', $clean)->get();

        if (!$result->count()):
            $result = Search::where('type', 'performer')->where(function ($q) use ($query) {
                foreach ($query as $t):
                    $q->orWhere('term', $t);
                endforeach;
            })->get();
        endif;

        $pArray = [];
        foreach ($result as $r):
            $pArray[] = $r->performer_id;
        endforeach;
        if (count($pArray)):
            $performers = Performer::with('upcoming_concerts')
                ->whereIn('id', $pArray)
                ->with('images')
                ->take(10)
                ->get();
        else:
            $performers = [];
        endif;


        $results2 = Search::where('type', 'concert')->where('term', $clean)->get();

        if (!$results2->count()):
            $results2 = Search::where('type', 'concert')->where('term', 'all', $query)->get();
        endif;
        $cArray = [];
        foreach ($results2 as $r):
            $cArray[] = $r->concert_id;
        endforeach;

        // DebugHelper::pdd($cArray, false);
        if (count($cArray)):
            $concerts = Concert::with('performers')
                ->where(function ($q) use ($cArray) {
                    foreach ($cArray as $cid):
                        $q->orWhere('id', $cid);
                    endforeach;
                })->
                where('date', '>', date('Y-m-d H:i:s'))
                ->with('performers.images')
                ->with('venue')
                ->with('location')
                ->take(50)
                ->orderBy('date', 'asc')
                ->get();
        else:
            $concerts = [];
        endif;

        if (strlen($clean) > 3):
            $slugit = StringHelper::create_slug($clean);
            $cities = Location::where('slug', 'LIKE', "%{$slugit}%")->orWhere('city', 'LIKE', "%{$clean}%")->get();
        else:
            $cities = false;
        endif;

        $data = ['performers' => $performers, 'concerts' => $concerts, 'cities' => $cities];

        $breadcrumbs = [
            ['link' => "/search/{$terms}", 'title' => 'search results: <span>' . $clean . '</span>'],
        ];
        View::share('breadcrumbs', $breadcrumbs);
        View::share('terms', $terms);
        $this->layout->bigsearch = View::make('frontend.global.bigsearch');
        $this->layout->content = View::make('frontend.search.results', $data);
    }
}