<?php


class Spinner
{

    public $type;
    public $gender;
    public $started;


    public static function getText($type, $data = null)
    {
        switch ($type) {
            case 'pv':

                $currentText = DB::table('page_texts')->where('concert_slug', $data['slug'])
                    ->where('venue_id', $data['venue_id'])
                    ->where('type', 'pv')
                    ->first();
                if (!$currentText):

                    $performerVenueText = Spinner::performer_venue_text($data['performer_id'], $data['venue_id'], $data['slug']);
                    $insertData = [
                        'performer_id' => $data['performer_id'],
                        'venue_id' => $data['venue_id'],
                        'type' => 'pv',
                        'concert_slug' => $data['slug'],
                        'expire' => isset($performerVenueText->expire) ? $performerVenueText->expire : '00:00:00',
                        'text' => $performerVenueText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);

                    return $performerVenueText->text;
                else:
                    return $currentText->text;
                endif;
                break;
            case 'pb';
                $currentText = DB::table('page_texts')->where('performer_id', $data['performer_id'])
                    ->where('type', 'pb')
                    ->first();
                if (!$currentText):

                    $performerBioText = Spinner::performer_bio_text($data['performer_id']);
                    $insertData = [
                        'performer_id' => $data['performer_id'],
                        'type' => 'pb',
                        'expire' => $performerBioText->expire,
                        'text' => $performerBioText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);

                    return $performerBioText->text;
                else:
                    return $currentText->text;
                endif;
                break;
            case 'pd':
                $currentText = DB::table('page_texts')->where('performer_id', $data['performer_id'])
                    ->where('type', 'pd')
                    ->first();
                if (!$currentText):

                    $performerDiscographyText = Spinner::performer_discography($data['performer_id']);
                    $insertData = [
                        'performer_id' => $data['performer_id'],
                        'type' => 'pd',
                        'expire' => $performerDiscographyText->expire,
                        'text' => $performerDiscographyText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);

                    return $performerDiscographyText->text;
                else:
                    return $currentText->text;
                endif;
                break;
            case 'pt':
                $currentText = DB::table('page_texts')->where('performer_id', $data['performer_id'])
                    ->where('type', 'pt')
                    ->first();
                if (!$currentText):

                    $performerTourText = Spinner::performer_tour_dates($data['performer_id']);
                    $insertData = [
                        'performer_id' => $data['performer_id'],
                        'type' => 'pt',
                        'expire' => $performerTourText->expire,
                        'text' => $performerTourText->text,
                        'custom' => false,
                    ];
                    DB::table('page_texts')->insert($insertData);

                    return $performerTourText->text;
                else:
                    return $currentText->text;
                endif;
                break;
            case 'citytext':
                $currentText = DB::table('city_text')->where('location_id', $data['location_id'])->first();
                if (!$currentText):

                    $cityText = Spinner::city_text($data['location_id']);
                    $insertData = [
                        'location_id' => $data['location_id'],
                        'expire' => $cityText->expire,
                        'text' => $cityText->text,
                        'custom' => false,
                    ];
                    DB::table('city_text')->insert($insertData);

                    return $cityText->text;
                else:
                    return $currentText->text;
                endif;
                break;
            default:
                return "Currently no information available! Check back later!";
                break;
        }
    }

    public static function performer_venue_text($performerId = null, $venueId = null, $concertSlug = null)
    {
        $returnData = new stdClass();


        $concertInVenue = Concert::where('slug', $concertSlug)
            ->where('date', '>', date('Y-m-d'))
            ->where('venue_id', $venueId)
            ->with('performers')
            ->with('venue')
            ->with('venue.tnVenue')
            ->with('tickets')
            ->with('location')
            ->orderBy('date', 'desc')
            ->get();
        $firstConcert = $concertInVenue->first();
        $state = isset($firstConcert->location->state) ? $firstConcert->location->state : ''; //spinner failes here
        $fullState = $firstConcert->location->state_full;
        $city = $firstConcert->location->city;
        $venue = $firstConcert->venue;
        $eventCount = $concertInVenue->count();
        $concertInStates = Concert::where('slug', $concertSlug)
            ->where('date', '>', date('Y-m-d H:i:s'))
            ->where('venue_id', '<>', $venueId)
            ->with('performers')
            ->with('venue')
            ->with('tickets')
            ->whereHas('location', function ($query) use ($state) {
                $query->where('state', $state);
            })
            ->orderBy('date', 'desc')
            ->get();

        $performers = $concertInVenue->first()->performers()->orderBy('plays', 'desc')->get();
        $pCount = $performers->count();

        switch ($pCount):
            case 0:
                return false;
                # code...
                break;
            case 1:
                $performer = $performers->first();
                $gender = $performer->gender;
                if ($gender == 'male'):
                    $hisHer = 'his';
                elseif ($gender == 'female'):
                    $hisHer = 'her';
                else:
                    $hisHer = 'their';
                endif;
                $type = VarsHelper::normalize_band_person($performer);
                //TODO: IF ENDS ON THE S MAKE IT R
                $hasHave = ($type == 'Person') ? 'has' : 'will have';
                $hasHave = ($type == 'Group') ? 'have' : 'will have';
                $isAre = ($type == 'Person') ? 'is' : '';
                $isAre = ($type == 'Group') ? 'are' : 'is';
                $himHerThem = 'them';
                if ($type == 'Person' && ($gender == 'male' || $gender == 'female')):
                    $himHerThem = ($gender == 'male') ? "him" : "her";
                endif;
                $performerString = "<a href='/concerts/{$performer->slug}'>{$performer->name}</a>";
                $performerStringS = StringHelper::properize($performer->name);
                break;
            case 2:
                $performer1 = $performers[0];
                $performer2 = $performers[1];
                $performerString = "<a href='/concerts/{$performer1->slug}'>{$performer1->name}</a> and <a href='/concerts/{$performer2->slug}'>{$performer2->name}</a>";
                $hasHave = "have";
                $isAre = "are";
                $himHerThem = "them";
                $performerStringS = "their";
                $hisHer = "their";
                break;
            default: //three or more
                //grab first three
                $performer1 = $performers[0];
                $performer2 = $performers[1];
                $performer3 = $performers[2];
                $performerString = "<a href='/concerts/{$performer1->slug}'>{$performer1->name}</a>, <a href='/concerts/{$performer2->slug}'>{$performer2->name}</a> and <a href='/concerts/{$performer3->slug}'>{$performer3->name}</a>";
                $hasHave = "have";
                $isAre = "are";
                $himHerThem = "them";
                $performerStringS = "their";
                $hisHer = "their";
                break;
        endswitch;


        $returnData->text = '';
        $sentence1 = "";
        $sentence2 = "";
        $sentence3 = "";
        $sentence4 = "";
        $sentence5 = "";
        $sentence6 = "";
        $sentence7 = "";
        $sentence8 = "";
        $sentence9 = "";
        $disclaimer = "";
        $expire = $firstConcert->date;
        $date = date('F j', strtotime($expire));
        $time = date('g:i A', strtotime($expire));
        // dd($venue);
        $venueAddress = '';
        if ($venue->tnVenue):
            $venueAddress = "{$venue->tnVenue->street_1}
						{$venue->tnVenue->street_2},
						{$venue->tnVenue->city},
						{$venue->tnVenue->state}
						{$venue->tnVenue->zip}";
        endif;
        $returnData->expire = $expire;

        $nextConcert = ($concertInStates->first()) ? $concertInStates->first() : false;

        // PART I
        $sentence1 = Spinner::_performer_venue('1', '1');
        $sentence1 = str_replace('{city}', $city, $sentence1);
        $sentence1 = str_replace('{date}', $date, $sentence1);
        $sentence2 = Spinner::_performer_venue('1', '2');
        //
        $sentence2 = str_replace('{venue_address}', $venueAddress, $sentence2);

        if ($time == "3:30 AM"):
            $sentence3 = "The concert start time is yet to be announced";
        else:
            $sentence3 = Spinner::_performer_venue('1', '3');
            $sentence3 = str_replace('{concert_time}', $time, $sentence3);
        endif;
        // PART II
        if ($nextConcert):
            $sentence4 = Spinner::_performer_venue('2', '1a');
            $sentence4 = str_replace('{city}', $city, $sentence4);
            $sentence5 = Spinner::_performer_venue('2', '2a');
            $sentence5 = str_replace('{city}', $city, $sentence5);
            $sentence5 = str_replace('{event_count}', $eventCount, $sentence5);
            $sentence5 = str_replace('{has_have}', $hasHave, $sentence5);
            $sentence6 = Spinner::_performer_venue('2', '3a');
            $concertString = date('F j', strtotime($nextConcert->date));
            $sentence6 = str_replace('{concert_dates_list}', $concertString, $sentence6);
            $sentence6 = str_replace('{is_are}', $isAre, $sentence6);
            $sentence6 = str_replace('{has_have}', $hasHave, $sentence6);
        else:
            $sentence4 = Spinner::_performer_venue('2', '1b');
            $sentence4 = str_replace('{city}', $city, $sentence4);
            $sentence4 = str_replace('{his_her}', $hisHer, $sentence4);
            $sentence4 = str_replace('{has_have}', $hasHave, $sentence4);
            $sentence5 = Spinner::_performer_venue('2', '2b');
            $sentence5 = str_replace('{date}', date('F j', strtotime($firstConcert->date)), $sentence5);

        endif;
        // PART III
        $ticketCount = 0;
        $prices = [];
        // $highPrice = [];
        foreach ($concertInVenue as $vc):
            foreach ($vc->tickets as $ticket):
                $ticketCount += $ticket->quantity;
                $prices[] = $ticket->actual_price;
            endforeach;
        endforeach;
        if ($ticketCount):
            asort($prices);
            $lowPrice = "\$" . $prices[0];
            $highPrice = "\$" . end($prices);
        else:
            $lowPrice = "\$" . rand(60, 100);
            $highPrice = "\$" . rand(130, 600);
            $ticketCount = "many";
        endif;

        // $ticketCount = (isset($ticket->quantity)) ? $ticket->quantity : rand(10,100);
        $lowSeat = "general admission";
        $highSeat = "premium";


        $sentence7 = Spinner::_performer_venue('3', '1');
        $sentence7 = str_replace('{low_price}', $lowPrice, $sentence7);
        $sentence7 = str_replace('{high_price}', $highPrice, $sentence7);
        $sentence7 = str_replace('{low_section}', $lowSeat, $sentence7);
        $sentence7 = str_replace('{high_section}', $highSeat, $sentence7);
        $sentence7 = str_replace('{ticket_count}', $ticketCount, $sentence7);

        // PART IV
        if ($concertInStates->count()):
            $stateConcert = $concertInStates->first();
            $sentence8 = Spinner::_performer_venue('4', '1');
            $sentence8 = str_replace('{full_state}', $fullState, $sentence8);
            $sentence8 = str_replace('{other_city}', $stateConcert->location->city, $sentence8);
            $sentence8 = str_replace('{other_venue}', $stateConcert->venue->name, $sentence8);

            $sentence9 = Spinner::_performer_venue('4', '2');
            $sentence9 = str_replace('{other_city}', $stateConcert->location->city, $sentence9);
            $sentence9 = str_replace('{other_date}', date('F j', strtotime($stateConcert->date)), $sentence9);
        endif;

        // PART V

        $disclaimer = Spinner::_performer_venue('5', 'disclaimer');
        $disclaimer = str_replace('{date}', date('F j', strtotime($firstConcert->date)), $disclaimer);
        // $disclaimer = str_replace('{sale_phone}', $phone, $disclaimer);


        //SPIN AND SPIT
        $sentence1 = Spinner::spin($sentence1);
        $sentence2 = Spinner::spin($sentence2);
        $sentence3 = Spinner::spin($sentence3);
        $sentence4 = Spinner::spin($sentence4);
        $sentence5 = Spinner::spin($sentence5);
        $sentence6 = Spinner::spin($sentence6);
        $sentence7 = Spinner::spin($sentence7);
        $sentence8 = Spinner::spin($sentence8);
        $sentence9 = Spinner::spin($sentence9);
        $disclaimer = Spinner::spin($disclaimer);
        $text = $sentence1 . " " . $sentence2 . " " . $sentence3 . " " . $sentence4 . " " . $sentence5 . " " . $sentence6 . " " . $sentence7 . " " . $sentence8 . " " . $sentence9 . " " . $disclaimer;
        $text = str_replace('{performer}', $performerString, $text);
        $text = str_replace('{venue}', $venue->name, $text);
        $text = str_replace('{him_her_them}', $himHerThem, $text);
        $text = str_replace("{performer's}", $performerStringS, $text);
        $returnData->text = $text;
        return $returnData;
    }

