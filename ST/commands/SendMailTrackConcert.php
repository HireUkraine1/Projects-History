<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class SendMailTrackConcert extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:send-mail-track-concert';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'send email to user when they have track concert';


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
        ini_set("memory_limit", "-1");
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $this->clearSkipConcert();
        $this->sendEmail();
    }

    public function clearSkipConcert()
    {
        $allTrackConcerts = ConcertTrack::all();
        foreach ($allTrackConcerts as $trackConcert):
            try {
                $concert = Concert::where('id', $trackConcert->concert_id)->first();
                if ($concert instanceof Concert):
                    if (strtotime($concert->date) < strtotime(date("Y-m-d"))):
                        $trackConcert->delete();
                    endif;
                else:
                    $trackConcert->delete();
                endif;
            } catch (Exception $e) {
                echo $e->getMessage() . " line: " . $e->getLine();
            }

        endforeach;
    }

    public function sendEmail()
    {
        $allTrackConcerts = ConcertTrack::all();
        foreach ($allTrackConcerts as $trackConcert):
            $concert = Concert::with('tnConcert')->where('id', $trackConcert->concert_id)->first();
            $tnTix = $this->_get_tickets($concert->tnConcert->id);
            $tickets = isset($tnTix->Tickets->TicketGroup2) ? $tnTix->Tickets->TicketGroup2 : false;
            $arrayTicketData = [];
            if ($tickets):
                foreach ($tickets as $ticket):
                    if (array_key_exists($ticket->Section, $arrayTicketData)):
                        if ($arrayTicketData[$ticket->Section]['minPrice'] < VarsHelper::addPercent($ticket->convertedActualPrice, 0)) $arrayTicketData[$ticket->Section]['minPrice'] = VarsHelper::addPercent($ticket->convertedActualPrice, 0);
                        $arrayTicketData[$ticket->Section]['countTicket'] += $ticket->TicketQuantity;
                    else:
                        $arrayTicketData[$ticket->Section]['minPrice'] = VarsHelper::addPercent($ticket->convertedActualPrice, 0);
                        $arrayTicketData[$ticket->Section]['countTicket'] = $ticket->TicketQuantity;
                    endif;
                endforeach;

                if (isset($arrayTicketData[$trackConcert->place_section])):
                    if ($trackConcert->price_tickets > 0):
                        $minPrice = $arrayTicketData[$trackConcert->place_section]['minPrice'];
                        if ($trackConcert->price_tickets > $minPrice):
                            $subject = "Price drop: concert $concert->name at  $concert->date";
                            $dif = $trackConcert->price_tickets - $minPrice;
                            $message = "Best price, per person:  \$$minPrice - drop to \$$dif";
                            $this->_sendTracking($trackConcert->fan_id, $trackConcert->concert_id, $subject, $message);
                        elseif ($trackConcert->price_tickets < $minPrice):
                            $subject = "Price rise: concert $concert->name at  $concert->date";
                            $dif = '$' . $minPrice - $trackConcert->price_tickets;
                            $message = "Best price, per person:  \$$minPrice - up to \$$dif";
                            $this->_sendTracking($trackConcert->fan_id, $trackConcert->concert_id, $subject, $message);
                        endif;
                    endif;

                    if ($trackConcert->count_tickets > 0):
                        $minCountTicket = $arrayTicketData[$trackConcert->place_section]['countTicket'];
                        if ($trackConcert->count_tickets > $minCountTicket):
                            echo 'Sendemail';
                            $subject = "Count ticket drop: concert $concert->name at  $concert->date";
                            $dif = $trackConcert->count_tickets - $minCountTicket;
                            $message = "Count ticket drop: now $minCountTicket pcs - drop to $dif pcs.";
                            $this->_sendTracking($trackConcert->fan_id, $trackConcert->concert_id, $subject, $message);
                        endif;
                    endif;
                endif;
            endif;
        endforeach;
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

    private function _sendTracking($fanId, $concert_id, $subject, $message)
    {
        $fan = Fan::where('id', $fanId)->first();
        echo "\n\r email sent to $fan->name ";
        $notices['concert_id'] = $concert_id;
        $notices['subject'] = $subject;
        $notices['message'] = $message;
        Mail::send('emails.frontend.fans.track-concerts', array('fan' => $fan, 'notices' => $notices), function ($message) use ($fan, $subject) {
            $message->to($fan->email, $fan->name)->subject($subject);
        });
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