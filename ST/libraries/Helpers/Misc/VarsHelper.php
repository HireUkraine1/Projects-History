<?php

class VarsHelper
{
    public static function parse_lfm_images($images = NULL)
    {
        foreach ($images as $img):
            $image[$img->size] = $img->{'#text'};
        endforeach;
        return $image;
    }

    public static function get_cdn($type = 'aws')
    {
        $cdn = Config::get("site.cdn.{$type}");
        return $cdn;
    }

    public static function get_performer_image($images, $type = 'thumb', $abs = false)
    {
        $lfmAlternative = ($type == 'thumb') ? "large" : "extralarge";
        $sale = null;
        $lfm = null;
        foreach ($images as $img):
            if ($img->pivot->size == $lfmAlternative):
                $lfm = $img->path;
            elseif ($img->pivot->size == $type):
                $sale = $img->path;
            endif;
        endforeach;
        if ($sale):
            return $sale;
        elseif ($lfm):
            return $lfm;
        else:
            if ($abs):
                return "/assets/frontend/images/no-image-main.png";
            else:
                return "https://{$_SERVER['HTTP_HOST']}/assets/frontend/images/no-image-main.png";
            endif;
        endif;

    }

    public static function get_album_image($images, $type = 'thumb')
    {
        $lfmAlternative = ($type == 'thumb') ? "large" : "extralarge";
        $sale = null;
        $lfm = null;
        foreach ($images as $img):
            if ($img->pivot->size == $lfmAlternative):
                $lfm = $img->path;
            endif;
            if ($img->pivot->size == 'thumb'):
                $sale = $img->path;
            endif;
        endforeach;
        if ($sale):
            return $sale;
        elseif ($lfm):
            return $lfm;
        else:
            return "/assets/frontend/images/no-image-{$type}.png";
        endif;

    }


    public static function get_image($images, $type = 'thumb')
    {
        switch ($type):
            case 'thumb':
                if (isset($images['sale'][$type])):
                    return $images['sale'][$type];
                elseif (isset($images['performer']['large'])):
                    return $images['performer']['large'];
                else:
                    return "/assets/frontend/images/no-image-thumb.png";
                endif;
                break;
            case 'main':
                if (isset($images['sale'][$type])):
                    return $images['sale'][$type];
                elseif (isset($images['performer']['extralarge'])):
                    return $images['performer']['extralarge'];
                else:
                    return "/assets/frontend/images/no-image-main.png";
                endif;
                break;
        endswitch;
        return "/assets/frontend/images/no-image-main.png";
    }

    public static function get_setlist_stats($sets)
    {
        $songs = array();
        if ($sets):
            foreach ($sets['sets'] as $tour):
                if (is_array($tour['sets'])):
                    foreach ($tour['sets'] as $tourset):
                        foreach ($tourset as $song):
                            if (isset($songs[$song['title']])):
                                $songs[$song['title']]++;
                            else:
                                $songs[$song['title']] = 1;
                            endif;
                        endforeach;
                    endforeach;
                endif;
            endforeach;
        else:
            return false;
        endif;
        arsort($songs);
        return $songs;
    }

    public static function get_tour_guests($performer)
    {

    }

    public static function get_past_guests($sets)
    {

        $guests = array();
        if ($sets):
            foreach ($sets['sets'] as $tour):
                if (is_array($tour['sets'])):
                    foreach ($tour['sets'] as $tourset):
                        foreach ($tourset as $song):
                            if ($song['with']):
                                if (!isset($guests[$song['with']['performer_mbz_id']]['count']))
                                    $guests[$song['with']['performer_mbz_id']]['count'] = 0;
                                $guests[$song['with']['performer_mbz_id']]['name'] = $song['with']['performer'];
                                $guests[$song['with']['performer_mbz_id']]['count']++;
                                $guests[$song['with']['performer_mbz_id']]['slug'] = (isset($song['with']['slug'])) ? $song['with']['slug'] : false;
                            endif;
                        endforeach;
                    endforeach;
                endif;
            endforeach;
        else:
            return false;
        endif;
        arsort($guests);
        $guests = array_slice($guests, 0, 10);
        return $guests;
    }