    public static function _performer_venue($part = null, $index = null)
    {

        $sentences = [
            '1' => [
                '1' => [
                    "Get {{ready|excited|pumped}} {city}! {venue} will {{host|welcome}} {performer} on {date}.",
                    "{city}, {{it’s time|be ready}} to get {{excited|energized}} about the upcoming {performer} {{concert|event|performance}} at {venue}.",
                    "{{Hey|Howdy}} {city}! Are you {{ready|excited|pumped}} {{to see|to attend|to get to see|to go see}} {performer} at {venue}?",
                ],
                '2' => [
                    "{{You will not|You won’t}} want to miss {{this concert|this performance|this event}}. {venue} {{is located at|can be found at}} {venue_address} and we have {{driving directions|map}} {{available|listed}} {{here|below|on this page}}.",
                    "{{Fans|Loyal fans}} {{do not want|will not want}} to miss this {{concert|concert|event}}! The address to the {venue} is {venue_address} and {{you can find|there are}} {{driving directions|maps}} below.",
                    "{{This is|This {{concert|event}} is}} a must-see performance and {{you won't want to miss it|should not miss it|shouldn't miss the opportunity to {{see it|attend|go see it}}}}! The address to {venue} is {venue_address} and {{driving directions|maps}} {{can be found|are located}} below."
                ],
                '3' => [
                    "The concert starts at {concert_time}.",
                    "Event starts at {concert_time}.",
                ]
            ],
            '2' => [
                '1a' => [
                    "{{Get ready|Get pumped|Be excited}} {performer} {{fans|concertgoers}} in {city} because we have  {{amazing|great|wonderful|hopeful}} news! ",
                    "{{Good|Great|Wonderful|Exciting}} news for {performer's} {{fans|concertgoers}} in {city}.",
                    "{city} {{fans|concertgoers}} {{get ready for|listen for|check out}} {{the|some}} {{good|amazing|great|awesome|wonderful}} news! ",

                ],
                '2a' => [
                    "{event_count} {{events|concerts|performances|shows}} {{were|have been|are}} {{scheduled|booked|penciled-in}} for {city}.",
                    "{event_count} {performer's} {{events|concerts|performances|shows}} {{will|are going to|are set to|are scheduled to|are penciled in to|are booked to}} be {{held|hosted}} in {city}.",
                    "{{Currently|Right now|As of today}}, {performer} {has_have} {event_count} {{events|concerts|performances|shows}} in {city}.",

                ],
                '3a' => [
                    "{{Fans|Concertgoers}} {{will have|will get|will get|have|are given}} {{an opportunity|a chance}} {{to see|to attend|to witness}} {performer} on {concert_dates_list}.",
                    "{performer} {{will have|{is_are} {{going|planning|scheduled|booked}} to have}} {{events|concerts|performances|shows}} on {concert_dates_list}.",
                    "{performer} {{will have|{{scheduled|booked|set}}}} {{more|additional|other}} {{events|concerts|performances|shows}} on {concert_dates_list}.",

                ],
                '1b' => [

                    "{{Keep in mind|Don't forget|Remember}} that {performer} {has_have} {{only one|just one|one single}} {{event|concert|performance|show}} in {city}!",
                    "The {{sad|bad}} news is this {{event|concert|performance|show}} is {{the only|the single}} {performer} {{event|concert|performance|show}} {{in your city|near you|near your city}}.",
                    "{{Don't forget|Remember|Keep in mind}} {{you will only have|you're given only|you have only}} one {{chance|opportunity}} {{to attend|to see|to watch}} {performer} in {city}!",
                ],

                '2b' => [
                    "The {{anticipated|long awaited}} {{event|concert|performance|show}} at {venue} on {date} will probably sell out very soon.",
                    "This {{only|single}} {{event|concert|performance|show}} at {venue} on {date} {{will|is going to}} {{imminently|most likely|inevitably}} sell out.",
                    "Tickets {{are going to|will most likely|are going to|are likely to}} sell out as fans {{are rushing|are in a rush|are hurrying|will be rushing}} {{to get|to buy|to purchase}} tickets for {performer's} {{event|concert|performance|show}} on {date} in {venue}.",

                ],
            ],
            '3' => [
                '1' => [
                    "{{There are {ticket_count} tickets|About {ticket_count} tickets are}} {{available|released|posted}} for {performer's} {{event|concert|performance|show}}. {{The cheapest ticket|The lowest priced ticket}} is {low_price} while {{the best seats|VIP seats|luxury seats}} {{start|begin}} at {high_price}.",
                    "{{Currently|Today|As of today|Right now}} {ticket_count} tickets {{are on sale|are posted for sale|are listed for sale}} {{starting at|for as low as|beginning at}} {low_price} {{and range|while others range}} {{all the way up to|upwards of}} {high_price}.",
                    "{{Hurry|Be quick|Move fast|Act now}} because there are only {ticket_count} {{tickets left|tickets available}}! These {{hot|nearly sold out|almost-gone}} tickets won’t last long. Ticket {{choices|options|selections}} start at {low_price}. {{You can sit in|You can get}} {{a better spot|premium seats}} for {high_price}.",
                ]
            ],
            '4' => [
                '1' => [
                    "{{Can't go see|Won’t be able to see}} this {{event|concert|performance|show}}? For {{the true|real|loyal}} {performer} fans {{living|residing}} in {full_state} {{there will be|there is}} {{at least one more|another}} {{event|concert|performance|show}} in {other_city} at {other_venue}.",
                    "{{Unable to attend|Can't go}}? {{ {{Loyal|Enthusiastic|Diehard}} fans of {performer}|{performer's}{{loyal|diehard|true|loyal}} fans}} {{will get|are given|have}} another chance to see {him_her_them} in {full_state} in {other_city} hosted by {other_venue}. ",
                    "Can't see this {{event|concert|performance|show}}? {performer} {{decided to please|surprised}} {his_her_their} {{loyal|diehard|true|loyal}} fans {{with|by having}} another {full_state} {{event|concert|performance|show}} in {other_city} at {other_venue}.",
                ],
                '2' => [
                    "{{Be|Get}} ready {{to take|to go on}} a {{short|fun|brief|exciting}} road trip to {other_city} and {{see|attend another concert for|witness}} {performer} on {other_date}.",
                    "{{Guess it is time|It may be time}} for a {{exciting|brief|fun|much awaited|much needed}} road trip to {{see|attend another concert for|witness}}  {performer} in {other_city} on {other_date}.",
                    "Start {{planning|getting excited for}} a {{short|fun|brief|exciting|quick}} trip to {other_city} on {other_date}. You {{will get|are getting}} {{another|at least one more|one more}} chance to {{see|attend another concert for|witness}}  {performer}.",
                ]
            ],
            '5' => [
                'disclaimer' =>
                    [
                        "All {performer} {venue} tickets come with a 100% money back guarantee so purchase with confidence. If you have questions you can call us at {sale_phone}. Be sure to follow <a target=’_blank’ href='https://www.twitter.com/site' rel=’no follow’>@site</a> on twitter."
                    ]
            ]
        ];


        return $sentences[$part][$index][array_rand($sentences[$part][$index])];

    }

    /********************* SPINNNER **********************/

    public static function spin($string, $seedPageName = false, $openingConstruct = '{{', $closingConstruct = '}}', $separator = '|')
    {

        # If we have nothing to spin just exit
        if (strpos($string, $openingConstruct) === false) {
            return $string;
        }

        # Find all positions of the starting and opening braces
        $startPositions = Spinner::strpos_all($string, $openingConstruct);
        $endPositions = Spinner::strpos_all($string, $closingConstruct);

        # There must be the same number of opening constructs to closing ones
        if ($startPositions === false OR count($startPositions) !== count($endPositions)) {
            return $string;
        }

        # Optional, always show a particular combination on the page
        if ($seedPageName) {
            mt_srand(crc32($_SERVER['REQUEST_URI']));
        }

        # Might as well calculate these once
        $openingConstructLength = mb_strlen($openingConstruct);
        $closingConstructLength = mb_strlen($closingConstruct);

        # Organise the starting and opening values into a simple array showing orders
        foreach ($startPositions as $pos) {
            $order[$pos] = 'open';
        }
        foreach ($endPositions as $pos) {
            $order[$pos] = 'close';
        }
        ksort($order);

        # Go through the positions to get the depths
        $depth = 0;
        $chunk = 0;
        foreach ($order as $position => $state) {
            if ($state == 'open') {
                $depth++;
                $history[] = $position;
            } else {
                $lastPosition = end($history);
                $lastKey = key($history);
                unset($history[$lastKey]);

                $store[$depth][] = mb_substr($string, $lastPosition + $openingConstructLength, $position - $lastPosition - $closingConstructLength);
                $depth--;
            }
        }
        krsort($store);

        # Remove the old array and make sure we know what the original state of the top level spin blocks was
        unset($order);
        $original = $store[1];

        # Move through all elements and spin them
        foreach ($store as $depth => $values) {
            foreach ($values as $key => $spin) {
                # Get the choices
                $choices = explode($separator, $store[$depth][$key]);
                $replace = $choices[mt_rand(0, count($choices) - 1)];

                # Move down to the lower levels
                $level = $depth;
                while ($level > 0) {
                    foreach ($store[$level] as $k => $v) {
                        $find = $openingConstruct . $store[$depth][$key] . $closingConstruct;
                        if ($level == 1 AND $depth == 1) {
                            $find = $store[$depth][$key];
                        }
                        $store[$level][$k] = Spinner::str_replace_first($find, $replace, $store[$level][$k]);
                    }
                    $level--;
                }
            }
        }

        # Put the very lowest level back into the original string
        foreach ($original as $key => $value) {
            $string = Spinner::str_replace_first($openingConstruct . $value . $closingConstruct, $store[1][$key], $string);
        }

        return $string;
    }

    private static function strpos_all($haystack, $needle)
    {
        $offset = 0;
        $i = 0;
        $return = false;

        while (is_integer($i)) {
            $i = mb_strpos($haystack, $needle, $offset);

            if (is_integer($i)) {
                $return[] = $i;
                $offset = $i + mb_strlen($needle);
            }
        }

        return $return;
    }

    private static function str_replace_first($find, $replace, $string)
    {
        # Ensure we are dealing with arrays
        if (!is_array($find)) {
            $find = array($find);
        }

        if (!is_array($replace)) {
            $replace = array($replace);
        }

        foreach ($find as $key => $value) {
            if (!empty($value)) {
                if (($pos = mb_strpos($string, $value)) !== false) {
                    # If we have no replacement make it empty
                    if (!isset($replace[$key])) {
                        $replace[$key] = '';
                    }

                    $string = mb_substr($string, 0, $pos) . $replace[$key] . mb_substr($string, $pos + mb_strlen($value));
                }
            }
        }

        return $string;
    }

