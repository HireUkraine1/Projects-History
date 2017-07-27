<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetAlbumTrackDataCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-albumtrack';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Gets all available albums and tracks data and updates as needed';

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
        $this->stage_performers();
        $this->getAlbums();
        //
        $this->stage_albums();
        $this->getTracks();
        $this->getTrackInfo();
    }

    public function stage_performers()
    {
        //	DB::table('tmp_album_stage')->truncate();
        $performers = Performer::all();
        $i = 0;
        $total = count($performers);
        foreach ($performers as $p):
            $i++;
            if (DB::table('tmp_album_stage')->where('performer_id', $p->id)->count() == 0):
                $stage = ['performer_id' => $p->id, 'success' => 0];
                DB::table('tmp_album_stage')->insert($stage);
                if (!($i % 500)) echo "\n\rStaged {$i} of {$total}";
            endif;
        endforeach;
    }

    public function getAlbums()
    {

        $lfmConfig = Config::get("lastfm.lfmkeys");
        $this->lfm = new \Dandelionmood\LastFm\LastFm($lfmConfig['key'], $lfmConfig['secret']);
        // echo "RE";
        $staged = DB::table('tmp_album_stage')->where('success', 0)->take(1000)->get();
        $tot = count($staged);
        echo "STARTED $tot";
        mail("email@site.com", "STARTED: Getting albums", "Total: " . $tot);
        //set the flag
        foreach ($staged as $p):
            DB::table('tmp_album_stage')->where('id', $p->id)->update(array('success' => '-3'));
        endforeach;
        mail("email@site.com", "FLAG SET", "Total: " . $tot);
        $i = 0;
        foreach ($staged as $togo) :
            $performer = Performer::find($togo->performer_id);
            $i++;
            if (!($i % 25)):
                $sleepfor = rand(1, 10);
                echo "\n\t\t\t\t\t SLEEP FOR " . $sleepfor . " Seconds!";
                sleep($sleepfor);
            endif;
            echo "\n\r$performer->name with: ";
            $page = 1;
            $albums = $this->_get_albums($performer);
            echo count($albums) . " albums";
            if ($albums):
                $this->_save_albums($albums, $performer);
                $success = 1;
            else:
                $success = -1;
            endif;
            DB::table('tmp_album_stage')->where('id', $togo->id)->update(array('success' => $success));
        endforeach;
        echo "Finished all";
    }

    private function _get_albums($performer, $page = 1, $totalPages = 1)
    {
        try {
            if ($page <= $totalPages):
                $args = ($performer->mbz_id) ? ['mbid' => $performer->mbz_id, 'page' => $page] : ['artist' => $performer->name, 'page' => $page];
                //get data
                $lfmData = $this->lfm->artist_getTopAlbums($args);
                if (!isset($lfmData->topalbums->album)) return array();
                $totalPages = $lfmData->topalbums->{"@attr"}->totalPages;
                $albums = $lfmData->topalbums->album;

                $page++;
                if ($page <= $totalPages):
                    //recurse inside for next page
                    $return = $this->_get_albums($performer, $page, $totalPages);
                    if (is_array($return)):
                        $albums = array_merge($return, $albums);
                    endif;
                endif;
                // return $albums;
            endif;
            return $albums;

        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    private function _save_albums($albums, $performer)
    {
        foreach ($albums as $a):
            try {

                echo ".";
                // echo "\n\r\t\t$a->name ..";
                $slug = StringHelper::create_slug($performer->name . " " . $a->name);

                $album = Album::firstOrCreate([
                    'slug' => $slug,
                    'rank' => $a->{'@attr'}->rank,
                ]);
                $album->play_count = $a->playcount;
                $album->mbz_id = $a->mbid;
                $album->title = $a->name;
                $album->performer = $performer->name;
                $album->save();
                $images = VarsHelper::parse_lfm_images($a->image);
                foreach ($images as $key => $img):
                    $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);

                    $s3path = S3Helper::putRemote($img, "albums/", $name);
                    $im = Image::firstOrCreate([
                        'path' => $s3path
                    ]);
                    $type = "lfm_album";
                    $size = $key;
                    // $im->path = $img;
                    // $im->save();
                    $album->images()->detach($im->id);
                    $album->images()->attach($im, array('type' => $type, 'size' => $size));
                    //    DebugHelper::pdd($im->toArray());
                endforeach;
                //i don't know why i need to detach and re-attach this shit...
                $performer->albums()->detach($album->id);
                $performer->albums()->attach($album->id);

            } catch (Exception $e) {
                echo $e->getTraceAsString();

            }
        endforeach;
        echo "OK!";
    }

    public function stage_albums()
    {
        //	DB::table('tmp_track_stage')->truncate();
        $albums = Album::all();
        $i = 0;
        $total = count($albums);
        foreach ($albums as $a):
            $i++;
            if (DB::table('tmp_track_stage')->where('album_id', $a->id)->count() == 0):
                $stage = ['album_id' => $a->id, 'success' => 0];
                DB::table('tmp_track_stage')->insert($stage);
                if (!($i % 5000)) echo "\n\rStaged {$i} of {$total}";
            endif;
        endforeach;
    }

    public function getTracks()
    {
        $feedId = date('U');
        mail("email@site.com", "FeedID: {$feedId} - STARTED", "Total: ");

        while (true):

            $take = 50;
            $lfmConfig = Config::get("lastfm.lfmkeys");
            $this->lfm = new \Dandelionmood\LastFm\LastFm($lfmConfig['key'], $lfmConfig['secret']);
            // echo "RE";
            $staged = DB::table('tmp_track_stage')->where('success', 0)->take($take)->get();
            if (!$staged) exit();
            // echo "ER";
            // die();

            $tot = count($staged);
            // var_dump($performers);

            echo "STARTED $tot";
            // mail("email@site.com", "FeedID: {$feedId} - STARTED", "Total: ".$tot);
            //set the flag
            foreach ($staged as $p):
                DB::table('tmp_track_stage')->where('id', $p->id)->update(array('success' => '-3'));
            endforeach;
            // mail("email@site.com", "FeedID: {$feedId} - FLAG SET", "Total: ".$tot);
            $i = 0;
            foreach ($staged as $sa):
                $i++;
                try {

                    $album = Album::find($sa->album_id);
                    if ($album):
                        $args = ($album->mbz_id) ? ['mbid' => $album->mbz_id] : ['artist' => $album->performer, 'album' => $album->title];
                        //get data
                        $lfmData = $this->lfm->album_getInfo($args);
                        if ($lfmAlbum = $lfmData->album):
                            $album->release_date = (strtotime($lfmAlbum->releasedate)) ? date('Y-m-d', strtotime($lfmAlbum->releasedate)) : '0001-01-01';
                            $album->listeners = $lfmAlbum->listeners;
                            $album->mbz_id = $lfmAlbum->mbid;
                            $album->save();
                            if (isset($lfmAlbum->tracks->track)):
                                foreach ($lfmAlbum->tracks->track as $lfmTrack):
                                    // if(!Track::where('album_id', $album->id)->where('rank', $lfmTrack->{'@attr'}->rank)->count()):
                                    $track = new Track;
                                    $track->album_id = $album->id;
                                    $track->duration = (isset($lfmTrack->duration)) ? $lfmTrack->duration : 0;
                                    $track->listeners = (isset($lfmAlbum->listeners)) ? $lfmAlbum->listeners : '';
                                    $track->mbz_id = (isset($lfmTrack->mbid)) ? $lfmTrack->mbid : '';
                                    $track->name = (isset($lfmTrack->name)) ? $lfmTrack->name : '<no title>';
                                    $track->playcount = 0;
                                    $track->rank = (isset($lfmTrack->{'@attr'}->rank)) ? $lfmTrack->{'@attr'}->rank : '';
                                    $track->save();
                                    // endif;
                                endforeach;
                                DB::table('tmp_track_stage')->where('id', $sa->id)->update(array('success' => '1')); //tracks and album saved
                            else:
                                DB::table('tmp_track_stage')->where('id', $sa->id)->update(array('success' => '-2')); //no tracks but album updated
                            endif;
                        endif;
                    else:
                        DB::table('tmp_track_stage')->where('id', $sa->id)->update(array('success' => '-1'));// no album info
                    endif;
                } catch (Exception $e) {
                    echo $e->getTraceAsString(); //continue foreach loop
                    DB::table('tmp_track_stage')->where('id', $sa->id)->update(array('success' => '-5')); //something fail, probably rerun.
                }
            endforeach;
            if (!$i % 10000) mail("email@site.com", "FeedID: {$feedId} - at:", "i: $i");
        endwhile;
        mail("email@site.com", "FeedID: {$feedId} -ALL DONE", "Total: $i");

    }

    public function getTrackInfo()
    {
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        while (true):
            $start = date('U');
            $runId = date('U');
            $trackErrors = 0;
            $albumErrors = 0;
            $lfmConfig = Config::get("lastfm.lfmkeys");
            $this->lfm = new \Dandelionmood\LastFm\LastFm($lfmConfig['key'], $lfmConfig['secret']);

            // $albums = Album::take(2)->with('tracks')->where('');
            $staged = DB::table('tmp_track_stage')->take(2000)->where('success', '1')->get();
            $subject = gethostname() . " TOOK 2000 (ID: {$runId})";

            if (count($staged)):
                $first = $staged[0]->id;
                $last = end($staged)->id;
            else:
                $subject = gethostname() . " FINISHED (ID: {$runId})";
                mail("email@site.com", $subject, "Nothing to do");
                die();
            endif;

            DB::table('tmp_track_stage')->whereBetween('id', array($first, $last))->update(array('success' => 8));

            echo "\n\rFlags set! Seconds from start: " . date('U') - $start;

            $inProgress = DB::table('tmp_track_stage')->where('success', 8)->count();
            $completed = DB::table('tmp_track_stage')->where('success', 2)->count();
            $message = " As of " . date('F j G:i') . " we have \n\r {$inProgress} in progress \n\r {$completed} - completed. \n\r STAY STRONG!";
            mail('email@site.com', $subject, $message);
            $i = 0;
            foreach ($staged as $stagedAlbum):
                try {
                    DB::table('tmp_track_stage')->where('id', $stagedAlbum->id)->update(array('success' => 7));
                    sleep(1);
                    $i++;
                    $album = Album::where('id', $stagedAlbum->album_id)->with('tracks')->first();
                    echo "\n\r $album->title ({$album->performer}) with " . count($album->tracks) . " tracks \n\r\t";
                    foreach ($album->tracks as $track):
                        try {
                            $args = ($track->mbz_id) ? ['mbid' => $track->mbz_id] : ['artist' => $album->performer, 'track' => $track->name];
                            //get data
                            $lfmData = $this->lfm->track_getInfo($args);
                            if ($lfmTrack = $lfmData->track):
                                $listeners = $lfmTrack->listeners;
                                $playcount = $lfmTrack->playcount;
                                $track->listeners = $listeners;
                                $track->playcount = $playcount;
                                $track->save();
                                DB::table('tmp_track_stage')->where('id', $stagedAlbum->id)->update(array('success' => 2));
                                echo ".";
                            else:
                                echo "!";
                            endif;

                        } catch (Exception $e) {
                            echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getTraceAsString();
                            $trackErrors++;
                            // $subject = gethostname()." ERROR IN TRACK: (ID: {$runId})";
                            // $errors .= "chocked on $i staged id: $album->id with "$e->getMessage()."\n\r".$e->getTraceAsString();
                            //	mail("email@site.com", $subject, $message);
                        }
                    endforeach;
                    echo "OK";
                } catch (Exception $e) {
                    echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getTraceAsString();
                    $albumErrors++;
                    // $subject = gethostname()." ERROR IN ALBUM: (ID: {$runId})";
                    // $message = "chocked on $i staged id: $stagedAlbum->id with ".$e->getTraceAsString();
                    // mail("email@site.com", $subject, $message);
                }
            endforeach;
            $subject = gethostname() . " RUN FINISHED: (ID: {$runId})";
            $message = "Errors: track: $trackErrors album: $albumErrors";
            mail("email@site.com", $subject, $message);
        endwhile;
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