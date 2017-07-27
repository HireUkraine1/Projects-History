<?php
ini_set("memory_limit", "-1");

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RunDailyCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:run-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crons to run daily.';

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
        $file = '/var/www/site/app/storage/logs/daily.lock';
        $runId = date('U');
        if (!file_exists($file)):
            $handle = fopen($file, 'w');
            fclose($handle);
            $refresh = false;
            echo "\n\r________________________START_______________________";
            //lets try populating performers
            mail("email@site.com", "[$runId] Daily Run Started!", "Watch!");
            echo "\n\r________________________GET PERFORMERS_______________________";
            $p = new GetPerformerDataCommand;
            $p->populatePerformers();
            echo "\n\r________________________GET VENUES_______________________";
            $v = new GetVenueDataCommand;
            $v->getTnVenues();
            mail("email@site.com", "[$runId] process", "Getting TN Concert Information");
            echo "\n\r________________________GET CONCERTS_______________________";
            $c = new GetConcertDataCommand;
            $c->getTnConcerts();
            echo "\n\r________________________GET CONCERT PERFORMERS_______________________";
            $c->getTnConcertPerformers();

            mail("email@site.com", "[$runId] process", "Creating or Updating Spin Text");
            echo "\n\r________________________SPIN CITY_______________________";
            $spinner = new SpinCommand();
            $spinner->spin_text();

            // $t = new GetTicketsCommand();
            // $t->getTickets();
            // mail("email@site.com", "[$runId] process", "Got performers, venues, concerts, and tickets");

            //assign genre
            mail("email@site.com", "[$runId] process", "Assigning Genres");
            echo "\n\r________________________ASSIGN GENRES_______________________";
            $g = new AssignGenresCommand();
            $g->assignGenres();

            mail("email@site.com", "[$runId] process", "Updating Counts");
            echo "\n\r________________________UPDATE COUNTS_______________________";
            $cnt = new UpdateCountsCommand();
            $cnt->update_concert_count();

            mail("email@site.com", "[$runId] process", "Updating Search Index");
            echo "\n\r________________________UPDATE INDEX_______________________";
            $index = new SearchIndexCommand();
            $index->indexPerformers();
            $index->indexConcerts();

            echo "\n\r________________________END_______________________";

            mail("email@site.com", "[$runId] process", "Sending Emails");
            echo "\n\r________________________SEND EMAILS_______________________";
            $sn = new SendNoticesCommand();
            $sn->tourTracker();


            mail("email@site.com", "[$runId] Daily Run Finished!", "Finished!!");
            unlink($file);
            echo "\n\r________________________END_______________________";
        else:
            mail("email@site.com", "[$runId] Daily Run could not begin!", "Not finished previous!!");
        endif;

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(// array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