    public static function performer_bio_text($performerId = null)
    {
        $returnData = new stdClass();
        $returnData->text = '';
        $returnData->expire = date('Y-m-d', strtotime("+1 week"));
        $sentence1 = "";
        $sentence2 = "";
        $sentence3 = "";
        $sentence4 = "";
        $sentence5 = "";
        $possesive = 'their';
        $years = 'many';

        $performer = Performer::with('upcoming_concerts')
            ->with('upcoming_concerts.tnConcert')
            ->with('upcoming_concerts.venue')
            ->with('upcoming_concerts.location')
            ->with('upcoming_concerts.venue.tnVenue')
            ->with('concerts')
            ->with('concerts.location')
            ->with('concerts.genres')
            ->where('id', $performerId)->remember(20)->first();


        $concerts = $performer->concerts()->with('venue')->with('location')->with('genres')->orderBy('date', 'ASC')->get();
        $genres = VarsHelper::get_top_genre($concerts);

        if ($performer->gender):
            $possesive = ($performer->gender == 'male') ? "his" : "her";
        endif;

        $genre = (isset($genres['genre'])) ? $genres['genre'] : "{$possesive} genre's";

        $albums = $performer->albums()
            ->with('tracks')
            ->where('release_date', '<>', '0000-00-00 00:00:00');
        // ->orderBy('release_date','ASC')
        // ->get();

        $firstAlbum = $albums->orderBy('release_date', 'ASC')->first();

        $topAlbum = $albums->orderBy('play_count', 'DESC')->first();
        $allAlbums = $performer->albums()->with('tracks')->get();

        $type = VarsHelper::normalize_band_person($performer);
        $founded = false;
        $fresh = true;
        $collabString = false;
        $breakCount = 0;
        if ($type === "Group"):
            $founded = strtotime($performer->formed) ? date('Y', strtotime($performer->formed)) : false;
        endif;
        if (!$founded):
            if ($firstAlbum):
                $founded = date('Y', strtotime($firstAlbum->release_date));
            endif;
        endif;
        if ($founded):
            $years = (date('Y') - $founded);
            $fresh = ($years < 7) ? true : false;
        else:
            $fresh = true;
        endif;
        $performerSets = PerformerSets::where(["performer_id" => $performer->id])->first();//->toArray();//first(['performer_id'=> $performer->id]);
        if ($performerSets) $performerSets = $performerSets->toArray();
        $collaborators = VarsHelper::get_past_guests($performerSets);
        if ($collaborators):
            array_slice($collaborators, 0, 3);
            $tmp = [];
            foreach ($collaborators as $c) :
                $tmp[] = $c['name'];
            endforeach;
            switch (count($collaborators)):
                case 1:
                    $collabString = "<i>$tmp[0]</i>";
                    break;
                case 2:
                    $collabString = "<i>$tmp[0]</i> and <i>$tmp[1]</i>";
                    break;
                case 3:
                default:
                    $collabString = "<i>$tmp[0]</i>, <i>$tmp[1]</i> and <i>$tmp[2]</i>";
                    break;
            endswitch;
        endif;

        //past tours
        $pastTours = PerformerSets::where('performer_id', $performer->id)->get();
        $pastDates = [];
        if (count($pastTours)):
            foreach ($pastTours as $tour):
                if ($tour->sets):
                    foreach ($tour->sets as $concert):
                        if (strpos($concert['date'], date('Y')) === false): //avoid sets from this tour
                            $pastDates[] = date('U', strtotime($concert['date']));
                        endif;
                    endforeach;
                endif;
            endforeach;
            asort($pastDates);
            if (count($pastDates)):
                $lastPlay = end($pastDates);
                $breakCount = (date('U') - $lastPlay) / 60 / 60 / 24 / 365;
            else:
                $breakCount = 0;
            endif;
        else:
            $breakCount = 0;
        endif;

        //PART I, Sentence 1
        switch ($concerts->count()):
            case 0: //nada
                $sentence1 = Spinner::_bio_vars('1', '1c');
                // $sentence1 = str_replace('{performer}', $performer->name, $sentence1);

                break;
            case 1: //only concert
                $firstConcert = $concerts->first();
                $returnData->expire = $firstConcert->date;

                $sentence1 = Spinner::_bio_vars('1', '1b');
                // $sentence1 = str_replace('{performer}', $performer->name, $sentence1);
                $sentence1 = str_replace('{first_city}', $firstConcert->location->city, $sentence1);
                $sentence1 = str_replace('{first_date}', date('F j', strtotime($firstConcert->date)), $sentence1);
                $sentence1 = str_replace('{first_venue}', $firstConcert->venue->name, $sentence1);
                $sentence1 = str_replace('{season}', date('Y', strtotime($firstConcert->date)), $sentence1);
                break;
            default: //we have couple
                $firstConcert = $concerts->first();
                $lastConcert = $concerts->last();
                $returnData->expire = $lastConcert->date;

                $sentence1 = Spinner::_bio_vars('1', '1a');
                // $sentence1 = str_replace('{performer}', $performer->name, $sentence1);
                $sentence1 = str_replace('{first_city}', $firstConcert->location->city, $sentence1);
                $sentence1 = str_replace('{first_venue}', $firstConcert->venue->name, $sentence1);

                $sentence1 = str_replace('{first_date}', date('F j', strtotime($firstConcert->date)), $sentence1);
                $sentence1 = str_replace('{last_city}', $lastConcert->location->city, $sentence1);
                $sentence1 = str_replace('{last_venue}', $lastConcert->venue->name, $sentence1);
                $sentence1 = str_replace('{last_date}', date('F j', strtotime($lastConcert->date)), $sentence1);

                $season = VarsHelper::get_season($firstConcert->date, $lastConcert->date);
                $sentence1 = str_replace('{season}', $season, $sentence1);
                break;
        endswitch;

        // PART I Sentence 2 - the random stops on tour
        if ($concerts->count() > 2):
            $sentence2Data = VarsHelper::get_random_concerts($concerts);
            $randomList = '';
            if ($sentence2Data->random):
                $evz = [];
                foreach ($sentence2Data->random as $ran):
                    $evz[] = $ran->location->city . " on " . date('F j', strtotime($ran->date));
                endforeach;
                $randomList = StringHelper::create_list($evz, 'i', true);
                $sentence2 = Spinner::_bio_vars('1', '2a');
                $sentence2 = str_replace('{random_events_sentence}', $randomList, $sentence2);
            else: // it has two or less concerts
                $sentence2 = Spinner::_bio_vars('1', '2b');
            endif;
        endif;
        // PART II Fresh or Old
        if ($fresh)://2b
            //Sentence 1
            if ($firstAlbum):
                $fatrakcs = $firstAlbum->tracks()->first();
                if ($fatrakcs):
                    $sentence3 = Spinner::_bio_vars('2b', '1a');
                    $firstAlbumTopTrack = $firstAlbum->tracks()->orderBy('playcount', 'DESC')->first();

                    $sentence3 = str_replace('{first_album_year}', date('Y', strtotime($firstAlbum->release_date)), $sentence3);
                    $sentence3 = str_replace('{first_album}', StringHelper::wrap($firstAlbum->title, 'i'), $sentence3);
                    $sentence3 = str_replace('{first_album_top_song}', StringHelper::wrap($firstAlbumTopTrack->name, '"', false), $sentence3);
                else:
                    $sentence3 = Spinner::_bio_vars('2b', '1b');
                endif;
            else:
                $sentence3 = Spinner::_bio_vars('2b', '1b');
            endif;
            //Sentence 2
            if ($topAlbum && $collabString): //we have colabs and albums

                $topYear = date('Y', strtotime($topAlbum->release_date));
                $sentence4 = Spinner::_bio_vars('2b', '2a');
                $sentence4 = str_replace('{collaborators}', $collabString, $sentence4);
                $sentence4 = str_replace('{top_album_year}', $topYear, $sentence4);
                $sentence4 = str_replace('{top_album}', StringHelper::wrap($topAlbum->title, 'i'), $sentence4);

            elseif ($topAlbum): //we don't have colabs
                $topSongs = VarsHelper::get_top_tracks($allAlbums);
                $topSongsString = StringHelper::create_list($topSongs);
                if ($topSongsString):
                    $sentence4 = Spinner::_bio_vars('2b', '2c');
                    $sentence4 = str_replace('{top_songs}', $topSongsString, $sentence4);
                endif;
            else: //we got nothing
                $sentence4 = Spinner::_bio_vars('2b', '2b');
            endif;
        else: //old artist 2a
            //Sentence 1
            if ($firstAlbum):
                $firstAlbumTopTrack = $firstAlbum->tracks()->orderBy('playcount', 'DESC')->first();
                if ($firstAlbumTopTrack):
                    $sentence3 = Spinner::_bio_vars('2a', '1a');

                    $sentence3 = str_replace('{first_album_year}', date('Y', strtotime($firstAlbum->release_date)), $sentence3);
                    $sentence3 = str_replace('{year_count}', $years, $sentence3);
                    $sentence3 = str_replace('{first_album}', StringHelper::wrap($firstAlbum->title, 'i'), $sentence3);
                    $sentence3 = str_replace('{first_album_top_song}', StringHelper::wrap($firstAlbumTopTrack->name, '"', false), $sentence3);
                    $genreType = ['Comedy', 'Other', 'Las Veggas Shows', 'Classical', 'Children / Family', 'Performance Series', 'Festival / Tour'];
                    $songTracks = (in_array($genre, $genreType)) ? "pieces" : "{{songs|tracks|tunes}}";
                    $sentence3 = str_replace('{songs_tracks}', $songTracks, $sentence3);
                endif;


            else:
                $sentence3 = Spinner::_bio_vars('2a', '1b');
            endif;
            //Sentence 2
            if ($topAlbum && $collabString): //we have colabs and albums
                $topSongs = VarsHelper::get_top_tracks($allAlbums);
                $topSongsString = StringHelper::create_list($topSongs);

                $topYear = date('Y', strtotime($topAlbum->release_date));
                $sentence4 = Spinner::_bio_vars('2a', '2a');
                $sentence4 = str_replace('{collaborators}', $collabString, $sentence4);
                $sentence4 = str_replace('{top_album_year}', $topYear, $sentence4);
                $sentence4 = str_replace('{top_album}', StringHelper::wrap($topAlbum->title, 'i'), $sentence4);
                $sentence4 = str_replace('{top_songs}', $topSongsString, $sentence4);

            elseif ($topAlbum): //we don't have colabs
                $topSongs = VarsHelper::get_top_tracks($allAlbums);
                $topSongsString = StringHelper::create_list($topSongs);
                $sentence4 = Spinner::_bio_vars('2a', '2c');
                $sentence4 = str_replace('{top_songs}', $topSongsString, $sentence4);
            else: //we got nothing
                $sentence4 = Spinner::_bio_vars('2a', '2b');
            endif;
        endif;


        // part III last sentence
        if ($firstAlbum && $topAlbum): //has albums
            $firstAlbumTopTrack = $firstAlbum->tracks()->orderBy('playcount', 'DESC')->first();
            $topAlbumTopTrack = $topAlbum->tracks()->orderBy('playcount', 'DESC')->first();
            if (isset($firstAlbumTopTrack->id) && isset($topAlbumTopTrack->id)):
                if ($firstAlbumTopTrack->id === $topAlbumTopTrack->id): //songs are the same
                    $sentence5 = Spinner::_bio_vars('3b', '1');
                else:
                    $sentence5 = Spinner::_bio_vars('3a', '1');
                endif;
                $sentence5 = str_replace('{top_album_year}', date('Y', strtotime($topAlbum->release_date)), $sentence5);
                $sentence5 = str_replace('{top_song}', StringHelper::wrap($topAlbumTopTrack->name, '"', false), $sentence5);
                $sentence5 = str_replace('{top_album_song}', StringHelper::wrap($topAlbumTopTrack->name, '"', false), $sentence5);
                $sentence5 = str_replace('{top_album}', StringHelper::wrap($topAlbum->title, 'i'), $sentence5);
            endif;
        endif;

        $sentence1 = Spinner::spin($sentence1);
        $sentence2 = Spinner::spin($sentence2);
        $sentence3 = Spinner::spin($sentence3);
        $sentence4 = Spinner::spin($sentence4);
        $sentence5 = Spinner::spin($sentence5);
        $text = $sentence1 . " " . $sentence2 . " <br><br> " . $sentence3 . " " . $sentence4 . " " . $sentence5;
        $text = str_replace('{performer}', $performer->name, $text);
        $text = str_replace("{performer's}", StringHelper::properize($performer->name), $text);
        $text = str_replace('{genre}', $genre, $text);
        $text = str_replace('{possesive}', $possesive, $text);


        $returnData->text = $text;

        return $returnData;
    }

    private static function _bio_vars($part = false, $index = false)
    {
        if (!$index || !$part) return '';
        /**** NOTES
         *
         *
         * [1][1a] -sentence 1 with at least 2 concerts
         * [1][1b] -sentence 1 with only one concert
         * [1][1c] -no events
         *
         * [1][2a] -sentence 2 with at least 1 other concerts besides start and end
         * [1][2b] -sentence 2 wihtout any onther concerts in between
         * [1][2c] - no events
         *
         *
         * a = IF artists has been around 10 or more years
         * b = Upcoming artists < 10 years
         * [2a/b][1a] -sentence 1 with performer having albums
         * [2a/b][1b] -sentence 1 without any albums
         * [2a/b][2c] -sentence 1 without collabs but with albums
         *
         * [2a/b][2a] -sentence 2 with performer having albums AND collaborators
         * [2a/b][2b] -sentence 2 without any albums (and no collaborators)
         * [2a/b][2c] -sentence 3 without collaborators but with albus
         *
         * [3a][1] - if top song is not the same as first album top song
         * [3b][1] - top song is the same as first album song
         ******************/

        $sentences =
            [
                '1' => [
                    '1a' =>
                        [
                            "The {{long|lengthy}} wait for a {performer} tour is {{over|no more|finally over}}. {{Recently|Not long ago|A Few weeks ago|A little while back}}, {performer} {{announced|revealed}} a {{awaited|anticipated|expected}} {season} tour {{beginning|starting|kicking of}} in {first_city} on {first_date} at {first_venue} and {{ending on|closing on|finishing on}} {last_date} in {last_city} at {last_venue}.",

                            "A {{new|brand new}} {season} {{{{tour|schedule}} has {{finally|}} been {{announced|revealed|released}}|was {{announced|revealed|released}}}} by {performer} and it {{begins|starts|kicks off}} in {first_city} on {first_date} at {first_venue} and {{ends on|closes on|finishes on}} {last_date} in {last_city} at {last_venue}.",

                            "{performer} {{has recently|recently}} excited {{fans|concertgoers}} with a long {{awaited|anticipated}} {season} {{tour|tour schedule}} {{beginning|starting|kicking off}} in {first_city} on {first_date} at {first_venue} and {{ending on|closing on|finishing on}} {last_date} in {last_city} at {last_venue}."
                        ],
                    '1b' =>
                        [
                            "{{Good news for|Hey,}} {first_city} residents! {{The wait for a {performer} concert is {{over|no more}}|{{Fans|Concertgoers}} no longer have to wait}} for {performer} {{to come to your city|to visit your city|to play at your city}}.{performer} {{recently|just}} {{announced|revealed}} {{only one|one}} {{concert|event}} on {first_date} at {first_venue}.",
                            "{{Amazing|Awesome|Wonderful}} news for {{everyone|concertgoers|{performer} fans}} {{living in|from}} {first_city}. {{Only one|One single}} {{concert|event}} {{has been|was just}} {{announced|revealed|scheduled}} by {performer} and it {{will be held|will be hosted}} on {first_date} at {first_venue}.",

                            "If you {{live|happen to live}} in {first_city}, you are {{in luck|one lucky {performer} fan}}. {performer} {{has recently announced|finally announced|will have|will perform}} {{only one|a single}} concert in {first_city} on {first_date} at {first_venue}."
                        ],
                    '1c' =>
                        [
                            "Sadly, {{at this point|as of today}}, {performer} {{has not|hasn’t}} {{scheduled any {{events|concerts}}|announced any {{events|concerts}}}}.",

                            "{{Bad news|No good news}} for {performer} fans. {{No schedule has been announced|No new {{concerts|events|tour dates}} have been {{announced|set|scheduled}}}} {{as of today|for this upcoming season}}.",

                            "{performer} {{has not announced any|has not revealed any}} plans to {{tour|have any concerts}} in {{North America|the US and Canada}}."
                        ],
                    '2a' =>
                        [
                            "{{Other stops|Additional stops|More cities|More stops|{performer} has other {{cities|stops|places|great locations}}}} scheduled on the tour include {random_events_sentence}.",

                            "{{The tour|{performer's} tour|This great tour}} {{will also include|will have|has scheduled}} {{concerts|performances|events}} in {random_events_sentence}.",

                            "{{Other|Additional}} {{concerts|performances|events}} {{scheduled|booked|set}} on the tour include {random_events_sentence}."
                        ],
                    '2b' =>
                        [
                            "{{Unfortunately|Regrettably}}, {{there aren't any|there are no}} {{other|additional}} {{events scheduled|{performer} appearances}} {{for this {{short|brief}} tour|in this quick tour}}.",

                            "{{This|{performer's}}} tour is {{rather|quite}} {{brief|short}}, {performer} is not {{going to have|scheduling|booking}} any {{additional|more}} stops.",

                            "{performer} won't be {{visiting|performing in|having concerts in}} any {{additional|other}} {{cities|places}} during this {{brief|short|quick}} tour. {{Fans|True fans}} should {{keep an eye|watch for|track {performer}}} for {{additional|any new}} announcements",
                        ],
                    '2c' =>
                        [
                            "",
                        ],
                ],
                '2a' => [
                    '1a' =>
                        [
                            "{{Starting with the|Since the|Beginning with the}} {{release of {possesive} first album|release of {possesive} debut album|debut of {possesive} first album}} {first_album} over {year_count} years ago, {performer} {{has always|has consistently|has continuously}} {{offered|provided|given}} {{eager {{listeners|fans|concertgoers}}|hungry ears|loyal {{listeners|fans|concertgoers}}}} {{a {{unique|one of a kind|legendary}} {{blend|mix|array}} of|an eclectic {{blend|mix|array}} of|a special variety of}} {genre} {songs_tracks}.",

                            "{performer} {{released {possesive} first album|released {possesive} debut album|debuted {possesive} first album}} of {genre} {songs_tracks} {{titled|named}} {first_album} over {year_count} years ago.",

                            "{{Eager {{listeners|fans|concertgoers}} of {performer}|Loyal {{listeners|fans|concertgoers}} of {performer} }} were first {{offered|provided|given}} {{a {{unique|one of a kind|legendary}} {{blend|mix|array}} of|an eclectic {{blend|mix|array}} of|a special variety of}} {genre} {songs_tracks} over {year_count} years ago when {performer} {{released {possesive} first album|released {possesive} debut album|debuted {possesive} first album}} {{titled|named}} {first_album}."
                        ],
                    '1b' =>
                        [
                            "",
                        ],
                    '2a' =>
                        [
                            "{{Over the years|Over time|Since then|throughout their career}} {performer} {{has had tour and album {{collaborations|joint productions|associations|features|partnerships}} with|has {{collaborated on|jointly produced|associated in|been feature on|partnered on}} tours and albums with}} {collaborators} and has {{produced|released}} {{top|popular|well known}} {{hits|tracks|pieces}} like {top_songs}.",

                            "The release of {{top|popular|well known}} {{hits|tracks|pieces}} like {top_songs} from {performer} {{has paved the way|has lead the way|helped lead the way}} for {{tour and album {{collaborations|joint productions|associations|partnerships}} with|{{collaborations on|joint productions on|associations on|partnerships on}} tours and albums with}} {collaborators}.",

                            "{{Throughout|Over}} {performer's} {{career|history}} there {{have been|has been|was a lot of}} {{tour and album {{collaborations|joint productions|associations|partnerships}} with|{{collaborations on|joint productions on|associations on|partnerships on}} tours and albums with}} {collaborators} and releases of {{top|popular|well known}} {{hits|tracks|pieces}} like {top_songs}."
                        ],
                    '2b' =>
                        [
                            ""
                        ],
                    '2c' =>
                        [
                            "{{Over the years|Over time|Since then|throughout their career}} {performer} has {{produced|released}} {{top|popular|well known}} {{hits|tracks}} like {top_songs}.",
                        ]
                ],
                '2b' => [
                    '1a' =>
                        [
                            "{{Beginning in|Starting in|First introduced in}} {first_album_year}, {performer} {{{{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} with the release of|{{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}} with {{hits|tracks}} like|{{captivated|charmed|won over|gripped|captured}} listeners' attention with}} {first_album_top_song}.",

                            "{performer} first {{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} and {{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}} with the release of the {{hit|track}} {first_album_top_song} {{from|off}} the album {first_album} in {first_album_year}.",

                            "{{With the release|From the release|Because of the success}} of the {{hit|track}} {first_album_top_song} from the album {first_album} in {first_album_year}, {performer} {{{{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} with the release of|{{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}}}}."
                        ],
                    '1b' =>
                        [
                            "",
                        ],
                    '2a' =>
                        [
                            "{{This {{hit|track}} was followed up with|This {{hit|track}} {{paved|cleared|lead}} the way for|This {{set up|laid|created}} the {{foundation|groundwork|infrastructure}} for}} the {{popular|top|hit}} album {top_album} in {top_album_year} and {{future|eventual|subsequent}} {{collaborations|joint productions|associations|partnerships}} with {collaborators}.",

                            "The {{popular|top}} album {top_album}, released in {top_album_year}, {{followed|came after}} the {{popular|top}} {{hit|track}} and {{{{paved|cleared|lead}} the way for|{{set up|laid|created}} the {{foundation|groundwork|infrastructure}} for}} {{future|eventual|subsequent}} {{collaborations|joint productions|associations|partnerships}} with {collaborators}.",
                            "{{Future|Eventual|Subsequent}} {{collaborations|joint productions|associations|partnerships}} with {collaborators} {{were made possible|were set up}} {{through|because of|in part due to}} the {{success|popularity|reputation}} of the {{popular|top}} album {top_album} released in {top_album_year}."
                        ],
                    '2b' =>
                        [
                            ""
                        ],
                    '2c' =>
                        [
                            "{{Over the years|Over time|Since then|throughout their career}} {performer} has {{produced|released}} {{top|popular|well known}} {{hits|tracks|pieces}} like {top_songs}.",

                            "{performer}, throughout {possesive} career, has produced top hits like {top_songs}.",

                            "While touring and performing, {performer} managed to produced multiple {{{{great|popular|favorite}} {{tracks|hits}}|amazing {{tracks|hits}}}} among which are {top_songs}"
                        ]
                ],
                '3a' => [
                    '1' => [
                        "{performer} {{top|most popular}} {{hit|track|recording}} {{came|arrived|debuted}} in {top_album_year} with the release of the {{hit|track|recording}} {top_song} and {{continues to be|is still|has been}} a {{fan favorite|top hit|crowd pleaser}} to this day.",

                        "A {{perennial|continual}} {{fan favorite|top hit|crowd pleaser}}, {top_song}, was a {{top|popular}} {{hit|track|recording}} from {performer} released in {top_album_year}.",

                        "{top_song}, released in {top_album_year}, is a  {{top|popular}} {{hit|track|recording}} and has {{continued to be|still been|been}} a {{fan favorite|top hit|crowd pleaser}}."
                    ],
                ],
                '3b' => [
                    '1' => [
                        "{{Even after so many years|Despite a lengthy career}}, {performer's} {{top hit|most popular hit|most liked hit}} {{still remains|remains|has remained}} {top_album_song} from {top_album} {{released|recorded}} in {top_album_year}.",

                        "{top_album_song}, {{even after so many years|after so many years}}, remains the {{top played|top|most popular}} hit from {top_album} ({top_album_year}).",

                        "{{It's hard to disagree with the fact|It's widely agreed upon}} that {top_album_song} from album {top_album} {{remains|is still|still remains}} {performer's} {{top|most popular|most played|most listened to}} {{recording|hit|track}} since {top_album_year}",

                        "Even after so many years, {performer} top hit still remains {top_album_song} from {top_album} released in {top_album_year}.",
                    ],
                ],
            ];

        return $sentences[$part][$index][array_rand($sentences[$part][$index])];

    }

