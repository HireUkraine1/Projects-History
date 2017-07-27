<?php

class ConcertController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Concert Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'ConcertController@showWelcome');
    |
    */
    protected $layout = 'frontend.layouts.site';

    public function index($withevents = true)
    {
        $fullList = Performer::has('upcoming_concerts')
            ->orderBy('name', 'asc')
            ->get();
        // DebugHelper::pdd($fullList->count(),true); die();
        $A_Z_artists_list = array();
        $A_Z_artists_list['other'] = [];
        foreach ($fullList as $artist) {
            // if ($artist->upcoming_concerts->count()):
            $first_char = strtoupper($artist->name[0]);
            if (ctype_alpha($first_char)):
                if (isset($A_Z_artists_list[$first_char]) == FALSE) {
                    $A_Z_artists_list[$first_char] = array();
                }
                array_push($A_Z_artists_list[$first_char], $artist);
            else:
                array_push($A_Z_artists_list['other'], $artist);
            endif;
            // endif;
        }
        $breadcrumbs = [
            ['link' => '/concerts', 'title' => 'All Artists'],
        ];
        View::share('breadcrumbs', $breadcrumbs);

        $metadata = [
            'meta' => [
                'description' => "Find all artists on tour or that have at least one concert scheduled",
                'keywords' => "site, Tours, Concerts, Artists",
                'robots' => "index, follow",
                'title' => "All Artists Currently On Tour | site.com",
                'canonical' => url('/concerts'),
            ],
            'og' => [
                'description' => "Find all artists on tour or that have at least one concert scheduled",
                'keywords' => "site, Tours, Concerts, Artists",
                'robots' => "index, follow",
                'title' => "All Artists on Currently on Tour | site.com",
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->content = View::make('frontend.concerts.all-content', array('A_Z_artists_list' => $A_Z_artists_list));
        $this->layout->tagline = View::make('frontend.concerts.all-tagline');
    }

    public function all($withevents = true)
    {
        $fullList = Performer::orderBy('name', 'asc')
            ->get();

        $A_Z_artists_list = array();
        $A_Z_artists_list['other'] = [];
        foreach ($fullList as $artist) {
            // if ($artist->upcoming_concerts->count()):
            $first_char = strtoupper($artist->name[0]);
            if (ctype_alpha($first_char)):
                if (isset($A_Z_artists_list[$first_char]) == FALSE) {
                    $A_Z_artists_list[$first_char] = array();
                }
                array_push($A_Z_artists_list[$first_char], $artist);
            else:
                array_push($A_Z_artists_list['other'], $artist);
            endif;
            // endif;
        }

        $breadcrumbs = [
            ['link' => '/concerts', 'title' => 'All Artists'],
        ];
        View::share('breadcrumbs', $breadcrumbs);

        $metadata = [
            'meta' => [
                'description' => "Every Artist We Know",
                'keywords' => "site, Tours, Concerts, Artists",
                'robots' => "index, follow",
                'title' => "All Artists | site.com",
                'canonical' => url('/concerts'),
            ],
            'og' => [
                'description' => "Every Artist We Know",
                'keywords' => "site, Tours, Concerts, Artists",
                'robots' => "index, follow",
                'title' => "All Artists on | site.com",
            ]
        ];
        View::share('metadata', $metadata);
        $this->layout->content = View::make('frontend.concerts.all-all-content', array('A_Z_artists_list' => $A_Z_artists_list));
        $this->layout->tagline = View::make('frontend.concerts.all-all-tagline');
    }

    public function performer($slug = null)
    {
        if ($sendTo = UrlHelper::isRedirected("concerts/" . $slug))
            return Redirect::to($sendTo->to_url, 301);
        set_time_limit(0);
        $currentLocation = VarsHelper::get_location();
        if (!Session::has('fan')):

            $city = Location::where('slug', $currentLocation->slug)->get(['city'])->first();
            if (isset($city->city) && !empty($city->city)):
                $city = $city->city;
            else:
                $city = 'near you';
            endif;
        else:
            $city = false;
        endif;

        View::share('city', $city);

        $slugBits = explode('+', $slug);

        $featuredPerformers = Performer::with(array('featured' => function ($query) {
            $query->where('side', 1);
        }
        ))->take(8)->get();
        View::share('featuredPerformers', $featuredPerformers);

        $bitCnt = count($slugBits);
        switch ($bitCnt):
            case 2: //pv or pc
                $location = Location::where('slug', $slugBits[1])->first();
                if ($location): //this is performer city
                    $performerSlug = $slugBits[0];
                    $performer = Performer::with('images')
                        ->where('slug', $performerSlug)->first();

                    if (!$performer) App::abort('404');
                    $pid = $performer->id;
                    $concerts = Concert::where('date', '>', date('Y-m-d H:i:s'))
                        ->where('location_id', $location->id)
                        ->whereHas('performers', function ($query) use ($pid) {
                            $query->where('performers.id', $pid);
                        }
                        )->with('venue')
                        ->with('location')
                        ->orderBy('date', 'asc')->take(2)->get();
                    if (!$concerts->count()) App::abort('404');
                    View::share('concerts', $concerts);

                    $nearBy = DB::table('location_nearby')->where('center_slug', $location->slug)->where('distance', '<', 100)->take(60)->orderBy('distance', 'ASC')->get();
                    $slugs = [];
                    $slugs[] = 0; //always have something otherwise chocke line122

                    foreach ($nearBy as $nb):
                        $slugs[] = $nb->slug;
                    endforeach;
                    $nearByLocations = Location::where('event_count', '>', '0')
                        ->where('id', '<>', $location->id)
                        ->whereIn('slug', $slugs)
                        ->orderBy('event_count', 'desc')
                        ->remember(360)
                        ->get();
                    View::share('nearBy', $nearByLocations);

                    $performerDetails = PerformerDetails::where("performer_id", $performer->id)->get()->toArray();//first(['performer_id'=> $performer->id]);
                    // DebugHelper::pdd($performerDetails, false);

                    $performerSets = PerformerSets::where(["performer_id" => $performer->id])->first();//->toArray();//first(['performer_id'=> $performer->id]);
                    if ($performerSets) $performerSets = $performerSets->toArray();
                    $prevTours = [];
                    if ($performerSets):
                        foreach ($performerSets['sets'] as $tour):
                            if ($tour['state'] == $location->state):
                                $prevTours[date('U', strtotime($tour['date']))] = [
                                    'name' => $tour['tour'],
                                    'date' => $tour['date'],
                                    'venue' => $tour['venue'],
                                    'city' => $tour['city']
                                ];
                            endif;
                        endforeach;
                    endif;
                    asort($prevTours);
                    View::share('prevTours', $prevTours);
                    $videos = false;
                    if (!$prevTours):
                        //GET YOUTUBE
                        $ytKey = Config::get('google.youtube.key');
                        // var_dump($ytConfig);
                        $youtube = new Madcoda\Youtube(array('key' => $ytKey));
                        // Set Default Parameters
                        $band = ($performer->type == "Group") ? "band" : "";
                        $params = array(
                            'q' => "\"" . $performer->name . "\" " . $location->city . " concert|live -cover",
                            'type' => 'video',
                            'part' => 'id, snippet',
                            'maxResults' => 6,
                            'order' => 'viewCount'
                        );
                        // Make Intial Call. With second argument to reveal page info such as page tokens.
                        $videos = $youtube->searchAdvanced($params, true);
                    endif;
                    View::share('videos', $videos);

                    View::share('performer', $performer);
                    View::share('location', $location);
                    $breadcrumbs = [
                        ['link' => '/concerts', 'title' => 'All Artists'],
                        ['link' => '/concerts/' . $performer->slug, 'title' => $performer->name],
                        ['link' => '#', 'title' => $performer->name . " " . $location->city],
                    ];
                    View::share('breadcrumbs', $breadcrumbs);
                    $metadata = [
                        'meta' => [
                            'description' => "Get {$performer->name} {$location->city} tickets, concert information, driving direction, and more.",
                            'keywords' => "site, tickets, concert info, opening acts, guest performers, directions, date and time, questions and comments.",
                            'robots' => "noindex, nofollow",
                            'title' => "Past {$performer->name} {$location->city} Concerts",
                            'canonical' => url('concerts/' . $slug),
                        ],
                        'og' => [
                            'description' => "Get {$performer->name} {$location->city} tickets, concert information, driving direction, and more.",
                            'keywords' => "site, tickets, concert info, opening acts, guest performers, directions, date and time, questions and comments.",
                            'robots' => "index, follow",
                            'title' => "Past {$performer->name} {$location->city} Concerts",
                        ]
                    ];
                    View::share('metadata', $metadata);
                    $this->layout->customjs = View::make("frontend.concerts.performer-city-customjs");
                    $this->layout->tagline = View::make("frontend.concerts.performer-city-tagline");
                    $this->layout->content = View::make("frontend.concerts.performer-city-content");
                else: //performer venue
                    $concerts = Concert::where('slug', $slug)
                        ->where('date', '>', date('Y-m-d', strtotime('-24 hours')))
                        ->with('performers')
                        ->has('performers')
                        ->with('performers.images')
                        ->with('venue')
                        ->with('venue.tnVenue')
                        ->with('location')
                        ->orderBy('date', 'asc')
                        // ->remember(360)
                        ->get();
                    if (!$concerts->count()):
                        $fullSlug = "/concerts/{$slug}";
                        $redHack = DB::table('redirect_hack')->where('url', $fullSlug)->first();
                        if ($redHack) return Redirect::to($redHack->to_url, '301');

                        App::abort(404);
                    endif;
                    // DebugHelper::pdd($concerts, 1);
                    $lastConcert = $concerts->last();
                    $venue = $lastConcert->venue;
                    $tnVenue = $venue->tnVenue;
                    $guests = [];
                    $exclude = [];
                    foreach ($concerts as $concert):
                        foreach ($concert->performers as $p):
                            $guests[$p->id] = $p;
                            $exclude[] = $p->id;
                        endforeach;
                    endforeach;
                    $tickets = [];
                    foreach ($concerts as $pc):
                        foreach ($pc->tickets as $ticket):
                            $tickets[] = $ticket->toArray();
                        endforeach;
                    endforeach;
                    $concertsAfter = Concert::where('venue_id', $venue->id)
                        ->where('date', '>', date('Y-m-d H:i:s', strtotime($lastConcert->date)))
                        ->with('performers')
                        ->with('tnConcert')
                        ->whereHas('performers', function ($query) use ($exclude) {
                            $query->whereNotIn('performers.id', $exclude);
                        })
                        ->take(30)
                        ->orderBy('date', 'asc')
                        // ->remember(360)
                        ->get();

                    $cityConcerts = Concert::where('location_id', $lastConcert->location_id)
                        ->where('date', '>', date('Y-m-d H:i:s'))
                        ->with('performers')
                        ->with('venue')
                        ->with('performers.images')
                        ->with('tnConcert')
                        ->whereHas('performers', function ($query) use ($exclude) {
                            $query->whereNotIn('performers.id', $exclude);
                        })
                        ->take(8)
                        // ->orderByRaw('RAND()')
                        ->orderBy('date', 'asc')
                        // ->remember(360)
                        ->get();
                    // $firstConcert = $concert->first();

                    $performer = $concerts->first()->performers->first();
                    View::share('performer', $performer);
                    View::share('concerts', $concerts);
                    View::share('venue', $venue);
                    View::share('tnVenue', $tnVenue);
                    View::share('concertsAfter', $concertsAfter);
                    View::share('cityConcerts', $cityConcerts);
                    $data = [];
                    // Log::info(print_r($performer, true));
                    $data['performer_id'] = $performer->id;
                    $data['venue_id'] = $venue->id;
                    $data['slug'] = $slug;
                    $pvText = Spinner::getText('pv', $data);
                    View::share('pvText', $pvText);
                    $pvFaq = Spinner::perfromer_venue_qa($performer->id, $venue->id);
                    View::share('pvFaq', $pvFaq->text);

                    $breadcrumbs = [
                        ['link' => '/concerts', 'title' => 'All Artists'],
                        // ['link' => '/concerts/'.$performer->slug, 'title' => $performer->name],
                        ['link' => '#', 'title' => $concert->name . " " . $venue->name . " " . $lastConcert->location->city],
                    ];
                    View::share('breadcrumbs', $breadcrumbs);
                    $metadata = [
                        'meta' => [
                            'description' => "Get {$concert->name} {$venue->name} {$lastConcert->location->city} tickets, concert information, driving direction, and more.",
                            'keywords' => "site, tickets, concert info, opening acts, guest performers, directions, date and time, questions and comments.",
                            'robots' => "index, follow",
                            'title' => "{$concert->name} {$venue->name} {$lastConcert->location->city} Tickets",
                            'canonical' => url('concerts/' . $slug),
                        ],
                        'og' => [
                            'description' => "Get {$concert->name} {$venue->name} {$lastConcert->location->city} tickets, concert information, driving direction, and more.",
                            'keywords' => "site, tickets, concert info, opening acts, guest performers, directions, date and time, questions and comments.",
                            'robots' => "index, follow",
                            'title' => "{$concert->name} {$venue->name} {$lastConcert->location->city} Tickets",
                        ]
                    ];
                    View::share('metadata', $metadata);
                    $this->layout->tagline = View::make("frontend.concerts.performer-venue-tagline");
                    $this->layout->content = View::make("frontend.concerts.performer-venue-content");
                    $this->layout->customjs = View::make("frontend.concerts.performer-venue-customjs");
                endif;
                break;
            case 1: //performer
                $performerSlug = $slugBits[0];
                $performer = Performer::with('upcoming_concerts')
                    ->with('upcoming_concerts.tnConcert')
                    ->with('upcoming_concerts.venue')
                    ->with('upcoming_concerts.location')
                    ->with('upcoming_concerts.venue.tnVenue')
                    ->with('concerts')
                    ->with('concerts.location')
                    ->with('concerts.genres')
                    ->with('images')
                    ->where('slug', $performerSlug)->remember(360)->first();

                if (!$performer) App::abort(404); //throw 404

                if ($performer->upcoming_concerts->count() < 1) Response::make($this->layout, 404);
                $genre = VarsHelper::get_top_genre($performer->concerts);
                $albums = $performer->albums()->with('images')->where('release_date', '<>', '0000-00-00 00:00:00')->take(8)->orderBy('release_date', 'DESC')->orderBy('play_count', 'DESC')->get();

                $performerDetails = PerformerDetails::where("performer_id", $performer->id)->get()->toArray();//first(['performer_id'=> $performer->id]);

                $similarPerformers = (isset($performerDetails[0]['similar'])) ? $performerDetails[0]['similar'] : false;

                if ($similarPerformers):
                    $similarPerformers = VarsHelper::get_similar_performers($similarPerformers);
                endif;

                $performerSets = PerformerSets::where(["performer_id" => $performer->id])->first();//->toArray();//first(['performer_id'=> $performer->id]);

                if ($performerSets) $performerSets = $performerSets->toArray();

                $tourSongs = VarsHelper::get_setlist_stats($performerSets);
                $pastGuests = VarsHelper::get_past_guests($performerSets);
                $upcomingGuests = $performer->tour_guests();

                if ($tourSongs):
                    $tourSongs = array_slice($tourSongs, 0, 10);
                endif;

                $tracks = new Track;
                $data = [
                    'upcoming_guests' => $upcomingGuests,
                    'tour_songs' => $tourSongs,
                    'similar_performers' => $similarPerformers,
                    'performer_guests' => $pastGuests,
                    'most_played_songs' => $tracks->getMostPlayed(7),
                    'currentLocation' => $currentLocation,
                ];

                $breadcrumbs = [
                    ['link' => '/concerts', 'title' => 'All Artists'],
                    ['link' => '/concerts/' . $performer->slug, 'title' => $performer->name],
                ];
                View::share('breadcrumbs', $breadcrumbs);

                $spinData = [];
                $spinData['performer_id'] = $performer->id;
                $bio = Spinner::getText('pb', $spinData);
                $tourText = Spinner::getText('pt', $spinData);
                $discographyText = Spinner::getText('pd', $spinData);

                View::share('bio', $bio);
                View::share('tourText', $tourText);
                View::share('discographyText', $discographyText);

                $qAndA = Spinner::performer_qa($performer->id);
                View::share('qa', $qAndA->text);
                View::share('performer', $performer);
                View::share('albums', $albums);
                View::share('genre', $genre['genre']);

                $metadata = [
                    'meta' => [
                        'description' => "View {$performer->name} tour dates, concert schedule, top tour songs, concert guests, and discography",
                        'keywords' => "site, {$performer->name} tour dates, {$performer->name} concerts, tour songs, tour guests, {$performer->name} discography",
                        'robots' => "index, follow",
                        'title' => "{$performer->name} Tour Dates & Info " . date('Y'),
                        'canonical' => url('concerts/' . $slug),
                    ],
                    'og' => [
                        'description' => "View {$performer->name} tour dates, concert schedule, top tour songs, concert guests, and discography",
                        'keywords' => "site, {$performer->name} tour dates, {$performer->name} concerts, tour songs, tour guests, {$performer->name} discography",
                        'robots' => "index, follow",
                        'title' => "{$performer->name} Tour dates & Info " . date('Y'),
                    ]
                ];
                View::share('metadata', $metadata);
                $this->layout->tagline = View::make('frontend.concerts.performer-tagline');
                $this->layout->content = View::make('frontend.concerts.performer-content', $data);
                if ($performer->upcoming_concerts->count() < 1) return Response::make($this->layout, 404);
                break;
            default: //nobody knows what is in there, probably malformed URL
                App::abort(404);
                break;
        endswitch;
    }

}
