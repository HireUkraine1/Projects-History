<?php
/*use Guzzle\Service\Inspector;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;*/

/**
 *
 */
class SetList
{

    private $_client;
    private $_endpoint;

    function __construct($config = null)
    {
        if (!$config) return null;
        $this->_endpoint = "http://api.setlist.fm/rest/0.1/";
        $this->_client = new Guzzle\Service\Client($this->_endpoint);
    }

    public function search_setlist($mbid = null, $country = 'US')
    {
        $setlist = [];
        if (!$mbid):
            return $setlist;
        endif;
        try {
            try {
                $guzz = $this->_client->get("search/setlists.json?artistMbid={$mbid}&countryCode={$country}", ['timeout' => 5])->send();
            } catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
                echo $e->getMessage();
                return $setlist;
            }
            // dd($guzz);
            if ($guzz->getStatusCode() == 200):
                $response = $guzz->json();
            else:
                echo $e->getMessage();
                //$err = print_r($e->getTrace(), true);
                echo "failed on parse set" . $e->getMessage();
                return "Timeout not found";
                return $setlist;
            endif;
            $total = $response['setlists']['@total'];
            $pp = $response['setlists']['@itemsPerPage'];
            $numOfRequests = ceil($total / $pp);

            $i = 1;
            while ($i <= $numOfRequests):
                if (isset($response['setlists']['setlist'])):
                    foreach ($response['setlists']['setlist'] as $set):
                        $event = [];
                        $event['tour'] = isset($set['@tour']) ? $set['@tour'] : '';
                        $event['sl_id'] = isset($set['@id']) ? $set['@id'] : '';
                        $event['date'] = isset($set['@eventDate']) ? $set['@eventDate'] : '';
                        $event['venue'] = isset($set['venue']['@name']) ? $set['venue']['@name'] : '';
                        $event['city'] = isset($set['venue']['city']['@name']) ? $set['venue']['city']['@name'] : '';
                        $event['state'] = isset($set['venue']['city']['@stateCode']) ? $set['venue']['city']['@stateCode'] : '';
                        $event['state_full'] = isset($set['venue']['city']['@state']) ? $set['venue']['city']['@state'] : '';
                        $event['country'] = isset($set['venue']['city']['country']['@code']) ? $set['venue']['city']['country']['@code'] : '';
                        if (isset($set['sets'])):
                            $event['sets'] = $this->_parse_set($set['sets']);
                        else:
                            $event['sets'] = false;
                        endif;

                        array_push($setlist, $event);
                    endforeach;
                endif;
                $i++; //get next page
                if ($i <= $numOfRequests):
                    $response = $this->_client->get("search/setlists.json?artistMbid={$mbid}&countryCode={{$country}}&p={$i}")->send()->json();
                endif;
                if (!($i % 30)):
                    $secs = rand(1, 15);
                    sleep($secs);
                endif;
            endwhile;
        } catch (Exception $e) {
            echo $e->getMessage();
            ////$err = print_r($e->getTrace(), true);
            mail("email@site.com", "failed on parse set", $e->getMessage() . $err);
        }

