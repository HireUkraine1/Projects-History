<?php

class TicketController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default City Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |
    |
    */
    protected $layout = 'frontend.layouts.site';

    public function tickets($id = null)
    {
        if (Session::has('fan')):
            $target = 'concert';
        else:
            $target = 'login';
        endif;

        $concert = Concert::with('tnConcert')->with('venue')->with('venue.tnVenue')->where('id', $id)->first();
        if (!$concert) App::abort('404');
        $breadcrumbs = [
            ['link' => "/tickets/{$id}", 'title' => 'Tickets For ' . $concert->name],
        ];
        View::share('breadcrumbs', $breadcrumbs);

        $metadata = [
            'meta' => [
                'description' => "Get event tickets",
                'keywords' => "tickets,$concert->name",
                'title' => $concert->name . " Tickets",
            ],
        ];
        View::share('metadata', $metadata);

        $tnTix = $this->_get_tickets($concert->tnConcert->id);

        $tickets = isset($tnTix->Tickets->TicketGroup2) ? $tnTix->Tickets->TicketGroup2 : false;

        if ($tickets):
            if (count($tickets) && isset($tickets->EventID)): //failsafe for TN sending only one ticket group not an array, them fuckers
                $tickArray = [];
                $tickArray[] = $tickets;
                $tickets = $tickArray;
            endif;
            $minMax = VarsHelper::ticketMinMax($tickets);
            $ticketData = ["MinPrice" => $minMax->minPrice, "MaxPrice" => $minMax->maxPrice, "TicketCounts" => $minMax->quantity];
            $arrayTicketData = [];
            foreach ($tickets as $ticket):
                $split = (count($ticket->ValidSplits->int) > 1) ? $ticket->ValidSplits->int : [($ticket->ValidSplits->int)];
                asort($split);
                $ticketData['Tickets'][] = ['id' => $ticket->ID, 'section' => $ticket->Section, 'row' => $ticket->Row, 'price' => VarsHelper::addPercent($ticket->convertedActualPrice, 0), 'real_price' => $ticket->ActualPrice, 'retail_price' => $ticket->RetailPrice, 'converted_price' => $ticket->convertedActualPrice, 'notes' => $ticket->Notes, 'eticket' => ($ticket->deliveryOptions == 'ID') ? true : false, 'preferred' => ($ticket->Rating > 0) ? true : false, 'quantity' => $split];
                if (array_key_exists($ticket->Section, $arrayTicketData)):
                    if ($arrayTicketData[$ticket->Section]['minPrice'] < VarsHelper::addPercent($ticket->convertedActualPrice, 0)) $arrayTicketData[$ticket->Section]['minPrice'] = VarsHelper::addPercent($ticket->convertedActualPrice, 0);
                    $arrayTicketData[$ticket->Section]['countTicket'] += $ticket->TicketQuantity;
                else:
                    $arrayTicketData[$ticket->Section]['minPrice'] = VarsHelper::addPercent($ticket->convertedActualPrice, 0);
                    $arrayTicketData[$ticket->Section]['countTicket'] = $ticket->TicketQuantity;
                endif;

            endforeach;

            $checkTrack = null;
            if (Session::has('fan')):

                $sessionFan = Session::get('fan');
                $checkTrack = ConcertTrack::where('fan_id', $sessionFan->id)->where('concert_Id', $id)->get();

                if (count($checkTrack) < 0):
                    $checkTrack = null;
                endif;

            endif;

            $sort = Input::get('sort');
            if ($sort == "desc"):
                usort($ticketData['Tickets'], function ($a, $b) {
                    return $b['price'] - $a['price'];
                });

            elseif ($sort == 'asc'):
                usort($ticketData['Tickets'], function ($a, $b) {
                    return $a['price'] - $b['price'];
                });
            endif;
            // DebugHelper::pdd($concert->tnConcert->venue_config_id,true);
            $this->layout = View::make('frontend.layouts.tncheckout');
            $this->layout->customjs = View::make('frontend.tickets.customjs2', ['tnConcert' => $concert->tnConcert, 'ticketData' => $ticketData]);
            $this->layout->tagline = View::make('frontend.tickets.tickets-tagline');
            $this->layout->content = View::make('frontend.tickets.tickets-custom-content', ['concert' => $concert, 'ticketData' => $ticketData, 'id' => $id, 'target' => $target, 'arrayTicketData' => $arrayTicketData, 'concertTrack' => $checkTrack]);
        else:
            // $this->layout->customjs = View::make('frontend.tickets.customjs');
            $this->layout->content = View::make('frontend.tickets.tickets-custom-content', ['ticketData' => false, 'id' => $id, 'concert' => $concert, 'arrayTicketData' => null, 'concertTrack' => []]);
        endif;
    }

    private function _get_tickets($tnEventId = null)
    {
        $tn = new TicketNetwork\Api\TicketNetwork('ticketnetwork.tnProdData');
        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $params = array(
            'websiteConfigId' => $loadedConfig,
            'websiteConfigID' => $loadedConfig,
            'translationLanguageId' => '0',
            'eventId' => $tnEventId,
        );
        $tickets = $tn->run('GetEventTickets2', $params)->GetEventTickets2Result;
        return $tickets;
    }

    public function pre_checkout() //add breadcrumbs //do someting with cart data
    {
        if (Input::has('id')): //diz post
            Session::forget('cartData'); // clear old pending data
            $cartData = Input::all();
            $concert = Concert::where('id', $cartData['event_id'])->with('performers')->with('performers.images')->with('venue')->with('location')->first();
            $cartData['concert'] = $concert;
            Session::put('cartData', $cartData);
        else:
            $cartData = Session::get('cartData');
            $concert = Concert::where('id', $cartData['event_id'])->with('performers')->with('performers.images')->with('venue')->with('location')->first();
            $cartData['concert'] = $concert;
        endif;
        $fan = Session::get('fan');

        if (isset($cartData['id'])):

            if (isset($fan->info) && $fan->info->status == 1): //paid mmember
                return Redirect::to('/checkout/step2');
            else:
                $subPrice = $cartData['total'];
                $percent = 13 / 100;
                $fee = $subPrice * $percent;
                if ($fee < 45):
                    return Redirect::to('/checkout/step2');
                endif;

                $redirect = ['redirect' => 1];
                Session::put('cartRedirect', $redirect);
                $breadcrumbs = [
                    ['link' => '/concerts/' . $concert->slug, 'title' => $concert->name],
                    ['link' => '/tickets/' . $concert->id, 'title' => $concert->name . " Tickets"],
                    ['link' => '#', 'title' => "Review Order"],
                ];
                View::share('breadcrumbs', $breadcrumbs);
                $this->layout->tagline = View::make('frontend.tickets.confirm-tagline', ['concert' => $cartData['concert']]);
                $this->layout->content = View::make('frontend.tickets.confirm2-content', ['concert' => $cartData['concert'], 'tickets' => $cartData, 'fan' => $fan]);
            endif;
        else:
            return Redirect::to('/');
        endif;
    }

    public function checkout()
    {
        $fan = Session::get('fan');
        $cartData = Session::get('cartData');
        if (!$cartData) return Redirect::to('/stage');
        $tnEventId = $cartData['concert']->tnConcert->id;
        $seessionId = StringHelper::randomString(5);
        $brokerId = Config::get('ticketnetwork.tnProdData.brokerID');
        Session::forget('cartData');//clear cart data
        if (isset($fan->info->status) && $fan->info->status == 1):
            $siteNumber = 1;
            $ppcsrc = "&ppcsrc={$fan->id}";
        else:
            $siteNumber = 0;
            $ppcsrc = '&ppcsrc=0';
        endif;

        $encryptedInfo = UrlHelper::hackPurchaseURL($tnEventId, $cartData['id'], $siteNumber);
        if ($encryptedInfo):
            $purchaseUrl = 'https://tickettransaction2.com/Checkout.aspx?e=' . $encryptedInfo . $ppcsrc . '&treq=' . $cartData['qty'] . '&wcid=14832&SessionId=' . $seessionId;
        else: //failsafe mechanism
            $purchaseUrl = "https://tickettransaction2.com/Checkout.aspx?brokerid={$brokerId}&sitenumber={$siteNumber}&tgid={$cartData['id']}&treq={$cartData['qty']}&evtID={$cartData['event_id']}&SessionId={$seessionId}{$ppcsrc}";
        endif;
        return Redirect::to($purchaseUrl);
    }

    public function cancel_checkout()
    {
        Session::forget('cartData');
        Session::forget('redirect');
        return Redirect::to('/stage');
    }

    public function ticket_specials()
    {
        $breadcrumbs = [
            ['link' => "/ticket-specials", 'title' => 'Ticket Specials '],
        ];
        View::share('breadcrumbs', $breadcrumbs);

        $metadata = [
            'meta' => [
                'description' => "Looking for ticket promotions including ticket giveaways, coupon codes, and more? Follow our social media accounts to get the latest ticket deals.",
                'keywords' => "tickets",
                'title' => "site Concert Ticket Coupon Codes and Specials",
            ],
        ];
        View::share('metadata', $metadata);
        $this->layout->content = View::make('frontend.tickets.ticketspecials-content');
        $this->layout->tagline = View::make('frontend.tickets.ticketspecials-tagline');
    }

    private function _getEvent($tnEventId = null)
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
    }

    private function _getVenueConfig($tnVenueId = null)
    {
        $tn = new TicketNetwork\Api\TicketNetwork('ticketnetwork.tnProdData');
        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $params = array(
            // 'websiteConfigId'		=>  $loadedConfig,
            'websiteConfigID' => $loadedConfig,
            'venueID' => $tnVenueId,
            // 'versionNumber'			=> '1',

        );
        // DebugHelper::pdd($params,true);
        $venueConfig = $tn->run('GetVenueConfigurations', $params)->GetVenueConfigurationsResult;
        DebugHelper::pdd($venueConfig, true);
        return $venueConfig;
    }

}