    public static function performer_discography($performerId = null)
    {
        //PReP
        $returnData = new stdClass();
        $returnData->text = '';
        $returnData->expire = false;
        $performer = Performer::with('upcoming_concerts')
            ->with('upcoming_concerts.tnConcert')
            ->with('upcoming_concerts.venue')
            ->with('upcoming_concerts.location')
            ->with('upcoming_concerts.venue.tnVenue')
            ->with('concerts')
            ->with('concerts.location')
            ->with('concerts.genres')
            ->where('id', $performerId)->first();
        $sentence1 = "";
        $sentence2 = "";
        $sentence3 = "";
        $sentence4 = "";
        $sentence5 = "";
        $expire = date('Y-m-d H:i:s', strtotime("+1 month"));
        $possesive = 'their';
        $performerHasHave = 'has';
        $years = 1;
        $concerts = $performer->concerts()->with('genres')->orderBy('date', 'ASC')->get();
        $genreholder = VarsHelper::get_top_genre($concerts);
        $genre = $genreholder['genre'];
        if (!$genre) $genre = "Pop";
        if ($performer->gender):
            $possesive = ($performer->gender == 'male') ? "his" : "her";
        endif;
        $albums = $performer->albums()
            ->with('tracks')
            ->where('release_date', '<>', '0000-00-00 00:00:00');
        // ->orderBy('release_date','ASC')
        // ->get();
        $totalAlbums = $albums->count();
        if ($totalAlbums === 0): //we have no albums, no need to run this shit
            $sentence1 = Spinner::_discography('1', '0');
        else:
            $isFeatured = FeaturedPerformer::where('performer_id', $performer->id)->count();

            $firstAlbum = $albums->orderBy('release_date', 'ASC')->first();
            $topAlbum = $albums->orderBy('play_count', 'DESC')->first();
            $topAlbums = $albums->orderBy('play_count', 'DESC')->take(2)->get();
            $allAlbums = $albums->get();
            $listners = $performer->listeners;
            $type = VarsHelper::normalize_band_person($performer);
            $founded = false;
            if ($type === "Group"):
                $founded = strtotime($performer->formed) ? date('Y', strtotime($performer->formed)) : false;
                $hasHave = 'have';
            endif;
            if (!$founded):
                if ($firstAlbum):
                    $founded = date('Y', strtotime($firstAlbum->release_date));
                endif;
            endif;
            if ($founded):
                $years = (date('Y') - $founded);
            endif;

            //
            // [are/is {{some of|a few of|a couple of}}]

            // PART I
            // Sentence 1
            $sentence1 = Spinner::_discography('1', '1');
            $firstAlbumYear = date('Y', strtotime($firstAlbum->release_date));
            $firstAlbum = $firstAlbum->title;
            $sentence1 = str_replace('{first_album}', StringHelper::wrap($firstAlbum, 'i'), $sentence1);
            $sentence1 = str_replace('{first_album_year}', $firstAlbumYear, $sentence1);

            // PART II'
            if ($years > 5):
                $sentence2 = Spinner::_discography('2', '1a');
                $sentence2 = str_replace('{count_years}', $years, $sentence2);
            else:
                $sentence2 = Spinner::_discography('2', '1b');
                $sentence2 = str_replace('{performer_has_have}', $performerHasHave, $sentence2);
            endif;

            // PARTI III
            if ($topAlbum->listeners > 50000):
                $sentence3 = Spinner::_discography('3', '1a');
                $sentence3 = str_replace('{top_album}', StringHelper::wrap($topAlbum->title, 'i'), $sentence3);
                $sentence3 = str_replace('{top_album_year}', date('Y', strtotime($topAlbum->release_date)), $sentence3);
            else:
                if ($totalAlbums > 2):
                    $sentence3 = Spinner::_discography('3', '1b');
                    // top_album_list
                    $topAlbumsString = VarsHelper::spinner_top_album_list($topAlbums);
                    $sentence3 = str_replace('{top_album_list}', $topAlbumsString, $sentence3);
                    $isAre = ($topAlbums->count() > 1) ? 'are' : 'is';
                    $sentence3 = str_replace('{is_are}', $isAre, $sentence3);

                    $sentence4 = Spinner::_discography('3', '2');
                    $topSongs = VarsHelper::get_top_tracks($allAlbums);
                    $isAre = (count($topSongs) > 1) ? 'are' : 'is';
                    $hasHave = (count($topSongs) > 1) ? 'have' : 'has';
                    $topSongsString = StringHelper::create_list($topSongs);
                    $someOneOf = (count($topSongs) > 1) ? "{{some of|a few of|a couple of}}" : "one of";
                    $someOneOfU = (count($topSongs) > 1) ? "{{Some of|A few of|A couple of}}" : "One of";

                    $sentence4 = str_replace('{top_songs}', $topSongsString, $sentence4);
                    $sentence4 = str_replace('{is_are}', $isAre, $sentence4);
                    $sentence4 = str_replace('{has_have}', $hasHave, $sentence4);
                    $sentence4 = str_replace('{some_one_of}', $someOneOf, $sentence4);
                    $sentence4 = str_replace('{some_one_of_u}', $someOneOfU, $sentence4);


                else:
                    $sentence3 = Spinner::_discography('3', '1c'); //NO OTHER ALBUMS
                endif;
            endif;
            // PART IV
            if ($isFeatured):
                $sentence5 = Spinner::_discography('4', '1');
                $sentence5 = str_replace('{year}', date('Y'), $sentence5);
            endif;
        endif;
        $sentence1 = Spinner::spin($sentence1);
        $sentence2 = Spinner::spin($sentence2);
        $sentence3 = Spinner::spin($sentence3);
        $sentence4 = Spinner::spin($sentence4);
        $sentence5 = Spinner::spin($sentence5);
        $text = $sentence1 . " " . $sentence2 . " " . $sentence3 . " " . $sentence4 . " " . $sentence5;
        $text = str_replace('{performer}', $performer->name, $text);
        $text = str_replace("{performer's}", StringHelper::properize($performer->name), $text);
        $text = str_replace('{genre}', $genre, $text);
        $text = str_replace('{possesive}', $possesive, $text);

        $returnData->text = $text;
        $returnData->expire = $expire;
        return $returnData;

    }

    private static function _discography($part = false, $index = false)
    {
        if (!$index || !$part) return '';

        $sentences =
            [
                '1' => [
                    '0' => [
                        "Unfortunatelly there are no albums for {performer}"
                    ],
                    '1' => [
                        "{{Beginning in|Starting in|First introduced in}} {first_album_year}, {performer} {{{{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} with the release of|{{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}} with the {{top|hit}} album}} {first_album}.",

                        "{performer} first {{{{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} with the release of|{{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}} with top album}} {first_album} in {first_album_year}.",

                        "{first_album_year} {{was the year|was when}} {performer} {{{{burst|erupted|sprung|emerged}} {{onto|into}} the {{scene|set|picture}} with the release of|{{captivated|charmed|won over|gripped}} {{audiences|crowds|the public}} with top album}} {first_album}."
                    ]
                ],
                '2' => [
                    '1a' => [
                        "{performer} {{has continued to release|has continued to grace us with|has repeatedly dished out}} {genre} {{hits|pieces|tracks}} {{for over|for more than}} {count_years} years.",

                        "{{For over|For more than}} {count_years} years, {performer} {{has continued to release|has continued to grace us with|has repeatedly dished out}} {genre} {{hits|pieces|tracks}}.",

                        "{performer} {{has delivered|has released}} over {count_years} years of {{hits|pieces|tracks}} in {genre}."
                    ],
                    '1b' => [
                        "{{Despite not being around for long|While still in the early stages of career|A relatively new performer}}, {performer} {{{performer_has_have} {{gained|built up|attained}} a reputation|continues to {{gain|buildup|attain}} {{notoriety|respect|fame}}}} in the {genre} genre {{scene|crowds|groups}} and {{{performer_has_have} built|{performer_has_have} established|is known for having}} a {{very strong|dedicated|die hard}} fan base.",

                        "{performer} {{{performer_has_have} {{gained|built up|attained}} a reputation|continues to {{gain|buildup|attain}} {{notoriety|respect|fame}}}} in the {genre} genre {{scene|crowds|groups}} {{despite not being around for very long|despite still being in the early stages of the career}}.",

                        "{performer} {{{performer_has_have} not been around for very long|{performer_has_have} just arrived on the scene}} but {{{performer_has_have} {{gained|built up|attained}} a reputation|continues to {{gain|buildup|attain}} {{notoriety|respect|fame}}}} in {genre} genre."
                    ]
                ],
                '3' => [
                    '1a' => [
                        "{{If you are not familiar with|If you have not {{listen to|heard from}}|If you are not a fan of}} {performer}, {{check out|listen to|enjoy}} {top_album} released in {top_album_year} {{to {{hear|find out|discover}} for yourself|and thank us later}}.",

                        "{{Check out|Listen to}} {top_album} that was released in {top_album_year} {{if you are not familiar with|if you have not {{listen to|heard of}}|if you are not a fan of}} {performer} and {{{{hear|find out|discover}} for yourself|and thank us later}}."
                    ],
                    '1b' => [
                        "{{Other albums of note|Other popular albums|Some other albums}} from {performer} {{include|are|have been}} {top_album_list}.",

                        "{performer's} {{other albums of note|other popular albums|other albums}} {{include|are|have been}} {top_album_list}. ",

                        "{top_album_list} {is_are} {performer's} {{more notable|more popular|other}} albums.",
                    ],
                    '1c' => [
                        ""
                    ],
                    '2' => [
                        "{performer's} {{most popular|top|best}} {{hits|pieces|tracks}} {{ {is_are}|{has_have} been|include}} {top_songs}.",

                        "{some_one_of_u} {performer's} {{most popular|top|best}} {{hits|pieces|tracks}} {has_have} been {top_songs}.",

                        "{top_songs} {{{is_are}|{has_have} been|include}} {some_one_of} {performer's} {{most popular|top|best}} {{hits|pieces|tracks}}.",
                    ]
                ],
                '4' => [
                    '1' => [
                        "{performer} {{continues to be|is still}} the {{most popular|top|best}} {genre} {{performers|artists}} {{today|around}} and is {{considered|recommended as|definitely}} one of sites \"{{must see|must watch}}\" {{performers|artists}} in {year}.",

                        "{performer} {{has been moved to|is considered|has made it to}} sites' \"{{must see|must watch}}\" {{list|group}} and {{continues to be|is still}} {{one of|amongst}} the {{most popular|top|best}} {genre} {{performers|artists}} {{today|around}}.",

                        "{{One of|Amongst}} site's \"{{must see|must watch}}\" {{performers|artists}}, {performer} {{continues to be|is still}} {{one of|amongst}} the {{most popular|top|best}} {genre} {{performers|artists}} {{today|around}}.",
                    ]

                ],
            ];
        return $sentences[$part][$index][array_rand($sentences[$part][$index])];

    }