        return $setlist;
    }

    private function _parse_set($data = array())
    {

        $return = [];
        if (isset($data['set']['song'])):
            $songs = $this->_parse_songs($data['set']['song']);
            // DebugHelper::pdd($data['set']);
            $return[0] = $songs;
        else:
            try {
                if (isset($data['set'])):
                    foreach ($data['set'] as $s):
                        $songs = $this->_parse_songs($s['song']);
                        array_push($return, $songs);
                    endforeach;
                endif;

            } catch (Exception $e) {
                echo $e->getMessage();
                //$err = print_r($e->getTrace(), true);
                mail("email@site.com", "failed on parse set", $e->getMessage() . $err);
                // var_dump($data);
                // die();
            }
        endif;
        return $return;
    }

    private function _parse_songs($data)
    {

        $return = []; //always return array (fixing SL fuckup)
        try {
            if (isset($data['@name'])):
                $song = [];
                $song['title'] = $data['@name'];
                $song['cover'] = false;
                $song['with'] = false;
                if (isset($data['cover'])):
                    $cover = [];
                    $cover['performer'] = ((isset($data['cover']['@name']))) ? $data['cover']['@name'] : false;
                    $cover['performer_mbz_id'] = ((isset($data['cover']['@mbid']))) ? $data['cover']['@mbid'] : false;
                    $song['cover'] = $cover;
                endif;
                if (isset($data['with'])):
                    $with = [];
                    $name = ((isset($data['with']['@name']))) ? $data['with']['@name'] : false;
                    $with['performer'] = $name;

                    $with['performer_mbz_id'] = ((isset($data['with']['@mbid']))) ? $data['with']['@mbid'] : false;
                    $performer = Performer::where('slug', StringHelper::create_slug($name))->first();
                    $with['slug'] = ($performer) ? $performer->slug : false;

                    $song['with'] = $with;
                endif;

                $song['info'] = ((isset($data['info']))) ? $data['info'] : false;
                $return[0] = $song;
            else:
                foreach ($data as $sng):
                    $song = [];
                    $song['title'] = $sng['@name'];
                    $song['cover'] = false;
                    $song['with'] = false;
                    if (isset($sng['cover'])):
                        $cover = [];

                        $name = ((isset($sng['cover']['@name']))) ? $sng['cover']['@name'] : false;
                        $cover['performer'] = $name;
                        $cover['performer_mbz_id'] = ((isset($sng['cover']['@mbid']))) ? $sng['cover']['@mbid'] : false;
                        $performer = Performer::where('slug', StringHelper::create_slug($name))->first();
                        $cover['slug'] = ($performer) ? $performer->slug : false;

                        $song['cover'] = $cover;


                    endif;
                    if (isset($sng['with'])):
                        $with = [];
                        $name = ((isset($sng['with']['@name']))) ? $sng['with']['@name'] : false;
                        $with['performer'] = $name;
                        $with['performer_mbz_id'] = ((isset($sng['with']['@mbid']))) ? $sng['with']['@mbid'] : false;
                        $performer = Performer::where('slug', StringHelper::create_slug($name))->first();
                        $with['slug'] = ($performer) ? $performer->slug : false;

                        $song['with'] = $with;
                    endif;
                    $song['info'] = ((isset($sng['info']))) ? $sng['info'] : false;
                    array_push($return, $song);
                endforeach;
            endif;
        } catch (Exception $e) {
            echo $e->getMessage();
            mail("email@site.com", "failed on parse set", $e->getMessage() . $err);
        }
        return $return;

    }

    public function get_venue($vid = '5bd6378c')
    {
        $response = $this->_client->get("venue/{$vid}")->send()->xml();
        dd($response);
    }

    public function get_artist($mbid = null)
    {
        return $this->_client->get("artist/{$mbid}")->send();
    }

    public function get_set($setId = '3ceb97b')
    {
        $response = $this->_client->get("setlist/{$setId}")->send()->xml();
        DebugHelper::pdd($response, false);
    }

    private function _parse_result($data)
    {
        try {
            $thisSet = [];
            $thisSet['event_date'] = $data->attributes()->eventDate->__toString();
            $thisSet['sl_id'] = $data->attributes()->id->__toString();
            $thisSet['updated_at'] = $data->attributes()->lastUpdated->__toString();
            $thisSet['tour'] = (isset($data->attributes()->tour)) ? $data->attributes()->tour->__toString() : '';
            $venue = [];
            $venue['name'] = $data->venue->attributes()->name->__toString();
            if (isset($data->venue->city->attributes()->stateCode)):
                $venue['state'] = $data->venue->city->attributes()->stateCode->__toString();
            else:
                $venue['state'] = '';
            endif;
            if (isset($data->venue->city->attributes()->state)):
                $venue['state_full'] = $data->venue->city->attributes()->state->__toString();
            else:
                $venue['state_full'] = '';
            endif;
            $venue['country'] = $data->venue->city->country->attributes()->code->__toString();
            $thisSet['venue'] = $venue;
            $thisSet['songs'] = [];
            $thisSet['encore'] = [];

            if ($data->sets->set):
                $this->_parse_set($data->sets);
                $songs = [];
                $encore = [];
                $covers = [];
                foreach ($data->sets->set as $s):
                    foreach ($s->song as $slsong):
                        $songdata = [];
                        $songdata['title'] = $slsong->attributes()->name->__toString();
                        $songdata['info'] = $slsong->info;
                        if (isset($slsong->cover)):
                            if (isset($slsong->cover->attributes()->name)):
                                $songdata['original_performer'] = $slsong->cover->attributes()->name->__toString();
                            endif;
                            if (isset($slsong->cover->attributes()->mbid)):
                                $songdata['original_performer_mbz_id'] = $slsong->cover->attributes()->mbid->__toString();
                            endif;
                        endif;
                        if (isset($s->attributes()->encore)):
                            array_push($encore, $songdata);
                        else:
                            array_push($songs, $songdata);
                        endif;
                    endforeach;
                endforeach;
                $thisSet['songs'] = $songs;
                $thisSet['encore'] = $encore;
            endif;
            return $thisSet;
        } catch (Exception $e) {
            echo "ERR" . $e->getMessage();
            return false;
        }
    }
}

?>