<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetInfoTicketsStatisticsCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-info-tickets-tn-statistics';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'get info about all tickets from TicketNetwork';

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
        ini_set("memory_limit", "-1");
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $this->getAllTickets();
    }

    public function getAllTickets()
    {

        if (file_exists('/var/www/site/app/storage/logs/concert_tn.log')) {
            Mail::send([], [], function ($message) {

                $message->from('us@example.com', 'site');

                $message->to('email@site.com')->subject('take tickets info from TN previous not Finish ');

                $message->attach('/var/www/site/app/storage/logs/concert_tn.log');

            });
        } else {
            $tn = new TicketNetwork\Api\TicketNetwork;
            $timeStart = microtime(true);
            $loadedConfig = Config::get('ticketnetwork.tnProdData.websiteConfigID');
            $futureConcerts = Concert::where('date', '>', date('Y-m-d H:i:s'))->get();
            $count = $futureConcerts->count();

            $fileLog = fopen('/var/www/site/app/storage/logs/concert_tn.log', 'a');
            fwrite($fileLog, " COUNT CONCERT: $count ");
            fclose($fileLog);

            foreach ($futureConcerts as $futureConcert) {
                echo "yet $count \n";
                $count -= 1;
                try {
                    sleep(1);
                    $fileLog = fopen('/var/www/site/app/storage/logs/concert_tn.log', 'a');
                    fwrite($fileLog, "\n sale concert id: " . $futureConcert->id);
                    fclose($fileLog);

                    $params = array(
                        'eventId' => $futureConcert->concertTN->id,
                        'websiteConfigId' => $loadedConfig,
                        'translationLanguageId' => 0
                    );

                    $tickets = $tn->run('GetEventTickets', $params);

                    if (isset($tickets->GetEventTicketsResult->Tickets->TicketGroup)):
                        $concertTickets = $tickets->GetEventTicketsResult->Tickets->TicketGroup;
                    else:
                        $concertTickets = [];
                    endif;

                    $concert = new ConcertTicketMongo;
                    $concert->create_date = date('Y-m-d');
                    $concert->sale_concert_id = $futureConcert->id;
                    $concert->tn_concert_id = $futureConcert->concertTN->id;
                    $concert->tickets_info = $concertTickets;
                    $concert->save();

                } catch (Exception $e) {
                    $fileLog = fopen('/var/www/site/app/storage/logs/concert_tn.log', 'a');
                    fwrite($fileLog, " " . $e->getMessage() . " line: " . $e->getLine() . " do not have any tickets ");
                    fclose($fileLog);

                }

            };

            $time = microtime(true) - $timeStart;
            echo "Script time: " . round($time, 2) . "sec";


            $fileLog = fopen('/var/www/site/app/storage/logs/concert_tn.log', 'a');
            fwrite($fileLog, "\n Script time: " . round($time, 2) . "sec");
            fclose($fileLog);

            Mail::send([], [], function ($message) {
                $message->from('info@site.com', 'site');
                $message->to('email@site.com')->subject('Finish take tickets info from TN');
                $message->attach('/var/www/site/app/storage/logs/concert_tn.log');

            });

            unlink('/var/www/site/app/storage/logs/concert_tn.log');
        };

    }

}