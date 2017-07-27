<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class GetGeoCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:populate-geo';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Shitload of cities and zipcodes, canada is large...beware..';

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
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();

    }

    public function fu()
    {
        mail("email@site.com", "add blocks started", "go on...gooutsid");

        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $filename = "/var/www/blocks2.csv";
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        $c = 0;
        $i = 0;
        $f = 0;
        $header = NULL;
        $copy = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$copy) {
                    $copy = $row;
                } elseif (!$header) {
                    $header = $row;
                } else {
                    $c++;
                    // var_dump($row);
                    $data = array_combine($header, $row);
                    $start = $data['startIpNum'];
                    $end = $data['endIpNum'];
                    $id = $data['locId'];
                    DB::table('tmp_ip_loc')->insert(array('end' => $end, 'start' => $start, 'locid' => $id));
                    echo "Inserted $start - $end";

                }
                // if($i == 1000) die();
            }
            fclose($handle);
        }
        mail("email@site.com", "add blocks finished", "processed $c with $i items inserted and $f failed.");

    }

    public function create_locations($rerun = true)
    {
        $start = date('U');
        $files = ["US" => "/var/www/us.csv", "CA" => "/var/www/ca.csv"];
        $header = NULL;
        $i = 0;
        $u = 0;
        $c = 0;
        if ($rerun):
            GeoLocation::truncate();
            Location::truncate();
            echo "\n\rTruncated GEO and Locations";
        endif;
        try {
            if (($handle = fopen($files['US'], 'r')) !== FALSE):
                echo "Doing US\n\r";
                while (($row = fgetcsv($handle, 0, ',')) !== FALSE):
                    if (!$header):
                        $header = $row;
                    else:
                        //do locations
                        $slugy = StringHelper::create_slug($row[2] . " " . $row[5]);
                        $fstate = StringHelper::lookup_state($row[5], $row[12]);
                        $location = Location::firstOrCreate(['slug' => $slugy]);
                        $location->city = $row[2];
                        $location->state = $row[5];
                        $location->state_full = $fstate;
                        $location->country = 'US';
                        $location->save();
                        $i++;
                        $u++;
                        echo ($i % 500) ? "" : "\n\r\t INSERTED $i RUNTIME: " . (date('U') - $start) / 60 . " minutes ..";
                    endif;
                endwhile;
            endif;
            if (($handle = fopen($files['CA'], 'r')) !== FALSE):
                echo "\nDoing CA\n\r";

                while (($row = fgetcsv($handle, 0, ',')) !== FALSE):
                    $slugca = StringHelper::create_slug($row[3] . " " . $row[4]);
                    $fprovince = StringHelper::lookup_state($row[4], 'CA');

                    $location = Location::firstOrCreate(['slug' => $slugca]);
                    $location->city = $row[3];
                    $location->state = $row[4];
                    $location->state_full = $fprovince;
                    $location->country = 'CA';
                    $location->save();
                    $i++;
                    $c++;
                    echo ($i % 50000) ? "" : "\n\r\t INSERTED $c TOTAL OF $i RUNTIME: " . (date('U') - $start) / 60 . " minutes ..";
                endwhile;
            endif;
        } catch (Exception $e) {
            Log::info("_____________________________________________________________________________");
            Log::info($e->getTraceAsString());
            Log::info("_____________________________________________________________________________");
            echo "\n\r ERROR Occurred: " . $e->getMessage();
            // var_dump($item);
        }

        echo "\n\r\t INSERTED $i CANADA $c and  US $u ADDING OTHER MISSING LOCS ";
        $tmps = DB::table('tmp_geo')->get();
        foreach ($tmps as $item):
            $slug = StringHelper::create_slug($item->city . " " . $item->state);
            $location = Location::where('slug', $slug)->first();
            if (!$location):
                $location = new Location;
                $location->slug = $slug;
                $location->city = ucwords(strtolower($item->city));
                $location->state = $item->state;
                $location->state_full = $item->state_full;
                $location->country = $item->country;
                $location->save();
                echo "\n\r Created $item->zip in $item->city - $item->state ";
            endif;
        endforeach;
    }

    public function update_missing_geos()
    {
        try {
            $tmps = DB::table('tmp_geo')->get();
            $i = 1;
            foreach ($tmps as $item):
                if ($item->zip):
                    $geo = GeoLocation::where('zip', $item->zip);
                    if ($geo->count() == 0):
                        $slug = StringHelper::create_slug($item->city . " " . $item->state);
                        $location = Location::where('slug', $slug)->first();
                        if ($location):
                            $newGeo = new GeoLocation;
                            $newGeo->city = ucwords(strtolower($item->city));
                            $newGeo->country = $item->country;
                            $newGeo->county = '';
                            $newGeo->lat = $item->lat;
                            $newGeo->long = $item->long;
                            $newGeo->metro_code = $item->metro;
                            $newGeo->phone_code = $item->area;
                            $newGeo->state_full = $item->state_full;
                            $newGeo->zip = $item->zip;
                            $newGeo->slug = $slug;
                            $newGeo->location_id = $location->id;
                            $newGeo->save();
                            echo "\n\r Updated $item->zip in $item->city - $item->state ";
                        else:
                            echo "\n\r NO LOCATION FOR $item->zip in $item->city - $item->state ";
                        endif;
                        $i++;
                    endif;
                endif;
            endforeach;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function populate_tmp()
    {
        // die();
        $i = 0;
        mail("email@site.com", "poptemp started", "go on...gooutsid");
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $filename = "/var/www/locs.csv";
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        DB::table('tmp_geo')->truncate();
        $header = NULL;
        $copy = NULL;
        $data = array();
        // $this->command->info('Start open csv file...');
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$copy) {
                    $copy = $row;
                } elseif (!$header) {
                    $header = $row;
                } else {
                    // var_dump($row);
                    $data = array_combine($header, $row);
                    // var_dump($data);
                    $slug = StringHelper::create_slug($data['city'] . " " . $data['region']);
                    if ($data['country'] == 'US' || $data['country'] == 'CA'):
                        DB::table('tmp_geo')->insert(
                            array(
                                'id' => $data['locId'],
                                'city' => $data['city'],
                                'country' => $data['country'],
                                'state' => $data['region'],
                                'slug' => $slug,
                                'zip' => strtoupper($data['postalCode']),
                                'lat' => $data['latitude'],
                                'long' => $data['longitude'],
                                'metro' => $data['metroCode'],
                                'area' => $data['areaCode'],
                                'state_full' => StringHelper::lookup_state($data['region'], $data['country'])
                            )
                        );
                        $i++;
                        echo "\n\r $slug";
                    endif;

                }
                // if($i == 1000) die();
            }
            fclose($handle);
        }
        mail("email@site.com", "poptemp finished", "$i items ");

    }

    public function add_blocks()
    {
        mail("email@site.com", "add blocks started", "go on...gooutsid");

        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $filename = "/var/www/blocks2.csv";
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        $c = 0;
        $i = 0;
        $f = 0;
        $header = NULL;
        $copy = NULL;
        $data = array();
        // $this->command->info('Start open csv file...');
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (!$copy) {
                    $copy = $row;
                } elseif (!$header) {
                    $header = $row;
                } else {
                    $c++;
                    // var_dump($row);
                    $data = array_combine($header, $row);
                    $start = $data['startIpNum'];
                    $end = $data['endIpNum'];
                    $tmpgeo = DB::table('tmp_geo')->find($data['locId']);
                    // echo '.';
                    if ($tmpgeo):
                        if ($tmpgeo->country == 'US' || $tmpgeo->country == 'CA'):
                            echo $tmpgeo->country;
                            $slugit = StringHelper::create_slug($tmpgeo->city . " " . $tmpgeo->state);
                            $location = Location::where('slug', $slugit)->first();
                            if ($location):
                                DB::table('ip_locations')->insert(array('end_ip' => $end, 'start_ip' => $start, 'location_id' => $location->id));
                                echo "\n\rInserted: $slugit ($start - $end)";
                                $i++;
                            else:
                                $f++;
                                echo "\n\r\t\tFAILED: $slugit ($start - $end)";
                            endif;
                        endif;
                    endif;
                }
            }
            fclose($handle);
        }
        mail("email@site.com", "add blocks finished", "processed $c with $i items inserted and $f failed.");

    }

    public function create_geo($rerun = true)
    {
        $start = date('U');
        mail("email@site.com", "POPULATE GEO STARTED", "Started at " . date('F j Y, H:i:s'));
        if ($rerun)  //realistically this should only run ONCE ...cities, zipcodes, and geo data NEVER changes..
        {
            GeoLocation::truncate();
            echo "\r\n Truncated Geo and Location";
        }
        //those two lines are essential.. will save from getting insanely huge logs
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $i = 0;
        $u = 0;
        $c = 0;
        $files = ["US" => "/var/www/us.csv", "CA" => "/var/www/ca.csv"];
        $header = NULL;
        try {

            if (($handle = fopen($files['CA'], 'r')) !== FALSE) {
                echo "\nDoing CA\n\r";
                while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
                    $slugca = StringHelper::create_slug($row[3] . " " . $row[4]);
                    $fprovince = StringHelper::lookup_state($row[4], 'CA');
                    $location = Location::where('slug', $slugca)->first();
                    $item = [];
                    $item['zip'] = $row[0];
                    $item['long'] = $row[2];
                    $item['lat'] = $row[1];
                    $item['city'] = $row[3];
                    $item['state'] = $row[4];
                    $item['state_full'] = $fprovince;
                    $item['country'] = 'CA';
                    $item['slug'] = $slugca;
                    $item['location_id'] = $location->id;
                    GeoLocation::create($item);
                    $i++;
                    $c++;
                    echo ($i % 50000) ? "" : "\n\r\t INSERTED $c TOTAL OF $i RUNTIME: " . (date('U') - $start) / 60 . " minutes ..";
                    //echo "\n\r CANADA: " . $item['city'] . " " . $item['state'];
                }
            }
        } catch (Exception $e) {
            Log::info("_____________________________________________________________________________");
            Log::info($e->getTraceAsString());
            Log::info("_____________________________________________________________________________");
            echo "\n\r ERROR Occurred: " . $e->getMessage();
            var_dump($item);
        }
        echo "\n\r\t INSERTED $i CANADA $c and  US $u ";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array( //			array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array( //			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }
}