    public static function performer_tour_dates($performerId = null)
    {
        //get concerts
        $returnData = new stdClass();
        $returnData->text = '';
        $returnData->expire = false;
        $performer = Performer::with('upcoming_concerts')
            ->with('upcoming_concerts.tnConcert')
            ->with('upcoming_concerts.venue')
            ->with('upcoming_concerts.location')
            ->with('upcoming_concerts.venue.tnVenue')
            ->with('concerts')
            ->with('concerts.location')
            ->with('concerts.genres')
            ->where('id', $performerId)->first();

        $sentence1 = "";
        $sentence2 = "";
        $sentence3 = "";
        $sentence4 = "";
        $sentence5 = "";
        $anniversary = false;
        $expire = date('Y-m-d H:i:s', strtotime('+1 week'));
        $season = 'this season';
        $founded = false;
        $concerts = $performer->concerts()->with('venue')->with('location')->orderBy('date', 'ASC')->get();
        $firstAlbum = $performer->albums()
            ->where('release_date', '<>', '0000-00-00 00:00:00')
            ->orderBy('release_date', 'ASC')->first();


        $type = VarsHelper::normalize_band_person($performer);

        if ($type === "Group"):
            $founded = strtotime($performer->formed) ? date('Y', strtotime($performer->formed)) : false;
        endif;
        if (!$founded):
            if ($firstAlbum):
                $founded = date('Y', strtotime($firstAlbum->release_date));
            endif;
        endif;
        if ($founded):
            $years = date('Y') - $founded;
            $anniversary = ($years % 5) ? false : true; //if mod 5, not anniversary
        endif;
        $eventCount = $concerts->count();
        $numArray = [0 => 'no', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five'];
        $prettyCount = (isset($numArray[$eventCount])) ? $numArray[$eventCount] : $eventCount;
        switch ($eventCount):
            case 0:
                $sentence1 = Spinner::_tour_dates('0');
                break;
            case 1: //if only one concerts load 1,2,4a, and anniversary
                $firstConcert = $concerts->first();
                $expire = $firstConcert->date;
                $season = VarsHelper::get_season($firstConcert->date, $firstConcert->date);
                $sentence1 = Spinner::_tour_dates('1');
                $sentence1 = str_replace('{season}', $season, $sentence1);

                $sentence2 = Spinner::_tour_dates('2');
                $firstVenue = $firstConcert->venue->name;
                $firstDate = date('F j', strtotime($firstConcert->date));
                $firstCity = $firstConcert->location->city;
                $sentence2 = str_replace('{first_venue}', $firstVenue, $sentence2);
                $sentence2 = str_replace('{first_city}', $firstCity, $sentence2);
                $sentence2 = str_replace('{first_date}', $firstDate, $sentence2);

                $sentence4 = Spinner::_tour_dates('4a');

                $sentence5 = ($anniversary) ? Spinner::_tour_dates('anniversary') : '';
                $sentence5 = str_replace('{founded}', $prettyCount, $sentence5);

                break;
            case 2: //if two 1,2,4, and anniversary
                $firstConcert = $concerts->first();
                $lastConcert = $concerts->last();
                $expire = $lastConcert->date;

                $season = VarsHelper::get_season($firstConcert->date, $lastConcert->date);

                $sentence1 = Spinner::_tour_dates('1');
                $sentence1 = str_replace('{season}', $season, $sentence1);
                $sentence2 = Spinner::_tour_dates('2');
                $firstVenue = $firstConcert->venue->name;
                $firstDate = date('F j', strtotime($firstConcert->date));
                $firstCity = $firstConcert->location->city;
                $sentence2 = str_replace('{first_venue}', $firstVenue, $sentence2);
                $sentence2 = str_replace('{first_city}', $firstCity, $sentence2);
                $sentence2 = str_replace('{first_date}', $firstDate, $sentence2);

                $sentence4 = Spinner::_tour_dates('4');
                $lastVenue = $lastConcert->venue->name;
                $lastDate = date('F j', strtotime($lastConcert->date));
                $lastCity = $lastConcert->location->city;
                $sentence4 = str_replace('{last_venue}', $lastVenue, $sentence4);
                $sentence4 = str_replace('{last_city}', $lastCity, $sentence4);
                $sentence4 = str_replace('{last_date}', $lastDate, $sentence4);

                $sentence5 = ($anniversary) ? Spinner::_tour_dates('anniversary') : '';
                $sentence5 = str_replace('{founded}', $prettyCount, $sentence5);

                break;
            default: //if at least 3 or more load 1,2,3,4,anniversary


                $firstConcert = $concerts->first();
                $lastConcert = $concerts->last();
                $expire = $lastConcert->date;

                $season = VarsHelper::get_season($firstConcert->date, $lastConcert->date);

                $sentence1 = Spinner::_tour_dates('1');
                $sentence1 = str_replace('{season}', $season, $sentence1);

                $sentence2 = Spinner::_tour_dates('2');
                $firstVenue = $firstConcert->venue->name;
                $firstDate = date('F j', strtotime($firstConcert->date));
                $firstCity = $firstConcert->location->city;
                $sentence2 = str_replace('{first_venue}', $firstVenue, $sentence2);
                $sentence2 = str_replace('{first_city}', $firstCity, $sentence2);
                $sentence2 = str_replace('{first_date}', $firstDate, $sentence2);

                $sentence3 = Spinner::_tour_dates('3');
                $sentence3Data = VarsHelper::get_random_concerts($concerts);
                $randomList = '';
                if ($sentence3Data->random):
                    $evz = [];
                    foreach ($sentence3Data->random as $ran):
                        $evz[] = StringHelper::wrap($ran->location->city, 'b', true) . " on " . StringHelper::wrap(date('F j', strtotime($ran->date)), 'b', true);
                    endforeach;
                    $randomList = StringHelper::create_list($evz, '', false);
                endif;
                $sentence3 = str_replace('{random_events_sentence}', $randomList, $sentence3);

                $sentence4 = Spinner::_tour_dates('4');
                $lastVenue = $lastConcert->venue->name;
                $lastDate = date('F j', strtotime($lastConcert->date));
                $lastCity = $lastConcert->location->city;
                $sentence4 = str_replace('{last_venue}', $lastVenue, $sentence4);
                $sentence4 = str_replace('{last_city}', $lastCity, $sentence4);
                $sentence4 = str_replace('{last_date}', $lastDate, $sentence4);

                $sentence5 = ($anniversary) ? Spinner::_tour_dates('anniversary') : '';
                $sentence5 = str_replace('{founded}', $prettyCount, $sentence5);
                break;
        endswitch;

        $sentence1 = Spinner::spin($sentence1);
        $sentence2 = Spinner::spin($sentence2);
        $sentence3 = Spinner::spin($sentence3);
        $sentence4 = Spinner::spin($sentence4);
        $sentence5 = Spinner::spin($sentence5);
        $sentences = [$sentence3, $sentence4, $sentence5];
        shuffle($sentences);
        $shuffledText = implode(' ', $sentences);

        $text = $sentence1 . " " . $sentence2 . " " . $shuffledText;
        $text = str_replace('{performer}', $performer->name, $text);
        $text = str_replace("{performer's}", StringHelper::properize($performer->name), $text);
        $text = str_replace('{event_count}', $prettyCount, $text);
        $returnData->text = $text;
        $returnData->expire = $expire;
        return $returnData;
    }

    private static function _tour_dates($index = false)
    {
        $sentences =
            [
                '0' => [
                    "{{Unfortunately|Bad news but}} {performer} {{has not {{announced|posted}}|has yet to {{announce|schedule}}}} any {{tour|concert}} dates yet. {{Stay tuned|Keep checking|Come back|return to site}}, or follow <a href='https://www.twitter.com/site' rel='no follow' target='_blank'>@site</a> {{to get tour|to receive| and read all the}} {{updates|tour news and updates}}!",

                    "No {{tour dates|events}} {{are posted yet|{{were|have been}} posted yet}} for {performer}, but {{return to|come back to}} site {{soon|within few days|in next few days}} or follow <a href='https://www.twitter.com/site' rel='no follow' target='_blank'>@site</a> {{to get tour|to receive|and read all the}} {{updates|tour news and updates}}.",

                    "{performer} didn't {{add|set}} {{any events|a tour schedule yet}}, but {{return to|come back to}} site {{soon|within few days|in next few days}} or follow <a href='https://www.twitter.com/site' rel='no follow' target='_blank'>@site</a> {{to get tour|to receive|and read all the}} {{updates|tour news and updates}}!"
                ],
                '1' => [
                    "{{Excited to see|Pumped to watch|Ready to go see}} {performer}? {{You're in luck|Wait no {{longer|more}}|Now you have your {{chance|opportunity|moment}}}} because {performer} has just {{announced|revealed|made public}} a {{new|upcoming|much anticipated}} {season} tour.",

                    "{performer} fans, its time to get {{excited|pumped|ready}}. During {season} {performer} {{will be|going to be}} on tour.",

                    "{performer} has {{announced|revealed|made public}} to many excited fans a {{new|upcoming|much anticipated}} {season} tour.",
                ],
                '2' => [
                    "The Tour {{will have|is scheduled for|is booked for}} {event_count} {{concerts|events|shows|performances}} {{beginning|starting|kicking off}} with a {{concert|event|show|performance}} in {first_city} on {first_date} at {first_venue}.",

                    "{{There will be|There are|Currently there are}} {event_count} {{concerts|events|shows|performances}} on this tour {{beginning|starting|kicking off}} with a {{concert|event|show|performance}} in {first_city} on {first_date} at {first_venue}.",

                    "The {{first stop|first location|starting point}} on the {event_count} {{show|concert}} tour will be {first_city} on {first_date} at {first_venue}.",
                ],
                '2a' => [
                    "{{As of today|At the moment}}, {performer} has not {{announced|revealed}} any tour dates. Fans are {{awaiting|looking forward to}} {performer} to {{release|announce}} {{a concert schedule|tour dates}}.",
                ],
                '3' => [
                    "{{Other|Addtional}} {{concert|event|show|performance}} {{stops|locations|destinations}} on the tour {{include|are|will be|are set to include}} {random_events_sentence}.",

                    "Keep in mind, that {random_events_sentence} {{will be|are}} {{some|a few}} of the other tour {{stops|locations|destinations}} {{currently|presently}} {{scheduled|booked|expected}} on the tour.",
                ],
                '4' => [
                    "The tour {{will come to an end|will end off|will have its last concert|will finalize}} in {last_city} on {last_date} at {last_venue}. ",

                    "The {{final|last}} {{stop|location|destination}} on the tour {{will be|is|is currently}} in {last_city} on {last_date} at {last_venue}. ",
                ],
                '4a' => [
                    "{performer} {{announced {{one|just one|one single}} concert and|There is {{only one|just one|one single}} concert for {performer}. Fans {{hope|expect|are anticipating}} {performer} }} {{will|might}} announce {{additional|other|more}} tour dates {{very soon|shortly|soon}}."
                ],
                'anniversary' => [
                    "{{Help|Join}} {performer} to {{celebrate|welcome|witness}} the {founded} year anniversary tour at a {{venue|location|destination}} near you."
                ]
            ];
        return $sentences[$index][array_rand($sentences[$index])];
    }

    public static function city_text($locationId)
    {
        $returnData = new stdClass();
        $returnData->text = '';
        $returnData->expire = false;

        $location = Location::where('id', $locationId)->first();
        $sentence1 = "";
        $sentence2 = "";
        $sentence3 = "";
        $sentence4 = "";
        $sentence5 = "";
        $sentence6 = Spinner::_city_vars('6');
        $sentence6 = str_replace('{city}', $location->city, $sentence6);
        $expire = date('Y-m-d', strtotime("+4 day"));
        $allConcerts = Concert::with('performers')
            ->with('genres')
            ->with('venue')
            ->where('location_id', $location->id)
            ->with('performers.featured')
            ->where('date', '>', date('Y-m-d', strtotime("+4 day")))
            ->whereHas('performers', function ($query) {
                $query->where('performers.id', "<>", 'NULL');
            })
            ->orderBy('date', 'ASC')->get();


        //SENTENCE 1
        $sentence1 = Spinner::_city_vars("1");
        $sentence1 = str_replace('{city}', $location->city, $sentence1);

        //SENTENCE 2 - regular event in next week
        $firstConcert = $allConcerts->first();
        if ($firstConcert):
            $sentence2 = Spinner::_city_vars("2");
            $firstPerformer = ($firstConcert->performers) ? $firstConcert->performers->first() : false;
            $firstPerformerName = ($firstPerformer) ? $firstPerformer->name : $firstConcert->name;
            $firstConcertDate = date('F j', strtotime($firstConcert->date));
            $firstConcertVenue = $firstConcert->venue->name;
            if ($firstPerformer):
                $sentence2 = str_replace('{performer}', "<a href='/concerts/{$firstPerformer->slug}' alt='{$firstPerformer->name} Tour Date'>" . StringHelper::cut_the($firstPerformer->name) . "</a>", $sentence2);
            else:
                $sentence2 = str_replace('{performer}', $firstPerformerName, $sentence2);
            endif;
            $sentence2 = str_replace('{venue}', $firstConcertVenue, $sentence2);
            $sentence2 = str_replace('{date}', $firstConcertDate, $sentence2);
            $sentence2 = str_replace('{city}', $location->city, $sentence2);
        endif;

        $featuredPerformer = false;
        $venues = false;

        //iterate concerts
        foreach ($allConcerts as $concert):
            if (!isset($venues[$concert->venue->id])) $venues[$concert->venue->id] = 1;
            $venues[$concert->venue->id]++;
            foreach ($concert->performers as $p):
                if ($p->featured || !$featuredPerformer && $p->name != $firstPerformerName) $featuredPerformer = $p;
            endforeach;
        endforeach;

        //SENTENCE 3
        if ($featuredPerformer):
            $tracks = [];
            $albums = $featuredPerformer->albums()->where('performer_id', $featuredPerformer->id)
                ->with(array('tracks' => function ($trackQuery) {
                    $trackQuery->orderBy('listeners', 'DESC');
                }))->orderBy('play_count', 'DESC')->take(3)->get();
            if (count($albums)):
                foreach ($albums as $album):
                    $set = false;
                    foreach ($album->tracks as $tryTrack):
                        $badNames = ['intro', 'outro', 'no title', '-no title-', '', 'no name', 'unknown'];
                        if (!$set && trim($tryTrack->name) && !in_array(strtolower(trim($tryTrack->name)), $badNames)):
                            $tracks[] = $tryTrack->name;
                            $set = true;
                        endif;
                    endforeach;
                endforeach;
            endif;
            switch (count($tracks)):
                case 3:
                    $trackName1 = StringHelper::clean_song($tracks[0]);
                    $trackName2 = StringHelper::clean_song($tracks[1]);
                    $trackName3 = StringHelper::clean_song($tracks[2]);
                    $trackText = StringHelper::wrap($trackName1, '"', false) . ", " . StringHelper::wrap($trackName2, '"', false) . " and " . StringHelper::wrap($trackName3, '"', false);
                    break;
                case 2:
                    $trackName1 = StringHelper::clean_song($tracks[0]);
                    $trackName2 = StringHelper::clean_song($tracks[1]);
                    $trackText = StringHelper::wrap($trackName1, '"', false) . " and " . StringHelper::wrap($trackName2, '"', false);
                    break;
                case 1:
                    $trackName1 = StringHelper::clean_song($tracks[0]);
                    $trackText = StringHelper::wrap($trackName1, '"', false);
                    break;
                default:
                    //last ditch effort to grab something
                    $featuredPerformerSets = PerformerSets::where(["performer_id" => $featuredPerformer->id])->first();//->toArray();//first(['performer_id'=> $featuredPerformer->id]);
                    if ($featuredPerformerSets) $featuredPerformerSets = $featuredPerformerSets->toArray();
                    $tourSongs = VarsHelper::get_setlist_stats($featuredPerformerSets);
                    if ($tourSongs):
                        $tourSongs = array_values(array_flip(array_slice($tourSongs, 0, 3))); //slice, flip, rebase

                        switch (count($tourSongs)):
                            case 3:
                                $trackName1 = StringHelper::clean_song($tourSongs[0]);
                                $trackName2 = StringHelper::clean_song($tourSongs[1]);
                                $trackName3 = StringHelper::clean_song($tourSongs[2]);
                                $trackText = StringHelper::wrap($trackName1, '"', false) . ", " . StringHelper::wrap($trackName2, '"', false) . " and " . StringHelper::wrap($trackName3, '"', false);

                                break;
                            case 2:
                                $trackName1 = StringHelper::clean_song($tourSongs[0]);
                                $trackName2 = StringHelper::clean_song($tourSongs[1]);
                                $trackText = StringHelper::wrap($trackName1, '"', false) . " and " . StringHelper::wrap($trackName2, '"', false);
                                break;
                            case 1:
                                $trackName1 = StringHelper::clean_song($tourSongs[0]);
                                $trackText = StringHelper::wrap($trackName1, '"', false);
                                break;
                            default:
                                $trackText = false;
                                break;
                        endswitch; //count tourSongs
                    else:
                        $trackText = false;
                    endif;
                    break;
            endswitch; //count tracks

            if ($trackText):
                $sentence3 = Spinner::_city_vars("3a");
                $sentence3 = str_replace('{trackText}', $trackText, $sentence3);
            else:
                $sentence3 = Spinner::_city_vars("3b");
            endif;

            //grab genre
            $featuredPerformerConcerts = $featuredPerformer->concerts()->with('genres')->with('venue')->get();
            $featuredPerformerFirstConcert = $featuredPerformerConcerts->first();
            $badGenres = ['Other', 'Las Vegas S+hows', 'World - General', 'Religious', 'Classical', 'World - Celtic'];
            $hotGenre = VarsHelper::get_top_genre($featuredPerformerConcerts, $badGenres);
            $sentence3 = str_replace('{genre}', $hotGenre['genre'], $sentence3);
            $sentence3 = str_replace('{venue}', $featuredPerformerFirstConcert->venue->name, $sentence3);
            $sentence3 = str_replace('{date}', date('F j', strtotime($featuredPerformerFirstConcert->date)), $sentence3);
            $sentence3 = str_replace('{city}', $location->city, $sentence3);
            $sentence3 = str_replace('{performer}', "<a href='/concerts/{$featuredPerformer->slug}' alt='{$featuredPerformer->name} Tour Date'>" . StringHelper::cut_the($featuredPerformer->name) . "</a>", $sentence3);
            $expire = $featuredPerformerFirstConcert->date;
        endif;

        // SENTENCE 4 - hot genre

        $badGenres = ['Other', 'Las Vegas S+hows', 'World - General', 'Religious', 'Classical', 'World - Celtic'];
        $hotGenre = VarsHelper::get_top_genre($allConcerts, $badGenres);
        if ($hotGenre):
            $sentence4 = Spinner::_city_vars('4');
            $date1 = $allConcerts->first()->date;;
            $date2 = $allConcerts->last()->date;

            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $months = (($year2 - $year1) * 12) + ($month2 - $month1);
            $sentence4 = str_replace('{genre}', $hotGenre['genre'], $sentence4);
            $sentence4 = str_replace('{count}', $hotGenre['count'], $sentence4);
            $sentence4 = str_replace('{city}', $location->city, $sentence4);
            $sentence4 = str_replace('{monthcount}', $months, $sentence4);
        endif;

        //SENTENCE 5
        if ($venues):
            arsort($venues);
            reset($venues);
            $hVenueId = key($venues);

            $hotVenue = Venue::where('id', $hVenueId)
                ->with(array('concerts' => function ($cQuery) {
                    $cQuery->where('date', '>', date('Y-m-d H:i:s'))->orderBy('date', 'ASC');
                }))->first();
            $lastVenueConcert = $hotVenue->concerts->last();
            $sentence5 = Spinner::_city_vars(5);
            $sentence5 = str_replace('{date}', date('F j', strtotime($lastVenueConcert->date)), $sentence5);
            $sentence5 = str_replace('{month}', date('F', strtotime($lastVenueConcert->date)), $sentence5);
            $sentence5 = str_replace('{venue}', $hotVenue->name, $sentence5);
            $sentence5 = str_replace('{city}', $location->city, $sentence5);
            $sentence5 = str_replace('{event}', $lastVenueConcert->name, $sentence5);
        endif;

        $sentence1 = Spinner::spin($sentence1);
        $sentence2 = Spinner::spin($sentence2);
        $sentence3 = Spinner::spin($sentence3);
        $sentence4 = Spinner::spin($sentence4);
        $sentence5 = Spinner::spin($sentence5);
        $sentence6 = Spinner::spin($sentence6);
        $sentences = [$sentence2, $sentence3, $sentence4, $sentence5];

        shuffle($sentences);

        $shuffledText = implode(' ', $sentences);
        $text = $sentence1 . " " . $shuffledText;

        $text = trim($text);
        if (!$text):
            $text = "Sorry, there are no concerts in $location->city as of right now." . $sentence6;
        endif;
        $returnData->expire = date('Y-m-d', strtotime("+4 day"));
        $returnData->text = $sentence1 . " " . $sentence2 . " " . $sentence3 . " " . $sentence4 . " " . $sentence5 . " " . $sentence6;
        return $returnData;

    }

    private static function _city_vars($index = false)
    {
        if (!$index) return '';
        $sentences =
            [
                '1' =>
                    [
                        "{{Attention|Hey}} {{concertgoers|music heads|music fans|fans|concert fans|loyal fans}} {{of|in|living in}} {city}, {{awesome|fun|great|amazing|exciting}} {{new|brand new}} {{music is|tunes are|concerts are|performers are|artists are}} {{ {{coming|heading}} {{your way|to your city}}.|{{coming|heading}} {{to your city|to happening spots nearby|to venues near you}}.|{{playing|performing}} {{across|throughout various}} venues in {city}!}}",
                        "{{Good|Awesome|Fun|Great|Amazing|Exciting}} {{new|brand-new|modern|original}} {{music will be|tunes will be|concerts will be}} {{performed|on display|headlining}} {{for the ears|in front}} of {{concertgoers|music heads|music fans|fans|concert fans|loyal fans}} {{of|in|living in}} {city}.",
                        "{city} {{concertgoers|music heads|music fans|fans|concert fans|loyal fans}}, {{good|awesome|fun|great|amazing|exciting}} {{new|brand-new|modern|original}} {{music is|tunes are|concerts are|performers are|artists are}} {{coming|heading}} {{your way|your direction|to your town|to your city}}."
                    ],
                '2' =>
                    [
                        "{{This week|Over the next few days}}, the {{concert|event|show}} {{lineup|schedule}} for {city} will {{feature|spotlight|include}} the {{popular|most anticipated|long awaited}} {performer} {{concert|event|show}} at {venue} on {date}.",
                        "There are {{many touring {{artists|performers}}|many {{performers|artists}} {{on tour|touring|playing|having a show|having a concert|doing concerts}} }} in {city}. {performer} {{will be featured|will make an appearance|will have a {{show|concert}} }} {{this week|this upcoming week|within few days|in short few days}}, on {date} in {venue}.",
                        "{{From this week's|In this week's}} {city} {{line up|schedule|event schedule|concert lineup}} {{of many|of}} touring {{performers|artists}}, {venue} {{is featuring|will be featuring}} {performer} on {date}.",
                    ],
                '3a' =>
                    [
                        "{{Be sure to|Make sure to|You should}} also {{check out|catch}} {performer}, {{one of {genre} music’s top {{artists|performers}}|a top {{artist|performer}} in {genre} music|one of {genre} music’s most popular {{artist|performer}} }}, {{play|perform}} {{top|popular|great}} {{hits|tracks|pieces}} {{like|such as}} {trackText} {{during|while at|when at}} the {{concert|show|performance|event}} at {venue} on {date} in {city}.",
                        "{city} {{can also|will also|may also}} {{catch|witness|listen to|checkout|hear}} {{top|popular|great}} {{hits|tracks|pieces}} {{like|such as}} {trackText} at {venue} in {city} on {date} {{during|while at|when at}} the {performer} {{concert|show|event|performance}}.",
                        "On {date} you {{can|may|should}} {{check out|catch}} {{one of {genre} music's top {{artists|performers}}|a top {{artist|performer}} in {genre} music|one of {genre} music’s more popular {{artists|performers}}}}, {performer}, {{play|perform}} {{top|popular|great}} {{hits|tracks|pieces}} {{like|such as}} {trackText} at {venue}."
                    ],
                '3b' =>
                    [
                        "{{Be sure to|Make sure to|You should}} also {{check out|catch}} {performer}, {{one of {genre} music’s top {{artists|performers}}|a top {{artists|performers}} in {genre} music|one of {genre} music’s more popular {{artists|performers}}}}, {{play|perform}} {{top|popular|great}} {{hits|tracks|pieces}} {{during|while at|when at}} the {{concert|show|performance|event}} in {venue} on {date} in {city}.",
                        "{city} {{can also|will also|may also}} {{catch|witness|listen to|checkout|hear}} {{top|popular|great}} {{hits|tracks|pieces}} at {venue} in {city} on {date} {{during|while at|when at}} the {performer} {{concert|show|event|performance}}.",
                        "On {date} you {{can|may|should}} {{check out|catch|make to see}} {{one of {genre} top {{artists|performers}}|a top {{artist|performer}} in {genre}|one of {genre} more popular {{artists|performers}}}}, {performer}, {{play|perform}} {{top|popular|great}} {{hits|tracks|pieces}} at {venue}."
                    ],
                '4' =>
                    [
                        "{{Over|During|Within}} the {{next|upcoming}} {monthcount} months, {{there will be a wave of|there are|you can see|there is going to be}} {{over|more than}} {count} {genre} {{concerts|shows|events|performances}} {{coming|heading}} to {city}.",
                        "{genre} {{concertgoers|music heads|music fans|fans|concert fans}} in {city} {{should be|will be|are going to be}} {{happy|satisfied|stoked|excited|busy}} {{over|during|for}} the {{next|upcoming}} {monthcount} months {{as|because}} there {{will be|is going to be|is scheduled to be}} {{over|more than}} {count} {genre} {{concerts|shows|events|performances}}.",
                        "{city} {{will see|will witness|will experience|will host}} {count} {genre} {{concerts|shows|events|performances}} {{over|during|for}} the {{next|upcoming}} {monthcount} months {{which|that}} {{should|will}} {{leave|keep}} {genre} {{concertgoers|music heads|music fans|fans|concert fans}} in {city} {{happy|satisfied|stoked|excited|busy}}."
                    ],
                '5' =>
                    [
                        "{{Currently,|As of today,|At this time,}} the {{popular|happening}} {venue} {{is going to be|will be}} {{jam packed|packed|booked up|filled}} with {{many|a lot of|a vast array of|a big list of}} {{cool|awesome|fun|much anticipated}} {{performances|events|concerts}}. {venue} has {{events|shows|concerts}} {{scheduled|booked}} {{up till|until|all the way up till}} {date} with the {{last|final}} {{show|event|concert}} being {event}.",
                        "{{If|In case}} you {{are looking for|want to find}} {{the happening|the busiest|the coolest|the most popular}} {{venue|location}} in {city}, {venue} {{is the place to be|is exactly that|is what you need|is what you want|is the spot|is definitely the place|is where you want to be|is where you want to go|is expecting you|would not disappoint you|would be the answer}}. {{The venue has|The venue features|The venue's calendar features|The venue's calendar has}} {{a lot|many|a big list|a huge list|a big set}} of {{exciting|new|fun}} {{events|concerts|shows}} {{booked|scheduled|penciled in|announced}} {{all the way until|all the way up to|till}} {date} with {event}. This {{is the|is set to be|will be}} {{the last|the final| the concluding}} {{event|concert|show}} on the {{announced|published|official}} {{concert calendar|concert schedule}} for {venue}.",
                        "{venue} is {{the most|a very|one of the}} {{most popular|most happening|most prominent}} {{venues|places|spots}} in {city}. It has {{booked|scheduled}} {{concerts|shows|performances|events}} from now {{up until|all the way through|all the way up to}} {month}, {{with the last|with the final}} {{concert|show|performance|event}} being {event} on {date}."
                    ],
                '6' => [
                    "{{For all your|When it comes to your|To Fulfill your}} {city} {{concert|event}} {{needs|tickets|information|news}}, {{check out|follow the hashtag|go to|check out the hashtag}} <a href='https://twitter.com/hashtag/site' target='_blank'>#site</a> or {{follow|add}} <a href='https://twitter.com/site' target='_blank'>@site</a> on Twitter. {{For certain events|Often|Usually|Most likely}} site also {{carry parking passes|have information on parking|have parking passes}} {{as well as|and}} VIP {{passes|tickets}}, {{front row|premium|sold-out}} tickets, and backstage {{passes|tickets}} for all {city} {{concerts|events|shows|performances}}.",
                ]
            ];
        return $sentences[$index][array_rand($sentences[$index])];
    }

    public static function perfromer_venue_qa($performerId, $venueId)
    {
        $returnData = new stdClass();

        $performer = Performer::where('id', $performerId)
            ->with('concerts')
            ->whereHas('concerts', function ($query) use ($venueId) {
                $query->where('date', '>', date('Y-m-d'));
            })
            ->with('concerts.performers')
            ->with('concerts.tickets')
            ->with('concerts.location')
            ->first();
        $venue = Venue::where('id', $venueId)->with('geoLocation')->with('tnVenue')->first();
        $venueAddress = "{$venue->tnVenue->street_1} {$venue->tnVenue->street_2} {$venue->tnVenue->city}, {$venue->tnVenue->state} - {$venue->tnVenue->zip}";
        $venueConcerts = [];
        $stateConcerts = [];
        $guests = [];
        $concertz = [];
        if (isset($performer->concerts)):
            $concertz = $performer->concerts;
        endif;
        foreach ($concertz as $concert):
            foreach ($concert->performers as $p):
                if ($p->id != $performerId) $guests[$p->id] = $p->name;
            endforeach;
        endforeach;
        $stringGuests = '';

        $lastGuest = array_pop($guests);
        if (count($guests)):
            $stringGuests = implode(', ', $guests);
        endif;
        $stringGuests = ($stringGuests) ? $stringGuests . " and " . $lastGuest : $lastGuest;
        foreach ($concertz as $concert):
            if ($concert->venue_id == $venueId) $venueConcerts[] = $concert;
            if ($concert->location->state == $venue->geoLocation->state && $concert->venue_id != $venueId) $stateConcerts[] = $concert;
        endforeach;

        $returnData->text = '';
        $returnData->expire = false;
        $sets = [
            [
                'question' => "",
                'answer' => ""
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ]
        ];
        //TODO WHAT TO DO IF NO CONCERTS??
        //
        //Q1
        $sets[0]['question'] = Spinner::_qa_performer_venue_vars(1, 1, 'question');
        if ($stringGuests):
            $sets[0]['answer'] = Spinner::_qa_performer_venue_vars(1, 1, 'answer');
        else:
            $sets[0]['answer'] = Spinner::_qa_performer_venue_vars(1, 1, 'answer_noguests');
        endif;

        $sets[1]['question'] = Spinner::_qa_performer_venue_vars(1, 2, 'question');
        $sets[1]['answer'] = Spinner::_qa_performer_venue_vars(1, 2, 'answer');

        $sets[2]['question'] = Spinner::_qa_performer_venue_vars(1, 3, 'question');
        $sets[2]['answer'] = Spinner::_qa_performer_venue_vars(1, 3, 'answer');

        $sets[3]['question'] = Spinner::_qa_performer_venue_vars(1, 4, 'question');
        $sets[3]['answer'] = Spinner::_qa_performer_venue_vars(1, 4, 'answer');

        //Special Cases
        $sets[4]['question'] = Spinner::_qa_performer_venue_vars(2, 1, 'question');
        $concertCount = $performer->concerts->count();
        $firstConcert = $performer->concerts->first();
        $returnData->expire = $firstConcert->date;

        //new chek $nextConcert
        if (count($venueConcerts) == 1):
            $sets[4]['answer'] = Spinner::_qa_performer_venue_vars(2, 1, 'answer');
        elseif (count($venueConcerts) > 1):
            // $nextConcert = false;
            $nextConcert = (isset($venueConcerts[1])) ? $venueConcerts[1] : false;
            /*
            foreach ($venueConcerts as $nc):
             if(!$nextConcert && $nc->id != $firstConcert->id):
              $nextConcert = $nc;
             endif;
            endforeach;
            */
            if ($nextConcert):
                $sets[4]['answer'] = Spinner::_qa_performer_venue_vars(2, 1, 'answer_more_events');

                $sets[4]['answer'] = str_replace('{date_2}', date('F j', strtotime($nextConcert->date)), $sets[4]['answer']);
                $sets[4]['answer'] = str_replace('{event_time_2}', date('g:i a', strtotime($nextConcert->date)), $sets[4]['answer']);
            else:
                $sets[4]['answer'] = "There are no concerts for this performer at this venue";
            endif;
        else: //none
            $sets[4]['answer'] = "There are no concerts for this performer at this venue";
        endif;
        //finish chek $nextConcert

        $sets[5]['question'] = Spinner::_qa_performer_venue_vars(2, 2, 'question');
        if ($concertCount == 1): //only one concert in the city
            $sets[5]['answer'] = Spinner::_qa_performer_venue_vars(2, 2, 'answer');
        elseif ($concertCount > 1): //there are other events
            if (count($venueConcerts) > 1): // there is one in the same venue
                $nextVenueConcert = $venueConcerts[1];
                $sets[5]['answer'] = Spinner::_qa_performer_venue_vars(2, 2, 'answer_more');
                $sets[5]['answer'] = str_replace('{date_2}', date('F j', strtotime($nextVenueConcert->date)), $sets[5]['answer']);
                if (count($stateConcerts)):
                    $firstState = $stateConcerts[0];
                    $sets[5]['answer'] .= Spinner::_qa_performer_venue_vars(2, 2, 'answer_in_nearby_city');
                    $sets[5]['answer'] = str_replace('{city_2}', $firstState->location->city, $sets[5]['answer']);
                    $sets[5]['answer'] = str_replace('{date_2}', date('F j', strtotime($firstState->date)), $sets[5]['answer']);
                endif;
            elseif (count($stateConcerts)):
                $firstState = $stateConcerts[0];
                $sets[5]['answer'] = Spinner::_qa_performer_venue_vars(2, 2, 'answer');
                $sets[5]['answer'] .= Spinner::_qa_performer_venue_vars(2, 2, 'answer_in_nearby_city');
                $sets[5]['answer'] = str_replace('{city_2}', $firstState->location->city, $sets[5]['answer']);
                $sets[5]['answer'] = str_replace('{date_2}', date('F j', strtotime($firstState->date)), $sets[5]['answer']);
            else:
                $sets[5]['answer'] .= Spinner::_qa_performer_venue_vars(2, 2, 'answer');
            endif;
        endif;

        $sets[6]['question'] = Spinner::_qa_performer_venue_vars(3, 1, 'question');
        $sets[6]['answer'] = Spinner::_qa_performer_venue_vars(3, 1, 'answer');

        $sets[7]['question'] = Spinner::_qa_performer_venue_vars(3, 2, 'question');
        $sets[7]['answer'] = Spinner::_qa_performer_venue_vars(3, 2, 'answer');

        $sets[8]['question'] = Spinner::_qa_performer_venue_vars(3, 3, 'question');
        $sets[8]['answer'] = Spinner::_qa_performer_venue_vars(3, 3, 'answer');

        $sets[9]['question'] = Spinner::_qa_performer_venue_vars(3, 4, 'question');
        $sets[9]['answer'] = Spinner::_qa_performer_venue_vars(3, 4, 'answer');

        $ticket = $firstConcert->tickets->first();
        $lowPrice = "\$" . rand(60, 120);
        $highPrice = "\$" . rand(130, 600);
        $ticketCount = (isset($ticket->quantity)) ? $ticket->quantity : rand(10, 100);
        $lowSeat = (isset($ticket->low_seat) && $ticket->low_seat != '') ? $ticket->low_seat : "general admission";
        $highSeat = (isset($ticket->high_seat) && $ticket->high_seat != '') ? $ticket->high_seat : "premium sections";
        $ticketCount = (isset($ticket->quantity) && $ticket->quantity != 0) ? $ticket->quantity : "many";

        foreach ($sets as $k => $set):
            foreach ($set as $qak => $val):
                $sets[$k][$qak] = str_replace('{event_name}', $firstConcert->name, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{venue}', $venue->name, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{date}', date('F j', strtotime($firstConcert->date)), $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{city}', $venue->tnVenue->city, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{performer}', $performer->name, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{date}', date('F j', strtotime($firstConcert->date)), $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{event_time}', date('g:i a', strtotime($firstConcert->date)), $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{low_price}', $lowPrice, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{high_price}', $highPrice, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{low_section}', $lowSeat, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{high_section}', $highSeat, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{performer_list}', $stringGuests, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{venue_address}', $venueAddress, $sets[$k][$qak]);
                $sets[$k][$qak] = str_replace('{ticket_number}', $ticketCount, $sets[$k][$qak]);
                $sets[$k][$qak] = Spinner::spin($sets[$k][$qak]);

            endforeach;
        endforeach;
        $shortSet = [];
        $shortSet[] = $sets[rand(0, 3)];
        $shortSet[] = $sets[rand(4, 5)];
        $shortSet[] = $sets[rand(6, 9)];
        shuffle($shortSet);
        $returnData->text = $shortSet;
        return $returnData;
    }

    public static function _qa_performer_venue_vars($set = null, $questionIndex = null, $qa = '')
    {
        $sentences = array(
            '1' => [
                '1' => [
                    'question' => "{{Who are|What are}} {{the opening acts and {{guest|tour guests}} performers|some other performers}} for {event_name} at {venue} in {city}?",
                    'answer' => "{{Currently|Right now}} {{the opening acts|the guests}} for {event_name} at {venue} are set to be {performer_list}.",
                    'answer_noguests' => "{{Currently|At this point}} there aren't any {{opening acts|tour guests}} for {performer}"
                ],
                '2' => [
                    'question' => "Is the {event_name} {{concert|event}} at {venue} in {city} sold out?",
                    'answer' => "{{Not at all.|Fortunatelly not!}} There are still {ticket_number} tickets {{left in stock|remaining}} for the {event_name} {{concert|performance}} in {city} at {venue} but {{they are selling fast|the show will get sold out}} {{so buy|so get}} them before {{it's too late|all tickets are gone}}."
                ],
                '3' => [
                    'question' => "When do {event_name} {venue} tickets go on sale?",
                    'answer' => "Tickets for {event_name} in {city} are on sale now."
                ],
                '4' => [
                    'question' => "Where {performer} {city} concert located?",
                    'answer' => "The {performer} {venue} concert is located at {venue_address}"
                ],
            ],
            '2' => [
                '1' => [
                    'question' => "{{What time|When}} does the {event_name} concert in {city} {{start|begin}}?",
                    'answer' => "The {event_name} concert on {date} {{start|begin}} times can change prior to concert. Check with the venue, and we {{recommend|suggest|advise}} {{getting|arriving}} {{there|to the show}} {{20 minutes beffore the show|at least 20 min early}} {{so you don't miss a|to ensure you do not miss a}} minute of the {{concert|event|performance}}.",
                    'answer_more_events' => "The {event_name} {{concert|show}} on {date} start time can change prior to the event. Please check your tickets. The concert on {date_2} start time hasn't been set yet. We {{recommend|suggest|advise}} {{getting|arriving}} {{there|to the show}} {{20 minutes beffore the show|at least 20 min early}} {{so you don't miss a|to ensure you do not miss a}} minute of the {{concert|event|performance}}.",
                ],
                '2' => [
                    'question' => "{{Are there any other|Will there be any additional}} {event_name} concerts in {city} besides the concert on {date}?",
                    'answer' => "There are no other {event_name} concerts other than the one on {date}.",
                    'answer_more' => "Yes, {performer} will also have a concert on {date_2} at {venue}.",
                    'answer_in_nearby_city' => "However, there is a concert in {city_2} on {date_2}.",
                ],
            ],
            '3' => [
                '1' => [
                    'question' => "How will you deliver my {event_name} {city} Tickets?",
                    'answer' => "{{There are several different ways to get|Few ways to get}} {event_name} {city} tickets delivered to you. The physical {event_name} {city} tickets are shipped via FedEx. There are also e-tickets and downloadable {event_name} {city} tickets which are delivered to you electronically instantly after purchase.",
                ],
                '2' => [
                    'question' => "How much are {event_name} {city} concert tickets?",
                    'answer' => "{event_name} {city} concert tickets start at {low_price} and range as high as {high_price} for better seats.",
                ],
                '3' => [
                    'question' => "Where is the best place to buy cheap {event_name} {city} concert tickets?",
                    'answer' => "site is one of the best places to find cheap {event_name} {venue} concert tickets because we have one of the largest selections of tickets to choose from.",
                ],
                '4' => [
                    'question' => "Are there any backstage passes, VIP, or meet and greet tickets available for the {event_name} {venue} concert?",
                    'answer' => "Many times there are backstage passes, VIP tickets, or meet and greet tickets for the {event_name} concert in {city}.",
                ],
            ]
        );
        return $sentences[$set]
        [$questionIndex]
        [$qa];

    }

    public static function performer_qa($performerId)
    {
        $returnData = new stdClass();
        $returnData->text = '';
        $returnData->expire = false;
        $performer = Performer::with('upcoming_concerts')->with('upcoming_concerts.location')->where('id', $performerId)->first();
        $performerDetails = PerformerDetails::where("performer_id", $performer->id)->get()->toArray();//first(['performer_id'=> $performer->id]);
        $similarPerformers = (isset($performerDetails[0]['similar'])) ? $performerDetails[0]['similar'] : false;

        if ($similarPerformers):
            $similarPerformers = VarsHelper::get_similar_performers($similarPerformers);
        endif;

        $performerSets = PerformerSets::where(["performer_id" => $performer->id])->first();//->toArray();//first(['performer_id'=> $performer->id]);

        if ($performerSets) $performerSets = $performerSets->toArray();

        $tourSongs = VarsHelper::get_setlist_stats($performerSets);
        $pastGuests = VarsHelper::get_past_guests($performerSets);
        $upcomingGuests = $performer->tour_guests();
        if ($tourSongs):
            $tourSongs = array_slice($tourSongs, 0, 10);
        endif;
        $eventCount = $performer->upcoming_concerts->count();
        $year = date('Y');
        $gender = $performer->gender;
        if ($gender == 'male'):
            $hisHer = 'his';
        elseif ($gender == 'female'):
            $hisHer = 'her';
        else:
            $hisHer = 'their';
        endif;

        $sets = [
            [
                'question' => "",
                'answer' => ""
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ],
            [
                'question' => "",
                'answer' => "",
            ]
        ];

        //first special case
        $sets[0]['question'] = Spinner::_qa_perfromer(0, 'question');
        if ($upcomingGuests):
            $tempHold = [];
            foreach ($upcomingGuests as $guest):
                $tempHold[$guest['slug']] = $guest['name'];
            endforeach;

            $lastGuest = array_pop($tempHold);
            $sets[0]['answer'] = Spinner::_qa_perfromer(0, 'answer_guests');
            if ($tempHold):
                $guestString = implode(', ', $tempHold);
                $guestString .= " and " . $lastGuest;
            else:
                $guestString = $lastGuest;
            endif;
            $sets[0]['answer'] = str_replace('{tour_guests}', $guestString, $sets[0]['answer']);
        else:
            $sets[0]['answer'] = Spinner::_qa_perfromer(0, 'answer_noguests');
        endif;

        $sets[1]['question'] = Spinner::_qa_perfromer(1, 'question');

        if ($tourSongs):
            $topSongsString = '';
            $sets[1]['answer'] = Spinner::_qa_perfromer(1, 'answer_songs');
            $tmpTop = array_slice($tourSongs, 0, 3);
            $okTop = [];
            foreach ($tmpTop as $key => $value):
                $okTop[] = $key;
            endforeach;
            $lastSong = array_pop($okTop);
            if ($okTop):
                $topSongsString = implode(', ', $okTop) . " and " . $lastSong;
            else:
                $topSongsString = $lastSong;
            endif;
            $sets[1]['answer'] = str_replace('{top_songs}', $topSongsString, $sets[1]['answer']);
        else:
            $sets[1]['answer'] = Spinner::_qa_perfromer(1, 'answer_nosongs');
        endif;

        $sets[2]['question'] = Spinner::_qa_perfromer(2, 'question');
        if ($pastGuests):
            $guestHolder = [];
            foreach ($pastGuests as $g):
                $guestHolder[] = $g['name'];
            endforeach;
            $guestHolder = array_slice($guestHolder, 0, 3);
            $lastGuest = array_pop($guestHolder);
            if ($guestHolder):
                $colabString = implode(', ', $guestHolder) . " and " . $lastGuest;
            else:
                $colabString = $lastGuest;
            endif;
            $sets[2]['answer'] = Spinner::_qa_perfromer(2, 'answer_colab');
            $sets[2]['answer'] = str_replace('{colab_list}', $colabString, $sets[2]['answer']);
        else:
            $sets[2]['answer'] = Spinner::_qa_perfromer(2, 'answer_nocolab');
        endif;

        $sets[3]['question'] = Spinner::_qa_perfromer(3, 'question');
        if ($eventCount):
            $tourCities = [];
            $returnData->expire = $performer->upcoming_concerts->last()->date;
            foreach ($performer->upcoming_concerts as $concert):
                $tourCities[$concert->location->slug] = "{$concert->location->city} ({$concert->location->state})";
            endforeach;
            $lastCity = array_pop($tourCities);
            $tourCitiesString = '';
            if ($tourCities):
                $tourCitiesString = implode(', ', $tourCities) . " and " . $lastCity;
            else:
                $tourCitiesString = $lastCity;
            endif;
            $sets[3]['answer'] = Spinner::_qa_perfromer(3, 'answer');
            $sets[3]['answer'] = str_replace('{tour_cities}', $tourCitiesString, $sets[3]['answer']);
        else:
            $sets[3]['answer'] = Spinner::_qa_perfromer(3, 'answer_notour');
        endif;

        $sets[4]['question'] = Spinner::_qa_perfromer(4, 'question');
        if ($eventCount):
            $sets[4]['answer'] = Spinner::_qa_perfromer(4, 'answer');
            $sets[4]['answer'] = str_replace('{event_count}', $eventCount, $sets[4]['answer']);
        else:
            $sets[4]['answer'] = Spinner::_qa_perfromer(4, 'answer_notour');
        endif;

        $sets[5]['question'] = Spinner::_qa_perfromer(5, 'question');
        if ($similarPerformers):
            $spHolder = [];
            foreach ($similarPerformers as $sp):
                $spHolder[$sp->slug] = $sp->name;
            endforeach;
            $lastSimilar = array_pop($spHolder);
            $similarPerformersString = '';
            if ($spHolder):
                $similarPerformersString = implode(', ', $spHolder) . " and " . $lastSimilar;
            else:
                $similarPerformersString = $lastSimilar;
            endif;
            $sets[5]['answer'] = Spinner::_qa_perfromer(5, 'answer');
            $sets[5]['answer'] = str_replace('{similar_artists}', $similarPerformersString, $sets[5]['answer']);
        else:
            $sets[5]['answer'] = Spinner::_qa_perfromer(5, 'answer_nosimilar');
        endif;

        $sets[6]['question'] = Spinner::_qa_perfromer(6, 'question');
        $sets[6]['answer'] = Spinner::_qa_perfromer(6, 'answer');

        $sets[7]['question'] = Spinner::_qa_perfromer(7, 'question');
        $sets[7]['answer'] = Spinner::_qa_perfromer(7, 'answer');

        $sets[8]['question'] = Spinner::_qa_perfromer(8, 'question');
        $sets[8]['answer'] = Spinner::_qa_perfromer(8, 'answer');


        foreach ($sets as $key => $set):
            foreach ($set as $subkey => $value):
                $sets[$key][$subkey] = str_replace('{performer}', $performer->name, $sets[$key][$subkey]);
                $sets[$key][$subkey] = str_replace('{performers}', StringHelper::properize($performer->name), $sets[$key][$subkey]);
                $sets[$key][$subkey] = str_replace('{year}', $year, $sets[$key][$subkey]);
                $sets[$key][$subkey] = str_replace('{his_her}', $hisHer, $sets[$key][$subkey]);
                $sets[$key][$subkey] = Spinner::spin($sets[$key][$subkey]);

            endforeach;
        endforeach;

        $shortSet = [];
        $shortSet[] = $sets[rand(0, 3)];
        $shortSet[] = $sets[rand(4, 5)];
        $shortSet[] = $sets[rand(6, 8)];
        shuffle($shortSet);
        $returnData->text = $shortSet;
        return $returnData;
    }

    public static function _qa_perfromer($index, $subindex)
    {
        $quesetions = [
            [
                'question' => "Who’s on tour with {performer} in {year}?",
                'answer_noguests' => "Currently {performer} scheduled only solo acts on the tour.",
                'answer_guests' => "Special tour guests for {performer} tour include {tour_guests}."
            ],
            [
                'question' => "What songs will {performer} play on his tour?",
                'answer_nosongs' => "{performers} set list on this tour is going to be a surprise to all of us.  Should make for an exciting concert tour.",
                'answer_songs' => "In the past {performers} most played tour songs have been {top_songs}.",
            ],
            [
                'question' => "Who were the performers that {performer} collaborated with in the past?",
                'answer_nocolab' => "{performer} has appeared with {colab_list} on {his_her} past tours.",
                'answer_colab' => "There aren’t any notable performers or bands that {performer} collaborated with.",
            ],
            [
                'question' => "What cities will have concerts on {performers} tour?",
                'answer' => "{performers} tour will have dates in {tour_cities}.",
                'answer_notour' => "{performer} is not on tour right now.",
            ],
            [
                'question' => "How many tour dates are there on the {year} {performer} concert schedule?",
                'answer' => "There are {event_count} concerts on the {year} {performer} tour schedule.",
                'answer_notour' => "There aren’t any events for {performer}.",
            ],
            [
                'question' => "If I am a big fan of {performer}, who are some other artists I might like?",
                'answer' => "If you can’t wait to see {performer} on this new tour then you might also want to check out {similar_artists}.",
                'answer_nosimilar' => "Because {his_her} style is very unique, a fan can checkout out other performers in the same genre.",
            ],
            [
                'question' => "Where is the best place to go for {performer} tour date information?",
                'answer' => "site brings you all the latest news on everything tour dates including updates for the {performer} {year} tour.",
            ],
            [
                'question' => "Can you notify me if {performer} concert tickets become available?",
                'answer' => "Use our <a href='/tour-tracker-signup'>tour tracker</a> to stay on top of all the latest news including ticket updates and new concert dates?",
            ],
            [
                'question' => "Does this {performer} tour have any backstage passes, VIP tickets, or meet and greet tickets?",
                'answer' => "Yes, often times either backstage passes, meet and greet tickets, or vip tickets are available for {performer}. Check your city for availability.",
            ],
        ];
        return $quesetions[$index][$subindex];
    }

    # Similar to str_replace, but only replaces the first instance of the needle

    public static function _performer_city($part = null, $index = null)
    {

        $sentences =
            [
                '1' => [
                    '1' => "Currently {city} does not have {perfromer} scheduled for appearancees. The closest location to {city} where {performer} will play is {nearby_city}.",
                    'past' => "However, last time {performer} played in {city} was in {last_appearance_year}.",
                    'guests' => "That show had guest apperance of {past_guests}.",
                    'songs' => "{performer} played the following hits {past_songs_in_city}.",
                    'last' => "Fans are impatiently awaiting next {performer_s} concerts in {city}."
                ],
                '2' => [
                    '1' => "{perfromer} is making {his_her} way through North America, coming from straight from {previous_city} to {city}. Fans in {city} has been waiting for {performer_s} concert, which will happen on {date}.",
                    'albums' => "Fans are hoping {he_she} will play songs from last album, {last_album} like {top_last_songs_text} as well as classics like {other_songs}.",
                    'no_albums' => "Fans are hoping {he_she} will perform tracks from {his_her} last album and possibly some classics ones too.",
                    'solo_guests' => "In the past {performer} toured with {past_guests}, unfortunatelly, in {city} it is going to be just {perfromer}.",
                    'solo_no_guests' => "Typically {performer} toured alone without guests which is going to be the case in {city} as well.",
                    'no_solo_guests' => "Considering {performer_s} previous tours with {past_guests}, this time around, {he_she} will appear with {current_guests}.",
                    'no_solo_no_guests' => "{performer} had toured alone in the past, but to everyone's surprise, {he_she} will have {current_guests} this event in {city}.",
                    'last' => "{venue} will host {perfromer} on {date}. Additional information available on {event_page_link}.",
                ]
            ];

        return $sentences[$part][$index];
    }

    # Finds all instances of a needle in the haystack and returns the array

    private static function _get_performer_events_and_genre($performer = false)
    {
        $concerts = $performer->concerts()
            ->with('genres')
            ->with('venue')
            ->get();
        $genre = VarsHelper::get_top_genre($concerts);
        $tourData = new stdClass();
        $hotConcerts = new stdClass();
        switch ($concerts->count()):
            case 0:
                $hotConcerts->firstConcert = false;
                $hotConcerts->lastConcert = false;
                $hotConcerts->random1 = false;
                $hotConcerts->random2 = false;
                $hotConcerts->random3 = false;
                $hotConcerts->count = 0;
                break;
            case 1:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = false;
                $hotConcerts->random1 = false;
                $hotConcerts->random2 = false;
                $hotConcerts->random3 = false;
                $hotConcerts->count = 1;
                break;
            case 2:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = $concerts->last();
                $hotConcerts->random1 = false;
                $hotConcerts->random2 = false;
                $hotConcerts->random3 = false;
                $hotConcerts->count = 2;
                break;
            case 3:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = $concerts->last();
                $hotConcerts->random1 = $concerts[1]; //set random to be between
                $hotConcerts->random2 = false;
                $hotConcerts->random3 = false;
                $hotConcerts->count = 3;
                break;
            case 4:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = $concerts->last();
                $hotConcerts->random1 = $concerts[1]; //set random to be between
                $hotConcerts->random2 = $concerts[2];
                $hotConcerts->random3 = false;
                $hotConcerts->count = 4;
                break;
            case 5:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = $concerts->last();
                $hotConcerts->random1 = $concerts[1]; //set random to be between
                $hotConcerts->random2 = $concerts[2];
                $hotConcerts->random3 = $concerts[3];
                $hotConcerts->count = 5;
                break;

            default:
                $hotConcerts->firstConcert = $concerts->first();
                $hotConcerts->lastConcert = $concerts->last();
                $hotConcerts->random1 = $concerts[1]; //set random to be between
                $hotConcerts->random2 = $concerts[2];
                $hotConcerts->random3 = $concerts[rand(3, $concerts->count() - 2)];
                $hotConcerts->count = $concerts->count();
                break;
        endswitch;
        $tourData->genre = $genre;
        $tourData->concerts = $hotConcerts;
        $tourData->season = VarsHelper::get_tour_season($hotConcerts->firstConcert, $hotConcerts->lastConcert);
        return $tourData;
    }


}

?>