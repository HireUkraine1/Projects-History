<?php

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class TempCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:temp';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'data fuckin app';

    protected $lfm;

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
        ini_set("memory_limit", "-1");

        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $this->fixCf();

    }

    public function fixCf()
    {
        $tn = new TicketNetwork\Api\TicketNetwork('ticketnetwork.tnProdData');

        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');

        $params = array(
            'websiteConfigID' => $loadedConfig,
            'beginDate' => date('c'),
            'parentCategoryID' => 2,
            'whereClause' => 'CountryID = 217 OR CountryID = 38'
        );
        $events = $tn->run('GetEvents', $params)->GetEventsResult->Event;

        foreach ($events as $e):
            $ltn = TnConcert::where('id', $e->ID)->first();
            if ($ltn):
                $ltn->venue_config_id = $e->VenueConfigurationID;
                $ltn->save();
                echo "\n\r SAVED: " . $e->Name;
            else:
                echo "\n\r *************SKIPPED: " . $e->Name;
            endif;
        endforeach;
    }

    public function fixZip()
    {
        $tnVenues = TnVenue::all();
        $count = $tnVenues->count();
        echo "\n\r" . $count;
        $i = 0;
        foreach ($tnVenues as $v):
            $i++;
            $zip = $v->zip;
            echo "\n\r{$i} of {$count} {$zip} -> ";
            $v->zip = StringHelper::fix_zip($zip);
            $v->save();
            echo $v->zip;
        endforeach;
    }

    public function fixNullState()
    {
        $geos = GeoLocation::where('state')->get();
        foreach ($geos as $geo):
            $state = StringHelper::lookup_state($geo->state_full, $geo->country, true);
            if ($state):
                $geo->state = $state;
                $geo->save();
                echo ".";
            else:
                echo "\n\r{$geo->state_full} in {$geo->country}:  ";
                echo " NOTHING FOUND!!! ";
            endif;

        endforeach;
    }

    public function getSeatingCharts()
    {
        $allUpcoming = Concert::where('date', '>', date('Y-m-d H:i:s'))->with('tnConcert')->get();
        foreach ($allUpcoming as $concert):
            $tnConcert = $concert->tnConcert;
            $pos = strpos($tnConcert->map_url, "https://s3.amazonaws.com");
            if ($pos === false):
                $remote = $tnConcert->interactive_map_url;
                echo "\n\r{$remote} => ";
                $fileParts = explode('/', $remote);
                $filename = end($fileParts);
                try {
                    copy($remote, "/tmp/{$filename}");
                    echo " COPIED => ";
                    $key = "seats/{$filename}";
                    $type = "application/x-shockwave-flash";
                    S3Helper::put("/tmp/{$filename}", "" . $key, $type);
                    $fullpath = S3Helper::get_path($key);
                    unlink("/tmp/{$filename}");
                    echo $fullpath;
                    $tnConcert->interactive_map_url = $fullpath;
                    $tnConcert->save();
                    echo " SAVED!";
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "FAILED";
                }
            else:
                echo "\n\rSKIPPED $tnConcert->interactive_map_url";
            endif;
        endforeach;
    }

    public function getVenueMaps()
    {
        $allUpcoming = Concert::where('date', '>', date('Y-m-d H:i:s'))->with('tnConcert')->with('venue')->get();
        foreach ($allUpcoming as $concert):
            $tnConcert = $concert->tnConcert;
            $pos = strpos($tnConcert->map_url, "https://s3.amazonaws.com");
            if ($pos === false):
                echo "\n\r{$tnConcert->map_url}";
                $venue = $concert->venue;
                $vName = $venue->slug;
                $name = StringHelper::create_slug($concert->name . "-seating-chart");
                $s3path = S3Helper::putRemote($tnConcert->map_url, "venues/{$vName}/", $name);
                $tnConcert->map_url = $s3path;
                $tnConcert->save();
                echo "\n\r{$tnConcert->map_url}";
                echo "\n\r";
            endif;
        endforeach;
    }

    public function pullLFMP()
    {
        $performers = Performer::with('images')->get();
        $i = 0;
        mail("email@site.com", "Started Performer", $performers->count());
        foreach ($performers as $performer):
            echo "\n\r$performer->name > ";
            $i++;
            foreach ($performer->images as $img):
                $pos = strpos($img->path, "https://s3.amazonaws.com");
                if ($pos === false && $img->pivot->type == 'lfm'):
                    $name = StringHelper::create_slug($performer->id . " " . $performer->slug . " " . $img->pivot->size);
                    $s3path = S3Helper::putRemote($img->path, "performers/", $name);
                    $img->path = $s3path;
                    $img->save();
                    echo "|{$img->pivot->size}";
                endif;
                # code...
            endforeach;
        endforeach;
        echo "\n\r-------------FINISHED--------";
        mail("email@site.com", "ALL DONE", $i);
    }

    public function pullLFMA()
    {
        //104,021
        $skip = 75000;
        while ($skip < 105001):
            $i = 0;
            $albums = Album::with('images')->take(5000)->skip($skip)->get();
            $grabbed = $skip + 5000;
            mail("email@site.com", "Took {$skip} - {$grabbed} albums", "OK");
            foreach ($albums as $album):
                $i++;
                echo "\n\r$album->title > ";
                foreach ($album->images as $img):
                    try {
                        $pos = strpos($img->path, "https://s3.amazonaws.com");
                        if ($pos === false && $img->pivot->type == 'lfm_album' && ($img->pivot->size == 'large' || $img->pivot->size == 'extralarge')):
                            $name = StringHelper::create_slug($album->id . " " . StringHelper::create_slug($album->title) . " " . $img->pivot->size);
                            $s3path = S3Helper::putRemote($img->path, "albums/", $name);
                            if ($s3path):
                                $img->path = $s3path;
                                $img->save();
                                echo $img->pivot->size . "\t";
                            else:
                                echo "SKIPPED:{$img->pivot->size}";
                            endif;

                        endif;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                endforeach;
                if (!$i % 500) {
                    echo "Sleep on $i \n\r";
                    sleep(5);
                }
            endforeach;
            $skip += 5000;
        endwhile;
        echo "\n\r-------------FINISHED--------";
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