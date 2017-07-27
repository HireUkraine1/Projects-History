<?php

class OrderTicketsController extends BaseController
{
    protected $layout = 'admin.layouts.default';
    protected $conf = '';

    public function allSaleTickets()
    {
        if (Input::file('uploadfile')) {
            $file = Input::file('uploadfile');
            $fileOpen = fopen($file->getRealPath(), "r");
            if ($fileOpen) {
                $i = 0;
                while (!feof($fileOpen)) {
                    $array[$i] = (fgetcsv($fileOpen, ""));
                    if ($i == 0) {
                        $head = $array[$i];
                    } else {
                        $elements = $array[$i];
                        for ($y = 0; $y <= count($elements) - 1; $y++) {
                            $csvFileArray[$i][$head[$y]] = $elements[$y];
                        }
                    }

                    $i++;
                }

            } else {
                echo "Error: cann't open file";
            }
            fclose($fileOpen);
            if (isset($csvFileArray)) {
                foreach ($csvFileArray as $parametrs) {
                    $ticketId = $parametrs['ticket_request_id'];
                    $ticket = OrderTickets::where('ticket_request_id', $ticketId)->first();
                    if ($parametrs['ticket_request_id'] != null && $ticket == null) {
                        $oTime = strtotime($parametrs['order_date']);
                        $orderTime = date("Y-m-d H:i:s", $oTime);
                        $tnVenue = TnVenue::whereid($parametrs['venue_id'])->first();
                        $venueId = $tnVenue->venue_id;
                        $cTime = strtotime($parametrs['event_date']);
                        $concertTime = date("Y-m-d H:i:s", $cTime);
                        $concert = Concert::where('name', $parametrs['event'])->where('date', $concertTime)->where('venue_id', $venueId)->first();
                        if (is_object($concert)) {
                            $concertId = $concert->id;
                        } else {
                            $concertTime = date("Y-m-d", $cTime);
                            $name = $parametrs['event'];
                            $concert = Concert::where('name', $name)->where('date', 'LIKE', "$concertTime%")->where('venue_id', $venueId)->first();
                            if (is_object($concert)) {
                                $concertId = $concert->id;
                            } else {
                                $concertId = 0;
                            }
                        }

                        if ($parametrs['phone_order'] == 'Y') {
                            $phoneOrder = 1;
                        } else {
                            $phoneOrder = 0;
                        }

                        if ($parametrs['is_mobile'] == 'Y') {
                            $mobile = 1;
                        } else {
                            $mobile = 0;
                        }
                        $provider = 'TN';
                        $orderTicket = new OrderTickets;
                        $orderTicket->ticket_request_id = $parametrs['ticket_request_id'];
                        $orderTicket->website_name = $parametrs['website_name'];
                        $orderTicket->order_type = $parametrs['order_type'];
                        $orderTicket->order_at = $orderTime;
                        $orderTicket->save();
                    }
                }
            }
        }
        //$orderTicets=OrderTickets::all();
        $orderTicets = OrderTickets::with('fan')->paginate(10);

        if (!Input::get('page') || Input::get('page') == 1):
            $this->layout->addform = View::make('admin.orders.addform');
        endif;


        $this->layout->customjs = View::make('admin.orders.cssjs');

        $this->layout->content = View::make('admin.orders.order-tickets', ['orderTicets' => $orderTicets]);

    }

}
