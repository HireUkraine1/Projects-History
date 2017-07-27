<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class SearchIndexCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'miner:index';

    /**
     * The console command description.
     *cd /opt
     *
     * @var string
     */
    protected $description = 'Create Index for performers and events';

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
        $this->indexPerformers();
        $this->indexConcerts();
    }

    public function indexPerformers()
    {
        $replacements = ['
							1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six', '8' => 'eight', '9' => 'nine', '10' => 'ten',
            'i' => '1', 'ii' => '2', 'iii' => '3',
        ];
        $performers = Performer::all();
        $test = ["this", "is", "test", "of", "words"];
        foreach ($performers as $performer):
            $searchTerms = Search::where('type', 'performer')->where('performer_id', $performer->id)->first();
            $allKeywords = [];

            $name = strtolower($performer->name);
            if (!in_array($name, $allKeywords)) $allKeywords[] = $name;

            $keywords = explode(' ', $name);
            foreach ($keywords as $word):
                // array_push($allKeywords, $word);
                if (!in_array($word, $allKeywords)) $allKeywords[] = $word;
                $clean = preg_replace("/[^a-z0-9_\s-]/", "", $word);
                if (!in_array($clean, $allKeywords)) $allKeywords[] = $clean;

                // array_push($allKeywords, $clean);
                if (isset($replacements[$word])):
                    // array_push($allKeywords, $replacements[$word]);
                    if (!in_array($replacements[$word], $allKeywords)) $allKeywords[] = $replacements[$word];
                endif;
            endforeach;
            // $unique = array_unique($allKeywords);
            // $unique2 = array_unique($allKeywords2);
            if ($searchTerms):
                // echo "Found \n\r";
                $searchTerms->term = $allKeywords;
            else:
                $searchTerms = new Search;
                // echo "NOT FOUND \n\r";
                $searchTerms->type = 'performer';
                $searchTerms->performer_id = $performer->id;
                $searchTerms->name = $performer->name;

                $searchTerms->term = $allKeywords;
                // $searchTerms->term2 = $unique2;
            endif;
            // echo(json_encode($searchTerms));
            $searchTerms->save();
        endforeach;
    }

    public function indexConcerts()
    {
        $concerts = Concert::with('performers')->with('venue')->with('location')->get();
        foreach ($concerts as $concert):
            $allKeywords = [];
            $name = strtolower($concert->name);

            if (!in_array($name, $allKeywords)) $allKeywords[] = strtolower($name);
            $clean = preg_replace("/[^a-z0-9_\s-]/", "", $name);
            if (!in_array($clean, $allKeywords)) $allKeywords[] = strtolower($clean);
            $cnamearray = explode(' ', $clean);
            foreach ($cnamearray as $keyword) :
                $k = strtolower($keyword);
                if (!in_array($k, $allKeywords)) $allKeywords[] = $k;
            endforeach;
            //TODO: get nearby cities and epxlode them as well

            $performers = $concert->performers;
            foreach ($performers as $performer):
                $pname = strtolower($performer->name);
                if (!in_array($pname, $allKeywords)) $allKeywords[] = strtolower($pname);
                $clean = preg_replace("/[^a-z0-9_\s-]/", "", $pname);
                if (!in_array($clean, $allKeywords)) $allKeywords[] = $clean;
                $pnamearray = explode(' ', $clean);
                foreach ($pnamearray as $keyword) :
                    $k = strtolower($keyword);
                    if (!in_array($k, $allKeywords)) $allKeywords[] = $k;
                endforeach;
            endforeach;
            $venue = $concert->venue;
            $vname = strtolower($venue->name);
            $vnamearray = explode(' ', $vname);
            foreach ($vnamearray as $keyword) :
                if (!in_array($keyword, $allKeywords)) $allKeywords[] = $keyword;
            endforeach;

            $location = $concert->location;
            $lcity = strtolower($location->city);
            if (!in_array($lcity, $allKeywords)) $allKeywords[] = $lcity;
            $lstate = strtolower($location->state);
            if (!in_array($lstate, $allKeywords)) $allKeywords[] = $lstate;
            $lstate_full = strtolower($location->state_full);
            if (!in_array($lstate_full, $allKeywords)) $allKeywords[] = $lstate_full;
            $searchTerms = Search::where('type', 'concert')->where('concert_id', $concert->id)->first();
            if ($searchTerms):
                echo "Concert Found \n\r";
                $searchTerms->term = $allKeywords;
            else:
                $searchTerms = new Search;
                echo "Concert NOT found \n\r";
                $searchTerms->type = 'concert';
                $searchTerms->concert_id = $concert->id;
                $searchTerms->name = $concert->name;

                $searchTerms->term = $allKeywords;
                // $searchTerms->term2 = $unique2;
            endif;
            // echo(json_encode($searchTerms));
            $searchTerms->save();

            # code...
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