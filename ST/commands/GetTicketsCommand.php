<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetTicketsCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-tickets';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'get tickets';

    protected $tn;

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
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        // $this->getTickets();
        $this->getAllTickets();
    }

    public function getAllTickets()
    {
        $tn = new TicketNetwork\Api\TicketNetwork;

        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $params = array(
            'websiteConfigId' => $loadedConfig,
            'translationLanguageId' => 0

        );
        $tickets = $tn->run('GetEventTickets', $params)->GetEventTicketsResult->Tickets;
        var_dump($tickets->TicketGroup);
        die();

    }

    public function getTickets()
    {
        DB::table('tn_tickets')->truncate();
        $tn = new TicketNetwork\Api\TicketNetwork;
        // $tnConcert = TnConcert::all();
        $saleConcerts = Concert::with('tnConcert')->where('date', '>', date('Y-m-d H:i:s'))->get();
        //BECAUSE TN IS A BITCH
        $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
        $allEvents = count($saleConcerts);
        echo "\n\r Event count: " . $allEvents;
        $ongoing = 0;
        foreach ($saleConcerts as $sale):
            try {

                $concert = $sale->tnConcert;
                $ongoing++;
                //legacy
                //
                //
                //
                $params = array(
                    'eventId' => $concert->id,
                    'websiteConfigId' => $loadedConfig,
                    'translationLanguageId' => 0

                );
                $tickets = $tn->run('GetEventTickets2', $params)->GetEventTickets2Result->Tickets;
                if (isset($tickets->TicketGroup2)):
                    if (is_array($tickets->TicketGroup2)):
                        $arrayOfTickets = $tickets->TicketGroup2;
                    else:
                        $arrayOfTickets = [];
                        $arrayOfTickets[] = $tickets->TicketGroup2;
                    endif;


                    $total = count($arrayOfTickets);
                    echo "\n\r GOT $total tickets ($ongoing out of $allEvents)";
                    $i = 0;
                    foreach ($arrayOfTickets as $tnTicket):
                        try {
                            $i++;
                            $ticket = TnTicket::firstOrNew(['tn_id' => $tnTicket->ID]);
                            $ticket->concert_id = $concert->concert_id;
                            $ticket->tn_event_id = $concert->id;
                            $ticket->face_price = (isset($tnTicket->FacePrice)) ? $tnTicket->FacePrice : '';
                            $ticket->high_seat = (isset($tnTicket->HighSeat)) ? $tnTicket->HighSeat : '';
                            // $ticket->tn_id = (isset($tnTicket->ID)) ? $tnTicket->ID : '';
                            $ticket->low_seat = (isset($tnTicket->LowSeat)) ? $tnTicket->LowSeat : '';
                            $ticket->marked = (isset($tnTicket->Marked)) ? $tnTicket->Marked : '';
                            $ticket->notes = (isset($tnTicket->Notes)) ? $tnTicket->Notes : '';
                            $ticket->rating = (isset($tnTicket->Rating)) ? $tnTicket->Rating : '';
                            $ticket->rating_description = (isset($tnTicket->RatingDescription)) ? $tnTicket->RatingDescription : '';
                            $ticket->retail_price = (isset($tnTicket->RetailPrice)) ? $tnTicket->RetailPrice : '';
                            $ticket->row = (isset($tnTicket->Row)) ? $tnTicket->Row : '';
                            $ticket->quantity = (isset($tnTicket->TicketQuantity)) ? $tnTicket->TicketQuantity : '';
                            $ticket->group_type = (isset($tnTicket->TicketGroupType)) ? $tnTicket->TicketGroupType : '';
                            $ticket->converted_actual_price = (isset($tnTicket->convertedActualPrice)) ? $tnTicket->convertedActualPrice : '';
                            $ticket->mercury = (isset($tnTicket->isMercury)) ? $tnTicket->isMercury : '';
                            $ticket->actual_price = (isset($tnTicket->ActualPrice)) ? $tnTicket->ActualPrice : '';
                            $ticket->section = (isset($tnTicket->Section)) ? $tnTicket->Section : '';
                            $ticket->wholesale_price = (isset($tnTicket->WholesalePrice)) ? $tnTicket->WholesalePrice : '';
                            $ticket->save();
                            // $concert->associate($ticket);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    endforeach;
                else:
                    echo "\n\r Ticket group is not set";
                endif;

            } catch (Exception $e) {
                echo $e->getMessage();
            }
        endforeach;
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