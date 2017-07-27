<?php


class GeoController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function city_admin($city = null)
    {
        $this->layout->cssjs = View::make('admin.city.cssjs');
        $geo = new GeoLocation;
        $states = []; // $geo->get_states();
        $featuredCities = DB::table('featured_locations')->orderBy('city', 'ASC')->get();

        if ($city):
            $location = Location::where('slug', $city)->first();
            $concertData = $location->concerts();
            $cityData = GeoLocation::get_by_slug($city);
            $this->layout->content = View::make('admin.city.single', array(
                'cityData' => $cityData,
                'concertData' => $concertData,
                'isFeatured' => DB::table('featured_locations')->where('real_slug', '=', $cityData['slug'])->count()
            ));
        else:
            $this->layout->content = View::make('admin.city.all', array('featured' => $featuredCities));
        endif;
        $this->layout->sidebar = View::make('admin.city.sidebar', array('states' => $states));
        $this->layout->mainmenu = View::make('admin.mainmenu');
    }

    public function metro_cluster($slug = null)
    {
        $location = Location::where('slug', $slug)->with('burbs')->with('burbs.location')->first();
        if ($child = Burb::where('location_id', $location->id)->with('metro')->first()):
            Session::flash('message', "<h4>The city <strong>{$location->city}, {$location->state}</strong> is part of <strong>{$child->metro->city}, {$child->metro->state}</strong> metro</h4>You were redirected");
            return Redirect::to("/saleadminpanel/metro/" . $child->metro->slug);
        endif;
        $metros = Burb::with('location')->with('metro')->orderBy('parent_location_id', 'DESC')->get();
        $nearby = DB::table('location_nearby')->where('center_slug', $location->slug)->where('distance', '<', 20)->orderBy('distance', 'ASC')->get();
        View::share('nearby', $nearby);
        View::share('location', $location);
        $currentMetro = [];
        foreach ($location->burbs as $b):
            $currentMetro[] = $b->location_id;
        endforeach;
        View::share('currentMetro', $currentMetro);
        View::share('metros', $metros);
        $this->layout->content = View::make('admin.city.cluster');
        // $this->layout->sidebar = View::make('admin.city.sidebar-nearby');
        $this->layout->customjs = View::make('admin.city.nearbyjs');
    }
}