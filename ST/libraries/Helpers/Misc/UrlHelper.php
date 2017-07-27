<?php

class Urlhelper
{

    //might not actually use thi
    public static function choke($layout, $errorType = 404, $arguments = array())
    {
        if ($errorType == 'soft'):
            $layout->mainmenu = View::make('frontend.global.mainmenu');
            $layout->content = View::make('frontend.global.soft-404', $arguments);
            return $layout;
        else:
            return Redirect::to('/not-found-404');
        endif;
    }

    public static function isRedirected($slug)
    {
        $slug = (substr($slug, 0, 1) == '/') ? $slug : "/" . $slug;
        $slug = strtolower(trim($slug));
        return SlugRedirect::where('url', '=', $slug)->first();
        // return DB::table('active_redirects')->where('url','=', "/".$slug)->first();
    }

    public static function getRealSlug($slug)
    {
        $realSlug = DB::table('featured_locations')->where('feat_slug', '=', $slug)->first();
        return ($realSlug) ? $realSlug->real_slug : $slug;
    }

    public static function createRedirect($from, $to, $type = 301)
    {
        $from = (substr($from, 0, 1) == '/') ? $from : "/" . $from;
        $to = (substr($to, 0, 1) == '/') ? $to : "/" . $to;
        $redirect = SlugRedirect::firstOrNew(['url' => $from]);
        $redirect->to_url = $to;
        $redirect->redirect_type = $type;
        $redirect->status = 1;
        $redirect->save();
        return $redirect;
    }

    public static function hackPurchaseURL($tnEventId, $tid, $siteNum = 0)
    {
        try {
            $brokerId = Config::get('ticketnetwork.tnProdData.brokerID');
            $url = "xxxxxxxxxxxxxxxxxxxxxx";
            $client = new GuzzleHttp\Client();
            $res = $client->get($url);
            $body = $res->gesaleody();

            $startTag = "Ticks{$tid}\\', \\'";
            $endTag = "\\')\"";
            $searched = StringHelper::regexBetween($body, $startTag, $endTag);
            return $searched;
        } catch (Exception $e) {
            return false;
        }
    }

}