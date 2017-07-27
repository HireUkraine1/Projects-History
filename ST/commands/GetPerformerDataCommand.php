<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetPerformerDataCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:get-performer';

    /**
     * The console command description.
     *cd /opt
     * @var string
     */
    protected $description = 'Gets all available performer data and updates as needed';

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

        //get genres
        $this->pullGenres();
        //get performers
        //prep mongo
        DB::disableQueryLog();
        DB::connection('mysql')->disableQueryLog();
        $refresh = ['sl' => false, 'lfm' => false];
        $this->populatePerformers(false, $refresh);
        $this->stagePerformers();
    }

    public function pullGenres($refresh = false)
    {
        if ($refresh) Genre::truncate();
        $tn = new TicketNetwork\Api\TicketNetwork;
        $params = array();
        $genres = $tn->run('GetCategoriesMasterList', $params)->GetCategoriesMasterListResult->Category;
        foreach ($genres as $tnGenre):
            if ($tnGenre->ParentCategoryID == 2):
                if ($tnGenre):
                    $properName = ucwords(strtolower($tnGenre->ChildCategoryDescription));
                    $grandChild = str_replace('-', '', $tnGenre->GrandchildCategoryDescription);
                    if ($grandChild):
                        $properName .= " - " . ucwords(strtolower($grandChild));
                    endif;
                    $genre = Genre::firstOrCreate([
                        'slug' => StringHelper::create_slug($properName),
                        'tn_parent_category_id' => $tnGenre->ParentCategoryID,
                        'tn_child_category_id' => $tnGenre->ChildCategoryID,
                        'tn_grandchild_category_id' => $tnGenre->GrandchildCategoryID,
                    ]);
                    $genre->genre = $properName;
                    $genre->save();
                endif;
            endif;
        endforeach;
    }

    public function populatePerformers($rerun = false, $refresh = false)
    {

        if ($rerun):
            // Performer::truncate();
            // PerformerDetails::query("db.performers.remove({});");
        endif;
        $getCount = 5;
        // Log::info("_____________________________________________________________________________");
        // Log::info("Starting getting all performers...from TN ");
        //init ticket network instance
        $tn = new TicketNetwork\Api\TicketNetwork;

        $params = array('parentCategoryID' => 2);
        $performers = $tn->run('GetPerformerByCategory', $params)->GetPerformerByCategoryResult->Performer;
        $i = 0;
        mail("email@site.com", "STARTED: Getting performers", "Total: " . count($performers));
        //prep album table for next run
        DB::table('tmp_album_stage')->truncate();
        foreach ($performers as $performer):
            MinerHelper::insert_performer($performer, $refresh);
        endforeach;

    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(//			array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }


}
