<?php

class CityController extends BaseController
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

    public function index()
    {

        $featuredUS = FeaturedLocation::where('country', 'US')->orderBy('city', 'asc')->remember(300)->get();
        $featuredCA = FeaturedLocation::where('country', 'CA')->orderBy('city', 'asc')->remember(300)->get();
        $breadcrumbs = [
            ['title' => 'All Cities', 'link' => '/all-cities']
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [
            'meta' => [
                'description' => "Find a list of all the top concert cities in North America. Find your city and check out all the great upcoming concerts",
                'keywords' => "site, Concert Cities, Tour Cities",
                'robots' => "index, follow",
                'title' => "Top Concert Cities in North America | site.com",
                'canonical' => url('/all-cities'),
            ],
            'og' => [
                'description' => "Find a list of all the top concert cities in North America. Find your city and check out all the great upcoming concerts",
                'keywords' => "site, Concert Cities, Tour Cities",
                'robots' => "index, follow",
                'title' => "Top Concert Cities in North America | site.com",

            ]
        ];
        View::share('metadata', $metadata);

        $this->layout->tagline = View::make('frontend.city.all-cities-tagline');
        $this->layout->content = View::make('frontend.city.all-cities-content', array('featuredUS' => $featuredUS, 'featuredCA' => $featuredCA));
    }

    public function all_all()
    {
        if (Cache::has('US_StateList')):
            $US_StateList = Cache::get('US_StateList');
        else:
            $allUS = Location::where('country', 'US')->where('event_count', '>', 0)->orderBy('state', 'asc')->orderBy('city', 'asc')->remember(300)->get();

            $US_StateList = array();
            foreach ($allUS as $city) {
                if (isset($US_StateList[$city->state]) == FALSE) {
                    $US_StateList[$city->state] = array();
                }
                array_push($US_StateList[$city->state], $city);
            }
            Cache::put('US_StateList', $US_StateList, 60);
        endif;

        if (Cache::has('CA_StateList')):
            $CA_StateList = Cache::get('CA_StateList');
        else:
            $allUS = Location::where('country', 'CA')->where('event_count', '>', 0)->orderBy('state', 'asc')->orderBy('city', 'asc')->remember(300)->get();

            $CA_StateList = array();
            foreach ($allUS as $city) {
                if (isset($CA_StateList[$city->state]) == FALSE) {
                    $CA_StateList[$city->state] = array();
                }
                array_push($CA_StateList[$city->state], $city);

            }
            Cache::put('CA_StateList', $CA_StateList, 60);
        endif;


        $breadcrumbs = [
            ['title' => 'All Cities', 'link' => '/all-cities'],
            ['title' => 'Full List [A-Z]', 'link' => '/all-cities/a-z']
        ];
        View::share('breadcrumbs', $breadcrumbs);
        $metadata = [];
        View::share('metadata', $metadata);

        $this->layout->tagline = View::make('frontend.city.all-cities-tagline');
        $this->layout->tagline = View::make('frontend.city.all-all-cities-tagline');
        $this->layout->content = View::make('frontend.city.all-all-cities-content', array('allUS' => $US_StateList, 'allCA' => $CA_StateList));
    }

    public function city($slug = false)
    {
        $currentUri = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $rebit = explode('?', $currentUri);
        $relCannonical = $rebit[0];
        //when it's featured city, get real slug
        $viewall = (Input::get('viewall') && Input::get('viewall') === 1) ? true : false;
        View::share('viewall', $viewall);
        $take = ($viewall) ? 500 : 40;
        $parts = explode('+', $slug);
        $locslug = $parts[0];
        $realSlug = UrlHelper::getRealSlug($locslug);
        $location = Location::where('slug', $realSlug)->with('burbs')->first();
        $lid = $location->id;
        $robots = "index,follow";
        if ($child = Burb::where('location_id', $location->id)->with('metro')->first()):
            $metroSlug = $child->metro->slug;
            if ($redirect = UrlHelper::isRedirected($metroSlug)):
                $metroSlug = $redirect->to_url;
            endif;
            if (isset($parts[1])): //we have a filter
                $filter = $parts[1];
                return Redirect::to($metroSlug . "+" . $filter, 301);
            else:
                return Redirect::to($metroSlug, 301);
            endif;
        endif;

        $time = date('U');
        $cityConcerts = Concert::where('location_id', $location->id)
            ->whereBetween('date', array(date('Y-m-d'), date('Y-m-d', strtotime("+1 year", $time))))
            ->whereHas('performers', function ($query) {
                $query->where('performers.id', "<>", 'NULL');
            })
            ->orderBy('date', 'asc')
            ->get();
        $cityDates = [];
        foreach ($cityConcerts as $cc):
            $month = date('n', strtotime($cc->date));
            $year = date('Y', strtotime($cc->date));
            $cityDates[$month . "-" . $year]['start'] = date("{$year}-{$month}-01");
            $dayCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $cityDates[$month . "-" . $year]['end'] = date("{$year}-{$month}-{$dayCount}");
        endforeach;


        View::share('cityDates', $cityDates);
        $burbs = [];
        foreach ($location->burbs as $burb):
            $burbs[] = $burb->location_id;
        endforeach;
        $burbs[] = $location->id;
        View::share('location', $location);
        View::share('realSlug', $realSlug);
        View::share('citySlug', $locslug);
        $filterString = date('Y');
        $hasFilter = false;
        $genreString = '';
        $nearBy = DB::table('location_nearby')->where('center_slug', $location->slug)->where('distance', '<', 100)->take(60)->orderBy('distance', 'ASC')->get();
        $slugs = [];
        //check if child - set to parent
        //
        foreach ($nearBy as $nb):
            $slugs[] = $nb->slug;
        endforeach;
        $nearByLocations = Location::where('event_count', '>', '0')
            ->where('id', '<>', $location->id)
            ->whereIn('slug', $slugs)
            ->orderBy('event_count', 'desc')
            ->remember(360)
            ->get();

        $nearByIds = [];
        $cleanNearBy = [];
        foreach ($nearByLocations as $loc):
            $nearByIds[] = $loc->id;
            $cleanNearBy[$loc->id] = $loc;
        endforeach;
        if ($nearByIds):
            $metros = Burb::whereIn('location_id', $nearByIds)->with('metro')->with('location')->get();
            foreach ($metros as $metro):
                if (isset($cleanNearBy[$metro->location->id])) unset($cleanNearBy[$metro->location->id]);
            endforeach;
        endif;


        $cleanNearBy = array_slice($cleanNearBy, 0, 10);
        View::share('nearby', $cleanNearBy);
        // $genres = [];
        $genres = Genre::whereHas('concerts', function ($query) use ($lid) {
            $query->where('location_id', $lid)->where('date', '>', date('Y-m-d'));
        })->remember(30)->get();

        // dd($genres->toArray());
        $spinData['location_id'] = $location->id;


        if (isset($parts[1])): //we have a filter
            if ($parts[1] === "") return Redirect::to($parts[0], 301); //fix trailing "+" issue
            $hasFilter = true;
            if ($genre = Genre::where('slug', $parts[1])->first()):
                if (!$genre) App::abort(404); //throw 404 NOT sure what this does, becuase it will go into dates

                $gId = $genre->id;
                $concerts = Concert::with('performers')
                    ->with('venue')
                    ->with('tnConcert')
                    ->with('performers.images')
                    ->with('genres')
                    ->where('location_id', $location->id)
                    ->where('date', '>', date('Y-m-d'))
                    ->whereHas('genres', function ($q) use ($gId) {
                        $q->where('genres.id', $gId);
                    })
                    ->whereHas('performers', function ($query) {
                        $query->where('performers.id', "<>", 'NULL');
                    })
                    ->take($take)
                    ->orderBy('date', 'ASC')
                    ->remember(360)
                    ->get();
                if (!$concerts->count()) App::abort('404');
                $title = "{$location->city} {$genre->genre} Concerts " . date('Y') . ". {$location->city}, {$location->state} {$genre->genre} Concert Calendar";
                $description = "{$genre->genre} Concerts scheduled in {$location->city} in " . date('Y') . ". Find a full {$location->city}, {$location->state} {$genre->genre} concert calendar and schedule";
                $keywords = "site, {$location->city} {$genre->genre} Concerts, {$location->city} {$genre->genre} Concert Schedule, {$location->city} {$genre->genre} Concert Calendar";
                $mainText = ucwords("view upcoming " . date('Y') . " {$genre->genre} concerts in {$location->city}");

            else: //it's a date

                if (!isset($parts[1]) || !isset($parts[2])) App::abort(404); //throw 404
                $start = date('Y-m-d', strtotime($parts[1]));
                $end = date('Y-m-d', strtotime($parts[2]));
                //replace this with eager loading
                // $concerts = $location->concerts()->whereBetween('date', array($start, $end))->orderBy('date','ASC')->get();
                $concerts = Concert::with('performers')
                    ->with('venue')
                    ->with('tnConcert')
                    ->with('performers.images')
                    ->with('genres')
                    ->where('location_id', $location->id)
                    ->whereBetween('date', array($start, $end))
                    ->whereHas('performers', function ($query) {
                        $query->where('performers.id', "<>", 'NULL');
                    })
                    ->take($take)
                    ->orderBy('date', 'ASC')
                    ->remember(360)
                    ->get();

                if (!$concerts->count()) App::abort('404');

                $startMonth = date('M', strtotime($start));
                $startMonthNum = date('n', strtotime($start));
                $endMonth = date('M', strtotime($end));
                $startYear = date('Y', strtotime($start));

                $datediff = strtotime($end) - strtotime($start) + 1;
                $rangeDayCount = ceil($datediff / (60 * 60 * 24));

                $robots = ($startMonth == $endMonth && $rangeDayCount == cal_days_in_month(CAL_GREGORIAN, $startMonthNum, $startYear)) ? 'index,follow' : "noindex,nofollow";

                $filterString = date('F Y', strtotime($start));

                $title = "{$location->city} {$filterString} Concerts. {$location->city}, {$location->state} {$filterString} Concert Calendar";
                $description = "{$filterString} Concerts scheduled in {$location->city}. Find a full {$location->city}, {$location->state} {$filterString} concert calendar and schedule";
                $keywords = "site, {$location->city} {$filterString} Concerts, {$location->city} {$filterString} Concert Schedule, {$location->city} {$filterString} Concert Calendar";
                $mainText = ucwords("view upcoming {$location->city} concerts in {$filterString} ");
                // $this->layout->content = View::make('frontend.city.single-content', array('concerts'=>$concerts, 'filter' => $filterString));
            endif;
        else:
            $concerts = Concert::with('performers')
                ->with('venue')
                ->with('tnConcert')
                ->with('performers.images')
                ->with('genres')
                ->wherein('location_id', $burbs)
                ->where('date', '>', date('Y-m-d'))
                ->whereHas('performers', function ($query) {
                    $query->where('performers.id', "<>", 'NULL');
                })
                ->take($take)
                ->orderBy('date', 'ASC')
                ->remember(360)
                ->get();
            $mainText = Spinner::getText('citytext', $spinData);

            $title = "{$location->city} Concerts " . date('Y') . ". {$location->city}, {$location->state} Concert Calendar";
            $description = "Concerts scheduled in {$location->city} in " . date('Y') . ". Find a full {$location->city}, {$location->state} concert calendar and schedule";
            $keywords = "site, {$location->city} Concerts, {$location->city} Concert Schedule, {$location->city} Concert Calendar";
        endif;
        $breadcrumbs = [
            // ['link' => '/all-cities', 'title' => 'All Cities'],
            ['link' => '/' . $slug, 'title' => $location->city]
        ];
        View::share('breadcrumbs', $breadcrumbs);

        $metadata = [
            'meta' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => $robots,
                'googlebot' => $robots,
                'msnbot' => $robots,
                'title' => $title,
                'canonical' => $relCannonical
            ],
            'og' => [
                'description' => $description,
                'keywords' => $keywords,
                'robots' => "index, follow",
                'title' => $title

            ]
        ];
        View::share('metadata', $metadata);
        View::share('concerts', $concerts);
        View::share('genres', $genres);
        View::share('filterString', $filterString);
        View::share('hasFilter', $hasFilter);
        View::share('text', $mainText);
        $this->layout->tagline = View::make('frontend.city.city-tagline');
        $this->layout->content = View::make('frontend.city.city-content');
        $this->layout->customjs = View::make('frontend.city.city-customjs');
    }

}