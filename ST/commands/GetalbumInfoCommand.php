<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetalbumInfoCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-albums-info';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Get all available albums and tracks data for all performer';

    protected $lfm;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $timeStart;

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
        $this->timeStart = microtime(true);
        ini_set("memory_limit", "-1");
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();

        $this->getPerformerAlbum();
    }

    public function getPerformerAlbum($skip = 0, $i = 1)
    {
        if ($i <= Performer::count()) {

            $performers = Performer::take(1)->skip($skip)->orderBy("id")->get(array('id', 'mbz_id', 'name'));

            try {
                foreach ($performers as $performer):

                    DB::table('tmp_performer')->update(array('number_under_account' => $i, 'performer_id' => $performer->id, 'note' => '0'));
                    $this->_get_Lfm_Performer_Albums($performer->id, $i);
                    DB::table('tmp_performer')->update(array('note' => '1'));
                    $i++;
                endforeach;

                $skip += 1;
                $this->getPerformerAlbum($skip, $i);
            } catch (Exception $e) {
                echo "ERR\n\r" . $e->getMessage() . "\n\r";

            }
        } else {
            DB::table('tmp_performer')->update(array('note' => 'finish'));
        }
    }

    public function _get_Lfm_Performer_Albums($performerId, $numberUnderAccount)
    {
        $lfmConfig = Config::get("lastfm.lfmkeys");
        $this->lfm = new \Dandelionmood\LastFm\LastFm($lfmConfig['key'], $lfmConfig['secret']);
        $performer = Performer::find($performerId);
        echo "\n\r $performer->name \n\r";
        $albums = $this->_get_albums($performer, $numberUnderAccount);

        $countAlbum = count($albums);
        $i = 0;
        if ($countAlbum > 1):
            foreach ($albums as $album):
                echo "$album->name - $performer->name \n\r";

                $i++;
            endforeach;
            if ($albums):
                Echo "\n\r take all album of $performer->name \n\r";
                $this->_save_albums($albums, $performer, $numberUnderAccount);
            endif;
        elseif ($countAlbum == 1):
            if ($albums):
                Echo "\n\r take all album of $performer->name \n\r";
                $this->_save_album($albums, $performer, $numberUnderAccount);
            endif;
        else:
            echo "\n\r null";
            DB::table('error_import_album')->insert(
                array('number_under_account' => $numberUnderAccount,
                    'performer_id' => $performerId,
                    'performer_name' => $performer->name,
                    'note' => "performer hasn't albums"
                ));
        endif;

    }

    private function _get_albums($performer, $numberUnderAccount, $page = 1, $totalPages = 1)
    {
        try {
            if ($page <= $totalPages):
                $args = ($performer->mbz_id) ? ['mbid' => "$performer->mbz_id", 'page' => "$page"] : ['artist' => "$performer->name", 'page' => "$page"];
                //time
                $time = microtime(true) - $this->timeStart;
                $this->timeStart = microtime(true);

                if ($time < 1):
                    $sleepfor = rand(1, 2);
                    echo "\n\r Previous request to lfm was < 1 seconds ago. Need sleep FOR $sleepfor Seconds, before take albums $performer->name from lfm page= $page \n\r";
                    sleep($sleepfor);
                else:
                    echo "\n\r Previous request to lfm was $time сек. Take albums $performer->name from lfm page= $page \n\r";
                endif;

                $lfmData = $this->lfm->artist_getTopAlbums($args);

                if (!isset($lfmData->topalbums->album)):
                    return array();
                endif;
                $totalPages = $lfmData->topalbums->{"@attr"}->totalPages;
                $albums = $lfmData->topalbums->album;
                $page++;
                if ($page <= $totalPages):
                    //recurse inside for next page
                    $return = $this->_get_albums($performer, $numberUnderAccount, $page, $totalPages);
                    if (is_array($return)):
                        $albums = array_merge($return, $albums);
                    endif;
                endif;

            endif;
            return $albums;//return all performer albums

        } catch (Exception $e) {
            echo "ERR\n\r" . $e->getMessage() . "\n\r";
            DB::table('error_import_album')->insert(
                array('number_under_account' => $numberUnderAccount,
                    'performer_id' => $performer->id,
                    'performer_name' => $performer->name,
                    'note' => 'function _get_albums: line-' . $e->getLine() . ' message-' . $e->getMessage()
                ));

        }
    }

    private function _save_albums($albums, $performer, $numberUnderAccount)
    {
        foreach ($albums as $a):
            try {
                echo ".";
                // echo "\n\r\t\t$a->name ..";
                $slug = StringHelper::create_slug($performer->name . " " . $a->name);
                $album = Album::where('slug', $slug)->where('rank', $a->{'@attr'}->rank)->first();
                $albumInfo = ['title' => $a->name, 'artist' => $a->artist->name, 'mbid' => $a->mbid];
                if ($album):
                    $album->play_count = $a->playcount;
                    $album->mbz_id = $a->mbid;
                    $album->save();
                    if (count($album->images) !== 2):
                        $images = VarsHelper::parse_lfm_images($a->image);

                        foreach ($images as $key => $img):
                            if ($key == 'large' || $key == 'extralarge'):

                                $checkImgExist = @get_headers($img);

                                if (preg_match("/(200 OK)/", $checkImgExist[0])):
                                    $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                                    $s3path = S3Helper::putRemote($img, "albums/", $name);
                                    $im = Image::firstOrCreate(['path' => $s3path]);
                                    $type = "lfm_album";
                                    $size = $key;
                                    $album->images()->detach($im->id);
                                    $album->images()->attach($im, array('type' => $type, 'size' => $size));

                                else:
                                    DB::table('error_import_album')->insert(
                                        array('number_under_account' => $numberUnderAccount,
                                            'performer_id' => $performer->id,
                                            'performer_name' => $performer->name,
                                            'note' => 'image for album ' . $album->title . ' not exist'
                                        ));
                                endif;

                            endif;

                        endforeach;
                    endif;

                else:
                    $album = Album::create(['slug' => $slug, 'rank' => $a->{'@attr'}->rank,]);
                    $album->play_count = $a->playcount;
                    $album->mbz_id = $a->mbid;
                    $album->title = $a->name;
                    $album->performer = $performer->name;
                    $album->save();

                    //add images album
                    $images = VarsHelper::parse_lfm_images($a->image);

                    foreach ($images as $key => $img):
                        if ($key == 'large' || $key == 'extralarge'):

                            $checkImgExist = @get_headers($img);

                            if (preg_match("/(200 OK)/", $checkImgExist[0])):
                                $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                                $s3path = S3Helper::putRemote($img, "albums/", $name);
                                $im = Image::firstOrCreate(['path' => $s3path]);
                                $type = "lfm_album";
                                $size = $key;
                                $album->images()->detach($im->id);
                                $album->images()->attach($im, array('type' => $type, 'size' => $size));

                            else:
                                DB::table('error_import_album')->insert(
                                    array('number_under_account' => $numberUnderAccount,
                                        'performer_id' => $performer->id,
                                        'performer_name' => $performer->name,
                                        'note' => 'image for album ' . $album->title . ' not exist'
                                    ));
                            endif;

                        endif;

                    endforeach;

                endif;
                $this->getTrackInfo($album->id, $albumInfo, $performer, $numberUnderAccount);
                //i don't know why i need to detach and re-attach this shit...
                $performer->albums()->detach($album->id);
                $performer->albums()->attach($album->id);
            } catch (Exception $e) {
                echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getLine();
                DB::table('error_import_album')->insert(
                    array('number_under_account' => $numberUnderAccount,
                        'performer_id' => $performer->id,
                        'performer_name' => $performer->name,
                        'note' => 'function _save_albums: line-' . $e->getLine() . ' message-' . $e->getMessage()
                    ));
            }
        endforeach;
        echo "OK!";
    }

    public function getTrackInfo($albumId, $albumInfo, $performer, $numberUnderAccount)
    {

        try {

            $album = Album::find($albumId);
            if ($album):
                $args = ($album->mbz_id) ? ['mbid' => $albumInfo['mbid'], 'artist' => $albumInfo['artist'], 'album' => $albumInfo['title']] : ['artist' => $albumInfo['artist'], 'album' => $albumInfo['title']];
                //get data
                $time = microtime(true) - $this->timeStart;
                $this->timeStart = microtime(true);

                if ($time < 1):
                    $sleepfor = rand(1, 2);
                    echo "\n\t\t\t\t\t Previous request to lfm was < 1 seconds ago. Need sleep FOR " . $sleepfor . " Seconds! Before take tracks of album $album->title -$album->performer \n\t\t\t\t\t ";
                    sleep($sleepfor);
                else:
                    echo "\n\t\t\t\t\t Previous request to lfm was $time сек.Take tracks of album $album->title -$album->performer \n\t\t\t\t\t ";
                endif;
                $lfmData = $this->lfm->album_getInfo($args);


                if (isset($lfmData->album)):
                    if ($lfmAlbum = $lfmData->album):
                        $album->release_date = (strtotime($lfmAlbum->releasedate)) ? date('Y-m-d', strtotime($lfmAlbum->releasedate)) : '0001-01-01';
                        $album->listeners = $lfmAlbum->listeners;
                        $album->mbz_id = $lfmAlbum->mbid;
                        $album->save();
                        //check album has any tracks
                        if (isset($lfmAlbum->tracks->track)):
                            //check count of tracks if more then 1 track lfm have another structure
                            if (count($lfmAlbum->tracks->track) > 1):
                                foreach ($lfmAlbum->tracks->track as $lfmTrack):

                                    if (!Track::where('album_id', $album->id)->where('rank', $lfmTrack->{'@attr'}->rank)->count()):
                                        $track = new Track;
                                        $track->album_id = $album->id;
                                        $track->duration = (isset($lfmTrack->duration)) ? $lfmTrack->duration : 0;
                                        $track->listeners = (isset($lfmAlbum->listeners)) ? $lfmAlbum->listeners : '';
                                        $track->mbz_id = (isset($lfmTrack->mbid)) ? $lfmTrack->mbid : '';
                                        $track->name = (isset($lfmTrack->name)) ? $lfmTrack->name : '<no title>';
                                        $track->playcount = 0;
                                        $track->rank = (isset($lfmTrack->{'@attr'}->rank)) ? $lfmTrack->{'@attr'}->rank : '';
                                        $track->save();
                                    endif;
                                endforeach;

                            endif;
                            if (count($lfmAlbum->tracks->track) == 1):
                                if (!Track::where('album_id', $album->id)->where('rank', $lfmAlbum->tracks->track->{'@attr'}->rank)->count()):
                                    $track = new Track;
                                    $track->album_id = $album->id;
                                    $track->duration = (isset($lfmAlbum->tracks->track->duration)) ? $lfmAlbum->tracks->track->duration : 0;
                                    $track->listeners = (isset($lfmAlbum->listeners)) ? $lfmAlbum->listeners : '';
                                    $track->mbz_id = (isset($lfmAlbum->tracks->track->mbid)) ? $lfmAlbum->tracks->track->mbid : '';
                                    $track->name = (isset($lfmAlbum->tracks->track->name)) ? $lfmAlbum->tracks->track->name : '<no title>';
                                    $track->playcount = 0;
                                    $track->rank = (isset($lfmAlbum->tracks->track->{'@attr'}->rank)) ? $lfmAlbum->tracks->track->{'@attr'}->rank : '';
                                    $track->save();
                                endif;
                            endif;
                        else:
                            DB::table('error_import_album')->insert(
                                array('number_under_account' => $numberUnderAccount,
                                    'performer_id' => $performer->id,
                                    'performer_name' => $performer->name,
                                    'note' => 'function getTrackInfo: album ' . $albumInfo['title'] . ' hasnt tracks'
                                ));
                        endif;
                    endif;
                else:
                    DB::table('error_import_album')->insert(
                        array('number_under_account' => $numberUnderAccount,
                            'performer_id' => $performer->id,
                            'performer_name' => $performer->name,
                            'note' => 'function getTrackInfo: album' . $albumInfo['title'] . 'not found in lfm'
                        ));
                endif;
            endif;
        } catch (Exception $e) {
            echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getLine();
            DB::table('error_import_album')->insert(
                array('number_under_account' => $numberUnderAccount,
                    'performer_id' => $performer->id,
                    'performer_name' => $performer->name,
                    'note' => 'function getTrackInfo: line-' . $e->getLine() . ' message-' . $e->getMessage()
                ));
        }

    }

    private function _save_album($albums, $performer, $numberUnderAccount)
    {

        try {
            echo ".";
            // echo "\n\r\t\t$a->name ..";
            $slug = StringHelper::create_slug($performer->name . " " . $albums->name);
            $album = Album::where('slug', $slug)->where('rank', $albums->{'@attr'}->rank)->first();
            $albumInfo = ['title' => $albums->name, 'artist' => $albums->artist->name, 'mbid' => $albums->mbid];

            if ($album):
                $album->play_count = $albums->playcount;
                $album->mbz_id = $albums->mbid;
                $album->save();

                if (count($album->images) != 2):
                    $images = VarsHelper::parse_lfm_images($albums->image);

                    foreach ($images as $key => $img):
                        if ($key == 'large' || $key == 'extralarge'):
                            $checkImgExist = @get_headers($img);
                            if (preg_match("/(200 OK)/", $checkImgExist[0])):
                                $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                                $s3path = S3Helper::putRemote($img, "albums/", $name);
                                $im = Image::firstOrCreate(['path' => $s3path]);
                                $type = "lfm_album";
                                $size = $key;
                                $album->images()->detach($im->id);
                                $album->images()->attach($im, array('type' => $type, 'size' => $size));
                            else:
                                DB::table('error_import_album')->insert(
                                    array('number_under_account' => $numberUnderAccount,
                                        'performer_id' => $performer->id,
                                        'performer_name' => $performer->name,
                                        'note' => 'image for album ' . $album->title . ' not exist'
                                    ));
                            endif;

                        endif;
                    endforeach;
                endif;


            else:
                $album = Album::create(['slug' => $slug, 'rank' => $albums->{'@attr'}->rank,]);
                $album->play_count = $albums->playcount;
                $album->mbz_id = $albums->mbid;
                $album->title = $albums->name;
                $album->performer = $performer->name;
                $album->save();
                $images = VarsHelper::parse_lfm_images($albums->image);

                foreach ($images as $key => $img):
                    if ($key == 'large' || $key == 'extralarge'):
                        $checkImgExist = @get_headers($img);
                        if (preg_match("/(200 OK)/", $checkImgExist[0])):
                            $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                            $s3path = S3Helper::putRemote($img, "albums/", $name);
                            $im = Image::firstOrCreate(['path' => $s3path]);
                            $type = "lfm_album";
                            $size = $key;
                            $album->images()->detach($im->id);
                            $album->images()->attach($im, array('type' => $type, 'size' => $size));
                        else:
                            DB::table('error_import_album')->insert(
                                array('number_under_account' => $numberUnderAccount,
                                    'performer_id' => $performer->id,
                                    'performer_name' => $performer->name,
                                    'note' => 'image for album ' . $album->title . ' not exist'
                                ));
                        endif;
                    endif;
                endforeach;


            endif;
            //add tracks album
            $this->getTrackInfo($album->id, $albumInfo, $performer, $numberUnderAccount);
            //add images album
            $performer->albums()->detach($album->id);
            $performer->albums()->attach($album->id);

        } catch (Exception $e) {
            echo "ERR\n\r" . $e->getMessage() . "\n\r";
            DB::table('error_import_album')->insert(
                array('number_under_account' => $numberUnderAccount,
                    'performer_id' => $performer->id,
                    'performer_name' => $performer->name,
                    'note' => 'function _save_album: line-' . $e->getLine() . ' message-' . $e->getMessage()
                ));
        }
        echo "OK!";
    }

}