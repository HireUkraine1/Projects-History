<?php

/*use  \Dandelionmood\LastFm\LastFm;
*/

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;
use Guzzle\Http\Client;

/*use MusicBrainz\MusicBrainz;
use MusicBrainz\Filters\ArtistFilter;
//use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\HttpAdapters;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;*/


//set_time_limit(3200); //60 seconds = 1 minute
class UtilityController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Utility Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'UtilityController@showWelcome');
    |
    */
    protected $layout = 'frontend.layouts.fullwidth';
    protected $lfm = null;
    protected $albums = [];

    function xml_attribute($object, $attribute)
    {
        if (isset($object[$attribute])) return (string)$object[$attribute];
    }


    public function report()
    {
        $tn = new TicketNetwork\Api\TicketNetwork('ticketnetwork.tnProdData');

        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $params = array(
            'websiteConfigId' => $loadedConfig,
            'websiteConfigID' => $loadedConfig,
        );
        $report = $tn->run('GetReportsByWebsite', $params)->GetReportsByWebsiteResult;
        DebugHelper::pdd($report, true);

    }



    public function wordcombos($words)
    {
        if (count($words) <= 1) {
            $result = $words;
        } else {
            $result = array();
            for ($i = 0; $i < count($words); ++$i) {
                $firstword = $words[$i];
                $remainingwords = array();
                for ($j = 0; $j < count($words); ++$j) {
                    if ($i <> $j) $remainingwords[] = $words[$j];
                }
                $combos = $this->wordcombos($remainingwords);
                for ($j = 0; $j < count($combos); ++$j) {
                    $result[] = $firstword . ' ' . $combos[$j];
                }
            }
        }
        return $result;
    }


    public function getPerformerAlbum($skip = 110, $i = 111)
    {
        if ($i <= 111) {
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
                echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getTraceAsString();
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
        $albums = $this->_get_albums($performer);

        $countAlbum = count($albums);
        $i = 0;
        if ($countAlbum > 1):
            foreach ($albums as $album):
                echo "$album->name - $performer->name \n\r";

                $i++;
            endforeach;
            if ($albums):
                Echo "\n\r take all album of $performer->name \n\r";
                $this->_save_albums($albums, $performer);
            endif;
        elseif ($countAlbum == 1):
            if ($albums):
                Echo "\n\r take all album of $performer->name \n\r";
                $this->_save_album($albums, $performer);
            endif;
        else:
            echo "\n\r null album";
            DB::table('error_import_album')->insert(
                array('number_under_account' => $numberUnderAccount,
                    'performer_id' => $performerId,
                    'performer_name' => $performer->name,
                    'note' => "performer hasn't albums"
                ));
            die();

        endif;

    }

    private function _get_albums($performer, $page = 1, $totalPages = 1)
    {

        if ($page <= $totalPages):
            $args = ($performer->mbz_id) ? ['mbid' => $performer->mbz_id, 'page' => $page] : ['artist' => $performer->name, 'page' => $page];
            $sleepfor = rand(1, 2);
            echo "\n\r SLEEP FOR $sleepfor Seconds! Take albums $performer->name from lfm page= $page \n\r";
            sleep($sleepfor);

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

        endif;
        return $albums;//return all performer albums 

    }

    private function _save_albums($albums, $performer)
    {
        foreach ($albums as $a):
            try {
                echo ".";
                $slug = StringHelper::create_slug($performer->name . " " . $a->name);
                $album = Album::where('slug', $slug)->where('rank', $a->{'@attr'}->rank)->first();
                if ($album):
                    $album->play_count = $a->playcount;
                    $album->mbz_id = $a->mbid;
                    $album->save();
                else:
                    $album = Album::create(['slug' => $slug, 'rank' => $a->{'@attr'}->rank,]);
                    $album->play_count = $a->playcount;
                    $album->mbz_id = $a->mbid;
                    $album->title = $a->name;
                    $album->performer = $performer->name;
                    $album->save();
                    $this->getTrackInfo($album->id);
                endif;

                //add images album
                $images = VarsHelper::parse_lfm_images($a->image);

                foreach ($images as $key => $img):
                    if ($key == 'large' || $key == 'extralarge'):
                        $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                        $s3path = S3Helper::putRemote($img, "albums/", $name);
                        $im = Image::firstOrCreate(['path' => $s3path]);
                        $type = "lfm_album";
                        $size = $key;
                        $album->images()->detach($im->id);
                        $album->images()->attach($im, array('type' => $type, 'size' => $size));
                    endif;
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

    public function getTrackInfo($albumId)
    {

        try {

            $album = Album::find($albumId);
            if ($album):
                $args = ($album->mbz_id) ? ['mbid' => $album->mbz_id] : ['artist' => $album->performer, 'album' => $album->title];
                //get data
                $sleepfor = rand(1, 2);
                echo "\n\t\t\t\t\t SLEEP FOR " . $sleepfor . " Seconds! Save tracks of album $album->title -$album->performer \n\t\t\t\t\t ";
                sleep($sleepfor);
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
                    endif;
                endif;
            endif;
        } catch (Exception $e) {
            echo "ERR\n\r" . $e->getMessage() . "\n\r" . $e->getTraceAsString();
        }

    }

    private function _save_album($albums, $performer)
    {
        try {
            echo ".";
            // echo "\n\r\t\t$a->name ..";
            $slug = StringHelper::create_slug($performer->name . " " . $albums->name);
            $album = Album::where('slug', $slug)->where('rank', $albums->{'@attr'}->rank)->first();
            if ($album):
                $album->play_count = $albums->playcount;
                $album->mbz_id = $albums->mbid;
                $album->save();
            else:
                $album = Album::create(['slug' => $slug, 'rank' => $albums->{'@attr'}->rank,]);
                $album->play_count = $albums->playcount;
                $album->mbz_id = $albums->mbid;
                $album->title = $albums->name;
                $album->performer = $performer->name;
                $album->save();
                //add tracks album
                $this->getTrackInfo($album->id);
            endif;

            //add images album
            $images = VarsHelper::parse_lfm_images($albums->image);

            foreach ($images as $key => $img):
                if ($key == 'large' || $key == 'extralarge'):
                    $name = StringHelper::create_slug($album->id . " " . $album->title . " " . $key);
                    $s3path = S3Helper::putRemote($img, "albums/", $name);
                    $im = Image::firstOrCreate(['path' => $s3path]);
                    $type = "lfm_album";
                    $size = $key;
                    $album->images()->detach($im->id);
                    $album->images()->attach($im, array('type' => $type, 'size' => $size));
                endif;
            endforeach;
            //i don't know why i need to detach and re-attach this shit...
            $performer->albums()->detach($album->id);
            $performer->albums()->attach($album->id);

        } catch (Exception $e) {
            echo $e->getTraceAsString();

        }
        echo "OK!";
    }


}