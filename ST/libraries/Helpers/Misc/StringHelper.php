<?php

class StringHelper
{
    public static function create_slug($string, $separator = '-')
    {
        $string = trim($string);
        if (!$string) return null;
        //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = strtolower($string);
        //Strip any unwanted characters
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", $separator, $string);
        return $string;
    }

    public static function hash($string, $separator = '')
    {
        $string = trim($string);
        if (!$string) return null;
        //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = strtolower($string);
        //Strip any unwanted characters
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", $separator, $string);
        return $string;
    }


    public static function clean_venue_name($venue)
    {
        //sometimes venue comes with garbage in it...
        $fixCase = str_replace('(Formerly', '(formerly', $venue);// if some smartass put (Formerly, then we convert that to lover case just that
        $vArray = explode('(formerly', $fixCase);
        return trim($vArray[0]); //return not dignity
    }

    public static function fix_zip($zip)
    {
        return strtoupper(str_replace(' ', '', $zip));
    }

    public static function lookup_state($state, $country, $reverse = false)
    {
        if (!$state || !$country) return null;

        $state = (string)$state;
        $states = [
            'US' => ['00' => null, 'FM' => 'Federated States of Micronesia', 'MH' => 'Marshall Islands', 'PW' => 'Palau', 'AS' => 'American Somoa', 'GU' => 'Guam', 'MP' => 'Northern Mariana Islands', 'PR' => 'Puerto Rico', 'VI' => 'Virgin Islands', 'UM' => 'US Minor Outlying Islands', 'AA' => 'US Armed Forces - Americas', 'AE' => 'US Armed Forces - Europe', 'AP' => 'US Armed Forces - Pacific', 'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DC' => 'Dist. Columbia', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'],
            'CA' => ['AB' => 'Alberta',
                'BC' => 'British Columbia',
                'LB' => 'Labrador',
                'MB' => 'Manitoba',
                'NB' => 'New Brunswick',
                'NF' => 'Newfoundland',
                'NS' => 'Nova Scotia',
                'NU' => 'Nunavut',
                'NW' => 'North West Territory',
                'ON' => 'Ontario',
                'PE' => 'Prince Edward Island',
                'QC' => 'Quebec',
                'SK' => 'Saskatchewen',
                'YT' => 'Yukon',
                'NL' => 'Newfoundland and Labrador',
                'NT' => 'Northwest Territories',
            ]
        ];
        if ($reverse):
            return array_search($state, $states[$country]);
        //implement if needed later
        else:
            try {
                if (!isset($states[strtoupper($country)])) return null;
                if (!isset($states[strtoupper($country)][strtoupper($state)])) return null;
                $lookedup = $states[strtoupper($country)][strtoupper($state)];
            } catch (Excption $e) {
                return null;
            }
            return $lookedup;
        endif;
    }

    public static function properize($string = false)
    {
        return $string . '\'' . ($string[strlen($string) - 1] != 's' ? 's' : '');
    }

    public static function create_list($items = false, $wrapTag = '"', $tag = false)
    {
        $total = count($items);
        if (!$total) return '';

        if ($total === 1):
            return StringHelper::wrap(end($items), $wrapTag, $tag);
        elseif ($total === 2):
            $rewrap = [];
            foreach ($items as $item):
                $rewrap[] = StringHelper::wrap($item, $wrapTag, $tag);
            endforeach;
            return implode(' and ', $rewrap);
        else:
            $last = array_pop($items);
            $last = StringHelper::wrap($last, $wrapTag, $tag);
            $rewrap = [];
            foreach ($items as $item):
                $rewrap[] = StringHelper::wrap($item, $wrapTag, $tag);
            endforeach;
            return implode(', ', $rewrap) . " and " . $last;
        endif;
    }

    public static function wrap($string, $tag = false, $isTag = true)
    {
        if ($isTag):
            $openTag = ($tag) ? "<{$tag}>" : '';
            $closeTag = ($tag) ? "</{$tag}>" : '';
        else:
            $openTag = ($tag) ? $tag : '';
            $closeTag = ($tag) ? $tag : '';
        endif;
        return $openTag . $string . $closeTag;
    }

    public static function cut_the($name)
    {
        $items = explode(' ', $name);
        $articles = ["a", "an", "the"];
        if (in_array(strtolower($items[0]), $articles)) $items[0] = '';
        return implode(' ', $items);
    }

    public static function clean_song($song)
    {

        $song = trim($song);
        if ($song === '') return "(no title)";
        $preserve = $song;
        // return $song;
        $song = preg_replace('/\([^)]*\)/', '', $song);
        $song = preg_replace('/\[[^)]*\]/', '', $song);
        $song = trim($song);
        if ($song === '') $song = $preserve;
        return $song;
    }

    public static function strongPassword($pass)
    {
        return true;
    }

    public static function randomString($str_len = 5)
    {
        $str = '';
        for ($i = 0; $i < $str_len; $i++):
            //97 is ascii code for 'a' and 122 is ascii code for z
            $str .= chr(rand(97, 122));
        endfor;
        return $str;
    }

    public static function cutString($string, $leng, $add = "...")
    {
        $newString = substr($string, 0, $leng);
        $newString = ($newString != $string) ? $newString . $add : $newString;
        return $newString;
    }

    public static function regexBetween($content, $start, $end)
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }

}

?>