    public static function get_similar_performers($similarList, $take = 5)
    {
        return Performer::with('images')->where(function ($query) use ($similarList) {
            foreach ($similarList as $s):
                if ($s['mbz_id'] && $s['match'] > 0.3) $query->orWhere('mbz_id', $s['mbz_id']);
            endforeach;
        })->take($take)->get();
    }

    public static function get_guest_performers($guestList)
    {
        return Performer::where(function ($query) use ($guestList) {
            foreach ($guestList as $id => $s):
                $query->orWhere('mbz_id', $id);
            endforeach;
        })->take(10)->get();
    }

    public static function get_location()
    {
        $ip = Request::getClientIp();
        $currentLocation = DB::table('ip_locations')->whereRaw("INET_ATON('{$ip}') BETWEEN `start_ip`  AND `end_ip`")->first();
        if (!$currentLocation):
            $fallbackLocation = GeoIP::getLocation();
            if (!$fallbackLocation['default']):
                $newCurrLoc = new stdClass();
                $newCurrLoc->slug = StringHelper::create_slug($fallbackLocation['city'] . " " . $fallbackLocation['state']);
                return $newCurrLoc;
            else:
                return false;
            endif;
        endif;
        return $currentLocation;
    }

    public static function get_top_genre($concerts, $skip = false)
    {
        $hotGenre = [];
        $tmpGenres = [];
        if ($concerts->count()):
            foreach ($concerts as $concert):
                $genres = ($concert->genres) ? $concert->genres : $concert->genres()->get();
                foreach ($genres as $genre):
                    $k = $genre->genre;
                    if ($skip):
                        if (!in_array($k, $skip)):
                            if (!isset($tmpGenres[$k])) $tmpGenres[$k] = 0;
                            $tmpGenres[$k]++;
                        endif;
                    else:
                        if (!isset($tmpGenres[$k])) $tmpGenres[$k] = 0;
                        $tmpGenres[$k]++;
                    endif;
                endforeach;
            endforeach;
        endif;
        if (count($tmpGenres)):
            asort($tmpGenres);
            end($tmpGenres);
            $originalIndex = key($tmpGenres);
            $hotGenre['genre'] = str_replace(' / ', '/', key($tmpGenres));
            $hotGenre['count'] = $tmpGenres[$originalIndex];
            return $hotGenre;
        endif;
        return false;
    }

    public static function get_top_tracks($albums, $count = 3)
    {
        $az = [];
        $topTracks = [];
        $trackz = [];
        if ($albums->count()):
            foreach ($albums as $album):
                $tracks = ($album->tracks) ? $album->tracks : $album->tracks()->get(); // if bad data passed
                foreach ($tracks as $track):
                    $cleanTrack = trim(StringHelper::clean_song($track->name));
                    $trackz[$cleanTrack] = $track->listeners;
                endforeach;
            endforeach;

        endif;
        arsort($trackz);
        $returnVal = [];
        $i = 0;
        foreach ($trackz as $track1 => $c):
            $i++;
            if ($i <= $count):
                $returnVal[] = $track1;
            endif;
        endforeach;
        return $returnVal;
    }


    public static function get_season($start, $end, $uppercase = false)
    {
        $startMonth = date('n', strtotime($start));
        $endMonth = date('n', strtotime($end));
        $startYear = date('Y', strtotime($start));
        $endYear = date('Y', strtotime($end));
        $seasons = array(1 => 'winter', 2 => 'winter', 12 => 'winter',
            3 => 'spring', 4 => 'spring', 5 => 'spring',
            6 => 'summer', 7 => 'summer', 8 => 'summer',
            9 => 'fall', 10 => 'fall', 11 => 'fall'
        );

        if ($uppercase):
            $seasonStart = ucwords($seasons[$startMonth]);
            $seasonEnd = ucwords($seasons[$endMonth]);
        else:
            $seasonStart = $seasons[$startMonth];
            $seasonEnd = $seasons[$endMonth];
        endif;
        if ($startYear < $endYear): //over year
            $overYear = true;
        else:
            $overYear = false;
        endif;

        if ($overYear):
            $tour = $seasonStart . " of " . date('Y', strtotime($start)) . " to $seasonEnd of " . date('Y', strtotime($end));
        else:
            if ($seasonStart == $seasonEnd): //avoids Spring to Spring 2014
                $tour = $seasonEnd . " " . date('Y', strtotime($start));
            else:
                $tour = "  $seasonStart - $seasonEnd " . date('Y', strtotime($start));
            endif;
        endif;
        return $tour;
    }


