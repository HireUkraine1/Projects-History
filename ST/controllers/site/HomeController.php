<?php

use Carbon\Carbon;

class HomeController extends BaseController
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
    protected $layout = 'frontend.layouts.home';


    public function index()
    {

        // echo Hash::make('WynDigity2014!');
        //
        $conf = App::make('conf');
        $currentLocation = VarsHelper::get_location();
        $geoConcerts = false;
        $naConcerts = false;
        $noGeoDupes = [];
        $noGeoPerformerDupes = [];
        $uniqueGeoPerformers = [];
        $uniqueGeoEvents = [];
        if (isset($currentLocation->location_id)):
            // if(false):
            $location = Location::where('id', $currentLocation->location_id)->first();
            // $location = Location::where('slug','las-vegas-nv')->first();
            $nearby = DB::table('location_nearby')
                ->join('locations', 'locations.slug', '=', 'location_nearby.slug')
                ->where('center_slug', $currentLocation->slug)
                ->where('distance', '<', 10)
                // ->take(20)
                ->orderBy('distance', 'ASC')
                ->select('locations.id', 'location_nearby.slug')
                ->get();
            $locIds = [];
            $locIds[] = $currentLocation->location_id;
            foreach ($nearby as $loc):
                $locIds[] = $loc->id;
            endforeach;

            $fps = FeaturedPerformer::where('geo', '1')->get();
            $psIds = [];
            foreach ($fps as $fpGeo):
                $psIds[] = $fpGeo->performer_id;
            endforeach;

            $geoConcerts = Concert::whereIn('location_id', $locIds)
                ->whereHas('performers', function ($q) use ($psIds) {
                    $q->whereIn('performers.id', $psIds);
                })
                ->with('performers')
                ->with('performers.images')
                ->with('location')
                ->with('venue')
                ->where('date', '>', date('Y-m-d'))
                ->orderBy('date', 'asc')
                ->take(20)
                ->get();


            foreach ($geoConcerts as $c):
                foreach ($c->performers as $p):
                    if (!in_array($p->id, $noGeoPerformerDupes)) $uniqueGeoPerformers[] = $c;
                    array_push($noGeoPerformerDupes, $p->id);
                endforeach;
            endforeach;

            foreach ($uniqueGeoPerformers as $c):
                // foreach ($c->performers as $p):
                if (!in_array($c->name, $noGeoDupes)) $uniqueGeoEvents[] = $c;
                array_push($noGeoDupes, $c->name);
                // endforeach;
            endforeach;
            // $arrayOfItems = [];
            $uniqueGeoEvents = array_slice($uniqueGeoEvents, 0, 7);
            $geoCount = count($uniqueGeoEvents);

            if ($geoCount < 7 && $geoCount < 5): //Not enough objects, lets get additional items

                $noDupes = [];
                $noDupes[] = 0;
                foreach ($geoConcerts as $i):
                    foreach ($i->performers as $p):
                        array_push($noDupes, $p->id);
                    endforeach;
                endforeach;
                $fpsNonGeo = FeaturedPerformer::where('home', '1')->whereNotIn('performer_id', $noDupes)->get();

                $psNonGeoId = [];
                foreach ($fpsNonGeo as $fpNg):
                    $psNonGeoId[] = $fpNg->performer_id;
                endforeach;

                $take = 7 - $geoCount;
                $naConcerts = Concert::whereIn('location_id', $locIds)
                    ->whereHas('performers', function ($q) use ($psNonGeoId) {
                        $q->whereIn('performer_id', $psNonGeoId);
                    })
                    ->with('performers')
                    ->with('performers.images')
                    ->with('location')
                    ->with('venue')
                    ->where('date', '>', date('Y-m-d'))
                    ->orderBy('date', 'asc')
                    ->take($take)
                    ->get();

            endif;
        else:
            $location = false;

            $fpsNonGeo = FeaturedPerformer::where('home', '1')->get();
            $psNonGeoId = [];
            foreach ($fpsNonGeo as $fpNg):
                $psNonGeoId[] = $fpNg->performer_id;
            endforeach;

            $naConcerts = Concert::whereHas('performers', function ($q) use ($psNonGeoId) {
                $q->whereIn('performer_id', $psNonGeoId);
            })
                ->with('performers')
                ->with('performers.images')
                ->with('location')
                ->with('venue')
                ->where('date', '>', date('Y-m-d'))
                ->orderBy('date', 'asc')
                ->take(7)
                ->get();
        endif;

        View::share('location', $location);


        $announcements = Announcement::where('status', 1)
            ->has('categories', '<>', 0)
            ->where('publish_date', '<', date('Y-m-d H:i:s'))
            ->with('categories')
            ->orderBy('publish_date', 'DESC')
            ->take($conf['home_announcements_per_page'])
            ->get();


        $this->layout->bigsearch = View::make('frontend.home.bigsearch');

        $contentData = [
            'naConcerts' => $naConcerts,
            'geoConcerts' => $uniqueGeoEvents,
            'announcements' => $announcements,
        ];
        $metadata = [
            'meta' => [
                'description' => "site.com: When it comes to tour dates, concert news, concert tickets, and tour info, we never miss a beat.",
                'keywords' => "site, tour dates, tour news, concert info, concert tickets",
                'robots' => "index, follow",
                'title' => "site.com: Never Miss a Beat on Tour Dates, News & Info",
            ],
            'og' => [
                'description' => "site.com: When it comes to tour dates, concert news, concert tickets, and tour info, we never miss a beat.",
                'keywords' => "site, tour dates, tour news, concert info, concert tickets",
                'robots' => "index, follow",
                'title' => "site.com: Never Miss a Beat on Tour Dates, News & Info",
            ]
        ];
        View::share('metadata', $metadata);

        $this->layout->head = View::make('frontend.home.head', $metadata);
        $this->layout->login = View::make('frontend.global.login');
        $this->layout->hero = View::make('frontend.home.hero');
        //['announcements' => $announcements, 'randomFeatured' => $randomFeatured]
        $this->layout->content = View::make('frontend.home.content', $contentData);
    }

}