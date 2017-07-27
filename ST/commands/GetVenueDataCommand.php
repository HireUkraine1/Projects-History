<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class GetVenueDataCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-venues';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Gets all available venues data and updates as needed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //get performers
        //		$this->populatePerformers();
        $this->getTnVenues();
    }

    public function getTnVenues($repull = false)
    {
        if ($repull):
            Venue::truncate();
            TnVenue::truncate();
        endif;
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $start = date('U');
        //let's do venues
        // Log::info("_____________________________________________________________________________");
        // Log::info("Starting getting all venues...from TN ");
        //init ticket network instance
        $tn = new TicketNetwork\Api\TicketNetwork;

        $params = array();

        $venues = $tn->run('GetVenue', $params)->GetVenueResult->Venue;
        $countries = ['United States of America', 'Canada'];
//		$countries = ['United States of America'];
        $northAmerican = [];
        foreach ($venues as $v1):
            if (in_array($v1->Country, $countries)):
                array_push($northAmerican, $v1);
            endif;
        endforeach;

        $i = 0;
        $changes = 0;
        $new = 0;
        $errors = 0;

        foreach ($northAmerican as $v):
            $i++;
            $vName = StringHelper::clean_venue_name($v->Name);
            $vPartialSlug = StringHelper::create_slug($vName);
            $zip = StringHelper::fix_zip($v->ZipCode);
            $country = ($v->Country == "Canada") ? "CA" : "US";
            $short_state = StringHelper::lookup_state($v->StateProvince, $country, true);

            if ($tnVenue = TnVenue::find($v->ID)): //venue exists in our db by slug

                $venue = $tnVenue->venue()->first();

                if ($vName != $venue->name): //venue name has changed, moved, etc
                    echo "\nVENUE NAME CHANGED: Changing: \t $venue->name \t -> \t $vName";
                    $partGeoSlug = $venue->geoLocation()->first()->slug;
                    //update venue changes
                    $partVenueSlug = StringHelper::create_slug($vName);
                    $venue->name = $vName;
                    $venue->slug = StringHelper::create_slug($partVenueSlug . " " . $partGeoSlug);
                    $venue->save();

                    //todo: setup redirect pointer
                    // $changes++;
                endif;

            else: //this is a new venue
                // $new++;
                $geo = GeoLocation::where('zip', '=', $zip)->first();
                //create geo location if not found in table..
                if (!isset($geo->id)):
                    $country = ($v->Country == "Canada") ? "CA" : "US";
                    $geo = new GeoLocation;
                    $geo->city = $v->City;
                    $geo->country = $country;
                    $geo->state = $short_state;
                    $geo->slug = StringHelper::create_slug($v->City . " " . $short_state);
                    $geo->state_full = $v->StateProvince;
                    $geo->zip = $zip;
                    $location = Location::where('slug', $geo->slug)->first();
                    if (!$location):
                        $location = new Location;
                        $location->city = $v->City;
                        $location->state = $short_state;
                        $location->state_full = $v->StateProvince;
                        $location->slug = $geo->slug;
                        $location->save();
                    endif;
                    $geo->location_id = $location->id;
                    $geo->save();

                endif;

                $venue = new Venue;
                $venue->geo_location_id = $geo->id;
                $venue->name = $vName;
                $venue->slug = StringHelper::create_slug($vPartialSlug . " " . $geo->slug);
                $venue->save();

                try {
                    $tnData = [
                        'capacity' => $v->Capacity, 'child_rules' => $v->ChildRules, 'city' => $v->City, //backup city and location data in case...
                        'country' => $country,
                        'directions' => $v->Directions,
                        'id' => $v->ID,
                        'notes' => $v->Notes,
                        'number_of_configurations' => $v->NumberOfConfigurations,
                        'parking' => $v->Parking,
                        'phone' => '',
                        'public_transportation' => $v->PublicTransportation,
                        'rules' => $v->Rules,
                        'state' => ($geo->state) ? $geo->state : $short_state,
                        'street_1' => $v->Street1,
                        'street_2' => $v->Street2,
                        'url' => $v->URL,
                        'willcall' => $v->WillCall,
                        'zip' => StringHelper::fix_zip($v->ZipCode),
                        'venue_id' => $venue->id
                    ];
                    $tnVenue = TnVenue::insert($tnData);
                } catch (Exception $e) {
                    echo "\nNOT INSERTED TN ID: $v->ID ($vName)" . $e->getMessage();
                }


            endif;
        endforeach;
        $time = (date('U') - $start) / 60;

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(//			array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}