    public static function normalize_band_person($performer)
    {
        $typeGroup = ['Group', '	Orchestra', 'Choir'];
        $typePerson = ['Character', 'Person'];
        $type = (in_array($performer->type, $typeGroup)) ? "Group" : (in_array($performer->type, $typePerson) ? "Person" : false);
        return $type;
    }

    public static function get_random_concerts($concerts, $randomCount = 3)
    {

        $r = [];
        $first = false;
        $last = false;
        $count = $concerts->count();
        switch ($count):
            case 1:
                $first = $concerts->first();
                $last = false;
                break;
            case 2:
                $first = $concerts->first();
                $last = $concerts->last();
                break;
            case 3:
                $first = $concerts->first();
                $r[] = $concerts[1];
                $last = $concerts->last();
                break;
            case 4:
                $first = $concerts->first();
                $r[] = $concerts[1];
                $r[] = $concerts[2];
                $last = $concerts->last();
                break;
            case 5:
                $first = $concerts->first();
                $r[] = $concerts[1];
                $r[] = $concerts[2];
                $r[] = $concerts[3];
                $last = $concerts->last();
                break;
            default:
                $first = $concerts->first();
                $last = $concerts->last();
                $i = 0;
                $tmp = [];
                foreach ($concerts as $concert):
                    $i++;
                    if ($i != 1 && $i != $count) $tmp[date('U', strtotime($concert->date))] = $concert;
                endforeach;

                //shuffle
                $keys = array_keys($tmp);
                // DebugHelper::pdd($keys, false);
                shuffle($keys);
                // DebugHelper::pdd($keys, false);
                $r = array();
                foreach ($keys as $key):
                    $r[$key] = $tmp[$key];
                endforeach;
                // DebugHelper::pdd($r, false);
                $r = array_slice($r, 0, $randomCount, true);
                // DebugHelper::pdd($r, false);
                ksort($r);
                break;
        endswitch;
        $return = new stdClass();
        $return->first = $first;
        $return->last = $last;
        $return->random = $r;
        return $return;
    }


    public static function spinner_top_album_list($topAlbums = null, $wrap = 'i')
    {
        $array = $topAlbums->toArray();
        $total = count($array);

        switch ($total):
            case 0:
                return '';
                break;
            case 1:
                $string = StringHelper::wrap($array[0]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[0]['release_date'])), $wrap);
                break;
            case 2:
                $string = StringHelper::wrap($array[0]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[0]['release_date'])), $wrap) . " and " . StringHelper::wrap($array[1]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[1]['release_date'])), $wrap);
                break;
            default:
                $string = StringHelper::wrap($array[0]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[0]['release_date'])), $wrap) . ", " . StringHelper::wrap($array[1]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[1]['release_date'])), $wrap) . " and " . StringHelper::wrap($array[2]['title'], $wrap) . " released in " . StringHelper::wrap(date('Y', strtotime($array[2]['release_date'])), $wrap);
                break;
        endswitch;
        return $string;
    }


    public static function signup($name = '', $email = '')
    {
        $email = strtolower(trim($email));
        $name = trim($name);
        if (DB::table('signups')->where('email', $email)->count()):
            return "Email $email exists!";
        else:
            DB::table('signups')->insert(array('name' => $name, 'email' => $email, 'date' => date('Y-m-d H:i:s')));
            return true;
        endif;
    }

    public static function ticketMinMax($tickets)
    {
        $data = new stdClass();
        $tmp = [];
        $qty = 0;
        foreach ($tickets as $ticket):
            $qty += $ticket->TicketQuantity;
            $tmp[] = $ticket->ActualPrice;
        endforeach;
        asort($tmp);
        $data->minPrice = array_values($tmp)[0];
        $data->maxPrice = end($tmp);
        $data->quantity = $qty;
        return $data;
    }

    public static function addPercent($price, $percent = 5)
    {
        $percent = $percent / 100;
        $added = ($price * $percent);
        $toPay = $price + $added;
        return $toPay;
    }
}