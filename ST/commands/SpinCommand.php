<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SpinCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:spinner';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'text spinner cron';

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
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $this->spin_text();
    }

    /**
     * [spin_text description]
     * @param  boolean $force [description]
     * @param  boolean $skipCustom [description]
     * @return [type]              [description]
     */
    public function spin_text($force = false, $skipCustom = true)
    {
        // city pages
        $allLocations = Location::all();
        foreach ($allLocations as $location):
            try {
                $currentText = DB::table('city_text')->where('location_id', $location->id)->first();
                echo "\n\r Location: " . $location->city . ", " . $location->state . "......";
                if ($currentText):

                    if (($force || $currentText->expire < date('Y-m-d h:i:s')) && !$currentText->custom):
                        $textData = Spinner::city_text($location->id);
                        $updateArray = ['text' => $textData->text, 'expire' => $textData->expire];
                        DB::table('city_text')->where('id', $currentText->id)->update($updateArray);
                    // echo "\n\r Updated loc: $location->city";
                    // echo " UPDATED";
                    else:
                        // echo " SKIPPED";
                    endif;
                else:
                    $textData = Spinner::city_text($location->id);
                    $insertArray = ['location_id' => $location->id, 'text' => $textData->text, 'custom' => 0, 'expire' => $textData->expire];
                    DB::table('city_text')->insert($insertArray);

                endif;
            } catch (Exception $e) {
                echo "\n\r" . $e->getMessage();
                echo $e->getTraceAsString();
            }
        endforeach;

        //performer pages
        $performers = Performer::all();
        foreach ($performers as $performer):
            try {
                $currentText = DB::table('page_texts')->where('performer_id', $performer->id)
                    ->where('type', 'pb')
                    ->first();
                if (!$currentText): //mo txt make ome
                    $performerBioText = Spinner::performer_bio_text($performer->id); //
                    $insertData = [
                        'performer_id' => $performer->id,
                        'type' => 'pb',
                        'expire' => $performerBioText->expire,
                        'text' => $performerBioText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);
                // echo "pb + ";
                else:  //there is text
                    if (!$currentText->custom): //check if not custom
                        $performerBioText = Spinner::performer_bio_text($performer->id);
                        $updateData = [
                            // 'performer_id' => $performer->id,
                            // 'type'		=> 'pb',
                            'expire' => $performerBioText->expire,
                            'text' => $performerBioText->text,
                            // 'custom' => false,
                        ];
                        DB::table('page_texts')->where('performer_id', $performer->id)->where('type', 'pb')->update($updateData);
                        // echo "pb * ";
                    endif;
                endif;

                $discoGraphyText = DB::table('page_texts')->where('performer_id', $performer->id)
                    ->where('type', 'pd')
                    ->first();
                if (!$discoGraphyText): //no text,create some
                    $performerBioText = Spinner::performer_discography($performer->id);
                    $insertData = [
                        'performer_id' => $performer->id,
                        'type' => 'pd',
                        'expire' => $performerBioText->expire,
                        'text' => $performerBioText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);
                // echo "pd + ";

                else:

                    if ($discoGraphyText->expire < date('Y-m-d h:i:s') && !$discoGraphyText->custom): //not expired?
                        $performerBioText = Spinner::performer_discography($performer->id);
                        $updateData = [
                            // 'performer_id' => $performer->id,
                            // 'type'		=> 'pd',
                            'expire' => $performerBioText->expire,
                            'text' => $performerBioText->text,
                            // 'custom' => false,
                        ];
                        DB::table('page_texts')->where('performer_id', $performer->id)->where('type', 'pd')->update($updateData);
                        // echo "pd * ";

                    endif;
                endif;

                $performerTourText = DB::table('page_texts')->where('performer_id', $performer->id)
                    ->where('type', 'pt')
                    ->first();
                if (!$performerTourText): //no text,create some
                    $performerBioText = Spinner::performer_tour_dates($performer->id);
                    $insertData = [
                        'performer_id' => $performer->id,
                        'type' => 'pt',
                        'expire' => $performerBioText->expire,
                        'text' => $performerBioText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);
                // echo "pt + ";

                else:

                    if ($performerTourText->expire < date('Y-m-d h:i:s') && !$performerTourText->custom): //not expired?
                        $performerBioText = Spinner::performer_tour_dates($performer->id);
                        $updateData = [
                            'expire' => $performerBioText->expire,
                            'text' => $performerBioText->text,
                        ];
                        DB::table('page_texts')->where('performer_id', $performer->id)->where('type', 'pt')->update($updateData);
                        // echo "pb * ";

                    endif;
                endif;
            } catch (Exception $e) {
                // echo "\n\r".$e->getMessage();
                echo $e->getTraceAsString();
            }
        endforeach;

        //concerts
        $concerts = Concert::where('date', '>', date('Y-m-d H:i:s'))->get();
        foreach ($concerts as $concert):
            try {
                // DB::table('page_texts')->where('concert_slug',$concert->slug)->delete();
                $performerVenueText = DB::table('page_texts')->where('venue_id', $concert->location_id)
                    ->where('concert_slug', $concert->slug)
                    ->where('type', 'pv')
                    ->first();
                if (!$performerVenueText): //no text,create some
                    $performerVenue = Spinner::performer_venue_text(null, $concert->location_id, $concert->slug);
                    $insertData = [
                        'concert_slug' => $concert->slug,
                        'location_id' => $concert->location_id,
                        'type' => 'pv',
                        'expire' => $performerVenue->expire,
                        'text' => $performerVenue->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);
                // echo "pv + ";

                else:

                    if ($performerVenueText->expire < date('Y-m-d h:i:s') && !$performerVenueText->custom): //not expired?
                        $performerVenue = Spinner::performer_venue_text(null, $concert->location_id, $concert->slug);
                        $insertData = [
                            'expire' => $performerVenue->expire,
                            'text' => $performerVenue->text,
                        ];
                        DB::table('page_texts')->where('venue_id', $concert->location_id)
                            ->where('concert_slug', $concert->slug)
                            ->where('type', 'pv')->update($insertData);

                    endif;
                endif;
            } catch (Exception $e) {
                // echo "\n\r".$e->getMessage();
                echo $e->getTraceAsString();
            }
        endforeach;
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