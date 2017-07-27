<?php

use Purekid\Mongodm\Model;

//http://bundles.laravel.com/bundle/mongodm

/**
 *
 */
class GeoLocation extends Eloquent
{

    static $collection = "";
    /** use specific config section **/
    public static $config = 'development';
    public $timestamps = false;
    protected $table = "geo_locations";
    protected $fillable = array(
        'country', 'state', 'state_full', 'city', 'slug', 'zip', 'lat', 'long', 'phone_code', 'metro_code', 'county', 'time_zone', 'population', 'location_id'
    );

    public static function get_by_slug($slug = null)
    {

        $parent = GeoLocation::where('slug', '=', $slug)->first();
        // $cities = GeoLocation::where('slug','=',$slug)->get();
        $total = 0;
        $children = [];

        $data = [
            'slug' => $parent->slug,
            'city' => $parent->city,
            'state_full' => $parent->state_full,
            'state' => $parent->state,
            'country' => $parent->country,
            'children' => $children,
            'total' => $total,
        ];
        return $data;
    }

    public static function get_groupped($groupby = 'state')
    {
        switch ($groupby):
            case 'state':
                $cities = DB::table('geo_locations')->get()->take(100);
                $country = 'US';
                $groupped = [];
                foreach ($cities as $city):
                    $groupped[$country][$city->state_full][$city->slug] = $city->city;
                endforeach;

                break;
            case defaut;

                break;
        endswitch;
        return $groupped;
    }

    public function location()
    {
        return $this->belongsTo('Location');
    }

    public function concerts()
    {
        return $this->hasMany('Concert');
    }

    public function venues()
    {
        return $this->belongsTo('Venue');
    }

    public function get_states($country = 'US')
    {
        return $this->groupBy('state')->where('country', $country)->get();
    }

    public function get_cities($state = null)
    {
        if ($state):
            return $this->groupBy('slug')->where('state', $state)->get();
        else:
            return false;
        endif;
    }

    //DO NOT USE THIS FUNCTION!!! it's garbage

    public function get_cities_with_events($state = null)
    {
        if ($state):
            return $this->groupBy('slug')->where('state', $state)->get();
        else:
            return false;
        endif;
    }


}